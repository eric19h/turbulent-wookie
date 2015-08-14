<?php

namespace Sudoux\EagleBundle\Controller;

use Sudoux\MortgageBundle\Controller\LoanApplicationSharedController as BaseController;
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
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File as SystemFile;

/**
 * Class LoanApplicationSharedController
 * @package Sudoux\EagleBundle\Controller
 * @author Eric Haynes
 */
class LoanApplicationSharedController extends BaseController
{
	const LOAN_NOT_FOUND_MESSAGE = 'The loan you requested does not exist or is not accessible.';

    /**
     * @param Request $request
     * @param $loanId
     * @param $coBorrowerId
     * @return Response
     */
	public function deleteCoborrowerAction(Request $request, $loanId, $coBorrowerId)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$securityContext = $this->container->get('security.context');
		$user = $securityContext->getToken()->getUser();
		$site = $this->get('sudoux.cms.site')->getSite();
		 
		$documentVocab = $site->getSettings()->getInheritedLoanDocumentVocab();
		$document = new LoanDocument();
		$documentForm = $this->createForm(new LoanDocumentType($documentVocab), $document);
		 
		// get loans by user
		if($securityContext->isGranted('ROLE_SITE_ADMIN')) {
			$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $loanId);		
		} else {
			$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBy(array('id' => $loanId, 'user' => $user));
		}
		 
		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}
		 
		$response = new \stdClass();
		$response->success = false;
		foreach($application->getCoBorrower() as $coBorrower) {
			if($coBorrower->getId() == $coBorrowerId) {
				$application->removeCoBorrower($coBorrower);
				$em->remove($coBorrower);
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
     * @param $statusId
     * @param $loanId
     * @return Response
     */
	public function updateMessageStatusAction($id, $statusId, $loanId)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$securityContext = $this->container->get('security.context');
		$user = $securityContext->getToken()->getUser();
		$site = $this->get('sudoux.cms.site')->getSite();
	
		$response = new \stdClass();
		$response->success = false;
		
		if($securityContext->isGranted('ROLE_SITE_ADMIN') || $securityContext->isGranted('ROLE_LOAN_OFFICER')) {
			$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $loanId);		
		} else {
			$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBy(array('id' => $loanId, 'user' => $user));
		}
		
		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}
		
		$thread = $application->getMessageThread();
		$message = null;
		foreach($thread->getMessage() as $m) {
			if($m->getId() == $id) {
				$message = $m;
				break;
			}
		}
	
		if(!isset($message)) {
			throw $this->createNotFoundException('Message not found');
		}
		
		if($message->getUser()->getId() != $user->getId()) {
			$message->setStatus($statusId);
			$em->persist($message);
			$em->flush();
			$response->message = "Status successfully updated";
			$response->success = true;
		} else {
			$response->message = "Message belongs to this user and was not updated";			
			$response->success = false;
		}
		
	
		return new Response(json_encode($response), 200, array('Content-Type'=>'application/json'));
	}

    /**
     * @param $loanId
     * @param $documentId
     */
	public function viewLoanDocumentAction($loanId, $documentId)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$site = $this->get('sudoux.cms.site')->getSite();
		$securityContext = $this->get('security.context');
		$user = $securityContext->getToken()->getUser();

		if($securityContext->isGranted('ROLE_SITE_ADMIN') || $securityContext->isGranted('ROLE_LOAN_OFFICER')) {
			$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $loanId);
		} else {
			$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneByUser($site, $user, $loanId);
		}
		
		if(!isset($application)) {
			throw $this->createNotFoundException($this::LOAN_NOT_FOUND_MESSAGE);
		}
		
		$documents = $application->getDocument();
		$document = null;
		foreach($documents as $d) {
			if($d->getId() == $documentId) {
				$document = $d;
				break;
			}
		}
		
		if(!isset($document)) {
			throw $this->createNotFoundException('Document not found');
		}
		
		$fileEntity = $document->getFile();
		
		if(!isset($fileEntity)) {
			header('HTTP/1.0 404 Not Found');
			exit;
		}
		
		$file = new SystemFile($fileEntity->getPath());
		
		if (isset($user) && isset($file)) {
			$fileSystemPath = sprintf('%s/../web/%s', $this->get('kernel')->getRootDir(), $fileEntity->getPath());
			if(is_file($fileSystemPath)) {
				$filePathArray = explode('.', $fileEntity->getPath());
				
				$extension = "";
				if(count($filePathArray) > 0) {
					$extension = end($filePathArray);
				}
				
				header(sprintf('Content-Disposition: attachment;filename="%s.%s"', $fileEntity->getName(), $extension));
				header(sprintf("X-Sendfile: %s", $fileSystemPath));
			}
		
		}
		exit;
	}

    /**
     * @param Request $request
     * @param $loanId
     * @param $documentId
     * @return Response
     */
	public function addLosDocumentFileAction(Request $request, $loanId, $documentId)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$site = $this->get('sudoux.cms.site')->getSite();
		$securityContext = $this->get('security.context');
		$user = $securityContext->getToken()->getUser();
		
		if($securityContext->isGranted('ROLE_SITE_ADMIN')) {
			$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $loanId);
		} else {
			$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneByUser($site, $user, $loanId);
		}
				
		$response = new \stdClass();
		$response->success = true;
		if(!isset($application)) {
			$response->success = false;
			$response->message = $this::LOAN_NOT_FOUND_MESSAGE;
		}
		
		$documents = $application->getDocument();
		$document = null;
		foreach($documents as $d) {
			if($d->getId() == $documentId) {
				$document = $d;
				break;
			}
		}
		
		if(!isset($document)) {
			$response->success = false;
			$response->message = 'The document you requested was not found or you do not have permission to access it.';
		}
		
		$loanUtil = $this->get('sudoux_mortgage.los_util');
		$loanUtil->setDocumentFile($application, $document);
		
		return new Response(json_encode($response), 200, array('Content-Type'=>'application/json'));
	}
}
