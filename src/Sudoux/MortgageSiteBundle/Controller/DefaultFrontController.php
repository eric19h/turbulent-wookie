<?php

namespace Sudoux\MortgageSiteBundle\Controller;

use Sudoux\Cms\SiteBundle\Model\SitemapNode;
use Symfony\Component\HttpFoundation\Request;
use Sudoux\Cms\SiteBundle\Controller\DefaultFrontController as BaseController;

/**
 * Class DefaultFrontController
 * @package Sudoux\MortgageUserBundle\Controller
 * @author Dan Alvare
 */
class DefaultFrontController extends BaseController
{
	public function getSitemapPages()
	{
		$em = $this->getDoctrine()->getEntityManager();
		$site = $this->get('sudoux.cms.site')->getSite();

		$pages = parent::getSitemapPages();

		$systemPages = array();

		$officerData = new \stdClass();
		$officerData->url = $this->generateUrl('sudoux_mortgage_loan_officer', array(), true);
		$officerData->modified = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findLastModifiedBySite($site);
		$officerData->pageCount = floor($em->getRepository('SudouxMortgageBundle:LoanOfficer')->countBySite($site) / (int)$this->container->getParameter('pager_results'));
		array_push($systemPages, $officerData);

		$branchData = new \stdClass();
		$branchData->url = $this->generateUrl('sudoux_mortgage_branch', array(), true);
		$branchData->modified = $em->getRepository('SudouxMortgageBundle:Branch')->findLastModifiedBySite($site);
		$branchData->pageCount = floor($em->getRepository('SudouxMortgageBundle:Branch')->countBySite($site) / (int)$this->container->getParameter('pager_results'));
		array_push($systemPages, $branchData);

		$branchStateData = new \stdClass();
		$branchStateData->url = $this->generateUrl('sudoux_mortgage_branch_by_state', array(), true);
		$branchStateData->modified = $em->getRepository('SudouxMortgageBundle:Branch')->findLastModifiedBySite($site);
		$branchStateData->pageCount = 1;
		array_push($systemPages, $branchStateData);

		$quoteData = new \stdClass();
		$quoteData->url = $this->generateUrl('sudoux_mortgage_form_quote', array(), true);
		$quoteData->modified = $site->getCreated();
		$quoteData->pageCount = 1;
		array_push($systemPages, $quoteData);

		$prequalData = new \stdClass();
		$prequalData->url = $this->generateUrl('sudoux_mortgage_loan_prequalify', array(), true);
		$prequalData->modified = $site->getCreated();
		$prequalData->pageCount = 1;
		array_push($systemPages, $prequalData);

		foreach($systemPages as $systemPage) {
			if($systemPage->pageCount > 1) {
				for($i=1; $i<=$systemPage->pageCount; $i++) {
					$page = new SitemapNode();
					if($i > 1) {
						$page->alias = $systemPage->url . '?page=' . $i;
					} else {
						$page->alias = $systemPage->url;
					}

					$page->priority = '1.0';
					$page->changefreq = 'monthly';
					$page->lastmod = $systemPage->modified;
					array_push($pages, $page);
				}
			} else {
				$page = new SitemapNode();
				$page->alias = $systemPage->url;
				$page->priority = '1.0';
				$page->changefreq = 'monthly';
				$page->lastmod = $systemPage->modified;
				array_push($pages, $page);
			}
		}

		$branches = $em->getRepository('SudouxMortgageBundle:Branch')->findAllActiveBySiteQuery($site)->getQuery()->getResult();
		foreach($branches as $branch) {
			$node = new SitemapNode();
			$node->alias = $this->generateUrl('sudoux_mortgage_branch_detail', array('id' => $branch->getId()), true);
			$node->priority = '1.0';
			$node->changefreq = 'monthly';
			$node->lastmod = $branch->getModified();
			array_push($pages, $node);
		}

		$loanOfficers = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findAllActiveBySiteQuery($site)->getQuery()->getResult();
		foreach($loanOfficers as $loanOfficer) {
			$node = new SitemapNode();
			$node->alias = $this->generateUrl('sudoux_mortgage_loan_officer_detail', array('id' => $loanOfficer->getId()), true);
			$node->priority = '1.0';
			$node->changefreq = 'monthly';
			$node->lastmod = $loanOfficer->getModified();
			array_push($pages, $node);
		}

		return $pages;
	}
}
