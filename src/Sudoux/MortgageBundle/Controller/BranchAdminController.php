<?php


namespace Sudoux\MortgageBundle\Controller;

use JMS\JobQueueBundle\Entity\Job;
use Symfony\Component\HttpFoundation\Response;

use Sudoux\Cms\LocationBundle\Entity\Location;

use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

use Sudoux\Cms\FileBundle\Entity\File;

use Sudoux\MortgageBundle\Entity\Branch;
use Sudoux\MortgageBundle\Form\BranchType;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class BranchAdminController
 * @package Sudoux\MortgageBundle\Controller
 * @author Dan Alvare
 */
class BranchAdminController extends Controller
{
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
					$query = $em->getRepository('SudouxMortgageBundle:Branch')->findAllBySiteLike($site, $search);
				}
			}
		}

		if(!isset($query)) {
			$query = $em->getRepository('SudouxMortgageBundle:Branch')->findAllBySiteQuery($site);
		}

    	$paginator = $this->get('knp_paginator');
    	$branches = $paginator->paginate(
    			$query,
    			$request->query->get('page', 1),
    			$this->container->getParameter('pager_results')
    	);
    	
        return $this->render('SudouxMortgageBundle:BranchAdmin:index.html.twig', array(
        	'branches' => $branches,
			'searchForm' => $searchForm->createView(),
			'search' => $search,
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Exception
     * @author Dan Alvare
     */
	public function exportAction()
	{
		$em = $this->getDoctrine()->getEntityManager();
		$site = $this->get('sudoux.cms.site')->getSite();
		$query = $em->getRepository('SudouxMortgageBundle:Branch')->findAllForExportBySiteQueryBuilder($site)->getQuery();
		$exporter = $this->get('sudoux.cms.site.export_util');
		$response = $exporter->exportQuery($query, 'branches.csv');
		return $response;
	}

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @author Dan Alvare
     */
    public function addAction(Request $request)
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();
    	$branch = new Branch();
    	
    	$form = $this->createForm(new BranchType($site), $branch);
    	
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    	
    		if ($form->isValid()) {
    			$user = $this->get('security.context')->getToken()->getUser();
    			$photoData = $form['branch_photo_file']->getData();
    			if(isset($photoData)) {
    				$photo = new File();
    				$photo->setName(sprintf('%s Branch Photo', $form['name']->getData()));
			    	$photo->setUser($user);
				    $photo->setSite($site);
				    $photo->setFile($photoData);
				    $branch->setBranchPhoto($photo);
    			}
    			
    			$branch->setSite($site);
    			$em->persist($branch);
    			$em->flush();
    			
    			$request->getSession()->getFlashBag()->add('success', 'Your branch has been created.');
    			
    			return $this->redirect($this->generateUrl('sudoux_mortgage_admin_branch'));
    		}
    	}
    	
        return $this->render('SudouxMortgageBundle:BranchAdmin:add.html.twig', array(
        	'form' => $form->createView(),	
        	'branch' => $branch,	
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
    	$branch = $em->getRepository('SudouxMortgageBundle:Branch')->findOneBySiteAndType($site, $id);
    	
    	if(!isset($branch)) {
            $request->getSession()->getFlashBag()->add('error', 'You do not have access to modify this branch.');
    		return $this->redirect($this->generateUrl('sudoux_mortgage_admin_branch'));
    	}
    	 
    	$form = $this->createForm(new BranchType($site), $branch);
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			$user = $this->get('security.context')->getToken()->getUser();
    			
    			$photoData = $form['branch_photo_file']->getData();
    			if(isset($photoData)) {
    				$photo = $branch->getBranchPhoto();
    				if(!isset($photo)) {
    					 $photo = new File();
    					 $photo->setName(sprintf('%s Branch Photo', $form['name']->getData()));
    					 $photo->setUser($user);
    					 $photo->setSite($site);
    				} 
	    			
    				$photo->setFile($photoData);
    				$branch->setBranchPhoto($photo); //prob dont need this. test it
    			}
    			 
    			$branch->setSite($site);
    			$em->persist($branch);
    			$em->flush();
    			 
    			$request->getSession()->getFlashBag()->add('success', 'Your branch has been updated.');
    			
    			return $this->redirect($this->generateUrl('sudoux_mortgage_admin_branch'));
    		}
    	}
    	 
    	return $this->render('SudouxMortgageBundle:BranchAdmin:edit.html.twig', array(
    			'form' => $form->createView(),
    			'branch' => $branch,
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
    	$branch = $em->getRepository('SudouxMortgageBundle:Branch')->findOneBySiteAndType($site, $id);
    	 
    	if(!isset($branch)) {
    		return $this->redirect($this->generateUrl('sudoux_mortgage_admin_branch'));
    	}
    	
    	if($request->getMethod() == 'POST') {
    		$branch->setActive(false);
    		$branch->setDeleted(true);
    		$em->persist($branch);
    		$em->flush();
    		
    		$request->getSession()->getFlashBag()->add('success', 'Your branch has been deleted.');
    		
    		return $this->redirect($this->generateUrl('sudoux_mortgage_admin_branch'));
    	}
    	
        return $this->render('SudouxMortgageBundle:BranchAdmin:delete.html.twig', array(
        	'branch' => $branch,		
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
		$branch = $em->getRepository('SudouxMortgageBundle:Branch')->findOneBy(array('id' => $id, 'site' => $site));
		
		if(!isset($branch)) {
			$this->createNotFoundException('Branch Not Found');
		}
		
		$branch->setBranchPhoto(null);
		$em->persist($branch);
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
	public function importBranchCsvAction(Request $request)
	{
		ini_set('auto_detect_line_endings', true);
		$site = $this->get('sudoux.cms.site')->getSite();
		$em = $this->getDoctrine()->getEntityManager();

		$form = $this->createFormBuilder()
			->add('branch_csv', 'file', array(
				'label' => 'Branch CSV',
				'required' => true,
				'constraints' => array(
					new NotBlank(),
				)
			))
			->getForm();

		if($request->getMethod() == 'POST') {
			$form->bindRequest($request);
			$uploadData = $form['branch_csv']->getData();

			// check if its valid
			if($form->isValid()) {
				try {
					$fs = new Filesystem();
					$directory = $this->get('kernel')->getRootDir() . '/../web/uploads/sites/' . $site->getId() . '/private';

					$filename = 'branch-import-' . date("Y-m-d H:i:s") . '.csv';

					$uploadedFile = $uploadData->move($directory, $filename);

					$filepath = $directory . '/' . $filename;

					$header = array('name','nmls_id','los_id','phone','fax','email','address1','address2','unit','city','state','zipcode','latitude','longitude','directions','description');
					$headerValid = true;

					if (($handle = fopen($filepath, "r")) !== FALSE) {
						$row = 0;

						while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

							// validate the header
							for($i=0; $i<count($data); $i++) {
								if($data[$i] != $header[$i]) {
									$headerValid = false;
									break;
								}
							}

							if(!$headerValid) {
								$request->getSession()->getFlashBag()->add('error', 'Invalid headers. Correct format is: ' . implode(',', $header));
							}

							break;
						}

						$em->flush();
					}

					if($headerValid) {
						$job = new Job('sudoux:mortgage:branch', array('import_branches_from_csv', sprintf('--site_id=%s', $site->getId()), sprintf('--csv_path=%s', $filepath), '--env=' . $this->get('kernel')->getEnvironment(), '--no-debug'), true);
						$em->persist($job);
						$em->flush();

						$request->getSession()->getFlashBag()->add('success', 'Your branches have been scheduled for import.');

						return $this->redirect($this->generateUrl('sudoux_mortgage_admin_branch'));
					}

				} catch(\Exception $e) {
					throw $e;
				}
			}
		}

		return $this->render('SudouxMortgageBundle:BranchAdmin:importBranchCsv.html.twig', array(
			'form' => $form->createView(),
		));
	}
}
