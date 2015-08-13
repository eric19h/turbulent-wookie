<?php

namespace Sudoux\MortgageBundle\Controller;

use JMS\JobQueueBundle\Entity\Job;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;

use Sudoux\Cms\FileBundle\Entity\File;

use Sudoux\MortgageBundle\Form\LoanOfficerType;

use Sudoux\MortgageBundle\Entity\LoanOfficer;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Class LoanOfficerAdminController
 * @package Sudoux\MortgageBundle\Controller
 * @author Dan Alvare
 */
class LoanOfficerAdminController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();

		$query = null;
		$search = null;

		$searchForm = $this->createFormBuilder()
			->add('search', 'text', array(
				'label' => 'Search',
				'required' => true,
				'attr' => array('class' => 'node-search', 'placeholder' => 'Enter Name'),
				'constraints' => array(
					new NotBlank(),
				)
			))
			->getForm();

		if($request->getMethod() == 'POST') {
			$searchForm->bindRequest($request);
			if($searchForm->isValid()) {
				$searchData = $request->request->get('form');
				if(array_key_exists('search', $searchData)) {
					$search = $searchData['search'];
					$query = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findAllBySiteLike($site, $search);
				}
			}
		}

		if(!isset($query)) {
			$query = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findAllBySiteQuery($site);
		}

    	$paginator = $this->get('knp_paginator');
    	$officers = $paginator->paginate(
    			$query,
    			$request->query->get('page', 1),
    			$this->container->getParameter('pager_results')
    	);
    	 
    	return $this->render('SudouxMortgageBundle:LoanOfficerAdmin:index.html.twig', array(
    		'officers' => $officers,
			'searchForm' => $searchForm->createView(),
			'search' => $search,
    	));
    	
    }

	/**
	 * @return mixed
	 */
	public function exportAction()
	{
		$em = $this->getDoctrine()->getEntityManager();
		$site = $this->get('sudoux.cms.site')->getSite();
		$query = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findAllForExportBySiteQueryBuilder($site)->getQuery();
		$exporter = $this->get('sudoux.cms.site.export_util');
		$response = $exporter->exportQuery($query, 'loan-officers.csv');
		return $response;
	}

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     */
    public function addAction(Request $request)
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();
    	$officer = new LoanOfficer();
		$officer->setSite($site);

        $siteType = $site->getSiteType();
        if(isset($siteType)) {
            $siteBranch = $site->getSettings()->getBranch();

            if (($siteType->getKeyName() == 'branch') && isset($siteBranch)) {
                $officer->setBranch($siteBranch);
            }
        }

    	$form = $this->createForm(new LoanOfficerType($site), $officer);
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			$user = $this->get('security.context')->getToken()->getUser();
    			$photoData = $form['officer_photo_file']->getData();
    			if(isset($photoData)) {
    				$photo = new File();
    				$photo->setName(sprintf('%s %s', $form['first_name']->getData(), $form['last_name']->getData()));
    				$photo->setUser($user);
    				$photo->setSite($site);
    				$photo->setFile($photoData);
    				$officer->setOfficerPhoto($photo);
    			}

    			$em->persist($officer);
    			$em->flush($officer);
    			
    			$request->getSession()->getFlashBag()->add('success', 'Your loan officer has been created.');
    			
    			return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_officer'));
    		}
    	}
    	 
    	return $this->render('SudouxMortgageBundle:LoanOfficerAdmin:add.html.twig', array(
			'form' => $form->createView(),
			'officer' => $officer,
    	));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     */
    public function editAction(Request $request, $id)
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();
    	$officer = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findOneBySiteAndType($site, $id);

        if(!isset($officer)) {
            $request->getSession()->getFlashBag()->add('error', 'You do not have access to modify this loan officer.');
            return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_officer'));
        }
    	
    	$form = $this->createForm(new LoanOfficerType($site), $officer);
    	
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			$user = $this->get('security.context')->getToken()->getUser();
    			$photoData = $form['officer_photo_file']->getData();
    			if(isset($photoData)) {
    				$existingPhoto = $officer->getOfficerPhoto();
    				if(isset($existingPhoto)) {
    					$em->remove($existingPhoto);	
    				}
    				
    				$photo = new File();
    				$photo->setName(sprintf('%s %s', $form['first_name']->getData(), $form['last_name']->getData()));
    				$photo->setUser($user);
    				$photo->setSite($site);
    				$photo->setFile($photoData);
    				$officer->setOfficerPhoto($photo);
    				$em->persist($photo);
    			}
    	
    			$em->persist($officer);
    			$em->flush($officer);
    			
    			$request->getSession()->getFlashBag()->add('success', 'Your loan officer has been updated.');
    			
    			return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_officer'));
    		}
    	}
    	
    	return $this->render('SudouxMortgageBundle:LoanOfficerAdmin:edit.html.twig', array(
    			'form' => $form->createView(),
    			'officer' => $officer,
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
    	$officer = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findOneBySite($site, $id, false);

		if(!isset($officer)) {
			$request->getSession()->getFlashBag()->add('error', 'You do not have access to modify this loan officer.');
			return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_officer'));
		}
    	    	
    	if($request->getMethod() == 'POST') {
			$officer->setActive(false);
			$officer->setDeleted(true);

    		$em->persist($officer);
    		$em->flush();
    		
    		$request->getSession()->getFlashBag()->add('success', 'Your loan officer has been deleted.');
    		
    		return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_officer'));
    	}
    	
    	return $this->render('SudouxMortgageBundle:LoanOfficerAdmin:delete.html.twig', array(
    		'officer' => $officer,
    	));
    }

    /**
     * @param $id
     * @return Response
     */
	public function ajaxPhotoDeleteAction($id)
	{
		$site = $this->get('sudoux.cms.site')->getSite();
		$em = $this->getDoctrine()->getEntityManager();
		$officer = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findOneBy(array('id' => $id, 'site' => $site));
		
		if(!isset($officer)) {
			$this->createNotFoundException('Loan Officer Not Found');
		}
		
		$officer->setOfficerPhoto(null);
		$em->persist($officer);
		$em->flush();
	
		$response = new \stdClass();
		$response->success = true;
		$response->message = 'The photo was removed successfully.';
		
		return new Response(json_encode($response), 200, array('Content-Type'=>'application/json'));
	}

	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 * @throws \Symfony\Component\Form\Exception\FormException
	 * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
	 */
	public function importOfficerCsvAction(Request $request)
	{
		ini_set('memory_limit', -1);
		set_time_limit(0);
		ini_set('auto_detect_line_endings', true);

		$site = $this->get('sudoux.cms.site')->getSite();
		$em = $this->getDoctrine()->getEntityManager();

		$form = $this->createFormBuilder()
			->add('loan_officer_csv', 'file', array(
				'label' => 'Loan Officer CSV',
				'required' => true,
				'constraints' => array(
					new NotBlank(),
				)
			))
			->getForm();

		if($request->getMethod() == 'POST') {
			$form->bindRequest($request);
			$uploadData = $form['loan_officer_csv']->getData();

			if($form->isValid()) {

				try {
					$fs = new Filesystem();
					$directory = $this->get('kernel')->getRootDir() . '/../web/uploads/sites/' . $site->getId() . '/private';

					$filename = 'loan-officer-import-' . date("Y-m-d H:i:s") . '.csv';

					$uploadedFile = $uploadData->move($directory, $filename);

					$filepath = $directory . '/' . $filename;

					$header = array('first_name','last_name','email','los_id','nmls_id','title','phone_office','phone_mobile','phone_tollfree','fax','signature','bio','branch_nmls_id');
					$headerValid = true;

					if (($handle = fopen($filepath, "r")) !== FALSE) {
						$row = 0;

						while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
							if($row == 0) {
								// validate the header
								for($i=0; $i<count($data); $i++) {
									if($data[$i] != $header[$i]) {
										$headerValid = false;
										break;
									}
								}

								if(!$headerValid) {
									$request->getSession()->getFlashBag()->add('error', 'Invalid headers. Correct format is: ' . implode(',', $header));
									break;
								}
							} else {

							}

							$row++;
						}

						$em->flush();
					}

					if($headerValid) {
						$job = new Job('sudoux:mortgage:loanofficer', array('import_loan_officers_from_csv', sprintf('--site_id=%s', $site->getId()), sprintf('--csv_path=%s', $filepath), '--env=' . $this->get('kernel')->getEnvironment(), '--no-debug'), true);
						$em->persist($job);
						$em->flush();

						$request->getSession()->getFlashBag()->add('success', 'Your loan officers have been scheduled for import.');
						return $this->redirect($this->generateUrl('sudoux_mortgage_admin_loan_officer'));
					}


				} catch(\Exception $e) {
					throw $e;
				}

			}
		}

		return $this->render('SudouxMortgageBundle:LoanOfficerAdmin:importOfficerCsv.html.twig', array(
			'form' => $form->createView(),
		));
	}

	/**
	 * @param $value
	 * @return null
	 */
	protected function getCsvValue($value)
	{
		if(empty($value)) {
			$value = null;
		}

		return $value;
	}
}
