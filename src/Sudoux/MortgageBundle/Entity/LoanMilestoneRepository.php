<?php

namespace Sudoux\MortgageBundle\Entity;

use Sudoux\Cms\SiteBundle\Entity\Site;

use Sudoux\Cms\SiteBundle\Model\SiteEntityRepository;

use Doctrine\ORM\EntityRepository;

/**
 * Class LoanMilestoneRepository
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 */
class LoanMilestoneRepository extends SiteEntityRepository
{
    /**
     * @param $groupId
     * @return \Doctrine\ORM\QueryBuilder
     */
	public function findByMilestoneGroupQuery($groupId) 
	{
		$q = $this
		->createQueryBuilder('q')
		->where('q.milestone_group = :milestone_group')
		->setParameter('milestone_group', $groupId)
		->orderBy('q.weight', 'ASC');
		
		return $q;
	}

    /**
     * @param Site $site
     * @param $losId
     * @param $groupLosId
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
	public function findOneMilestoneByLosId(Site $site, $losId, $groupLosId) 
	{
		//$siteIds = $site->getParentSiteIds();
		//array_push($siteIds, $site->getId());

		$q = $this
			->createQueryBuilder('m')
			->innerJoin('m.milestone_group', 'g')
			->where('m.los_id = :los_id')
			->andWhere('g.site = :site_id')
			->andWhere('g.los_id = :group_los_id')
			->setParameter('group_los_id', $groupLosId)
			->setParameter('site_id', $site->getId())
			->setParameter('los_id', $losId)
			->getQuery();

		return $q->getOneOrNullResult();
	}
}
