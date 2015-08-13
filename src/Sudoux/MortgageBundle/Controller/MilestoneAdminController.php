<?php

namespace Sudoux\MortgageBundle\Controller;

use JMS\JobQueueBundle\Entity\Job;

use SudoCms\SiteBundle\Entity\Settings;

use Sudoux\MortgageBundle\Entity\LoanOfficer;

use Sudoux\Cms\SiteBundle\Entity\Domain;

use Symfony\Component\Filesystem\Filesystem;

use Sudoux\Cms\SiteBundle\Entity\Site;

use Sudoux\MortgageBundle\Form\RealtyWareConnectionType;

use Sudoux\MortgageBundle\Entity\RealtyWareConnection;

use Sudoux\MortgageBundle\Entity\LoanMilestone;

use Sudoux\MortgageBundle\Entity\LoanMilestoneGroup;

use Sudoux\MortgageBundle\Form\CreditConnectionType;

use Sudoux\MortgageBundle\Entity\CreditConnection;

use Sudoux\MortgageBundle\Entity\PricingConnection;

use Sudoux\MortgageBundle\Form\PricingConnectionType;

use Sudoux\MortgageBundle\Form\LosConnectionType;

use Sudoux\MortgageBundle\Entity\LosConnection;

use Symfony\Component\Form\FormError;

use Sudoux\Cms\LocationBundle\Entity\Location;
use Symfony\Component\Validator\Constraints\NotNull;
use Sudoux\Cms\FileBundle\Entity\File;
use Sudoux\MortgageBundle\Entity\Branch;
use Sudoux\MortgageBundle\Form\BranchType;
use SudoCms\SiteBundle\Form\SettingsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class MilestoneAdminController
 * @package Sudoux\MortgageBundle\Controller
 */
class MilestoneAdminController extends Controller
{
	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function indexAction(Request $request)
	{
		$siteRequest = $this->get('sudoux.cms.site');
		$site = $siteRequest->getSite();
		$em = $this->getDoctrine()->getEntityManager();

		$milestoneGroups = $em->getRepository('SudouxMortgageBundle:LoanMilestoneGroup')->findAllBySite($site);

		return $this->render('SudouxMortgageBundle:MilestoneAdmin:index.html.twig', array(
			'milestoneGroups' => $milestoneGroups,
		));
	}

	/**
	 * @param Site $site
	 */
	protected function createMilestones(Site $site)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$loanUtil = $this->get('sudoux_mortgage.los_util');
		$losMilestoneGroups = $loanUtil->getAllMilestones($site);

		foreach($losMilestoneGroups['allMilestones'] as $losMilestoneGroup) {
			$milestoneGroup = $em->getRepository('SudouxMortgageBundle:LoanMilestoneGroup')->findOneByLosId($site, $losMilestoneGroup['ID']);

			if(!isset($milestoneGroup)) {
				$milestoneGroup = new LoanMilestoneGroup();
			}

			$milestoneGroup->setName($losMilestoneGroup['name']);
			$milestoneGroup->setLosId($losMilestoneGroup['ID']);
			$milestoneGroup->setSite($site);

			$i=0;
			foreach($losMilestoneGroup['milestones'] as $losMilestone) {
				$milestone = $em->getRepository('SudouxMortgageBundle:LoanMilestone')->findOneMilestoneByLosId($site, $losMilestone['ID'], $losMilestoneGroup['ID']);

				if(!isset($milestone)) {
					$milestone = new LoanMilestone();
					$milestoneGroup->addMilestone($milestone);
				}

				$milestone->setLosId($losMilestone['ID']);
				$milestone->setName($losMilestone['name']);
				$milestone->setWeight($i);
				$milestone->setMilestoneGroup($milestoneGroup);
				$em->persist($milestone);
				$i++;
			}

			$em->persist($milestoneGroup);
		}

		$em->flush();
	}
}
