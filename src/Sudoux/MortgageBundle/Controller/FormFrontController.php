<?php

namespace Sudoux\MortgageBundle\Controller;

use Sudoux\Cms\LocationBundle\Entity\Location;
use Sudoux\Cms\MessageBundle\Entity\Email;

use Sudoux\Cms\SiteBundle\Controller\FrontController;
use Sudoux\MortgageBundle\Form\QuoteType;

use Sudoux\Cms\FormBundle\Entity\Lead;

use Sudoux\Cms\SiteBundle\Entity\Site;

use Symfony\Component\Form\FormError;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class FormFrontController
 * @package Sudoux\MortgageBundle\Controller
 * @author Dan Alvare
 */
class FormFrontController extends FrontController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function quoteBlockAction()
	{
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		$em = $this->getDoctrine()->getEntityManager();
		$lead = new Lead();
		$lead->setSite($site);

		$showReferralSources = false;
		$referralSourceCount = $em->getRepository('SudouxCmsFormBundle:ReferralSource')->findAllActiveBySiteCount($site);
		if($referralSourceCount > 0) {
			$showReferralSources = true;
		}

		$location = new Location();
		$lead->setLocation($location);

		$form = $this->createForm(new QuoteType($site), $lead);

		return $this->render('SudouxMortgageBundle:FormFront:quoteBlock.html.twig', array(
			'form' => $form->createView(),
			'showReferralSources' => $showReferralSources,
		));
	}

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
	public function quoteAction(Request $request)
	{
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		
		$em = $this->getDoctrine()->getEntityManager();
		 
		$lead = new Lead();
		$form = $this->createForm(new QuoteType($site), $lead);

		$showReferralSources = false;
		$referralSourceCount = $em->getRepository('SudouxCmsFormBundle:ReferralSource')->findAllActiveBySiteCount($site);
		if($referralSourceCount > 0) {
			$showReferralSources = true;
		}

		if($request->getMethod() == 'POST') {
			 
			$form->bindRequest($request);
			 
			if($form->isValid()) {
				try {
					$lead->setSite($site);
					$status = $em->getRepository('SudouxCmsFormBundle:LeadStatus')->find($this->container->getParameter('default_lead_status'));
					$lead->setLeadStatus($status);
					$em->persist($lead);
					$em->flush();
						
					$this->get('session')->setFlash(
							'success',
							'Thank you for your submission'
					);
		
					$emailUtil = $this->get('sudoux.cms.message.email_util');
						
						
					// lead confirmation
					$email = new Email();
					$email->setSubject(sprintf("Thank you for contacting %s.", $site->getName()));
					$email->setMessage("Thank you for your submission. We will respond to your inquiry as soon as possible.");
					$email->setRecipient($lead->getEmail());
					$email->setSite($site);
		
					$emailUtil->logAndSend($email);
		
					// admin notification
					$message = sprintf('First Name: %s<br />', $form['first_name']->getData());
					$message .= sprintf('Last Name: %s<br />', $form['last_name']->getData());
					$message .= sprintf('Home Phone: %s<br />', $form['home_phone']->getData());
					$message .= sprintf('Email: %s<br />', $form['email']->getData());
					$message .= sprintf('Loan Type: %s<br />', $form['subject']->getData());
					$message .= sprintf('Loan Details: %s<br />', $form['message']->getData());

					$referralSources = $lead->getReferralSourceAsString();
					if(!empty($referralSources)) {
						$message .= sprintf('Referral Sources: %s<br />', $referralSources);
						$referralSourceDesc = $lead->getReferralSourceDesc();
						if(!empty($referralSourceDesc)) {
							$message .= sprintf('Referral Source Description:<br /> %s<br />', $referralSourceDesc);
						}
					}
		
					$email = new Email();
					$email->setSubject(sprintf('%s: Quote Form', $site->getName()));
					$email->setMessage($message);
					$email->setRecipient($site->getSettings()->getInheritedWebsiteEmail());
					$email->setRecipientName($site->getName());
					$email->setBcc($site->getSettings()->getInheritedWebsiteEmailBcc());
					$email->setSite($site);
		
					$emailUtil->logAndSend($email);
		
					return $this->redirect($this->generateUrl('sudoux_mortgage_form_quote'));
		
				} catch (\Exception $e) {
					$logger = $this->get('logger');
					$logger->crit($e->getMessage());
					throw $e;
				}
			}
		}
		
		return $this->render('SudouxMortgageBundle:FormFront:quote.html.twig', array(
			'form' => $form->createView(),
			'showReferralSources' => $showReferralSources,
		));
	}
}
