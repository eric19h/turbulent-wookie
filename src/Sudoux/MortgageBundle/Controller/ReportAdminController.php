<?php

namespace Sudoux\MortgageBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Sudoux\Cms\ApiBundle\Model\RealtyWareSDK;

use Sudoux\Cms\ApiBundle\OAuth\GrantType\ApiCredentials;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\HttpFoundation\Response;

use Sudoux\Cms\LocationBundle\Entity\Location;
use Symfony\Component\Validator\Constraints\NotNull;
use Sudoux\Cms\FileBundle\Entity\File;
use Sudoux\MortgageBundle\Entity\Branch;
use Sudoux\MortgageBundle\Form\BranchType;
use SudoCms\SiteBundle\Form\SettingsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Guzzle\Http\Client;
use CommerceGuys\Guzzle\Plugin\Oauth2\Oauth2Plugin;
use CommerceGuys\Guzzle\Plugin\Oauth2\GrantType\RefreshToken;

/**
 * Class ReportAdminController
 * @package Sudoux\MortgageBundle\Controller
 * @author Dan Alvare
 */
class ReportAdminController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
	public function loansAction(Request $request)
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$siteIds = $site->getAllChildSiteIds();
    	array_push($siteIds, $site->getId());
    	
    	$form = $this->createFormBuilder()
	    	->add('start_date', 'date', array(
	    			'widget' => 'single_text',
	    			'format' => 'MM-dd-yyyy',
	    			'attr' => array('class' => 'datepicker'),
	    	))
	    	->add('end_date', 'date', array(
	    			'widget' => 'single_text',
	    			'format' => 'MM-dd-yyyy',
	    			'attr' => array('class' => 'datepicker'),
	    	))
	    	->add('loan_officer', 'entity', array(
	        		'class' => 'Sudoux\MortgageBundle\Entity\LoanOfficer',
	        		'property' => 'fullName',
	        		'empty_value' => 'All Loan Officers',
	    			'required' => false,
	    			'attr' => array('class' => 'span3'),
					'query_builder' => function(EntityRepository $er) use ($site) {
						return $er->findAllBySiteQuery($site);
					}
	        ))
	    	->getForm();
    	
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    	
    		if ($form->isValid()) {
    			$startDate = $form['start_date']->getData();
    			$endDate = $form['end_date']->getData();
    			$loanOfficerId = $form['loan_officer']->getData();
    			$siteLoans = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findCountBySiteIds($siteIds, $startDate, $endDate, $loanOfficerId);
    		}
    	} else {
    		$siteLoans = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findCountBySiteIds($siteIds);
    	}
    	
        return $this->render('SudouxMortgageBundle:ReportAdmin:loans.html.twig', array(
        	'siteLoans' => $siteLoans,
        	'form' => $form->createView(),		
        ));
    }

    /**
     * @return Response
     */
    public function getDataSummaryAction()
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();
    	 
    	$response = new \stdClass();
    
    	$siteIds = $site->getAllChildSiteIds();
    	array_push($siteIds, $site->getId());
    	$connection = $this->getDoctrine()->getConnection();

    	$sql = sprintf("SELECT created, SUM(loan_application) AS loans, SUM(lead) AS leads
				FROM (SELECT DATE(created) created, COUNT(*) loan_application, 0 lead 
				      FROM loan_application a
				      WHERE site_id IN (%s) AND a.deleted = 0
				      GROUP BY created 
				      UNION ALL
				      SELECT DATE(created) created, 0 loan_application, COUNT(*) lead
				      FROM lead l
				      WHERE site_id IN (%s) AND l.lead_status_id = 1
				      GROUP BY created
				     ) as A
				GROUP BY created
				ORDER BY created ASC;", implode(',', $siteIds), implode(',', $siteIds));

    	$stmt = $connection->prepare($sql);
    	$stmt->execute();
    	$response = $stmt->fetchAll();
    	return new Response(json_encode($response), 200, array('Content-Type'=>'application/json'));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function businessDevelopmentAction(Request $request)
    {
    	$site = $this->get('sudoux.cms.site')->getSite();    
    	$rw = new RealtyWareSDK($site);
    	$sites = $rw->get('summary');

    	return $this->render('SudouxMortgageBundle:ReportAdmin:businessDevelopment.html.twig', array(
    		'sites' => $sites,
    		'chartData' => json_encode($sites),
    	));
    }

    /**
     * @param Request $request
     * @param $siteId
     * @return Response
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function businessDevelopmentLeadAction(Request $request, $siteId)
    {
    	
    	$form = $this->createFormBuilder()
	    	->add('status', 'entity', array(
	    			'class' => 'Sudoux\Cms\FormBundle\Entity\LeadStatus',
	    			'property' => 'name',
	    			'label' => 'Status',
	    			'required' => false,
	    			'empty_value' => 'Filter by Status',
	    	))
	    	->add('start_date', 'date', array(
	    			'widget' => 'single_text',
	    			'format' => 'MM-dd-yyyy',
	    			'attr' => array('class' => 'datepicker', 'placeholder' => 'Start Date'),
	    	))
	    	->add('end_date', 'date', array(
	    			'widget' => 'single_text',
	    			'format' => 'MM-dd-yyyy',
	    			'attr' => array('class' => 'datepicker', 'placeholder' => 'End Date'),
	    	))
	    	->getForm();
    	
    	$site = $this->get('sudoux.cms.site')->getSite();
    	$rw = new RealtyWareSDK($site);
    	
    	$url = 'leads/' . $siteId;
    	
    	$params = array();
    	$page = $request->query->get('page', 1);
    	if($page != 1) {
    		$params['page'] = $page;
    	}
    	
    	$formData = $request->query->get('form');
    	if(!empty($formData['status'])) {
    		$params['status'] = $formData['status'];
	    	// set the default status
    		$em = $this->getDoctrine()->getEntityManager();
	    	$defaultStatus = $em->getRepository('SudouxCmsFormBundle:LeadStatus')->find($formData['status']);
	    	if(isset($defaultStatus)) {
		    	$form->get('status')->setData($defaultStatus);
	    	}
    	}
    	
    	if(!empty($formData['start_date'])) {
    		$startDate = \DateTime::createFromFormat('m-d-Y', $formData['start_date']);
    		$params['start_date'] = $startDate->format('Y-m-d');
	    	$form->get('start_date')->setData($startDate);
    	}
    	
    	if(!empty($formData['end_date'])) {
    		$endDate = \DateTime::createFromFormat('m-d-Y', $formData['end_date']);
    		$params['end_date'] = $endDate->format('Y-m-d');
	    	$form->get('end_date')->setData($endDate);
    	}
    	
    	$url .= $this->createQueryString($params);
    	
    	$leadData = $rw->get($url);
    	//print_r($leadData);
    	//exit;
    	$paginator = $this->get('knp_paginator');
    	$leads = $paginator->paginate(
    			$leadData['leads'],
    			1,
    			$leadData['limit']
    	);
    	
    	$leads->setTotalItemCount($leadData['totalCount']);
    	$leads->setCurrentPageNumber($page);
    	
    	$site = new \stdClass();
    	$site->id = $siteId;
    	
    	return $this->render('SudouxMortgageBundle:ReportAdmin:businessDevelopmentLead.html.twig', array(
    		'leads' => $leads,
    		'form' => $form->createView(),
    		'leadSite' => $site,
    	));
    }

    /**
     * @param $params
     * @return string
     */
    protected function createQueryString($params)
    {
    	$queryString = "";
    	
    	if(count($params) > 0) {
	    	$queryArray = array();
	    	foreach($params as $key => $value) {
	    		array_push($queryArray, $key . "=" . $value);
	    	}
	    	$queryString = '?' . implode('&', $queryArray);
    	}
    	
    	return $queryString;
    }
}
