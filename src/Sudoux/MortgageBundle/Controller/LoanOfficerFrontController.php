<?php

namespace Sudoux\MortgageBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Sudoux\Cms\SiteBundle\Controller\FrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LoanOfficerFrontController
 * @package Sudoux\MortgageBundle\Controller
 * @author Dan Alvare
 */
class LoanOfficerFrontController extends FrontController
{
    /**
     * The indexAction will get the site and parent site loan officers.
     * If the site type is loan officer, it will attempt to redirect to the site loan officers detail page and if its null, throw a 404
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();

        $branchId = $request->query->get('branch');

		$searchData = $request->query->get('form');
		$search = null;
        // check for a redirect based on site type
        $siteType = $site->getSiteType()->getKeyName();
        if(isset($siteType)) {
            $siteLo = $site->getSettings()->getLoanOfficer();
            $siteBranch = $site->getSettings()->getBranch();

            if($siteType == 'loan_officer' && isset($siteLo)) {
                return $this->redirect($this->generateUrl('sudoux_mortgage_loan_officer_detail', array('id' => $siteLo->getId())));
            } else if($siteType == 'branch' && isset($siteBranch)) {
                $branchId = $siteBranch->getId();
            }
        }

		$branch = null;

    	if(isset($branchId) || isset($searchData)) {
			if(isset($searchData)) {
				if(array_key_exists('search', $searchData)) {
					$search = $searchData['search'];
					$query = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findAllBySiteLike($site, $search);
				} else {
					$request->getSession()->getFlashBag()->add('error', 'Your search parameter does not exist.');
					return $this->redirect($this->generateUrl('sudoux_mortgage_loan_officer'));
				}

			} elseif(isset($branchId)) {
				$branch = $em->getRepository('SudouxMortgageBundle:Branch')->findOneBySite($site, $branchId);
				if(!isset($branch)) {
					throw $this->createNotFoundException($this->container->getParameter('page_not_found_message'));
				}
				$query = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findAllBySiteAndBranchQuery($site, $branch);
			}

    	} else {    		
	    	$query = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findAllActiveBySiteQuery($site);
    	}

    	$paginator = $this->get('knp_paginator');
    	 
    	$officers = $paginator->paginate(
    			$query,
    			$request->query->get('page', 1),
    			$this->container->getParameter('pager_results')
    	);
    	
        return $this->render('SudouxMortgageBundle:LoanOfficerFront:index.html.twig', array(
        	'officers' => $officers,
        	'branch' => $branch,
			'search' => $search,
        ));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction(Request $request, $id)
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();
    	 
    	$officer = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findOneBySite($site, $id);
		
    	if(!isset($officer)) {
    		throw $this->createNotFoundException($this->container->getParameter('page_not_found_message'));
    	}
    	
        return $this->render('SudouxMortgageBundle:LoanOfficerFront:detail.html.twig', array(
        	'officer' => $officer	
        ));
    }

	/**
	 * @param Request $request
	 * @return Response
	 * @throws \Symfony\Component\Form\Exception\FormException
	 * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
	 */
	public function searchBlockAction(Request $request)
	{
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		$em = $this->getDoctrine()->getEntityManager();

		$branchEntities = $em->getRepository('SudouxMortgageBundle:Branch')->findAllBySiteQuery($site)->getQuery()->getResult();
		$branches = array();

		foreach($branchEntities as $branch) {
			$branchUrl = $this->generateUrl('sudoux_mortgage_loan_officer', array('branch' => $branch->getId()));
			$branches[$branchUrl] = $branch->getName();
		}

		$form = $this->createFormBuilder()
			->add('branch', 'choice', array(
				'label' => 'By Branch',
				'multiple' => false,
				'empty_value' => 'Select a branch',
				'required' => false,
				'choices' => $branches,
				'attr' => array('class' => 'jumpmenu-redirect branch-filter'),
			))
			->add('search', 'text', array(
				'label' => 'Search',
				'required' => false,
				'attr' => array('placeholder' => 'Enter Name', 'class' => 'search-field'),
			))
			->getForm();


		return $this->render('SudouxMortgageBundle:LoanOfficerFront:searchBlock.html.twig', array(
			'form' => $form->createView()
		));
	}

	/**
	 * @return Response
	 */
	public function loanOfficerLikeAjaxAction()
	{
		$site = $this->get('sudoux.cms.site')->getSite();
		$name = $this->getRequest()->query->get('name');

		$em = $this->getDoctrine()->getEntityManager();

		$loanOfficers = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findAllBySiteLike($site, $name)->getQuery()->getResult();

		$loanOfficersReturn = array();
		foreach($loanOfficers as $loanOfficer) {
			$loanOfficerName = sprintf('%s %s', $loanOfficer->getFirstName(), $loanOfficer->getLastName());
			array_push($loanOfficersReturn, array('label' => $loanOfficerName, 'value' => $loanOfficerName));
		}

		$jsonResponse = array('data' => $loanOfficersReturn);

		return new Response(json_encode($jsonResponse), 200, array('Content-Type'=>'application/json'));
	}
}
