<?php

namespace Sudoux\EagleBundle\Controller;

use Sudoux\MortgageBundle\Controller\LoanApplicationAdminController as BaseController;
use Sudoux\Cms\UserBundle\Form\UserType;
use Sudoux\MortgageBundle\Entity\MismoFormat;
use Symfony\Component\Validator\Constraints\NotBlank;

use Sudoux\Cms\MessageBundle\Entity\Email;

use Sudoux\MortgageBundle\Entity\LosConnection;

use Sudoux\MortgageBundle\Model\LosSync;

use Sudoux\Cms\SiteBundle\Entity\AuditLog;

use Symfony\Component\HttpFoundation\Response;

use Sudoux\Cms\MessageBundle\Entity\Thread;

use Sudoux\Cms\FileBundle\Entity\File;

use Sudoux\MortgageBundle\Form\LoanDocumentType;

use Sudoux\Cms\MessageBundle\Form\MessageType;

use Sudoux\Cms\MessageBundle\Entity\Message;

use Sudoux\MortgageBundle\Entity\LoanDocument;

use Sudoux\Cms\UserBundle\Entity\User;

use Sudoux\MortgageBundle\Entity\IncomeMonthly;

use Sudoux\MortgageBundle\Entity\AssetRealEstate;

use Sudoux\MortgageBundle\Entity\AssetAccount;

use Sudoux\MortgageBundle\Entity\Employment;

use Symfony\Component\HttpFoundation\Request;

use Sudoux\MortgageBundle\Model\Loan\LoanFormat;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sudoux\MortgageBundle\Entity\LoanApplication;
use Sudoux\MortgageBundle\Form\LoanApplicationType;
use Guzzle\Http\Exception\RequestException;
use JMS\JobQueueBundle\Entity\Job;

/**
 * Class LoanApplicationAdminController
 * @package Sudoux\MortgageBundle\Controller
 * @author Dan Alvare
 */
class LoanApplicationAdminController extends BaseController
{
	const LOAN_LOCKED_MESSAGE = 'Sorry, your loan can no longer be modified.';
	const LOAN_NOT_FOUND_MESSAGE = 'The loan you requested does not exist or is not accessible.';

    /**
     * @param Request $request
     * @return Response
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
	public function indexAction(Request $request)
	{
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		$em = $this->getDoctrine()->getEntityManager();
		
		$loanOfficerEntities = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findAllBySite($site);
		
		$loanOfficers = array();
		foreach($loanOfficerEntities as $officer) {
			$loanOfficers[$officer->getId()] = $officer->getFullName();
		}

      //  $this->renderView()
		
		$form = $this->createFormBuilder()
					->add('officer', 'choice', array(
							'choices' => $loanOfficers,
							'empty_value' => 'All Loan Officers',
							'label' => 'Filter applications by loan officer',
							'required' => false,
					))
					->getForm();
		
		if($request->getMethod() == 'POST') {
			$form->bindRequest($request);
			
			$officerId = $form['officer']->getData();
			if($officerId == "") {
				$query = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findAllBySiteQuery($site);
			} else {
				$officer = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findOneBySite($site, $officerId);
				if(isset($officer)) {
					$query = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findAllByLoanOfficerQuery($site, $officer);
				} else {
					throw $this->createNotFoundException("Loan officer not found.");
				}
			}
		} else {
			$query = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findAllBySiteQuery($site);
		}	
			
		$paginator = $this->get('knp_paginator');
		$applications = $paginator->paginate(
				$query,
				$request->query->get('page', 1),
				$this->container->getParameter('pager_results')
		);	
		
		return $this->render('SudouxMortgageBundle:LoanApplicationAdmin:index.html.twig', array(
			'applications' => $applications,
			'form' => $form->createView(),
		));
	}

    /**
     * @param Request $request
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
	public function applyStep1Action(Request $request, $id = null)
	{
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		 
		$em = $this->getDoctrine()->getEntityManager();
		$securityContext = $this->container->get('security.context');
		$user = $securityContext->getToken()->getUser();
				
		$newApplication = true;
		if(isset($id)) {
			$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
			if(!isset($application)) {
				throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
			}
			$newApplication = false;
		} else {
			$application = new LoanApplication();
			$siteLoanOfficer = $site->getSettings()->getLoanOfficer();
			if(isset($siteLoanOfficer)) {
				$application->setLoanOfficer($siteLoanOfficer);
			}
		}
		
		if($application->getLockStatus() > 0) {
			$session = $request->getSession();
			$session->getFlashBag()->add('error', $this::LOAN_LOCKED_MESSAGE);
			return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $id)));
		}

		$showReferralSources = false;
		$referralSourceCount = $em->getRepository('SudouxCmsFormBundle:ReferralSource')->findAllActiveBySiteCount($site);
		if($referralSourceCount > 0) {
			$showReferralSources = true;
		}
	
		$form = $this->createForm(new LoanApplicationType($site, $application), $application, array('validation_groups' => array('step1')));
		 
		if($request->getMethod() == 'POST') {
	
			$form->bindRequest($request);
			if($form->isValid()) {
				try {
					if($newApplication) {
						$application->setLastStepCompleted(1);
						$application->setSite($site);
						$application->setAdminUser($user);
						
						// set the defaults because the constructor is not called on prototype forms
						$coBorrowers = $application->getCoBorrower();
						if(count($coBorrowers) > 0) {
							foreach($coBorrowers as $coBorrower) {
								$coBorrower->setDefaults();
							}
						}
					}
					
					//echo 'Co-Borrowers: ' . count($application->getCoBorrower()); exit;
					
					$em->persist($application);
					$em->flush();
					
					return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_step2', array('id' => $application->getId())));
	
				} catch (\Exception $e) {
					$logger = $this->get('logger');
					$logger->crit($e->getMessage());
					throw $e;
				}
			}
		}
	
		return $this->render('SudouxMortgageBundle:LoanApplicationAdmin:applyStep1.html.twig', array(
			'form' => $form->createView(),
			'application' => $application,
			'showReferralSources' => $showReferralSources
		));
	}

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
	public function applyStep2Action(Request $request, $id)
	{
		$securityContext = $this->container->get('security.context');
		$user = $securityContext->getToken()->getUser();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		
		$em = $this->getDoctrine()->getEntityManager();
		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
	
		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}
		
		if($application->getLockStatus() > 0) {
			$session = $request->getSession();
			$session->getFlashBag()->add('error', $this::LOAN_LOCKED_MESSAGE);
			return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $id)));
		}	
		 
		$form = $this->createForm(new LoanApplicationType($site, $application), $application, array('validation_groups' => array('step2')));
		 
		if($request->getMethod() == 'POST') {
	
			$form->bindRequest($request);
			if($form->isValid()) {
				try {
					if($application->getLastStepCompleted() < 2) {
						$application->setLastStepCompleted(2);
					}
					$em->persist($application);
					$em->flush();
					
					return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_step3', array('id' => $application->getId())));
	
				} catch (\Exception $e) {
					$logger = $this->get('logger');
					$logger->crit($e->getMessage());
					throw $e;
				}
				 
			}
		}
	
		return $this->render('SudouxMortgageBundle:LoanApplicationAdmin:applyStep2.html.twig', array(
				'form' => $form->createView(),
				'application' => $application,
		));
	}

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
	public function applyStep3Action(Request $request, $id)
	{
		$securityContext = $this->container->get('security.context');
		$user = $securityContext->getToken()->getUser();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		
		$em = $this->getDoctrine()->getEntityManager();
		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
		 
		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}
		
		if($application->getLockStatus() > 0) {
			$session = $request->getSession();
			$session->getFlashBag()->add('error', $this::LOAN_LOCKED_MESSAGE);
			return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $id)));
		}
		 
		// Add employment if new app
    	$borrower = $application->getBorrower();
    	if($borrower->getEmployment()->count() == 0) {
	    	$employment = new Employment();
	    	$employment->setEndDate(new \DateTime());
	    	$borrower->addEmployment($employment); 
    	}
    	
    	$coBorrowers = $application->getCoBorrower();
		foreach($coBorrowers as $coBorrower) {
	    	if($coBorrower->getEmployment()->count() == 0) {
		    	$employment = new Employment();
		    	$employment->setEndDate(new \DateTime());
		    	$coBorrower->addEmployment($employment);     
	    	}		
    	}
		 
		$form = $this->createForm(new LoanApplicationType($site, $application), $application, array('validation_groups' => array('step3')));
		 
		if($request->getMethod() == 'POST') {
	
			$form->bindRequest($request);
			if($form->isValid()) {
				try {
					if($application->getLastStepCompleted() < 3) {
						$application->setLastStepCompleted(3);
					}
					
					// remove employment if not employed
					$borrowerEmployed = $borrower->getEmployed();
					if(!$borrowerEmployed) {
						$borrower->getEmployment()->clear();
					}
					
					foreach($coBorrowers as $coBorrower) {
						$coBorrowerEmployed = $coBorrower->getEmployed();
						if(!$coBorrowerEmployed) {
							$coBorrower->getEmployment()->clear();
						}
					}
					
					$em->persist($application);
					$em->flush();
					
					return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_step4', array('id' => $application->getId())));
	
				} catch (\Exception $e) {
					$logger = $this->get('logger');
					$logger->crit($e->getMessage());
					throw $e;
				}
				 
			}
		}
	
		return $this->render('SudouxMortgageBundle:LoanApplicationAdmin:applyStep3.html.twig', array(
				'form' => $form->createView(),
				'application' => $application,
		));
	}

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
	public function applyStep4Action(Request $request, $id)
	{
		$securityContext = $this->container->get('security.context');
		$user = $securityContext->getToken()->getUser();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		
		$em = $this->getDoctrine()->getEntityManager();
		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
		 
		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}
		
		if($application->getLockStatus() > 0) {
			$session = $request->getSession();
			$session->getFlashBag()->add('error', $this::LOAN_LOCKED_MESSAGE);
			return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $id)));
		}
		 
		if($application->getAssetAccount()->count() == 0) {
			$application->addAssetAccount(new AssetAccount());
		}
		 
		$form = $this->createForm(new LoanApplicationType($site, $application), $application, array('validation_groups' => array('step4')));
		 
		if($request->getMethod() == 'POST') {
	
			$form->bindRequest($request);
			if($form->isValid()) {
				try {
					if($application->getLastStepCompleted() < 4) {
						$application->setLastStepCompleted(4);
					}
					$em->persist($application);
					$em->flush();
					
					return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_step5', array('id' => $application->getId())));
	
				} catch (\Exception $e) {
					$logger = $this->get('logger');
					$logger->crit($e->getMessage());
					throw $e;
				}
			}
		}
	
		return $this->render('SudouxMortgageBundle:LoanApplicationAdmin:applyStep4.html.twig', array(
				'form' => $form->createView(),
				'application' => $application,
		));
	}

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
	public function applyStep5Action(Request $request, $id)
	{
		$securityContext = $this->container->get('security.context');
		$user = $securityContext->getToken()->getUser();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		
		$em = $this->getDoctrine()->getEntityManager();
		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
		 
		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}
		 
		if($application->getLockStatus() > 0) {
			$session = $request->getSession();
			$session->getFlashBag()->add('error', $this::LOAN_LOCKED_MESSAGE);
			return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $id)));
		}
		
		if($application->getIncomeMonthly()->count() == 0) {
			$application->addIncomeMonthly(new IncomeMonthly());
		}
		 
		$form = $this->createForm(new LoanApplicationType($site, $application), $application, array('validation_groups' => array('step5')));
		 
		if($request->getMethod() == 'POST') {
	
			$form->bindRequest($request);
			if($form->isValid()) {
				try {
					if($application->getLastStepCompleted() < 5) {
						$application->setLastStepCompleted(5);
					}
					
					$em->persist($application);
					$em->flush();
					
					return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_step6', array('id' => $application->getId())));
	
				} catch (\Exception $e) {
					$logger = $this->get('logger');
					$logger->crit($e->getMessage());
					throw $e;
				}
			}
		}
	
		return $this->render('SudouxMortgageBundle:LoanApplicationAdmin:applyStep5.html.twig', array(
				'form' => $form->createView(),
				'application' => $application,
		));
	}

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
	public function applyStep6Action(Request $request, $id)
	{
		$securityContext = $this->container->get('security.context');
		$user = $securityContext->getToken()->getUser();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		
		$em = $this->getDoctrine()->getEntityManager();
		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
	
		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}
		
		if($application->getLockStatus() > 0) {
			$session = $request->getSession();
			$session->getFlashBag()->add('error', $this::LOAN_LOCKED_MESSAGE);
			return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $id)));
		}
		 
		$form = $this->createForm(new LoanApplicationType($site, $application), $application, array('validation_groups' => array('step6')));
		 
		if($request->getMethod() == 'POST') {
	
			$form->bindRequest($request);
			if($form->isValid()) {
				try {
					if($application->getLastStepCompleted() < 6) {
						$application->setLastStepCompleted(6);
					}
					
					$em->persist($application);
					$em->flush();
					
					return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_step7', array('id' => $application->getId())));
					 
				} catch (\Exception $e) {
					$logger = $this->get('logger');
					$logger->crit($e->getMessage());
					throw $e;
				}
			}
		}
	
		return $this->render('SudouxMortgageBundle:LoanApplicationAdmin:applyStep6.html.twig', array(
				'form' => $form->createView(),
				'application' => $application,
		));
	}

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Exception
     */
	public function applyStep7Action(Request $request, $id)
	{
		$securityContext = $this->container->get('security.context');
		$user = $securityContext->getToken()->getUser();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		
		$em = $this->getDoctrine()->getEntityManager();
		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
	
		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}	
		
		$session = $request->getSession();
		if($application->getLockStatus() > 0) {
			$session->getFlashBag()->add('error', $this::LOAN_LOCKED_MESSAGE);
			return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $id)));
		}
		
		if(!$application->validate()) {
			$session->getFlashBag()->add('error', 'You must fill out all co-borrower information before you can submit the application.');
			return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_step6', array('id' => $application->getId())));
		}
		
		$form = $this->createForm(new LoanApplicationType($site, $application), $application, array('validation_groups' => array('step7')));
		 
		if($request->getMethod() == 'POST') {
			$form->bindRequest($request);
			if($form->isValid()) {
				try {
					
					$application->setCompleted(true);
					$application->setStatus(1);
					$em->persist($application);
					$em->flush();
					
					$job = new Job('sudoux:mortgage:loan', array('upsert_loan_to_los', sprintf('--loan_id=%s', $application->getId()), '--env=' . $this->get('kernel')->getEnvironment(), '--no-debug'), true, 'loan_process_queue');
    				$em->persist($job);
    				$em->flush();
					
					return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $id)));
					
				} catch (\Exception $e) {
					$session->getFlashBag()->add('error', 'Sorry, there was an issue processing your request. Our support team has been notified and will update you as soon as the issue has been resolved.');
					$logger = $this->get('logger');
					$logger->crit(" Application ID: " . $application->getId() . ' ' . $e->getMessage());
					throw $e;
				}
			}
		}
	
		return $this->render('SudouxMortgageBundle:LoanApplicationAdmin:applyStep7.html.twig', array(
				'form' => $form->createView(),
				'application' => $application,
		));
	}

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
	public function deleteAction(Request $request, $id)
	{
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		$em = $this->getDoctrine()->getEntityManager();
		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
		
		if(!isset($application)) {
    		throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
    	}
    	
    	$losId = $application->getLosId();
    	$loanLocked = false;
    	
    	if(isset($losId)) {
	    	$loanUtil = $this->get('sudoux_mortgage.los_util');
	    	$lockResponse = $loanUtil->loanLocked($application);
	    	
	    	if(isset($lockResponse)) {
	    		$loanLocked = $lockResponse['loan']['locked'];
		    	if(!$lockResponse['loan']['success']) {
                    $request->getSession()->getFlashBag()->add('error', $lockResponse['loan']['message']);
		    	}
	    	}
    	}
		
		if($request->getMethod() == 'POST') {
			if(isset($losId)) {
				$job = new Job('sudoux:mortgage:loan', array('delete_loan', sprintf('--loan_id=%s', $application->getId()), '--env=' . $this->get('kernel')->getEnvironment(), '--no-debug'), true, 'loan_process_queue');
				$em->persist($job);
			}
			$application->setDeleted(true);
			$em->persist($application);
			$em->flush();
			
			return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan'));
		}
		
		return $this->render('SudouxMortgageBundle:LoanApplicationAdmin:delete.html.twig', array(
			'loanApplication' => $application,
			'loanLocked' => $loanLocked
		));
	}

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \AccessDeniedHttpException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
	public function memberAction(Request $request, $id)
	{
		
		$em = $this->getDoctrine()->getEntityManager();
		$securityContext = $this->container->get('security.context');
		$user = $securityContext->getToken()->getUser();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();

		$portalEnabled = $site->getSettings()->getInheritedMemberPortalEnabled();

		if(!$portalEnabled) {
			$request->getSession()->getFlashBag()->add('error', $this::LOAN_LOCKED_MESSAGE);
			return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan'));
		}

		$session = $request->getSession();
		
		$documentVocab = $site->getSettings()->getInheritedLoanDocumentVocab();
		$document = new LoanDocument();
		$documentForm = $this->createForm(new LoanDocumentType($documentVocab), $document);
		
		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
		
		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}
		
		if($application->getStatus() < 2) {
			$application->setStatus(2);
			$em->persist($application);
			$em->flush();
		}
		
		$loanForm = $this->createForm(new LoanApplicationType($site, $application), $application, array('validation_groups' => array('status')));
		
		$availableUsers = $em->getRepository('SudouxCmsUserBundle:User')->findAllBySingleSite($site);
		$loanUsers = $application->getClientUser();

        // remove the application user
        $applicationUser = $application->getUser();
		if(isset($applicationUser)) {
			foreach($availableUsers as $key => $u) {
				if($u->getId() == $applicationUser->getId()) {
					unset($availableUsers[$key]);
				}
			}
		}

        // remove the existing client users
		foreach($loanUsers as $loanUser) {
			foreach($availableUsers as $key => $siteUser) {				
				if($siteUser->getId() == $loanUser->getId()) {
					unset($availableUsers[$key]);
				}
			}
		}

		$userForm = $this->createFormBuilder()
						->add('additional_user_email', 'text', array(
							'label' => 'Invite an additional person to follow the status of this loan',
							'required' => true,
							'attr' => array('placeholder' => 'Email'),
							'constraints' => array(
								new NotBlank(),
								new \Symfony\Component\Validator\Constraints\Email(),
							)
						))
						->getForm();
		
		$documentChecklist = $site->getSettings()->getLoanDocumentVocab();
		 
		$message = new Message();
		$messageForm = $this->createForm(new MessageType(), $message);

		if($request->getMethod() == 'POST') {
			$formName = $request->query->get('form');
			$emailUtil = $this->get('sudoux.cms.message.email_util');
			
			switch($formName) {
				case 'loan':
					$loanForm->bindRequest($request);
					if($loanForm->isValid()) {
						$email = new Email();
						$email->setSubject("Your loan application status has been updated.");
						$email->setMessage(sprintf('Your loan has been updated to %s. Please <a href="%s">click here</a> to view.', $application->getStatusName(), $this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $application->getId()), true)));
						$email->setRecipient($application->getBorrower()->getEmail());
						$email->setRecipientName($application->getBorrower()->getFullName());
						$email->setSite($site);
						
						$emailUtil->logAndSend($email);
						
						$application->addEmail($email);
						
						$em->persist($application);
						$em->flush();
						
						$session->getFlashBag()->add('success', 'Your loan has been updated.');	
						
						return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $application->getId())));
					}
					break;
				case 'document':
					$documentForm->bindRequest($request);
					if($documentForm->isValid()) {
						$documentData = $documentForm['file_field']->getData();
						
						$file = new File();
						$file->setName($documentForm['name']->getData());
						$file->setUser($user);
						$file->setSite($site);
						$file->setFile($documentData);
						$file->setPublic(false);
						$document->setFile($file);
                        $document->setStatus(3); // accepted
						 
						$em->persist($document);
						
						$email = new Email();
						$email->setSubject("A new document has been added to your loan application.");
						$email->setMessage(sprintf('A new document has been added to your loan application. Please <a href="%s">click here</a> to view.', $this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $application->getId()), true)));
						$email->setRecipient($application->getBorrower()->getEmail());
						$email->setRecipientName($application->getBorrower()->getFullName());
						$email->setSite($site);
						
						$emailUtil->logAndSend($email);

						$application->addEmail($email);
						$application->addDocument($document);
						$em->persist($application);
						$em->flush();
						
						// queue the document
    					$job = new Job('sudoux:mortgage:loan', array('add_document', sprintf('--loan_id=%s', $application->getId()), sprintf('--document_id=%s', $document->getId()), '--env=' . $this->get('kernel')->getEnvironment(), '--no-debug'), true, 'loan_process_queue');
    					$em->persist($job);
    					$em->flush();
    					
						$session->getFlashBag()->add('success', 'The document has been added successfully.');
						
						return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $application->getId())));
					}
					break;
				case 'message':
					$messageForm->bindRequest($request);
					if($messageForm->isValid()) {
						$thread = $application->getMessageThread();
		    	
				    	if(!isset($thread)) {
				    		$thread = new Thread();
				    		
				    		$thread->setSubject(sprintf("Loan Application #%s Message Thread", $application->getId()));
				    		$application->setMessageThread($thread);
				    	}
				    	
			    		$message->setThread($thread);
				    	$message->setUser($user);
				    	
				    	$email = new Email();
				    	$email->setSubject("You have a new message about your loan application.");
				    	$email->setMessage(sprintf('You have a new message about your loan application. Please <a href="%s">click here</a> to view.', $this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $application->getId(), 'tab' => 'messages'), true)));
				    	$email->setRecipient($application->getBorrower()->getEmail());
				    	$email->setRecipientName($application->getBorrower()->getFullName());
				    	$email->setSite($site);
				    	
				    	$emailUtil->logAndSend($email);
				    	
		    			$application->addEmail($email);
				    	$em->persist($application);
				    	
						$em->persist($message);
		    			$em->flush();					

		    			$session->getFlashBag()->add('success', 'Your message was sent successfully.');
		    			
		    			return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $id, 'tab' => 'messages')));
					}
					
					break;
				case 'user':
					$userForm->bindRequest($request);
					if($userForm->isValid()) {

						$resetPasswordUrl = null;

						$additionalUserEmail = $userForm['additional_user_email']->getData();
						$additionalUser = $em->getRepository('SudouxCmsUserBundle:User')->findOneBy(array('email' => $additionalUserEmail));
						if(isset($additionalUser)) {
							$message = sprintf('You have been invited to view a loan application for %s. Please <a href="%s">click here</a> to login and view the application.', $application->getBorrower()->getFullName(), $this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $application->getId()), true));
						} else {
							$additionalUser = new User();
							$additionalUser->setUsername($additionalUserEmail);

							$factory = $this->get('security.encoder_factory');
							$encoder = $factory->getEncoder($additionalUser);
							$password = $encoder->encodePassword($additionalUser->generatePassword(), $additionalUser->getSalt());
							$additionalUser->setPassword($password);

							$additionalUser->setEmail($additionalUserEmail);
							$additionalUser->addSite($site);

							$memberRole = $em->getRepository('SudouxCmsUserBundle:Role')->findOneBy(array('role' => 'ROLE_MEMBER'));
							$additionalUser->addRole($memberRole);
							$additionalUser->addToken();
							$additionalUser->setTimezone($site->getTimezone());
							$resetPasswordUrl = $this->generateUrl('sudoux_cms_user_reset_password', array('token' => $additionalUser->getToken()), true);
							$message = sprintf('You have been invited to view a loan application for %s. Please <a href="%s">click here</a> to complete your registration.', $application->getBorrower()->getFullName(), $resetPasswordUrl);

							$em->persist($additionalUser);
						}

						$application->addClientUser($additionalUser);
						$em->persist($application);

						// add to audit log
						$auditLog = new AuditLog();
						$auditLog->setObject('Loan Application');
						$auditLog->setAction(sprintf('%s has been added to loan application #%s', $additionalUserEmail, $application->getId()));
						$auditLog->setUser($user);
						$auditLog->setSite($site);
						$em->persist($auditLog);

						$em->flush();

						// notify the user
						$email = new Email();
						$email->setRecipient($additionalUserEmail);
						$email->setRecipientName($additionalUserEmail);
						$email->setSubject($this->get('sudoux.cms.site')->getSiteVar('You have been invited to view a loan application', 'loan_application_invite_user_email_subject'));
						$email->setUser($user);
						$email->setSite($site);

						$tokens = array(
							'email' => $additionalUserEmail,
							'reset_password_url' => $resetPasswordUrl,
						);

						$message = $this->get('sudoux.cms.site')->getSiteVar($message, 'loan_application_invite_user_email_message', $tokens);

						$email->setMessage($message);
						$this->get('sudoux.cms.message.email_util')->logAndSend($email);

						$session->getFlashBag()->add('success', 'An additional user has been added to your application.');
						return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $application->getId(), 'tab' => 'users')));
					
					}
					
					break;
			}
			
		}
		
		return $this->render('SudouxMortgageBundle:LoanApplicationAdmin:member.html.twig', array(
				'loanApp' => $application,
				'documentChecklist' => $documentChecklist,
				'documentForm' => $documentForm->createView(),
				'messageForm' => $messageForm->createView(),
				'loanForm' => $loanForm->createView(),
				'userForm' => $userForm->createView(),
		));
	}

	public function loanSummaryAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$securityContext = $this->container->get('security.context');
		$user = $securityContext->getToken()->getUser();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		$session = $request->getSession();

		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
		$loanForm = $this->createForm(new LoanApplicationType($site, $application), $application, array('validation_groups' => array('status')));

		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}

		return $this->render('SudouxMortgageBundle:LoanApplicationAdmin:loanSummary.html.twig', array(
			'loanApp' => $application,
			'loanForm' => $loanForm->createView(),
		));
	}

    /**
     * @param $documentId
     * @param $statusId
     * @param $loanId
     * @return Response
     */
	public function setDocumentStatusAction($documentId, $statusId, $loanId)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		
		$response = new \stdClass();
		$response->success = true;
		$response->message = array();
		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $loanId);
		if(isset($application)) {
			$loanDocs = $application->getDocument();
			$document = $em->getRepository('SudouxMortgageBundle:LoanDocument')->find($documentId);
			
			if(!isset($document)) {
				$response->success = false;
				array_push($response->message, "Loan document not found.");
			}
			
			$loanHasDoc = false;
			foreach($loanDocs as $loanDoc) {
				if($loanDoc->getId() == $document->getId()) {
					$loanHasDoc = true;
					break;
				}
			}
			
			if($loanHasDoc) {
				if(array_key_exists($statusId, $document->statusValues)) {
					$document->setStatus($statusId);
					$em->persist($document);
					
					$response->status = $document->getStatusName();
					array_push($response->message, "Loan document status successfully changed.");
					
					if($statusId == 3) { // accepted
						$losConn = $site->getSettings()->getLos();
						$losId = $document->getLosId();
						if(isset($losConn) && !isset($losId)) {
							$job = new Job('sudoux:mortgage:loan', array('add_document', sprintf('--loan_id=%s', $application->getId()), sprintf('--document_id=%s', $document->getId()), '--env=' . $this->get('kernel')->getEnvironment(), '--no-debug'), true, 'loan_process_queue');
							$em->persist($job);
						}
					}
					
					$emailUtil = $this->get('sudoux.cms.message.email_util');
					
					$email = new Email();
					$email->setSubject("Your loan document status has been updated");
					$email->setMessage(sprintf('Your loan document "%s" status has been changed to %s.', $document->getName(), $document->getStatusName()));
					$email->setRecipient($application->getBorrower()->getEmail());
					$email->setRecipientName($application->getBorrower()->getFullName());
					$email->setSite($site);
					
					$emailUtil->logAndSend($email);
					
					$application->addEmail($email);
					$em->persist($application);
					
					$em->flush();
				} else {
					$response->success = false;
					array_push($response->message, "Loan document status status not found.");
				}
			} else {
				$response->success = false;
				array_push($response->message, "Loan document not found.");
			}
		} else {
			$response->success = false;
			array_push($response->message, $this::LOAN_NOT_FOUND_MESSAGE);			
		}
		
		return new Response(json_encode($response), 200, array('Content-Type'=>'application/json'));
	}

    /**
     * @param $loanId
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
	/*public function addLoanUserAction(Request $request, $loanId, $userId)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		
		if($request->getMethod() == 'POST') {
			$form->bindRequest($request);
			if($form->isValid()) {
				try {
		
					$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $loanId);
					if(!isset($application)) {
						$this->createNotFoundException('Sorry the loan application you requested was not found.');
					}
			
					$user = $em->getRepository('SudouxCmsUserBundle:User')->findOneBySite($site, $userId);
					if(!isset($user)) {
						$this->createNotFoundException('Sorry the user you requested was not found.');
					}
					
					$application->addClientUser($user);
					$em->persist($application);
					$em->flush();
					
					return $this->redirect($this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $loanId)));
				} catch (\Exception $e) {
					$logger = $this->get('logger');
					$logger->crit($e->getMessage());
					throw $e;
				}
			}
		}

		return new Response(json_encode($response), 200, array('Content-Type'=>'application/json'));
	}*/

    /**
     * @param $loanId
     * @param $userId
     * @return Response
     */
	public function removeLoanUserAction($loanId, $userId)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		
		$response = new \stdClass();
		$response->success = false;
		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $loanId);
		if(!isset($application)) {
			$this->createNotFoundException('Sorry the loan application you requested was not found.');
		}
		$applicationUsers = $application->getClientUser();
		foreach($applicationUsers as $user) {
			$applicationUserId = $user->getId();
			if($applicationUserId == $userId) {
				$application->removeClientUser($user);
				$em->persist($application);
				$em->flush();
				$response->success = true;
				break;
			}
		}
		
		return new Response(json_encode($response), 200, array('Content-Type'=>'application/json'));
	}

    /**
     * @param $id
     * @return Response
     */
	public function exportFanniemaeAction($id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();

		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}
		
		$fannieMaeDoc = '';
		
		$losConnection = $site->getSettings()->getInheritedLos();
		$losConnection = null;
		if(isset($losConnection)) {
			// export from the LOS
			try {
				$loanUtil = $this->get('sudoux_mortgage.los_util');
				$data = $loanUtil->exportLoan($application, "FNM32");
				if($data['exportString']['success']) {
					$fannieMaeDoc = $data['exportString']['message'];
				}
			} catch (\Exception $e) {
				$logger = $this->get('logger');
				$logger->crit($e->getMessage());
			}
			
		} else {	
			$fannieMaeDoc = $this->renderView('SudouxMortgageBundle:LoanApplicationAdmin:fanniemae.fnm.twig', 
								array('application' => $application, 'emptyString' => ''), 'text/plain');
		}	
		
		$loanNumber = $application->getLosLoanNumber();
		if(!isset($loanNumber)) {
			$loanNumber = $application->getId();
		}
		
		$headers = array(
			'Content-Type'=>'text/plain',
			'Content-disposition' => sprintf('attachment; filename=loan-%s.fnm', $loanNumber)
		);
		
		return new Response($fannieMaeDoc, 200, $headers);
	}

    /**
     * @param $id
     * @return Response
     */
	public function exportFanniemaeULDDAction($id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();

		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}
		
		$applicationXml = $this->renderView('SudouxMortgageBundle:LoanApplicationAdmin:fanniemae/fanniemae.xml.twig', array('application' => $application), 'text/xml');
		return new Response($applicationXml, 200, array('Content-Type'=>'application/xml'));
	}

    /**
     * @param $id
     * @return Response
     */
	public function exportMismo231Action($id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();

		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}



        $loanUtil   = $this->get('sudoux_mortgage.loanformat_util');

        $loanUtil->setType('mismo231');
        $applicationXml = $loanUtil->convertFromLoanApp($application);


        $loanNumber = $application->getLosLoanNumber();
        if(!isset($loanNumber)) {
            $loanNumber = $application->getId();
        }

        $headers = array(
            'Content-Type'=>'application/xml',
            'Content-disposition' => sprintf('attachment; filename=mismoLoan-%s.xml', $loanNumber)
        );

		return new Response($applicationXml, 200, $headers );

	}

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Eric Haynes
     */
    public function exportDestinyAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $siteRequest = $this->get('sudoux.cms.site');
        $site = $siteRequest->getSite();

        $application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
        if(!isset($application)) {
            throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
        }


        $loanUtil   = $this->get('sudoux_mortgage.loanformat_util');

        $loanUtil->setType('destiny');
        $applicationXml = $loanUtil->convertFromLoanApp($application);


        $loanNumber = $application->getLosLoanNumber();
        if(!isset($loanNumber)) {
            $loanNumber = $application->getId();
        }

        $headers = array(
            'Content-Type'=>'application/xml',
            'Content-disposition' => sprintf('attachment; filename=destinyLoan-%s.xml', $loanNumber)
        );

        return new Response($applicationXml, 200, $headers );


    }

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 * @author Eric Haynes
	 */
	public function importDestinyAction(Request $request)
	{
		try{
			$logger = $this->get('logger');
			$loanUtil   = $this->get('sudoux_mortgage.loanformat_util');
			$serializer = $this->container->get('jms_serializer');

			$clientIp = $request->getClientIp();
			$clientAgent = $request->headers->get('User-Agent');
			$clientOrigin = $request->headers->get('Origin');
			$requestData = $request->getContent();

			$logger->addInfo("XML LoanApp Posted from - ".$clientIp.PHP_EOL."User Agent - ".$clientAgent.PHP_EOL."Origin - ".$clientOrigin.PHP_EOL."Content - ".$requestData);

			$loanUtil->setType('destiny');


			$newLoan = $loanUtil->upsertLoanFromFormat($requestData);

			$headers = array(
				'Content-Type'=>'application/json'
			);

			$newLoanResponse = $serializer->serialize($newLoan,'json');
			return new Response( $newLoanResponse, 200 , $headers);


		}catch(\Exception $e){

			$error = $e->getMessage();
			$logger->crit($error);
			$headers = array(
				'Content-Type'=>'application/xml',
			);
			return new Response( $error, 500 , $headers);

		}





	}
    /*
    public function exportVantageAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $siteRequest = $this->get('sudoux.cms.site');
        $site = $siteRequest->getSite();

        $application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
        if(!isset($application)) {
            throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
        }

        $em->detach($application);

        $mismo = new LoanFormat($application);
        $vantageApp = $mismo->getVantage();

        $applicationXml = $this->renderView('SudouxMortgageBundle:LoanApplicationAdmin/formats:vantage.xml.twig', array('application' => $vantageApp), 'text/xml');

        $loanNumber = $application->getLosLoanNumber();
        if(!isset($loanNumber)) {
            $loanNumber = $application->getId();
        }

        $headers = array(
            'Content-Type'=>'application/xml',
            'Content-disposition' => sprintf('attachment; filename=vantageLoan-%s.xml', $loanNumber)
        );

        return new Response($applicationXml, 200, $headers );


    }
*/
    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Eric Haynes
     */
    public function exportVantageAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $siteRequest = $this->get('sudoux.cms.site');
        $site = $siteRequest->getSite();

        $application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
        if(!isset($application)) {
            throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
        }

        $loanUtil   = $this->get('sudoux_mortgage.loanformat_util');

        $loanUtil->setType('vantage');
        $applicationXml = $loanUtil->convertFromLoanApp($application);

        $loanNumber = $application->getLosLoanNumber();
        if(!isset($loanNumber)) {
            $loanNumber = $application->getId();
        }

        $headers = array(
            'Content-Type'=>'application/xml',
            'Content-disposition' => sprintf('attachment; filename=vantageLoan-%s.xml', $loanNumber)
        );

        return new Response($applicationXml, 200, $headers );


    }

    /**
     * @param Request $request
     */
	public function testAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		
		$loanUtil = $this->get('sudoux_mortgage.los_util');
		
		$file = $loanUtil->getFile("AAKLPCALBX-ys1e5xe4.pdf"); 
		exit;
	}

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
	public function resendLoanAction(Request $request, $id)
	{
		$securityContext = $this->container->get('security.context');
		$user = $securityContext->getToken()->getUser();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		
		$em = $this->getDoctrine()->getEntityManager();
		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
   //     $application = $em->getHydrator()->hydrateRow();
		
		if(!isset($application)) {
            $request->getSession()->getFlashBag()->add('error', "Sorry, the loan you requested was not found or not accessible");
		}
		
		$session = $request->getSession();
		try {
			$loanUtil = $this->get('sudoux_mortgage.los_util');
			$loanUtil->upsertLoanToLos($application);
			
			$session->getFlashBag()->add('success', "Your loan was resent to your LOS successfully.");
		} catch (\Exception $e) {
			$session->getFlashBag()->add('error', "Sorry, the loan you tried to send did not complete successfully. Our support team has been notified and will respond promptly.");
			
			$logger = $this->get('logger');
			$logger->crit($e->getMessage() . " Error for loan " . $application->getId());
		}
		
		return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan'));
	}
	
	/**
	 * @param Request $request
	 * @param int $id
	 */
	public function requestCreditAction(Request $request, $id)
	{
		$securityContext = $this->container->get('security.context');
		$user = $securityContext->getToken()->getUser();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		$em = $this->getDoctrine()->getEntityManager();
		
		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
		
		if(!isset($application)) {
			$request->getSession()->getFlashBag()->add('error', "Sorry, the loan you requested was not found or is not accessible");
			return $this->redirect($this->generateUrl('sudoux_cms_user_account'));
		}
		
		$conn = $site->getSettings()->getInheritedCreditConnection();	
		$creditClass = $conn->getCreditProvider()->getClassName();
		$creditRequest = new $creditClass($em, $conn);
		$creditRequest->getCreditByLoan($application);
		
		$request->getSession()->getFlashBag()->add('success', "Credit scores have been updated.");
		
		return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $id, 'tab' => 'credit')));
	}

	/**
	 * 
	 * @param Request $request
	 * @param int $id
	 */
	public function requestPricingAction(Request $request, $id)
	{
		$securityContext = $this->container->get('security.context');
		$user = $securityContext->getToken()->getUser();
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		$em = $this->getDoctrine()->getEntityManager();
		
		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);
		
		if(!isset($application)) {
			$request->getSession()->getFlashBag()->add('error', "Sorry, the loan you requested was not found or is not accessible");
			return $this->redirect($this->generateUrl('sudoux_cms_user_account'));
		}
		
		$conn = $site->getSettings()->getInheritedPricingConnection();
		$pricingClass = $conn->getPricingProvider()->getClassName();
		$pricingRequest = new $pricingClass($em, $conn);
		$pricingRequest->getPricing($application);
		
		$request->getSession()->getFlashBag()->add('success', "Pricing scenarios have been updated.");
		
		return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $id, 'tab' => 'pricing')));
	}

    /**
     * @param Request $request
     * @return Response
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
	public function importFanniemaeAction(Request $request)
	{
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		
		$form = $this->createFormBuilder()
			->add('fanniemae_file', 'file', array(
				'label' => 'Fannie Mae 3.2 File',
				'required' => true,
				'constraints' => array(
					new NotBlank(),		
				)
			))
			->getForm();
		
		if($request->getMethod() == 'POST') {
		
			$form->bindRequest($request);
			if($form->isValid()) {
				$fileData = $form['fanniemae_file']->getData();
				$filePath = $site->getPrivateUploadDir() . '/' . uniqid() . '.fnm';
				$fileData->move($site->getPrivateUploadDir(), $filePath);
				
				$request->getSession()->getFlashBag()->add('success', "Your loan has been created." );
				$loanUtil = $this->get('sudoux_mortgage.fanniemae_util');
				
				$fileContents = file_get_contents($filePath);
				$loanUtil->upsertLoanFromFanniemaeFormat($site, $fileContents);
				
				unlink($filePath);
			}
		}
		
		return $this->render('SudouxMortgageBundle:LoanApplicationAdmin:fannieMaeImport.html.twig', array(
			'form' => $form->createView(),
		));
	}

	/**
	 * @param Request $request
	 * @param $applicationId
	 * @param $documentId
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function sendDocumentToLosAction(Request $request, $applicationId, $documentId)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$site = $this->get('sudoux.cms.site')->getSite();
		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $applicationId);

		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}

		$document = $em->getRepository('SudouxMortgageBundle:LoanDocument')->findOneByLoan($application, $documentId);
		$document->setLosStatus(6);
		$em->persist($document);
		$em->flush();

		$request->getSession()->getFlashBag()->add('success', "The document has sent to the LOS.");

		// queue the document
		$job = new Job('sudoux:mortgage:loan', array('add_document', sprintf('--loan_id=%s', $application->getId()), sprintf('--document_id=%s', $document->getId()), '--env=' . $this->get('kernel')->getEnvironment(), '--no-debug'), true, 'loan_process_queue');
		$em->persist($job);
		$em->flush();

		return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $application->getId())));

	}

	/**
	 * @param Request $request
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function inviteBorrowerAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$site = $this->get('sudoux.cms.site')->getSite();
		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);

		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}

		$applicationUser = $application->getUser();

		if(isset($applicationUser)) {
			if($applicationUser->hasRole('ROLE_MEMBER')) {
				$request->getSession()->getFlashBag()->add('error', "There is already a user account associated with this loan.");
				return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $application->getId())));
			}
		}

		$borrowerEmail = $application->getBorrower()->getEmail();

		$emailUtil = $this->get('sudoux.cms.message.email_util');

		if(isset($borrowerEmail)) {
			// check if the borrower already has an account
			$existingUser = $em->getRepository('SudouxCmsUserBundle:User')->findOneBy(array('email' => $borrowerEmail));
			if(isset($existingUser)) {
				$application->setUser($existingUser);
				$em->persist($application);
				$em->flush($application);

				$emailMessage = sprintf('You have been invited to sign up at %s to send messages, documents and receive updates on your loan application. Please <a href="%s">click here</a> to login and view your application.', $site->getName(), $this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $application->getId()), true));
			} else {
				$emailMessage = sprintf('You have been invited to sign up at %s to send messages, documents and receive updates on your loan application. Please <a href="%s">click here</a> to register.', $site->getName(), $this->generateUrl('sudoux_mortgage_loan_account_registration', array('guid' => $application->getGuid()), true));
			}

			$email = new Email();
			$email->setSubject("About your loan application");
			$email->setMessage($emailMessage);
			$email->setRecipient($borrowerEmail);
			$email->setRecipientName($application->getBorrower()->getFirstName());
			$email->setSite($site);

			$emailUtil->logAndSend($email);
		}

		$request->getSession()->getFlashBag()->add('success', "Your invitation has been sent to the borrower.");

		return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $application->getId())));
	}
}
