<?php

namespace Sudoux\MortgageBundle\Entity;

use Sudoux\Cms\SiteBundle\Entity\Site;

use Sudoux\Cms\SiteBundle\Model\SiteEntityRepository;

use Doctrine\ORM\EntityRepository;

/**
 * Class LoanMilestoneGroupRepository
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 */
class LoanMilestoneGroupRepository extends SiteEntityRepository
{
    /**
     * @param Site $site
     * @param $losId
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
	public function findOneByLosId(Site $site, $losId)
	{
		$q = $this
		->createQueryBuilder('m')
		->where('m.site = :site')
		->andWhere('m.los_id = :los_id')
		->setParameter('los_id', $losId)
		->setParameter('site', $site->getId())
		->getQuery();
	
		$results = $q->getOneOrNullResult();
	
		return $results;
	}

	/**
	 * @param Site $site
	 * @return array
	 */
	public function findAllBySite(Site $site)
	{

		$q = $this->createQueryBuilder('q')
			->where('q.site = :site')
			->setParameter('site', $site->getId())
			->getQuery();


		$results = $q->getResult();

		return $results;
	}

	/**
	 * @param Site $site
	 * @return array
	 */
	public function findAllByParentSites(Site $site)
	{
		$siteIds = $site->getParentSiteIds();
		array_push($siteIds, $site->getId());

		$q = $this->createQueryBuilder('q')
			->where('q.site IN (:site_ids)')
			->setParameter('site_ids', $siteIds)
			->getQuery();


		$results = $q->getResult();

		return $results;
	}
}
