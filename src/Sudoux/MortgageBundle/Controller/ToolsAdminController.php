<?php

namespace Sudoux\MortgageBundle\Controller;

use JMS\JobQueueBundle\Entity\Job;
use SudoCms\SiteBundle\Entity\Settings;
use Sudoux\Cms\SiteBundle\Entity\Domain;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Sudoux\MortgageBundle\Entity\LoanOfficer;
use Symfony\Component\HttpFoundation\Response;


use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class ToolsAdminController extends Controller
{
	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function addLoanOfficerSitesAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$site = $this->get('sudoux.cms.site')->getSite();

		$loanOfficers = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findBy(array('site' => $site, 'active' => true, 'deleted' => false));
		$siteCount = 0;

		foreach($loanOfficers as $loanOfficer) {
			if(!$loanOfficer->hasSite()) {
				$newSite = new Site();
				$newSite->setParentSite($site);
				$newSite->setActive(true);
				$settings = new Settings();
				$newSite->setSettings($settings);

				$newSite->setName($loanOfficer->getFullName());
				$settings->setLoanOfficer($loanOfficer);
				$loanOfficer->setOfficerSite($newSite);
				$em->persist($loanOfficer);

				$siteEmail = $loanOfficer->getEmail();
				if(isset($siteEmail)) {
					$settings->setWebsiteEmail($siteEmail);
				}

				$siteType = $em->getRepository('SudouxCmsSiteBundle:SiteType')->findOneBy(array('key_name' => 'loan_officer'));
				$newSite->setSiteType($siteType);

				$parentDomain = $site->getPrimaryDomain();

				$subdomain = str_replace(' ', '', strtolower($loanOfficer->getFullName()));
				$subdomain = preg_replace("/[^a-z ]/", '', $subdomain);
				$subdomain .= '.' . $parentDomain->getDomain();

				$em->persist($settings);
				$em->persist($newSite);

				$subdomain = $this->getAvailableDomain($subdomain);

				$domain = new Domain();
				$domain->setDomain($subdomain);
				$domain->setDescription(sprintf('Primary domain for %s', $newSite->getName()));
				$domain->setSite($newSite);
				$newSite->setPrimaryDomain($domain);
				$em->persist($domain);

				$siteCount++;
			}
		}

		$em->flush();
		$session = $request->getSession();
		$session->getFlashBag()->add('success', $siteCount . ' loan officer sites have been created.');

		return $this->redirect($this->generateUrl('sudoux_cms_admin_internal_tools'));
	}

	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function addBranchSitesAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$site = $this->get('sudoux.cms.site')->getSite();

		$branches = $em->getRepository('SudouxMortgageBundle:Branch')->findBy(array('site' => $site, 'active' => true, 'deleted' => false));

		$siteCount = 0;
		foreach($branches as $branch) {
			if(!$branch->hasSite()) {
				$newSite = new Site();
				$newSite->setParentSite($site);
				$newSite->setActive(true);
				$settings = new Settings();
				$newSite->setSettings($settings);

				$newSite->setName($branch->getName());
				$settings->setBranch($branch);
				$branch->setBranchSite($newSite);
				$em->persist($branch);

				$siteEmail = $branch->getEmail();
				if(isset($siteEmail)) {
					$settings->setWebsiteEmail($siteEmail);
				}

				$siteType = $em->getRepository('SudouxCmsSiteBundle:SiteType')->findOneBy(array('key_name' => 'branch'));
				$newSite->setSiteType($siteType);

				$parentDomain = $site->getPrimaryDomain();

				$subdomain = str_replace(' ', '', strtolower($branch->getName()));
				$subdomain = preg_replace("/[^a-z ]/", '', $subdomain);
				$subdomain .= '.' . $parentDomain->getDomain();

				$em->persist($settings);
				$em->persist($newSite);

				$subdomain = $this->getAvailableDomain($subdomain);

				$domain = new Domain();
				$domain->setDomain($subdomain);
				$domain->setDescription(sprintf('Primary domain for %s', $newSite->getName()));
				$domain->setSite($newSite);
				$newSite->setPrimaryDomain($domain);
				$em->persist($domain);
				$siteCount++;
			}
		}

		$em->flush();
		$session = $request->getSession();
		$session->getFlashBag()->add('success', $siteCount . ' branch sites have been created.');

		return $this->redirect($this->generateUrl('sudoux_cms_admin_internal_tools'));
	}

	/**
	 * @param $domain
	 * @param int $i
	 * @return string
	 */
	protected function getAvailableDomain($domain, $i = 1)
	{
		$em = $this->getDoctrine()->getEntityManager();

		$domainEntity = $em->getRepository('SudouxCmsSiteBundle:Domain')->findOneBy(array('domain' => $domain));
		if(isset($domainEntity)) {
			$domainArray = explode('.', $domain);
			$domainArray[0] .= $i;
			$domain = implode('.', $domainArray);
			$domain = $this->getAvailableDomain($domain, $i++);
		}

		return $domain;
	}

	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function bulkCreateLoUsersAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$site = $this->get('sudoux.cms.site')->getSite();

		$loanOfficers = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findAllBySiteWithoutUser($site, 'child');
		if($request->getMethod() == 'POST') {

			$userCount = 0;
			$now = new \DateTime();
			foreach ($loanOfficers as $loanOfficer) {
				$loanOfficer->setAutoCreateUser(true);
				$loanOfficer->setModified($now);
				$em->persist($loanOfficer);
				$em->flush();
				$userCount++;
			}

			$request->getSession()->getFlashBag()->add('success', $userCount . ' users have been assigned.');
		}

		return $this->render('SudouxMortgageBundle:ToolsAdmin:bulkCreateLoUsers.html.twig', array(
			'loanOfficers' => $loanOfficers,
		));
		//return $this->redirect($this->generateUrl('sudoux_cms_admin_internal_tools'));

	}

	/**
	 * @param Request $request
	 * @return Response
	 */
	public function restructureSiteInheritanceAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$site = $this->get('sudoux.cms.site')->getSite();

		if($request->getMethod() == 'POST') {
			$branchAssignmentCount = 0;
			$loAssignmentCount = 0;
			$childSites = $site->getAllChildSites();

			foreach($childSites as $childSite) {
				$siteType = $childSite->getSiteType();
				if(isset($siteType)) {
					$childSiteParent = $childSite->getParentSite();
					if($siteType->getKeyName() == 'branch') {

						if(isset($childSiteParent)) {
							if($childSiteParent->getId() != $site->getId()) {
								$childSite->setParentSite($site);
								$em->persist($childSite);
								$branchAssignmentCount++;
							}
						}

					} elseif($siteType->getKeyName() == 'loan_officer') {
						$loanOfficer = $childSite->getSettings()->getLoanOfficer();
						if(isset($loanOfficer)) {
							$loanOfficerBranch = $loanOfficer->getBranch();
							if(isset($loanOfficerBranch)) {
								$branchSite = $loanOfficerBranch->getBranchSite();
								if(isset($branchSite)) {
									if(isset($childSiteParent)) {
										if ($childSiteParent->getId() != $branchSite->getId()) {
											$childSite->setParentSite($branchSite);
											$em->persist($childSite);
											$loAssignmentCount++;
										}
									}
								}
							}
						}
					}
				}

				if(($branchAssignmentCount + $loAssignmentCount) % 50 == 0) {
					$em->flush();
				}
			}

			$em->flush();

			$request->getSession()->getFlashBag()->add('success', sprintf('The site structure has been reset. %s branch sites were reassigned. %s loan officer sites were reassigned', $branchAssignmentCount, $loAssignmentCount));
		}

		return $this->render('SudouxMortgageBundle:ToolsAdmin:restructureSiteInheritance.html.twig');
	}

	/**
	 * @param Request $request
	 */
	public function syncLoanOfficersFromLosAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$site = $this->get('sudoux.cms.site')->getSite();
		$loanUtil = $this->get('sudoux_mortgage.los_util');
		$response = $loanUtil->getLoanOfficers($site);

		$losLoanOfficers = array();
		$siteLoanOfficers = $em->getRepository('SudouxMortgageBundle:LoanOfficer')->findAllActiveBySiteQuery($site, 'child')->getQuery()->getScalarResult();
		$keyedLoanOfficers = array();
		foreach($siteLoanOfficers as $loanOfficer) {
			$keyedLoanOfficers[$loanOfficer['o_los_id']] = $loanOfficer;
		}

		$existingLoanOfficers = array();
		$newLoanOfficers = array();
		$deletedLoanOfficers = array();

		if($response['success']) {
			$i = 0;
			foreach($response['loanOfficers'] as $loData) {
				if($request->getMethod() == 'POST') {
					$job = new Job('sudoux:mortgage:loanofficer', array('sync_loan_officers_with_los', sprintf('--site_id=%s', $site->getId()), '--env=' . $this->get('kernel')->getEnvironment(), '--no-debug'), true);
					$em->persist($job);
					$em->flush();
				}

				if(array_key_exists($loData['username'], $keyedLoanOfficers)) {
					array_push($existingLoanOfficers, $loData);
				} else {
					array_push($newLoanOfficers, $loData);
				}

				$i++;
			}
		} else {
			$request->getSession()->getFlashBag()->add('error', 'An error occurred processing the loan officers. The request to the LOS service failed.');
		}

		return $this->render('SudouxMortgageBundle:ToolsAdmin:syncLoanOfficersFromLos.html.twig', array(
			'existingLoanOfficers' => $existingLoanOfficers,
			'newLoanOfficers' => $newLoanOfficers,
			'deletedLoanOfficers' => $deletedLoanOfficers,
		));
	}
}
