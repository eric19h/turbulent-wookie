<?php

namespace Sudoux\MortgageBundle\Entity;

use Sudoux\Cms\UserBundle\Entity\User;

use Sudoux\Cms\SiteBundle\Entity\Site;

use Sudoux\Cms\SiteBundle\Model\SiteEntityRepository;

use Doctrine\ORM\EntityRepository;

/**
 * Class LoanApplicationRepository
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 */
class LoanApplicationRepository extends SiteEntityRepository
{
	/**
	 * @param Site $site
	 * @return Ambigous <\Doctrine\ORM\QueryBuilder, boolean, \Doctrine\ORM\\Doctrine\ORM\QueryBuilder>
	 */
	public function findAllBySiteQuery(Site $site) 
	{
		$siteIds = $site->getChildSiteIds();
		array_push($siteIds, $site->getId());
		
		$q = $this
			->createQueryBuilder('q')
            ->leftJoin('q.additional_site', 'a')
			->where('q.site IN (:site_ids)')
            ->orWhere('a.id = :site_id')
            ->andWhere('q.deleted = 0')
			->orderBy('q.created', 'DESC')
			->setParameter('site_ids', $siteIds)
            ->setParameter('site_id', $site->getId());
			
		return $q;
	}

    /**
     * @param Site $site
     * @param LoanOfficer $loanOfficer
     * @return \Doctrine\ORM\QueryBuilder
     */
	public function findAllByLoanOfficerQuery(Site $site, LoanOfficer $loanOfficer) 
	{
		$siteIds = $site->getChildSiteIds();
		array_push($siteIds, $site->getId());
		
		$q = $this
			->createQueryBuilder('q')
			->where('q.site IN (:site_ids)')
			->andWhere('q.deleted = 0')
			->andWhere('q.loan_officer = :loan_officer')
			->orderBy('q.created', 'DESC')
			->setParameter('loan_officer', $loanOfficer->getId())
			->setParameter('site_ids', $siteIds);
			
		return $q;
	}

    /**
     * @param Site $site
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
	public function findOneBySite(Site $site, $id)
	{
		$siteIds = $site->getChildSiteIds();
		array_push($siteIds, $site->getId());
		
		$q = $this
			->createQueryBuilder('q')
            ->leftJoin('q.additional_site', 'a')
			->where('q.site IN (:site_ids)')
            ->orWhere('a.id = :site_id')
			->andWhere('q.id = :id')
			->andWhere('q.deleted = 0')
			->setParameter('id', $id)
			->setParameter('site_ids', $siteIds)
            ->setParameter('site_id', $site->getId())
			->getQuery();

		return $q->getOneOrNullResult();
	}

	/**
	 * @param Site $site
	 * @param $guid
	 * @return mixed
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findOneBySiteAndGuid(Site $site, $guid)
	{
		$siteIds = $site->getChildSiteIds();
		array_push($siteIds, $site->getId());

		$q = $this
			->createQueryBuilder('q')
			->leftJoin('q.additional_site', 'a')
			->where('q.site IN (:site_ids)')
			->orWhere('a.id = :site_id')
			->andWhere('q.guid = :guid')
			->andWhere('q.deleted = 0')
			->setParameter('guid', $guid)
			->setParameter('site_ids', $siteIds)
			->setParameter('site_id', $site->getId())
			->getQuery();

		return $q->getOneOrNullResult();
	}

    /**
     * @param Site $site
     * @param User $user
     * @param $applicationId
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
	public function findOneByUser(Site $site, User $user, $applicationId)
	{
		$q = $this
			->createQueryBuilder('q')
			->leftJoin('q.client_user', 'c')
			->where('q.site = :site')
			->andWhere('q.user = :user_id OR c.id = :user_id')
			->andWhere('q.deleted = 0')
			->andWhere('q.id = :application_id')
			->setParameter('user_id', $user->getId())
			->setParameter('site', $site->getId())
			->setParameter('application_id', $applicationId)
			->getQuery();
				
		return $q->getOneOrNullResult();
	}

    /**
     * @param Site $site
     * @param User $user
     * @return \Doctrine\ORM\Query
     */
	public function findAllByUserQuery(Site $site, User $user)
	{
		$q = $this
			->createQueryBuilder('q')
			->leftJoin('q.client_user', 'c')
			->where('q.site = :site')
			->andWhere('q.user = :user_id OR c.id = :user_id')
			->andWhere('q.deleted = 0')
			->setParameter('user_id', $user->getId())
			->setParameter('site', $site->getId())
			->orderBy('q.created', 'DESC')
			->getQuery();

		return $q;
	}

    /**
     * @param Site $site
     * @param User $user
     * @return array
     */
	public function findCountByUserQuery(Site $site, User $user)
	{
		$q = $this
			->createQueryBuilder('q')
			->select('count(q.id) as total')
			->leftJoin('q.client_user', 'c')
			->where('q.site = :site')
			->andWhere('q.user = :user_id OR c.id = :user_id')
			->andWhere('q.deleted = 0')
			->setParameter('user_id', $user->getId())
			->setParameter('site', $site->getId())
			->orderBy('q.created', 'DESC');

		return $q->getQuery()->getScalarResult();
	}

    /**
     * @param Site $site
     * @param User $user
     * @return array
     */
	public function findNewMessagesCountByUserQuery(Site $site, User $user)
	{
		$q = $this
			->createQueryBuilder('l')
			->select('count(t.id) as messages')
			->join('l.message_thread', 't')
			->join('t.message', 'm')
			->where('l.site = :site')
			->andWhere('m.user != :user_id')
            ->andWhere('l.user = :user_id')
			->andWhere('l.deleted = 0')
			->andWhere('m.status = 0')
			->setParameter('user_id', $user->getId())
			->setParameter('site', $site->getId());
        //echo $q->getDQL(); exit;
		return $q->getQuery()->getScalarResult();
	}

    /**
     * @param $siteIds
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param null $loanOfficerId
     * @return array
     */
	public function findCountBySiteIds($siteIds, \DateTime $startDate = null, \DateTime $endDate = null, $loanOfficerId = null)
	{
		$q = $this
		->createQueryBuilder('l')
		->leftJoin('l.site', 's')
		->select('count(l.id) as total, s.name')
		->where('l.site IN (:siteIds)')
		->groupBy('l.site')
		->setParameter('siteIds', $siteIds);
			
		if(isset($startDate)) {
			$q->andWhere('l.created >= :start_date');
			$q->setParameter('start_date', $startDate);
		}
	
		if(isset($endDate)) {
			$q->andWhere('l.created <= :end_date');
			$q->setParameter('end_date', $endDate);
		}

		if(isset($loanOfficerId)) {
			$q->andWhere('l.loan_officer = :loan_officer');
			$q->setParameter('loan_officer', $loanOfficerId);
		}

		return $q->getQuery()->getScalarResult();
	}

    /**
     * @param $siteIds
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array
     */
	public function findCountByDateAndSiteIds($siteIds, \DateTime $startDate = null, \DateTime $endDate = null)
	{
		$q = $this
		->createQueryBuilder('l')
		->leftJoin('l.site', 's')
		->select('count(l.id) as total, s.name, s.created')
		->where('l.site IN (:siteIds)')
		->groupBy('DAY(l.created)')
		->setParameter('siteIds', $siteIds);
			
		if(isset($startDate)) {
			$q->andWhere('l.created >= :start_date');
			$q->setParameter('start_date', $startDate);
		}
	
		if(isset($endDate)) {
			$q->andWhere('l.created <= :end_date');
			$q->setParameter('end_date', $endDate);
		}

		return $q->getQuery()->getScalarResult();
	}

    /**
     * @param $losIds
     * @return array|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
	public function findByLosIds($losIds)
	{
		$q = $this->createQueryBuilder('q')
		->where('q.deleted = 0');
		if(is_array($losIds)) {
			$q->andWhere('q.los_id IN (:los_ids)');
			$q->setParameter('los_ids', $losIds);
			$q->orderBy('q.created', 'ASC');
			$result = $q->getQuery()->getResult();
		} else {
			$q->andWhere('q.los_id = :los_id');
			$q->setParameter('los_id', $losIds);			
			$result = $q->getQuery()->getOneOrNullResult();
		}
			
		return $result;
	}
}
