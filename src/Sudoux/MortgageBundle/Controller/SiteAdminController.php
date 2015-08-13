<?php

namespace Sudoux\MortgageBundle\Controller;

use SudoCms\SiteBundle\Entity\Settings;

use Symfony\Component\Form\FormError;

use Sudoux\Cms\LocationBundle\Entity\Location;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Symfony\Component\Validator\Constraints\NotNull;
use Sudoux\Cms\FileBundle\Entity\File;
use Sudoux\MortgageBundle\Entity\Branch;
use Sudoux\MortgageBundle\Form\BranchType;
use SudoCms\SiteBundle\Form\SiteType;
use SudoCms\SiteBundle\Form\SettingsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Class SiteAdminController
 * @package Sudoux\MortgageBundle\Controller
 * @author Dan Alvare
 */
class SiteAdminController extends Controller
{
    /**
     * @param Request $request
     * @param $type
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     */
	public function addAction(Request $request, $type, $id)
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	// check the site limit
    	$siteLimitWarning = $this->container->getParameter('site_limit_warning');
    	$customer = $site->getInheritedCustomer();
    	if(isset($customer)) {
	    	if($customer->getRemainingSiteCount() == 0) {
	    		$request->getSession()->getFlashBag()->add('error', $this->container->getParameter('site_limit_exceeded_message'));
	    		return $this->redirect($this->generateUrl('sudoux_cms_admin_site'));
	    	} else if($customer->getRemainingSiteCount() <= $siteLimitWarning) {
	    		$request->getSession()->getFlashBag()->add('warning', sprintf('You have %s sites remaining.', $customer->getRemainingSiteCount()));
	    	}
    	}
    	
    	$newSite = new Site(); 
    	$newSite->setParentSite($site);
    	$newSite->setActive(true);
    	$settings = new Settings();
    	$newSite->setSettings($settings);
    	
    	if($type == 'branch') {
    		$branch = $em->getRepository('SudouxMortgageBundle:Branch')->findOneBy(array('id' => $id, 'site' => $site));
    		if(isset($branch)) {
	    		$newSite->setName($branch->getName());
	    		$settings->setBranch($branch);
	    		$branch->setBranchSite($newSite);
	    		$em->persist($branch);
	    		//$subdomain = preg_replace("/[^a-z ]/", '', strtolower($branch->getName()));
		    	//$domain->setDomain($subdomain . '.' . $site->getPrimaryDomain()->getDomain());
    		} else {
    			throw $this->createNotFoundException('Branch not found');
    		}
    	} elseif($type == 'loan_officer') {    		
    		$loanOfficer = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findOneBy(array('id' => $id, 'site' => $site));
    		if(isset($loanOfficer)) {
    			$newSite->setName($loanOfficer->getFullName());
    			$settings->setLoanOfficer($loanOfficer);
    			$loanOfficer->setOfficerSite($newSite);
    			$em->persist($loanOfficer);
    		} else {
    			throw $this->createNotFoundException('Loan Officer not found');    			
    		}
    	} else {
    		throw new AccessDeniedException();
    	}
    	
    	$siteType = $em->getRepository('SudouxCmsSiteBundle:SiteType')->findOneBy(array('key_name' => $type));
    	$newSite->setSiteType($siteType);
    	 
    	$form = $this->createForm(new SiteType($site), $newSite);
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
			
    		$domain = $form['primary_domain']->getData();
    		if(isset($domain)) {
    			$violations = $this->container->get('validator')->validate($domain);
    			foreach($violations as $violation) {
    				$form['primary_domain']->get('domain')->addError(new FormError($violation->getMessage()));
    			}
    		}
    		
    		if ($form->isValid()) {
    			$em->persist($settings);
    			
		    	$domain = $newSite->getPrimaryDomain();
    			$domain->setDescription(sprintf('Primary domain for %s', $newSite->getName()));
    			$domain->setSite($newSite);
    			$em->persist($domain);
    			//$em->persist($settings);
    			$em->persist($newSite);
    			$em->flush();
    			
    			$configure = $form['configure']->getData();
    			if($configure) {
    				return $this->redirect(sprintf('http://%s/admin/site/settings/edit/general', $newSite->getPrimaryDomain()->getDomain()));
    			} else {
    				return $this->redirect($this->generateUrl('sudoux_cms_admin_site'));
    			}
    		}
    	}
    	
        return $this->render('SudouxMortgageBundle:SiteAdmin:add.html.twig', array(
        	'form' => $form->createView(),		
        	'type' => $type,
        	'id' => $id,
        ));
    }
}
