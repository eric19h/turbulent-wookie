<?php

namespace Sudoux\MortgageBundle\Entity;

use Sudoux\Cms\SiteBundle\Entity\Site;

use Sudoux\Cms\SiteBundle\Model\SiteEntityRepository;

use Doctrine\ORM\EntityRepository;

/**
 * Class LoanOfficerRepository
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 */
class LoanOfficerRepository extends SiteEntityRepository
{
    /**
     * @param Site $site
     * @param string $type
     * @return \Doctrine\ORM\QueryBuilder
     */
	public function findAllBySiteQuery(Site $site, $type = 'parent')
	{
        if($type == 'parent') {
            $siteIds = $site->getParentSiteIds();
        } else if($type == 'child') {
            $siteIds = $site->getChildSiteIds();
        }

		array_push($siteIds, $site->getId());

		$q = $this->createQueryBuilder('o')
			->where('o.site IN (:site_ids)')
			->andWhere('o.deleted = 0')
			->setParameter('site_ids', $siteIds)
			->addOrderBy('o.weight', 'ASC')
			->addOrderBy('o.last_name', 'ASC');

		return $q;
	}

	/**
	 * @param Site $site
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function findAllForExportBySiteQueryBuilder(Site $site)
	{
		$siteIds = $site->getChildSiteIds();
		array_push($siteIds, $site->getId());

		$q = $this->createQueryBuilder('o')
			->select("o.first_name, o.last_name, o.title, o.email, o.phone_office as officePhone, o.phone_mobile as mobilePhone, o.phone_tollfree as tollfreePhone, o.fax, o.nmls_id, o.los_id, o.active, o.created, u.username, d.domain as website")
			->leftJoin('o.officer_site', 's')
			->leftJoin('s.primary_domain', 'd')
			->leftJoin('o.user', 'u')
			->where('o.site IN (:site_ids)')
			->setParameter('site_ids', $siteIds)
			->andWhere('o.deleted = 0')
			->addOrderBy('o.weight', 'ASC')
			->addOrderBy('o.last_name', 'ASC');

		return $q;
	}

    /**
     * @param Site $site
     * @return \Doctrine\ORM\QueryBuilder
     */
	public function findAllActiveBySiteQuery(Site $site, $type = 'parent')
	{
		$q = $this->findAllBySiteQuery($site, $type)->andWhere('o.active = 1');
	
		return $q;
	}

    /**
     * @param Site $site
     * @return \Doctrine\ORM\QueryBuilder
     */
	public function findAllByParentSiteQuery(Site $site)
	{
		$siteIds = $site->getParentSiteIds();
		array_push($siteIds, $site->getId());

		$q = $this->createQueryBuilder('o')
			->where('o.site IN (:site_ids)')
			->andWhere('o.active = 1')
			->andWhere('o.deleted = 0')
			->setParameter('site_ids', $siteIds)
			->addOrderBy('o.weight', 'ASC')
			->addOrderBy('o.last_name', 'ASC');
	
		return $q;
	}

    /**
     * @param Site $site
     * @param Branch $branch
     * @return \Doctrine\ORM\QueryBuilder
     */
	public function findAllBySiteAndBranchQuery(Site $site, Branch $branch)
	{
		$siteIds = $site->getParentSiteIds();
		array_push($siteIds, $site->getId());
		
		$q = $this
			->createQueryBuilder('o')
			->innerJoin('o.branch', 'b')
			->where('o.site IN (:site_ids)')
			->andWhere('o.active = 1')
			->andWhere('o.deleted = 0')
			->andWhere('b.id = :branch_id')
			->setParameter('branch_id', $branch->getId())
			->setParameter('site_ids', $siteIds)
			->addOrderBy('o.weight', 'ASC')
			->addOrderBy('o.last_name', 'ASC');
	
		return $q;
	}

    /**
     * @param Site $site
     * @return \Doctrine\ORM\QueryBuilder
     */
	public function findAllBySiteType(Site $site)
	{
		$siteType = $site->getSiteType();
		
		$q = $this->createQueryBuilder('o')
				->where('o.deleted = 0')
				->andWhere('o.active = 1');
		
		$officer = $site->getSettings()->getLoanOfficer();
		$branch = $site->getSettings()->getBranch();
		
		if(isset($siteType)) {
			if($siteType->getKeyName() == 'branch' && isset($branch)) {
				
				$q->andWhere('o.branch = :branch');
				$q->setParameter('branch', $branch->getId());
				
			} else if($siteType->getKeyName() == 'loan_officer' && isset($officer)) {
				$q->andWhere('o.id = :officer');
				$q->setParameter('officer', $officer->getId());
			} else if($siteType->getKeyName() == 'corporate') {
				// get the officers on this site
				$siteIds = $site->getChildSiteIds();
				array_push($siteIds, $site->getId());
				$q->andWhere('o.site IN (:site_ids)');
				$q->setParameter('site_ids', $siteIds);
			} else {
				$q->andWhere('o.site = :site');
				$q->setParameter('site', $site->getId());			
			}
			
		} else {
			$q->andWhere('o.site = :site');
			$q->setParameter('site', $site->getId());						
		}
		
		$q->addOrderBy('o.weight', 'ASC');
		$q->addOrderBy('o.last_name', 'ASC');
		
		
		return $q;
	}

    /**
     * @param Site $site
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
	public function findOneBySite(Site $site, $id, $activeOnly = true)
	{
		$siteIds = $site->getParentSiteIds();
		array_push($siteIds, $site->getId());
		
		$q = $this->createQueryBuilder('o')
			->where('o.site IN (:site_ids)')
			->andWhere('o.deleted = 0');

			if($activeOnly) {
				$q->andWhere('o.active = 1');
			}

			$q->andWhere('o.id = :id')
			->setParameter('site_ids', $siteIds)
			->setParameter('id', $id);
	
		$result = $q->getQuery()->getOneOrNullResult();
	
		return $result;
	}

    /**
     * @param Site $site
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
	public function findOneBySiteAndLosId(Site $site, $id)
	{
		$siteIds = $site->getParentSiteIds();
		$childSiteIds = $site->getChildSiteIds();
		foreach($childSiteIds as $childSiteId) {
			array_push($siteIds, $childSiteId);
		}

		array_push($siteIds, $site->getId());
		
		$q = $this->createQueryBuilder('o')
			->where('o.site IN (:site_ids)')
			->andWhere('o.deleted = 0')
			->andWhere('o.active = 1')
			->andWhere('o.los_id = :id')
			->setParameter('site_ids', $siteIds)
			->setParameter('id', $id);
	
		$result = $q->getQuery()->getOneOrNullResult();
	
		return $result;
	}
    /**
     * @param Site $site
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByLosId( $id )
    {

        $q = $this->createQueryBuilder('o')
            ->Where('o.los_id = :id')
            ->setParameter('id', $id);

        $result = $q->getQuery()->getOneOrNullResult();

        return $result;
    }


	/**
	 * @param Site $site
	 * @param $id
	 * @return mixed
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function findOneBySiteAndType(Site $site, $id)
    {
        $siteIds = $site->getParentSiteIds();
        array_push($siteIds, $site->getId());

        $q = $this->createQueryBuilder('o')
            ->where('o.site IN (:site_ids)')
            ->leftJoin('o.branch', 'b')
            ->andWhere('o.deleted = 0')
            ->setParameter('site_ids', $siteIds);

        $siteBranch = $site->getSettings()->getBranch();
        $siteType = $site->getSiteType();
        $siteTypeKey = '';
        if(isset($siteType)) {
            $siteTypeKey = $siteType->getKeyName();
        }

        if(isset($siteBranch) && $siteTypeKey == 'branch') {
            $q->andWhere('o.id = :id AND b.id = :branch_id');
            $q->setParameter('id', $id);
            $q->setParameter('branch_id', $siteBranch->getId());
        } else {
            $q->andWhere('o.id = :id');
            $q->setParameter('id', $id);
        }

        $result = $q->getQuery()->getOneOrNullResult();

        return $result;
    }

	public function findAllBySiteLike(Site $site, $search)
	{

		$siteIds = $site->getChildSiteIds();

		array_push($siteIds, $site->getId());

		$q = $this->createQueryBuilder('o');
		$q->where("o.first_name LIKE :name OR o.last_name LIKE :name")
			->orWhere($q->expr()->concat('o.first_name', $q->expr()->concat($q->expr()->literal(' '), 'o.last_name')) . ' LIKE :name')
			->andWhere('o.site IN (:site_ids)')
			->andWhere('o.deleted = 0')
			->setParameter('site_ids', $siteIds)
			->addOrderBy('o.weight', 'ASC')
			->addOrderBy('o.last_name', 'ASC');

		$q->setParameter('name', $search . '%');

		return $q;
	}

	/**
	 * @param Site $site
	 * @return \DateTime
	 */
	public function findLastModifiedBySite(Site $site)
	{
		$siteIds = array();

		$parentSiteIds = $site->getParentSiteIds();
		$childSiteIds = $site->getChildSiteIds();

		foreach($parentSiteIds as $siteId) {
			array_push($siteIds, $siteId);
		}

		foreach($childSiteIds as $siteId) {
			array_push($siteIds, $siteId);
		}

		array_push($siteIds, $site->getId());

		$q = $this->createQueryBuilder('q')
			->select('q.modified')
			->where('q.site IN (:site_ids)')
			->andWhere('q.active = 1')
			->andWhere('q.deleted = 0')
			->setParameter('site_ids', $siteIds)
			->orderBy('q.modified', 'DESC')
			->setMaxResults(1);

		$results = $q->getQuery()->getScalarResult();

		if(count($results) == 0) {
			return new \DateTime();
		} else {
			return new \DateTime($results[0]['modified']);
		}
	}

	/**
	 * @param Site $site
	 * @return mixed
	 */
	public function countBySite(Site $site)
	{
		$siteIds = $site->getParentSiteIds();
		array_push($siteIds, $site->getId());

		$q = $this->createQueryBuilder('o')
			->select('count(o.id)')
			->where('o.site IN (:site_ids)')
			->setParameter('site_ids', $siteIds)
			->getQuery();

		return $q->getSingleScalarResult();
	}

	/**
	 * @param Site $site
	 * @param string $type
	 * @return array
	 */
	public function findAllBySiteWithoutUser(Site $site, $type = 'parent')
	{
		$q = $this->findAllBySiteQuery($site, 'child');
		$q->andWhere('o.user IS NULL');
		$q->andWhere('o.active=1');

		return $q->getQuery()->getResult();
	}
}
