<?php

namespace Sudoux\EagleBundle\Controller;

use Sudoux\EagleBundle\Entity\EagleLoanApplication;
use Sudoux\EagleBundle\Form\EagleLoanApplicationType;
use Sudoux\MortgageBundle\Controller\LoanApplicationFrontController as BaseController;
use Sudoux\Cms\SiteBundle\Controller\FrontController;
use Sudoux\Cms\SiteBundle\DependencyInjection\StringUtil;

use Sudoux\Cms\TaxonomyBundle\Entity\Vocabulary;
use Sudoux\Cms\UserBundle\Form\MemberType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;

use Sudoux\Cms\MessageBundle\Entity\Thread;

use Symfony\Component\HttpFoundation\Response;

use Sudoux\Cms\FileBundle\Entity\File;

use Sudoux\Cms\MessageBundle\Form\MessageType;

use Sudoux\MortgageBundle\Form\LoanDocumentType;

use Sudoux\Cms\MessageBundle\Entity\Message;

use Sudoux\MortgageBundle\Entity\LoanDocument;

use Sudoux\Cms\UserBundle\Entity\User;

use Sudoux\MortgageBundle\Entity\IncomeMonthly;

use Sudoux\MortgageBundle\Entity\AssetRealEstate;
use Sudoux\MortgageBundle\Entity\AssetAccount;
use Sudoux\MortgageBundle\Entity\Employment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sudoux\MortgageBundle\Entity\LoanApplication;
use Sudoux\MortgageBundle\Form\LoanApplicationType;
use JMS\JobQueueBundle\Entity\Job;
use Sudoux\Cms\MessageBundle\Entity\Email;

/**
 * Class LoanApplicationFrontController
 * @package Sudoux\MortgageBundle\Controller
 * @author Dan Alvare
 */
class LoanApplicationFrontController extends BaseController
{
	const LOAN_LOCKED_MESSAGE = 'Your loan can no longer be modified. Please contact your loan officer if you need to make a change.';
	const LOAN_NOT_FOUND_MESSAGE = 'The loan you requested does not exist or is not accessible.';
	const NOT_REGISTERED_MESSAGE = 'You must login or register before you can apply.';
	const PREQUAL_SUCCESS_MESSAGE = 'Thank you for completing your pre-qualification application. We will contact you as soon as possible.';
	const PORTAL_DISABLED = 'Sorry, this site does not support the member portal.';

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function prequalifyAction(Request $request)
    {
    	$securityContext = $this->container->get('security.context');
    	$user = $securityContext->getToken()->getUser();
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	$officers = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findBy(array('site' => $site, 'active' => true));
    	
    	$application = new LoanApplication();
    	$application->setIsPrequal(true);
    	$application->setSite($site);
    	
    	$siteLoanOfficer = $site->getSettings()->getLoanOfficer();
    	if(isset($siteLoanOfficer)) {
    		$application->setLoanOfficer($siteLoanOfficer);
    	}
    	
    	if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
    		// autofill user info
    		$application->getBorrower()->setEmail($user->getEmail());
	    	$application->setUser($user);
    	}

		$showReferralSources = false;
		$referralSourceCount = $em->getRepository('SudouxCmsFormBundle:ReferralSource')->findAllActiveBySiteCount($site);
		if($referralSourceCount > 0) {
			$showReferralSources = true;
		}
    	
    	$form = $this->createForm(new LoanApplicationType($site), $application, array('validation_groups' => array('prequalify')));
    	
    	if($request->getMethod() == 'POST') {
    		$form->bindRequest($request);
    		if($form->isValid()) {
    			try {
	    			$em->persist($application);
	    			$em->flush();
	    			
	    			$emailUtil = $this->get('sudoux.cms.message.email_util');
	    			
	    			// notify the borrrower
	    			$email = new Email();
	    			$email->setSubject('Pre-qualification Application Complete');
	    			$email->setMessage($this::PREQUAL_SUCCESS_MESSAGE);
	    			$email->setRecipient($application->getBorrower()->getEmail());
	    			$email->setRecipientName($application->getBorrower()->getFullName());
	    			$email->setSite($site);
	    			
	    			$emailUtil->logAndSend($email);

	    			// notifiy the site admin
	    			$email = new Email();
	    			$email->setSubject('A borrower has completed a pre-qualification application');

					$message = sprintf("A borrower has completed a pre-qualification application. <a href=\"%s\">Click here</a> to view the application.", $this->generateUrl('sudoux_mortgage_admin_loan_step1', array('id' => $application->getId()), true));
					$message .= sprintf('<p>Borrower: %s<br/>', $application->getBorrower()->getFullName());
					$message .= sprintf('Email: %s<br/>', $application->getBorrower()->getEmail());
					$message .= sprintf('Phone: %s</p>', $application->getBorrower()->getPhoneHome());
					if($application->getCoBorrower()->count() > 0) {
						$coBorrower = $application->getCoBorrower()->get(0);
						$message .= sprintf('<p>Co-Borrower: %s<br/>', $coBorrower->getFullName());
						$message .= sprintf('Email: %s<br/>', $coBorrower->getEmail());
						$message .= sprintf('Phone: %s</p>', $coBorrower->getPhoneHome());
					}

	    			$email->setMessage($message);

	    			$email->setSite($site);
	    			
	    			// notify the site admin or lo
	    			$loanOfficer = $application->getLoanOfficer();
	    			if(isset($loanOfficer)) {
	    				$notificationEmail = $loanOfficer->getEmail();
	    				$email->setRecipientName($loanOfficer->getFullName());
	    			} else {
	    				$notificationEmail = $site->getSettings()->getInheritedWebsiteEmail();
	    				$email->setRecipientName('Site Administrator');
	    			}

					$email->setBcc($site->getSettings()->getInheritedWebsiteEmailBcc());
	    			$email->setRecipient($notificationEmail);
	    			
	    			$emailUtil->logAndSend($email);
	    			
	    			$job = new Job('sudoux:mortgage:loan', array('add_loan_prospects', sprintf('--loan_id=%s', $application->getId()), '--env=' . $this->get('kernel')->getEnvironment(), '--no-debug'), true, 'loan_process_queue');
	    			$em->persist($job);
	    			$em->flush();

                    $redirectUrl = $site->getSettings()->getInheritedPrequalCompleteUrl();
                    if(isset($redirectUrl)) {
                        if($this->get('kernel')->getEnvironment() == 'dev') {
                            $redirectUrl = '/app_dev.php' . $redirectUrl;
                        }
                    } else {
                        $redirectUrl = $this->generateUrl('sudoux_mortgage_loan_prequalify_complete');
                    }

                    return $this->redirect($redirectUrl);

                } catch (\Exception $e) {
    				$logger = $this->get('logger');
    				$logger->crit($e->getMessage());
    			}
    		}
    	}
    	
        return $this->render('SudouxMortgageBundle:LoanApplicationFront:prequalify.html.twig', array(
        	'form' => $form->createView(),
        	'application' => $application,
        	'officers' => $officers,
			'showReferralSources' => $showReferralSources,
        ));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function prequalifyCompleteAction(Request $request)
    {
    	return $this->render('SudouxMortgageBundle:LoanApplicationFront:prequalifyComplete.html.twig');
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function applyStep1Action(Request $request, $id = null)
    {
    	$securityContext = $this->container->get('security.context');
    	if(!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ) {
    		$session = $request->getSession();
    		$session->getFlashBag()->add('error', $this::NOT_REGISTERED_MESSAGE);
    		return $this->redirect($this->generateUrl('sudoux_cms_member_register'));
    	}
    	
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	$user = $securityContext->getToken()->getUser();
    	$newApplication = true;
    	
    	if(isset($id)) {
    		$application = $em->getRepository('SudouxEagleBundle:EagleLoanApplication')->findOneBy(array('id' => $id, 'user' => $user));
    		if(!isset($application)) {
    			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
    		}
    		
    		if($application->getLockStatus() > 0) {
    			$session = $request->getSession();
    			$session->getFlashBag()->add('error', $this::LOAN_LOCKED_MESSAGE);
    			return $this->redirect($this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $id)));
    		}
    		
	    	$newApplication = false;
    	} else {
	    	$application = new EagleLoanApplication();
	    	$siteLoanOfficer = $site->getSettings()->getLoanOfficer();
	    	if(isset($siteLoanOfficer)) {
	    		$application->setLoanOfficer($siteLoanOfficer);
	    	}
    	}
    	
    	if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
    		// autofill user info
    		$application->getBorrower()->setEmail($user->getEmail());
    	}

		$showReferralSources = false;
		$referralSourceCount = $em->getRepository('SudouxCmsFormBundle:ReferralSource')->findAllActiveBySiteCount($site);
		if($referralSourceCount > 0) {
			$showReferralSources = true;
		}
    	 
    	$form = $this->createForm(new EagleLoanApplicationType($site, $application), $application, array('validation_groups' => array('step1')));
    	
    	if($request->getMethod() == 'POST') {
    		
    		$form->bindRequest($request);
    		
    		$this->validateChildEntity($application, $form, 'borrower');
    		
    		if($form->isValid()) {
    			try {
    				
    				$application->setSite($site);
			    	$application->setUser($user);
			    	
    				if($newApplication) {
	    				$application->setLastStepCompleted(1);
	    				
	    				$em->persist($application);
	    				$em->flush();
	    				// set the defaults because the constructor is not called on prototype forms
	    				$coBorrowers = $application->getCoBorrower();
	    				if(count($coBorrowers) > 0) {
	    					foreach($coBorrowers as $coBorrower) {
	    						$coBorrower->setDefaults();
	    					}
	    				}
	    				
	    				$email = new Email();
	    				$email->setSubject('A borrower has started a loan application');
						$message = sprintf("A borrower has started a loan application. <a href=\"%s\">Click here</a> to view the application.", $this->generateUrl('sudoux_mortgage_admin_loan_step1', array('id' => $application->getId()), true));
						$message .= sprintf('<p>Borrower: %s<br>', $application->getBorrower()->getFullName());
						$message .= sprintf('Email: %s<br>', $application->getBorrower()->getEmail());
						$message .= sprintf('Phone: %s</p>', $application->getBorrower()->getPhoneHome());
						if($application->getCoBorrower()->count() > 0) {
							$coBorrower = $application->getCoBorrower()->get(0);
							$message .= sprintf('<p>Co-Borrower: %s</p>', $coBorrower->getFullName());
							$message .= sprintf('Email: %s<br>', $coBorrower->getEmail());
							$message .= sprintf('Phone: %s</p>', $coBorrower->getPhoneHome());
						}

						$email->setMessage($message);
	    				$email->setSite($site);
	    				
	    				// notify the site admin or lo
	    				$loanOfficer = $application->getLoanOfficer();
	    				if(isset($loanOfficer)) {
	    					$notificationEmail = $loanOfficer->getEmail();
	    					$email->setRecipientName($loanOfficer->getFullName());
	    				} else {
	    					$notificationEmail = $site->getSettings()->getInheritedWebsiteEmail();
	    					$email->setRecipientName('Site Administrator');
	    				}
	    				$email->setRecipient($notificationEmail);
						$email->setBcc($site->getSettings()->getInheritedWebsiteEmailBcc());
	    				
	    				$emailUtil = $this->get('sudoux.cms.message.email_util');
	    				$emailUtil->logAndSend($email);

    				} else {
    					
	    				$em->persist($application);
	    				$em->flush();
    				}

		    		return $this->redirect($this->generateUrl('sudoux_mortgage_loan_apply_step2', array('id' => $application->getId())));
		    		
    			} catch (\Exception $e) {
    				$logger = $this->get('logger');
        			$logger->crit($e->getMessage());
        			throw $e;
    			}
    		}
    	}
    	 
    	return $this->render('SudouxEagleBundle:EagleLoanApplicationFront:applyStep1.html.twig', array(
    		'form' => $form->createView(),
    		'application' => $application,
			'showReferralSources' => $showReferralSources,
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
    	$application = $em->getRepository('SudouxEagleBundle:EagleLoanApplication')->findOneBy(array('id' => $id, 'user' => $user));

    	if(!isset($application)) {
    		throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
    	}
    	
    	if($application->getLockStatus() > 0) {
    		$session = $request->getSession();
    		$session->getFlashBag()->add('error', $this::LOAN_LOCKED_MESSAGE);
    		return $this->redirect($this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $id)));
    	}
    	
    	$hasLoanOfficer = false;
    	$loanOfficers = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findAllBySiteType($site);
    	if(count($loanOfficers) > 0) {
	    	$hasLoanOfficer = true;
    	}
    	
    	$form = $this->createForm(new EagleLoanApplicationType($site, $application), $application, array('validation_groups' => array('step2')));
    	
    	if($request->getMethod() == 'POST') {
    		$form->bindRequest($request);
    		if($form->isValid()) {
    			try {
    				
    				if($application->getLastStepCompleted() < 2) {
    					$application->setLastStepCompleted(2);
    				}


    				
    				$em->persist($application);
    				$em->flush();
		    		return $this->redirect($this->generateUrl('sudoux_mortgage_loan_apply_step3', array('id' => $application->getId())));
		    		
    			} catch (\Exception $e) {
    				$logger = $this->get('logger');
        			$logger->crit($e->getMessage());
        			throw $e;
    			}
    			
    		}
    	} 
    	 
    	return $this->render('SudouxEagleBundle:EagleLoanApplicationFront:applyStep2.html.twig', array(
    		'form' => $form->createView(),
    		'application' => $application,
    		'hasLoanOfficer' => $hasLoanOfficer,
			'site'	=> $site
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
    	$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBy(array('id' => $id, 'user' => $user));
    	
    	if(!isset($application)) {
    		throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
    	}
    	
    	if($application->getLockStatus() > 0) {
    		$session = $request->getSession();
    		$session->getFlashBag()->add('error', $this::LOAN_LOCKED_MESSAGE);
    		return $this->redirect($this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $id)));
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
    				
    				if($application->getLastStepCompleted() < 3) {
    					$application->setLastStepCompleted(3);
    				}
    				
    				$em->persist($application);
    				$em->flush();
		    		return $this->redirect($this->generateUrl('sudoux_mortgage_loan_apply_step4', array('id' => $application->getId())));
		    		
    			} catch (\Exception $e) {
    				$logger = $this->get('logger');
        			$logger->crit($e->getMessage());
        			throw $e;
    			}
    			
    		}
    	}
    	 
    	return $this->render('SudouxMortgageBundle:LoanApplicationFront:applyStep3.html.twig', array(
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
    	$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBy(array('id' => $id, 'user' => $user));
    	
    	if(!isset($application)) {
    		throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
    	}
    	
    	if($application->getLockStatus() > 0) {
    		$session = $request->getSession();
    		$session->getFlashBag()->add('error', $this::LOAN_LOCKED_MESSAGE);
    		return $this->redirect($this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $id)));
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
		    		return $this->redirect($this->generateUrl('sudoux_mortgage_loan_apply_step5', array('id' => $application->getId())));
		    		
    			} catch (\Exception $e) {
    				$logger = $this->get('logger');
        			$logger->crit($e->getMessage());
        			throw $e;
    			}    			
    		}
    	}
    	 
    	return $this->render('SudouxMortgageBundle:LoanApplicationFront:applyStep4.html.twig', array(
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
    	$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBy(array('id' => $id, 'user' => $user));
    	
    	if(!isset($application)) {
    		throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
    	}
    	
    	if($application->getLockStatus() > 0) {
    		$session = $request->getSession();
    		$session->getFlashBag()->add('error', $this::LOAN_LOCKED_MESSAGE);
    		return $this->redirect($this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $id)));
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
		    		return $this->redirect($this->generateUrl('sudoux_mortgage_loan_apply_step6', array('id' => $application->getId())));
		    		
    			} catch (\Exception $e) {
    				$logger = $this->get('logger');
        			$logger->crit($e->getMessage());
        			throw $e;
    			}
    		}
    	}
    	 
    	return $this->render('SudouxMortgageBundle:LoanApplicationFront:applyStep5.html.twig', array(
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
    	$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBy(array('id' => $id, 'user' => $user));
    	 
    	if(!isset($application)) {
    		throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
    	}
    	
    	if($application->getLockStatus() > 0) {
    		$session = $request->getSession();
    		$session->getFlashBag()->add('error', $this::LOAN_LOCKED_MESSAGE);
    		return $this->redirect($this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $id)));
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
    				return $this->redirect($this->generateUrl('sudoux_mortgage_loan_apply_step7', array('id' => $application->getId())));
    			
    			} catch (\Exception $e) {
    				$logger = $this->get('logger');
        			$logger->crit($e->getMessage());
        			throw $e;
    			}
    		}
    	}
    	 
    	return $this->render('SudouxMortgageBundle:LoanApplicationFront:applyStep6.html.twig', array(
    		'form' => $form->createView(),
    		'application' => $application,
    	));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function applyStep7Action(Request $request, $id)
    {
    	$securityContext = $this->container->get('security.context');
    	$user = $securityContext->getToken()->getUser();
    	$site = $this->get('sudoux.cms.site')->getSite();
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBy(array('id' => $id, 'user' => $user));
    	
    	if(!isset($application)) {
    		throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
    	}
    	
    	$session = $request->getSession();
    	
    	if($application->getLockStatus() > 0) {
    		$session->getFlashBag()->add('error', $this::LOAN_LOCKED_MESSAGE);
    		return $this->redirect($this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $id)));
    	}
    	
    	if(!$application->validate()) {
    		$session->getFlashBag()->add('error', 'You must fill out all co-borrower information before you can submit the application.');
    		return $this->redirect($this->generateUrl('sudoux_mortgage_loan_apply_step6', array('id' => $application->getId())));
    	}
    	
    	$form = $this->createForm(new LoanApplicationType($site, $application), $application, array('validation_groups' => array('step7')));
    	
    	if($request->getMethod() == 'POST') {    		
    		$form->bindRequest($request);
    		if($form->isValid()) {
    			try {
    				$application->setCompleted(true);
    				$application->setStatus(1);
    				$emailUtil = $this->get('sudoux.cms.message.email_util');
    				
    				// @todo - add this to settings
    				$email = new Email();
    				$email->setSubject('Loan Application Complete');
    				$email->setMessage("Thank you for completing your loan application. We will contact you shortly.");
    				$email->setRecipient($application->getBorrower()->getEmail());
    				$email->setRecipientName($application->getBorrower()->getFullName());
    				$email->setSite($site);
    				
    				$emailUtil->logAndSend($email);

    				$application->addEmail($email);

    				$email = new Email();
    				$email->setSubject('A borrower has completed a loan application');
					$message = sprintf("<p>A borrower has completed a loan application. <a href=\"%s\">Click here to view</a>.</p>", $this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $application->getId()), true));

					$propertyLocation = $application->getPropertyLocation();
					if(isset($propertyLocation)) {
						$message .= sprintf("<p>Property Address:<br> %s</p>", $application->getPropertyLocation()->getAddressString());
					}

					$message .= sprintf('<p>Borrower: %s<br>', $application->getBorrower()->getFullName());
					$message .= sprintf('Email: %s<br>', $application->getBorrower()->getEmail());
					$message .= sprintf('Phone: %s</p>', $application->getBorrower()->getPhoneHome());

					if($application->getCoBorrower()->count() > 0) {
						$coBorrower = $application->getCoBorrower()->get(0);
						$message .= sprintf('<p>Co-Borrower: %s<br>', $coBorrower->getFullName());
						$message .= sprintf('Email: %s<br>', $coBorrower->getEmail());
						$message .= sprintf('Phone: %s</p>', $coBorrower->getPhoneHome());
					}

					$email->setMessage($message);
    				$email->setSite($site);
    				
    				// notify the site admin or lo
    				$loanOfficer = $application->getLoanOfficer();
    				if(isset($loanOfficer)) {
    					$notificationEmail = $loanOfficer->getEmail();
	    				$email->setRecipientName($loanOfficer->getFullName());
    				} else {
    					$notificationEmail = $site->getSettings()->getInheritedWebsiteEmail();
    					$email->setRecipientName('Site Administrator');
    				}
					$email->setBcc($site->getSettings()->getInheritedWebsiteEmailBcc());
    				$email->setRecipient($notificationEmail);
    				
    				$emailUtil->logAndSend($email);

    				$em->persist($application);
    				$em->flush();
    				
    				// check and send data to the LOS
    				$job = new Job('sudoux:mortgage:loan', array('upsert_loan_to_los', sprintf('--loan_id=%s', $application->getId()), '--env=' . $this->get('kernel')->getEnvironment(), '--no-debug'), true, 'loan_process_queue');
    				$em->persist($job);
    				$em->flush();


                    $redirectUrl = $site->getSettings()->getInheritedLoanCompleteUrl();
                    if(isset($redirectUrl)) {
                        if($this->get('kernel')->getEnvironment() == 'dev') {
                            $redirectUrl = '/app_dev.php' . $redirectUrl;
                        }
                    } else {
                        $redirectUrl = $this->generateUrl('sudoux_mortgage_apply_complete');
                    }

                    return $this->redirect($redirectUrl);

    			} catch (\Exception $e) {

    				$session->getFlashBag()->add('error', 'Sorry, there was an issue processing your request. Our support team has been notified and will update you as soon as the issue has been resolved.');
					$logger = $this->get('logger');
					$logger->crit(" Application ID: " . $application->getId() . ' ' . $e->getMessage());
    			}
    		}
    	}
    	 
    	return $this->render('SudouxMortgageBundle:LoanApplicationFront:applyStep7.html.twig', array(
    		'form' => $form->createView(),
    		'application' => $application,
    	));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function applyCompleteAction(Request $request)
    {
    	return $this->render('SudouxMortgageBundle:LoanApplicationFront:applyComplete.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function memberAction(Request $request)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$securityContext = $this->container->get('security.context');
    	$user = $securityContext->getToken()->getUser();
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();

		$portalEnabled = $site->getSettings()->getInheritedMemberPortalEnabled();

		if(!$portalEnabled) {
			$request->getSession()->getFlashBag()->add('error', $this::LOAN_LOCKED_MESSAGE);
			return $this->redirect($this->generateUrl('sudoux_cms_user_account'));
		}
    
    	// get loans by user
    	$query = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findAllByUserQuery($site, $user);

    	/*if(count($applications) == 1) {
    		return $this->redirect($this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $applications->getId())));
    	}*/
    	
    	
    	$paginator = $this->get('knp_paginator');
    	$applications = $paginator->paginate(
    			$query,
    			$request->query->get('page', 1),
    			$this->container->getParameter('pager_results')
    	);
    	
    	return $this->render('SudouxMortgageBundle:LoanApplicationFront:member.html.twig', array(
    		'applications' => $applications
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

		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneByUser($site, $user, $id);
		$loanForm = $this->createForm(new LoanApplicationType($site, $application), $application, array('validation_groups' => array('status')));

		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}

		if(!$application->getCompleted()) {
			$session->getFlashBag()->add('error', 'You cannot access your loan details until you have completed the application.');
			return $this->redirect($this->generateUrl('sudoux_mortgage_loan_apply_step' . $application->getLastStepCompleted(), array('id' => $application->getId())));
		}

		return $this->render('SudouxMortgageBundle:LoanApplicationFront:loanSummary.html.twig', array(
			'loanApp' => $application,
			'loanForm' => $loanForm->createView(),
		));
	}


    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function loanDetailAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$securityContext = $this->container->get('security.context');
    	$user = $securityContext->getToken()->getUser();
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
		$session = $request->getSession();

		$portalEnabled = $site->getSettings()->getInheritedMemberPortalEnabled();

		if(!$portalEnabled) {
			$session->getFlashBag()->add('error', $this::PORTAL_DISABLED);
			return $this->redirect($this->generateUrl('sudoux_cms_user_account'));
		}

    	$documentVocab = $site->getSettings()->getInheritedLoanDocumentVocab();
		if(!isset($documentVocab)) {
			$documentVocab = new Vocabulary();
		}
    	$document = new LoanDocument();
    	$documentForm = $this->createForm(new LoanDocumentType($documentVocab), $document);
    	 
    	// get loans by user
    	$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneByUser($site, $user, $id);
    	 
    	if(!isset($application)) {
    		throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
    	}
    	
    	// redirect to last step if access is manually entered
    	if(!$application->getCompleted()) {
    		$session->getFlashBag()->add('error', 'You cannot access your loan details until you have completed the application.');
    		return $this->redirect($this->generateUrl('sudoux_mortgage_loan_apply_step' . $application->getLastStepCompleted(), array('id' => $application->getId())));
    	}
    	 
    	$documentChecklist = $site->getSettings()->getLoanDocumentVocab();
    	 
    	$message = new Message();
    	$messageForm = $this->createForm(new MessageType(), $message);
    	
    	$loanForm = $this->createForm(new LoanApplicationType($site, $application));
    	
    	if($request->getMethod() == 'POST') {
    		$formName = $request->query->get('form');
    		$emailUtil = $this->get('sudoux.cms.message.email_util');
    		$loanOfficer = $application->getLoanOfficer();
    		if(isset($loanOfficer)) {
    			$notificationEmail = $loanOfficer->getEmail();	
    			$notificationRecipient = $loanOfficer->getFullName(); 
    		} else {    			
    			$notificationEmail = $site->getSettings()->getInheritedWebsiteEmail();	
    			$notificationRecipient = "Site Administrator"; 
    		}
    		
    		switch($formName) {
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

    					$em->persist($document);
    					
    					$email = new Email();
    					$email->setSubject("A new document has been added to a loan application.");
    					$email->setMessage(sprintf('A new document has been added to a loan application. Please <a href="%s">click here</a> to view.', $this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $application->getId()), true)));
    					$email->setRecipient($notificationEmail);
    					$email->setRecipientName($notificationRecipient);
    					$email->setSite($site);
    					
    					$emailUtil->logAndSend($email);
    					
    					$application->addEmail($email);
    					$application->addDocument($document);
    					
    					$em->persist($application);
    					$em->flush();

						// autosend to los?
						$losConn = $site->getSettings()->getInheritedLos();
						if($losConn) {
							$autoSendDocs = $losConn->getAutoSendDocs();
							if($autoSendDocs) {
								// queue the document
								$document->setStatus(3); // accepted
								$em->persist($document);

								$job = new Job('sudoux:mortgage:loan', array('add_document', sprintf('--loan_id=%s', $application->getId()), sprintf('--document_id=%s', $document->getId()), '--env=' . $this->get('kernel')->getEnvironment(), '--no-debug'), true, 'loan_process_queue');
								$em->persist($job);
								$em->flush();
							}
						}
    					 
    					$session->getFlashBag()->add('success', 'Your document was uploaded successfully.');
   
    					return $this->redirect($this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $application->getId())));
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
    						$em->persist($application);
    					}
    						
    					$message->setThread($thread);
    					$message->setUser($user);
    					
    					$email = new Email();
    					$email->setSubject("You have a new message about a loan application.");
    					$email->setMessage(sprintf('You have a new message about a loan application. Please <a href="%s">click here</a> to view.',  $this->generateUrl('sudoux_mortgage_admin_loan_member', array('id' => $application->getId(), 'tab' => 'messages'), true)));
    					$email->setRecipient($notificationEmail);
    					$email->setRecipientName($notificationRecipient);
    					$email->setSite($site);
    						
    					$emailUtil->logAndSend($email);

    					$application->addEmail($email);
    					
    					$em->persist($application);
    					$em->persist($message);
    					$em->flush();
    					 
    					$session->getFlashBag()->add('success', 'Your message was successfully sent.');
    					
    					return $this->redirect($this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $application->getId(), 'tab' => 'messages')));
    				}
    					
    				break;
    		}
    	}
    	 
    	return $this->render('SudouxMortgageBundle:LoanApplicationFront:loanDetail.html.twig', array(
    			'loanApp' => $application,
    			'documentChecklist' => $documentChecklist,
    			'documentForm' => $documentForm->createView(),
    			'messageForm' => $messageForm->createView(),
    			'loanForm' => $loanForm->createView(),
    	));
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
    			$childProperty = explode('.', $propertyName);

    			if(count($childProperty) > 1) {
	    			$childPropertyName = end($childProperty);
	    			unset($childProperty[count($childProperty) - 1]);
    				$childForm = $form[$key];
    				foreach($childProperty as $pName) {
    					$childForm = $childForm[$pName];
    				}
	    			$childForm->get($childPropertyName)->addError(new FormError($violation->getMessage()));
    			} else {
	    			$form[$key]->get($propertyName)->addError(new FormError($violation->getMessage()));
    			}
    		}
    	} else {
    		$entity->{$setter}(null);
    	}
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function upsertLoanTestAction(Request $request, $id)
    {
    	// ea54aca3-b4b1-45ef-b548-de0476945363
    	$em = $this->getDoctrine()->getEntityManager();
    	$securityContext = $this->container->get('security.context');
    	$user = $securityContext->getToken()->getUser();
    	$site = $this->get('sudoux.cms.site')->getSite();
    	
    	$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneByUser($site, $user, $id);
    	
    	if(!isset($application)) {
    		throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
    	}
    	
    	// check and send data to the LOS
    	$loanUtil = $this->get('sudoux_mortgage.los_util');
    	$loanUtil->upsertLoanToLos($application);
    	
    	exit;
    }

	/**
	 *
	 */
	public function loanAccountRegistrationAction(Request $request, $guid)
	{
		$securityContext = $this->container->get('security.context');
		$user = $securityContext->getToken()->getUser();
		$site = $this->get('sudoux.cms.site')->getSite();

		$em = $this->getDoctrine()->getEntityManager();
		$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySiteAndGuid($site, $guid);

		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}

		$showForm = true;
		$appUser = $application->getUser();
		if(isset($appUser)) {
			if($appUser->hasRole('ROLE_MEMBER')) {
				// a user is already assigned
				$showForm = false;
			}
		}

		$user = new User();
		$user->setAlert(true);
		$user->setTimezone($site->getTimezone());

		// set the borrower info
		$user->setFirstName($application->getBorrower()->getFirstName());
		$user->setLastName($application->getBorrower()->getLastName());
		$user->setEmail($application->getBorrower()->getEmail());
		$user->setHomePhone($application->getBorrower()->getPhoneHome());
		$user->setCellPhone($application->getBorrower()->getPhoneMobile());

		$form = $this->createForm(new MemberType($site), $user);

		if($request->getMethod() == 'POST') {
			$form->bindRequest($request);

			$this->container->get('validator');
			// make sure ssn for borrower and user match
			if(preg_replace("/[^0-9]/", "", $form['ssn']->getData()) != preg_replace("/[^0-9]/", "", $application->getBorrower()->getSsn())) {
				$form['ssn']->addError(new FormError('Your ssn does not match the borrower ssn that is on this loan.'));
			}

			if ($form->isValid()) {
				$em = $this->getDoctrine()->getEntityManager();

				$factory = $this->get('security.encoder_factory');
				$encoder = $factory->getEncoder($user);
				$password = $encoder->encodePassword($form['plain_password']->getData(), $user->getSalt());
				$user->setPassword($password);
				$user->setActive(true);
				$user->addSite($site);

				$memberRole = $em->getRepository('SudouxCmsUserBundle:Role')->findOneBy(array('role' => 'ROLE_MEMBER'));
				$user->addRole($memberRole);
				$em->persist($user);

				$application->setUser($user);
				$em->persist($application);

				$em->flush();

				$request->getSession()->getFlashBag()->add('success', 'Your account has been created. Please login to view your loan.');

				return $this->redirect($this->generateUrl('sudoux_mortgage_loan_member_detail', array('id' => $application->getId())));
			}
		}

		return $this->render('SudouxMortgageBundle:LoanApplicationFront:loanAccountRegistration.html.twig', array(
			'loanApp' => $application,
			'form' => $form->createView(),
			'showForm' => $showForm,
		));
	}
}
