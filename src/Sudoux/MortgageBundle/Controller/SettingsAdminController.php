<?php

namespace Sudoux\MortgageBundle\Controller;

use JMS\JobQueueBundle\Entity\Job;

use SudoCms\SiteBundle\Entity\Settings;

use Sudoux\MortgageBundle\Entity\LoanOfficer;

use Sudoux\Cms\SiteBundle\Entity\Domain;

use Symfony\Component\Filesystem\Filesystem;

use Sudoux\Cms\SiteBundle\Entity\Site;

use Sudoux\MortgageBundle\Form\RealtyWareConnectionType;

use Sudoux\MortgageBundle\Entity\RealtyWareConnection;

use Sudoux\MortgageBundle\Entity\LoanMilestone;

use Sudoux\MortgageBundle\Entity\LoanMilestoneGroup;

use Sudoux\MortgageBundle\Form\CreditConnectionType;

use Sudoux\MortgageBundle\Entity\CreditConnection;

use Sudoux\MortgageBundle\Entity\PricingConnection;

use Sudoux\MortgageBundle\Form\PricingConnectionType;

use Sudoux\MortgageBundle\Form\LosConnectionType;

use Sudoux\MortgageBundle\Entity\LosConnection;

use Symfony\Component\Form\FormError;

use Sudoux\Cms\LocationBundle\Entity\Location;
use Symfony\Component\Validator\Constraints\NotNull;
use Sudoux\Cms\FileBundle\Entity\File;
use Sudoux\MortgageBundle\Entity\Branch;
use Sudoux\MortgageBundle\Form\BranchType;
use SudoCms\SiteBundle\Form\SettingsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class SettingsAdminController
 * @package Sudoux\MortgageBundle\Controller
 * @author Dan Alvare
 */
class SettingsAdminController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     */
	public function indexAction(Request $request)
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();
    	$settings = $site->getSettings();
    	 
    	$form = $this->createForm(new SettingsType($site), $settings, array('validation_groups' => array('mortgage')));
    	 
    	if($request->getMethod() == 'POST') { 
    		$form->bind($request);
    		
    		if ($form->isValid()) {
    			$em->persist($settings);
    			$em->flush();
    			
    			$session = $request->getSession();
    			$session->getFlashBag()->add('success', 'Your settings have been updated.');
    		} 
    	}
    	
        return $this->render('SudouxMortgageBundle:SettingsAdmin:index.html.twig', array(
        	'form' => $form->createView(),	
        	'settings' => $settings,	
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function losSettingsAction(Request $request)
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();
    	 
    	$settings = $site->getSettings();
    	$conn = $settings->getLos();
    	if(!isset($conn)) {
    		$conn = new LosConnection();
    	}
    	
    	// deserialize and normalize settings blob
    	$losSettings = $conn->getSettings();
    	if(isset($losSettings)) {
    		$losSettingsArray = unserialize($losSettings);
    		$losSettingsText = "";
    		foreach($losSettingsArray as $key => $value) {
    			$losSettingsText .= $key . ':' . $value . "\r\n";
    		}
    		$conn->setSettings($losSettingsText);
    	}

		$conn->setSite($site);
    
    	$form = $this->createForm(new LosConnectionType(), $conn);
    
    	if($request->getMethod() == 'POST') {
    		$form->bindRequest($request);
    		if($form->isValid()) {
    			$session = $request->getSession();
    			
    			// parse the settings
    			$losSettingsText = $conn->getSettings();
    			if(isset($losSettingsText)) {
    				$losSettings = array();
    				$settingsLines = explode("\r\n", $losSettingsText);
    				foreach($settingsLines as $setting) {
	    				// setup the key value pairs
	    				$settingsPair = explode(':', $setting);
						$key = trim($settingsPair[0]);

						array_shift($settingsPair); // remove the key
						if(count($settingsPair) > 1) {
							$value = implode(':', $settingsPair);
						} else {
							$value = $settingsPair[0];
						}


	    				$losSettings[$key] = trim($value);
    				}


    				$conn->setSettings(serialize($losSettings));
    			}
    			
    			$em->persist($conn);
    			$settings->setLos($conn);
    			$em->persist($settings);    			
    			
    			// try to connect 
    			$loanUtil = $this->get('sudoux_mortgage.los_util');
    			$losLogin = $loanUtil->tryLogin($site);
    			
    			if($losLogin['success']) {
    				// update milestones
	    			$job = new Job('sudoux:mortgage:los', array('upsert_milestones', sprintf('--site_id=%s', $site->getId()), '--env=' . $this->get('kernel')->getEnvironment(), '--no-debug'), true);
	    			$em->persist($job);
	    			$session->getFlashBag()->add('success', 'Your LOS settings have been updated.');
    			} else {
	    			$session->getFlashBag()->add('error', 'Failed to connect to the LOS');    				
    			}
    			
    			$em->flush();
    			
    		}
    	}
    
    	return $this->render('SudouxMortgageBundle:SettingsAdmin:losSettings.html.twig', array(
    		'form' => $form->createView(),
    		'losConnection' => $conn,
    	));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteLosSettingsAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$site = $this->get('sudoux.cms.site')->getSite();
    	 
    	$settings = $site->getSettings();
    	$conn = $settings->getLos();
    	 
    	if($this->getRequest()->getMethod() == 'POST') {
    	
    		$settings->setLos(null);
    		$em->persist($settings);
    		$em->remove($conn);
    		$em->flush();
    	
    		$this->get('session')->getFlashBag()->add('success', 'Your LOS connection was removed.');
    	
    		return $this->redirect($this->generateUrl('sudoux_mortgage_admin_settings_los'));
    	}
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewMilestonesAction(Request $request)
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$milestoneGroups = $em->getRepository('SudouxMortgageBundle:LoanMilestoneGroup')->findAllBySite($site);
    	
    	return $this->render('SudouxMortgageBundle:SettingsAdmin:viewMilestones.html.twig', array(
    		'milestoneGroups' => $milestoneGroups,
    	));
    }
    
    protected function createMilestones(Site $site) 
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$loanUtil = $this->get('sudoux_mortgage.los_util');
    	$losMilestoneGroups = $loanUtil->getAllMilestones($site);
    	
    	foreach($losMilestoneGroups['allMilestones'] as $losMilestoneGroup) {
    		$milestoneGroup = $em->getRepository('SudouxMortgageBundle:LoanMilestoneGroup')->findOneByLosId($site, $losMilestoneGroup['ID']);
    		 
    		if(!isset($milestoneGroup)) {
    			$milestoneGroup = new LoanMilestoneGroup();
    		}
    		 
    		$milestoneGroup->setName($losMilestoneGroup['name']);
    		$milestoneGroup->setLosId($losMilestoneGroup['ID']);
    		$milestoneGroup->setSite($site);
    		 
    		$i=0;
    		foreach($losMilestoneGroup['milestones'] as $losMilestone) {
	    		$milestone = $em->getRepository('SudouxMortgageBundle:LoanMilestone')->findOneMilestoneByLosId($site, $losMilestone['ID'], $losMilestoneGroup['ID']);
	    		
	    		if(!isset($milestone)) {
    				$milestone = new LoanMilestone();
	    			$milestoneGroup->addMilestone($milestone);
	    		}
	    		
    			$milestone->setLosId($losMilestone['ID']);
    			$milestone->setName($losMilestone['name']);
    			$milestone->setWeight($i);
    			$milestone->setMilestoneGroup($milestoneGroup);
    			$em->persist($milestone);
    			$i++;
    		}
    		 
    		$em->persist($milestoneGroup);
    	}
    	
    	$em->flush();
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function creditSettingsAction(Request $request)
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();
    	 
    	$settings = $site->getSettings();
    	$conn = $settings->getCreditConnection();
    	if(!isset($conn)) {
    		$conn = new CreditConnection();
    	}
    
    	$form = $this->createForm(new CreditConnectionType(), $conn);
    
    	if($request->getMethod() == 'POST') {
    		$form->bindRequest($request);
    		if($form->isValid()) {
    			$em->persist($conn);
    			$settings->setCreditConnection($conn);
    			$em->persist($settings);
    			$em->flush();
    			$session = $request->getSession();
    			$session->getFlashBag()->add('success', 'Your credit settings have been updated.');
    		}
    	}
    
    	return $this->render('SudouxMortgageBundle:SettingsAdmin:creditSettings.html.twig', array(
    		'form' => $form->createView(),
    	));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCreditSettingsAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$site = $this->get('sudoux.cms.site')->getSite();
    
    	$settings = $site->getSettings();
    	$conn = $settings->getCreditConnection();
    
    	if($this->getRequest()->getMethod() == 'POST') {
    		 
    		$settings->setCreditConnection(null);
    		$em->persist($settings);
    		$em->remove($conn);
    		$em->flush();
    		 
    		$this->get('session')->getFlashBag()->add('success', 'Your credit connection was removed.');
    		 
    		return $this->redirect($this->generateUrl('sudoux_mortgage_admin_settings_credit'));
    	}
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pricingSettingsAction(Request $request)
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();
    	 
    	$settings = $site->getSettings();
    	$conn = $settings->getPricingConnection();
    	if(!isset($conn)) {
    		$conn = new PricingConnection();
    	}
    
    	$form = $this->createForm(new PricingConnectionType(), $conn);
    
    	if($request->getMethod() == 'POST') {
    		$form->bindRequest($request);
    		if($form->isValid()) {
    			$em->persist($conn);
    			$settings->setPricingConnection($conn);
    			$em->persist($settings);
    			$em->flush();
    			$session = $request->getSession();
    			$session->getFlashBag()->add('success', 'Your pricing settings have been updated.');
    		}
    	}
    
    	return $this->render('SudouxMortgageBundle:SettingsAdmin:pricingSettings.html.twig', array(
    		'form' => $form->createView(),
    	));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePricingSettingsAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$site = $this->get('sudoux.cms.site')->getSite();
    
    	$settings = $site->getSettings();
    	$conn = $settings->getPricingConnection();
    
    	if($this->getRequest()->getMethod() == 'POST') {
    		 
    		$settings->setPricingConnection(null);
    		$em->persist($settings);
    		$em->remove($conn);
    		$em->flush();
    		 
    		$this->get('session')->getFlashBag()->add('success', 'Your pricing connection was removed.');
    		 
    		return $this->redirect($this->generateUrl('sudoux_mortgage_admin_settings_pricing'));
    	}
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function realtywareSettingsAction(Request $request)
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();
    	 
    	$settings = $site->getSettings();
    	$conn = $settings->getRealtyWareConnection();
    	if(!isset($conn)) {
    		$conn = new RealtyWareConnection();
    	}
    
    	$form = $this->createForm(new RealtyWareConnectionType(), $conn);
    
    	if($request->getMethod() == 'POST') {
    		$form->bindRequest($request);
    		if($form->isValid()) {
    			$em->persist($conn);
    			$settings->setRealtywareConnection($conn);
    			$em->persist($settings);
    			$em->flush();
    			$session = $request->getSession();
    			$session->getFlashBag()->add('success', 'Your RealtyWare settings have been updated.');
    		}
    	}
    
    	return $this->render('SudouxMortgageBundle:SettingsAdmin:realtywareSettings.html.twig', array(
    		'form' => $form->createView(),
    	));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteRealtywareSettingsAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$site = $this->get('sudoux.cms.site')->getSite();
    
    	$settings = $site->getSettings();
    	$conn = $settings->getRealtywareConnection();
    
    	if($this->getRequest()->getMethod() == 'POST') {
    		 
    		$settings->setRealtywareConnection(null);
    		$em->persist($settings);
    		$em->remove($conn);
    		$em->flush();
    		 
    		$this->get('session')->getFlashBag()->add('success', 'Your RealtyWare connection was removed.');
    		 
    		return $this->redirect($this->generateUrl('sudoux_mortgage_admin_settings_realtyware'));
    	}
    }

    /**
     * @param $entity
     * @param $form
     * @param $key
     */
    protected function validateChildEntity($entity, $form, $key)
    {
    	$method = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
    	$getter = 'get' . $method;
    	$setter = 'set' . $method;
    	$childEntity = $entity->{$getter}();
    	if(isset($childEntity)) {
    		$violations = $this->container->get('validator')->validate($childEntity);
    	
    		foreach($violations as $violation) {
    			$propertyName = $violation->getPropertyPath();
    			$form->get($propertyName)->addError(new FormError($violation->getMessage()));
    		}
    	} else {
	    	$entity->{$setter}(null);
    	}
    }
    
}
