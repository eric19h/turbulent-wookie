<?php

namespace Sudoux\MortgageBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Sudoux\Cms\SiteBundle\Entity\Domain;

use SudoCms\SiteBundle\Entity\Settings;

use Sudoux\MortgageBundle\Entity\Branch;

use Sudoux\MortgageBundle\Entity\LoanOfficer;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sudoux\Cms\SiteBundle\Entity\Site;

/**
 * Class SiteListener
 * @package Sudoux\MortgageBundle\EventListener
 * @author Dan Alvare
 */
class SiteListener 
{
    /**
     * @var ContainerInterface
     */
	public $container;

    /**
     * @var EntityManager
     */
	protected $em;

    /**
     * @param ContainerInterface $container
     */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

    /**
     * @param LifecycleEventArgs $args
     */
	public function postPersist(LifecycleEventArgs $args)
	{
		$this->em = $args->getEntityManager();
		$entity = $args->getEntity();
		if ($entity instanceof LoanOfficer || $entity instanceof Branch) {
			$site = $entity->getSite();
			
			$autoCreateSites = $site->getSettings()->getInheritedAutoCreateSites();
			if($autoCreateSites) {
				if ($entity instanceof LoanOfficer) {
					
					if(!$entity->hasSite()) {
						$newSite = new Site();
						$newSite->setParentSite($site);
						$newSite->setActive(true);
						$settings = new Settings();
						$newSite->setSettings($settings);
						 
						$newSite->setName($entity->getFullName());
						$settings->setLoanOfficer($entity);
						$entity->setOfficerSite($newSite);
						$this->em->persist($entity);
	
						$siteEmail = $entity->getEmail();
						if(isset($siteEmail)) {
							$settings->setWebsiteEmail($siteEmail);
						}
						
						$siteType = $this->em->getRepository('SudouxCmsSiteBundle:SiteType')->findOneBy(array('key_name' => 'loan_officer'));
						$newSite->setSiteType($siteType);
						 
						$parentDomain = $site->getPrimaryDomain();
					
						$subdomain = str_replace(' ', '', strtolower($entity->getFullName()));
						$subdomain = preg_replace("/[^a-z ]/", '', $subdomain);
						$subdomain .= '.' . $parentDomain->getDomain();
					
						$this->em->persist($settings);
						$this->em->persist($newSite);
						
						$subdomain = $this->getAvailableDomain($subdomain);
					
						$domain = new Domain();
						$domain->setDomain($subdomain);
						$domain->setDescription(sprintf('Primary domain for %s', $newSite->getName()));
						$domain->setSite($newSite);
						$this->em->persist($domain);
						$newSite->setPrimaryDomain($domain);
						
						$this->em->flush();
					}
				}
				
				if ($entity instanceof Branch) {
					if(!$entity->hasSite()) {
						$newSite = new Site();
						$newSite->setParentSite($site);
						$newSite->setActive(true);
						$settings = new Settings();
						$newSite->setSettings($settings);
						 
						$newSite->setName($entity->getName());
						$settings->setBranch($entity);
						$entity->setBranchSite($newSite);
						$this->em->persist($entity);
						
						$siteEmail = $entity->getEmail();
						if(isset($siteEmail)) {
							$settings->setWebsiteEmail($siteEmail);
						}
						
						$siteType = $this->em->getRepository('SudouxCmsSiteBundle:SiteType')->findOneBy(array('key_name' => 'branch'));
						$newSite->setSiteType($siteType);
						 
						$parentDomain = $site->getPrimaryDomain();
					
						$subdomain = str_replace(' ', '', strtolower($entity->getName()));
						$subdomain = preg_replace("/[^a-z ]/", '', $subdomain);
						$subdomain .= '.' . $parentDomain->getDomain();
					
						$this->em->persist($settings);
						$this->em->persist($newSite);
						
						$subdomain = $this->getAvailableDomain($subdomain);
					
						$domain = new Domain();
						$domain->setDomain($subdomain);
						$domain->setDescription(sprintf('Primary domain for %s', $newSite->getName()));
						$domain->setSite($newSite);
						$this->em->persist($domain);
						$newSite->setPrimaryDomain($domain);
						
						$this->em->flush();
					}
				}
			}
		}
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function postUpdate(LifecycleEventArgs $args)
	{
		$em = $args->getEntityManager();
		$entity = $args->getEntity();

		// delete the related sites if a branch or lo is deleted
		if ($entity instanceof Branch) {
			$deleted = $entity->getDeleted();
			if($deleted) {
				$entity->setLosId(null);
				$em->persist($entity);

				$branchSite = $entity->getBranchSite();
				$branchSite->setActive(false);
				$branchSite->setDeleted(true);
				$em->persist($branchSite);
				$em->flush();
			}
		}

		if ($entity instanceof LoanOfficer) {
			$deleted = $entity->getDeleted();
			if($deleted) {
				$entity->setLosId(null);
				$em->persist($entity);

				$officerSite = $entity->getOfficerSite();
				if(isset($officerSite)) {
					$officerSite->setActive(false);
					$officerSite->setDeleted(true);
					$em->persist($officerSite);
				}

				$em->flush();
			}
		}

	}

    /**
     * @param $domain
     * @param int $i
     * @return string
     */
	protected function getAvailableDomain($domain, $i = 1)
	{
		$domainEntity = $this->em->getRepository('SudouxCmsSiteBundle:Domain')->findOneBy(array('domain' => $domain));
		if(isset($domainEntity)) {
			$domainArray = explode('.', $domain);
			$domainArray[0] .= $i;
			$domain = implode('.', $domainArray); 
			$domain = $this->getAvailableDomain($domain, $i++);
		}
		
		return $domain;
	}
}