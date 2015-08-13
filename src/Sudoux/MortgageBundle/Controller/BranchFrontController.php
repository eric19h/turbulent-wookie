<?php

namespace Sudoux\MortgageBundle\Controller;

use Sudoux\Cms\SiteBundle\Controller\FrontController;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BranchFrontController
 * @package Sudoux\MortgageBundle\Controller
 * @author Dan Alvare
 */
class BranchFrontController extends FrontController
{
    /**
     * The indexAction lists all branches for the current site and child sites.
     * If the site is a branch or loan officer site, it will redirect to the corresponding branch detail page
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $state = null, $city = null)
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();

        $view = 'index.html.twig';
        // check for a redirect based on site type
        $siteType = $site->getSiteType();
        if(isset($siteType)) {
            $siteBranch = $site->getSettings()->getBranch();
            $siteLo = $site->getSettings()->getLoanOfficer();

            if(($siteType->getKeyName() == 'branch') && isset($siteBranch)) {
                // redirect to this branch detail page
                return $this->redirect($this->generateUrl('sudoux_mortgage_branch_detail', array('id' => $siteBranch->getId())));
            } else if($siteType->getKeyName() == 'loan_officer' && isset($siteLo)) {
                $loBranch = $siteLo->getBranch();
                if(!isset($siteBranch) && isset($loBranch)) {
                    $siteBranch = $loBranch;
                }

                if(isset($siteBranch)) {
                    return $this->redirect($this->generateUrl('sudoux_mortgage_branch_detail', array('id' => $siteBranch->getId())));
                } else {
                    throw $this->createNotFoundException('Sorry, this page was not found.');
                }
            }
        }
    	
        $searchData = $request->query->get('form');
    	$activeState = null;
        $search = null;

    	if(isset($state)) {
            if(isset($city)) {
                $query = $em->getRepository('SudouxMortgageBundle:Branch')->findAllBySiteStateAndCityQuery($site, $state, urldecode($city));
            } else {
                $query = $em->getRepository('SudouxMortgageBundle:Branch')->findAllBySiteAndStateQuery($site, $state);
            }

	    	$activeState = $state;
            $view = 'stateBranchList.html.twig';
    	} elseif(isset($searchData)) {
            if(array_key_exists('search', $searchData)) {
                $search = $searchData['search'];
                $query = $em->getRepository('SudouxMortgageBundle:Branch')->findAllBySiteLike($site, $search, true);
            }
    	} else {
            $query = $em->getRepository('SudouxMortgageBundle:Branch')->findAllActiveBySiteQuery($site);
        }
    		 
    	$paginator = $this->get('knp_paginator');
    	
    	$branches = $paginator->paginate(
    			$query,
    			$request->query->get('page', 1),
    			$this->container->getParameter('pager_results')
    	);

    	$states = $em->getRepository('SudouxMortgageBundle:Branch')->findStatesBySite($site);

        $licensedStateEntities = $site->getSettings()->getInheritedStateLicense();
        $licensedStates = array();
        if(count($licensedStateEntities) > 0) {
            foreach($licensedStateEntities as $state) {
                array_push($licensedStates, $state->getAbbreviation());
            }
        }

    	return $this->render('SudouxMortgageBundle:BranchFront:' . $view, array(
    		'branches' => $branches,
    		'states' => $states,
    		'activeState' => $activeState,
            'licensedStates' => $licensedStates,
            'search' => $search,
            'city' => $city,
    	));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction(Request $request, $id, $alias = null)
    {
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$branch = $em->getRepository('SudouxMortgageBundle:Branch')->findOneBySite($site, $id);
    	
    	if(!isset($branch)) {
    		throw $this->createNotFoundException($this->container->getParameter('page_not_found_message'));
    	}

        try{
            $location = $branch->getLocation();
            if(isset($location)) {
                $latitude = $location->getLatitude();

                if(empty($latitude)) {
                    $stateAbbr = '';
                    if(isset($state)) {
                        $stateAbbr = $state->getAbbreviation();
                    }

                    $geocoder = $this->get('geocoder.address');
                    $address = sprintf('%s %s, %s', $location->getAddress1(), $location->getCity(), $stateAbbr);
                    $geoAddress = $geocoder->getGeocodedData($address);

                    if(count($geoAddress) > 0) {
                        $location->setLatitude($geoAddress[0]['latitude']);
                        $location->setLongitude($geoAddress[0]['longitude']);
                        $em->persist($branch);
                        $em->flush();
                    }
                }
            }

        } catch (\Exception $e) {

        }

        return $this->render('SudouxMortgageBundle:BranchFront:detail.html.twig', array(
        	'branch' => $branch,		
        ));
    }

    public function searchBlockAction(Request $request)
    {
        $siteRequest = $this->get('sudoux.cms.site');
        $site = $siteRequest->getSite();
        $em = $this->getDoctrine()->getEntityManager();

        $states = array();
        $branches = $em->getRepository('SudouxMortgageBundle:Branch')->findAllActiveBySiteQuery($site)->getQuery()->getResult();
        if(count($branches) > 1) {
            foreach($branches as $branch) {
                $location = $branch->getLocation();
                if(isset($location)) {
                    $branchUrl = $this->generateUrl('sudoux_mortgage_branch_state', array('state' => strtolower($location->getState()->getAbbreviation())));
                    $states[$branchUrl] = $location->getState()->getName();
                }
            }
        }

        $form = $this->createFormBuilder()
            ->add('search', 'text', array(
                'label' => 'Search',
                'required' => false,
                'attr' => array('placeholder' => 'Enter Name', 'class' => 'search-field'),
            ))
            ->add('state', 'choice', array(
                'label' => 'State',
                'required' => false,
                'choices' => $states,
                'empty_value' => 'Select a State',
                'attr' => array('class' => 'jumpmenu-redirect state-filter'),
            ))
            ->getForm();

        return $this->render('SudouxMortgageBundle:BranchFront:searchBlock.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @return Response
     */
    public function branchLikeAjaxAction()
    {
        $site = $this->get('sudoux.cms.site')->getSite();
        $name = $this->getRequest()->query->get('name');

        $em = $this->getDoctrine()->getEntityManager();

        $branches = $em->getRepository('SudouxMortgageBundle:Branch')->findAllBySiteLike($site, $name)->getQuery()->getResult();

        $branchReturn = array();
        foreach($branches as $branch) {
            array_push($branchReturn, array('label' => $branch->getName(), 'value' => $branch->getName()));
        }

        $jsonResponse = array('data' => $branchReturn);
        return new Response(json_encode($jsonResponse), 200, array('Content-Type'=>'application/json'));
    }

    public function stateAction()
    {
        $siteRequest = $this->get('sudoux.cms.site');
        $site = $siteRequest->getSite();
        $em = $this->getDoctrine()->getEntityManager();

        $branches = $em->getRepository('SudouxMortgageBundle:Branch')->findAllActiveBySiteQuery($site)->getQuery()->getResult();

        $branchStates = array();

        foreach($branches as $branch) {
            $branchLocation = $branch->getLocation();
            if(isset($branchLocation)) {
                $branchState = $branchLocation->getState();
                if(isset($branchState)) {
                    if(array_key_exists($branchState->getAbbreviation(), $branchStates)) {
                        array_push($branchStates[$branchState->getAbbreviation()]->branches, $branch);
                    } else {
                        $branchStateObj = new \stdClass();
                        $branchStateObj->state = $branchState;
                        $branchStateObj->branches = array($branch);
                        $branchStates[$branchState->getAbbreviation()] = $branchStateObj;
                    }
                }
            }
        }

        ksort($branchStates);

        return $this->render('SudouxMortgageBundle:BranchFront:states.html.twig', array(
            'branchStates' => $branchStates,
        ));
    }
}
