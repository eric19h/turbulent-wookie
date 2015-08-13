<?php

namespace Sudoux\MortgageBundle\Entity;

use Sudoux\Cms\UserBundle\Entity\User;

use Sudoux\Cms\SiteBundle\Entity\Site;

use Sudoux\Cms\SiteBundle\Model\SiteEntityRepository;

use Doctrine\ORM\EntityRepository;

/**
 * Class LoanDocumentRepository
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 */
class LoanDocumentRepository extends SiteEntityRepository
{
    /**
     * @param LoanApplication $application
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
	public function findOneByLoan(LoanApplication $application, $id)
	{
		$q = $this
			->createQueryBuilder('d')
			->leftJoin('d.loan', 'l')
			->where('l = :loan_id')
			->andWhere('d.id = :id')
			->setParameter('id', $id)
			->setParameter('loan_id', $application->getId())
			->getQuery();

		return $q->getOneOrNullResult();
	}

    /**
     * @param LoanApplication $application
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
	public function findOneByLosId(LoanApplication $application, $id)
	{
		$q = $this
			->createQueryBuilder('d')
			->where('l.loan = :loan_id')
			->andWhere('d.los_id = :id')
			->setParameter('id', $id)
			->setParameter('loan_id', $application->getId())
			->getQuery();
				
		return $q->getOneOrNullResult();
	}

    /**
     * @param Site $site
     * @param $limit
     * @param $offset
     * @return array
     */
	public function findBySiteNotSentToLos(Site $site, $limit, $offset)
	{
		$losConnection = $site->getSettings()->getInheritedLos();
		$siteIds = $site->getChildSiteIds();
		array_push($siteIds, $site->getId());

		if(isset($losConnection)) {
			$q = $this
			->createQueryBuilder('d')
			->leftJoin('d.loan', 'l')
			->where('l.site IN (:site_ids)')
			->andWhere('d.los_id IS NULL')
			->andWhere('l.los_id IS NOT NULL')
			->andWhere('l.deleted = 0')
			->setParameter('site_ids', $siteIds)
			->setFirstResult($offset)
			->setMaxResults($limit)
			->getQuery();
			
			return $q->getResult();
		} else {
			return;
		}
		
		
	}
}
