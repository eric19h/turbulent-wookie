<?php

namespace Sudoux\MortgageUserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sudoux\Cms\UserBundle\Controller\UserFrontController as BaseController;

/**
 * Class UserFrontController
 * @package Sudoux\MortgageUserBundle\Controller
 * @author Dan Alvare
 */
class UserFrontController extends BaseController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function myAccountAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$securityContext = $this->container->get('security.context');
    	$user = $securityContext->getToken()->getUser();
    	$siteRequest = $this->get('sudoux.cms.site');
    	$site = $siteRequest->getSite();
    	$request = $this->getRequest();
		
    	$query = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findAllByUserQuery($site, $user);
    	
    	$newMessageCount = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findNewMessagesCountByUserQuery($site, $user);
    	
    	$paginator = $this->get('knp_paginator');
    	$applications = $paginator->paginate(
    			$query,
    			$request->query->get('page', 1),
    			$this->container->getParameter('pager_results')
    	);

    	return $this->render('SudouxCmsUserBundle:UserFront:myAccount.html.twig', array(
    		'applications' => $applications,
    		'newMessageCount' => $newMessageCount[0]['messages'],	
    	));
    }
}
