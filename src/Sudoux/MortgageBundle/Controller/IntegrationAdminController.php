<?php

namespace Sudoux\MortgageBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class IntegrationAdminController extends Controller
{
	public function losStatusAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();


		/*$dql = 'SELECT s FROM Sudoux\Cms\SiteBundle\Entity\Site s JOIN s.settings st JOIN st.los l WHERE s.deleted=0 AND s.active=1 AND st.los IS NOT NULL ORDER BY l.active DESC ';

		$query = $em->createQuery($dql);*/
		//$sites = $query->getResult();

		$query = $em->getRepository('SudouxMortgageBundle:LosConnection')->findAll();

		$paginator = $this->get('knp_paginator');
		$integrations = $paginator->paginate(
			$query,
			$request->query->get('page', 1),
			$this->container->getParameter('pager_results')
		);

		return $this->render('SudouxMortgageBundle:IntegrationAdmin:losStatus.html.twig', array(
			'integrations' => $integrations,
		));
	}
}
