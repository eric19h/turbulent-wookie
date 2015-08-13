<?php

namespace Sudoux\MortgageBundle\Entity;

use Sudoux\Cms\SiteBundle\Entity\Site;

use Sudoux\Cms\SiteBundle\Model\SiteEntityRepository;

use Doctrine\ORM\EntityRepository;

/**
 * Class BranchRepository
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 */
class BranchRepository extends SiteEntityRepository
{
    /**
     * @param Site $site
     * @param string $type
     * @return \Doctrine\ORM\QueryBuilder
     */
	public function findAllBySiteQuery(Site $site, $type = 'parent')
	{
		$q = $this->createQueryBuilder('q');

		$siteIds = array();
		if($type == 'parent') {
			$siteIds = $site->getParentSiteIds();
		} else if($type == 'child') {
			$siteIds = $site->getChildSiteIds();			
		}
		
		array_push($siteIds, $site->getId());
		
		if(count($siteIds) > 1) {
			$q->where('q.site IN (:site_ids)');
			$q->setParameter('site_ids', $siteIds);
		} else {
			$q->where('q.site = :site');
			$q->setParameter('site', $site->getId());
		}
		
		$q->andWhere('q.deleted = 0');

		$q->orderBy('q.weight', 'ASC');

	
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

		$q = $this->createQueryBuilder('b')
				->select("b.name, b.nmls_id, b.phone, b.fax, b.email, CONCAT(CONCAT(m.first_name, ' '), m.last_name) as branchManager, b.los_id, l.address1 as address, l.city, st.name as stateName, l.zipcode, b.active, d.domain as website")
				->leftJoin('b.location', 'l')
				->innerJoin('l.state', 'st')
				->leftJoin('b.branch_manager', 'm')
				->leftJoin('b.branch_site', 's')
				->leftJoin('s.primary_domain', 'd')
				->where('b.site IN (:site_ids)')
				->setParameter('site_ids', $siteIds)
				->andWhere('b.deleted = 0')
				->orderBy('b.name', 'ASC');

		return $q;
	}

    /**
     * @param Site $site
     * @param string $type
     * @return \Doctrine\ORM\QueryBuilder
     */
	public function findAllActiveBySiteQuery(Site $site, $type = 'parent')
	{
		$q = $this->findAllBySiteQuery($site, $type)->andWhere('q.active = 1');
	
		return $q;
	}

	/**
	 * @param Site $site
	 * @param $search
	 * @param bool $activeOnly
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function findAllBySiteLike(Site $site, $search, $activeOnly = false)
	{
		$siteIds = $site->getParentSiteIds();

		array_push($siteIds, $site->getId());

		$q = $this->createQueryBuilder('b');
		$q->where("b.name LIKE :name")
			->andWhere('b.site IN (:site_ids)')
			->andWhere('b.deleted = 0')
			->setParameter('site_ids', $siteIds)
			->setParameter('name', '%' . $search . '%');

		if($activeOnly) {
			$q->andWhere('b.active = 1');
		}

		$q->orderBy('b.name', 'ASC');

		return $q;
	}

    /**
     * @param Site $site
     * @return array
     */
	public function findStatesBySite(Site $site)
	{
		$siteIds = $site->getParentSiteIds();
		array_push($siteIds, $site->getId());
		
		$q = $this
			->createQueryBuilder('b')
			->innerJoin('b.location', 'l')
			->innerJoin('l.state', 's')
			->select('DISTINCT s.abbreviation')
			->where('b.site IN (:site_ids)')
			->andWhere('b.active = 1')
			->andWhere('b.deleted = 0')
			->setParameter('site_ids', $siteIds)
			->orderBy('b.weight', 'ASC')
			->getQuery()
			->getResult();
	
		return $q;
	}

    /**
     * @param Site $site
     * @param $stateAbbreviation
     * @return \Doctrine\ORM\QueryBuilder
     */
	public function findAllBySiteAndStateQuery(Site $site, $stateAbbreviation)
	{
		$siteIds = $site->getParentSiteIds();
		array_push($siteIds, $site->getId());
		
		$q = $this
			->createQueryBuilder('b')
			->innerJoin('b.location', 'l')
			->innerJoin('l.state', 's');
		
		if(count($siteIds) > 1) {
			$q->where('b.site IN (:site_ids)');
			$q->setParameter('site_ids', $siteIds);
		} else {
			$q->where('b.site = :site');
			$q->setParameter('site', $site->getId());
		}
		
		$q->andWhere('s.abbreviation = :state_abbreviation')
			->andWhere('b.active = 1')
			->andWhere('b.deleted = 0')
			->setParameter('state_abbreviation', $stateAbbreviation)
			->orderBy('b.weight', 'ASC');
	
		return $q;
	}

	/**
	 * @param Site $site
	 * @param $stateAbbreviation
	 * @param $city
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function findAllBySiteStateAndCityQuery(Site $site, $stateAbbreviation, $city)
	{
		$q = $this->findAllBySiteAndStateQuery($site, $stateAbbreviation);
		$q->andWhere('l.city = :city')
			->setParameter('city', $city);

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
		$siteIds = $site->getParentSiteIds();
		array_push($siteIds, $site->getId());

		$q = $this->createQueryBuilder('b')
		->where('b.site IN (:site_ids)')
		->andWhere('b.deleted = 0')
		->andWhere('b.active = 1')
		->andWhere('b.id = :id')
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
	public function findOneBySiteAndLosId(Site $site, $losId)
	{
		$siteIds = $site->getParentSiteIds();
		$childSiteIds = $site->getChildSiteIds();
		foreach($childSiteIds as $childSiteId) {
			array_push($siteIds, $childSiteId);
		}
		array_push($siteIds, $site->getId());

		$q = $this->createQueryBuilder('b')
			->where('b.site IN (:site_ids)')
			->andWhere('b.deleted = 0')
			->andWhere('b.active = 1')
			->andWhere('b.los_id = :los_id')
			->setParameter('site_ids', $siteIds)
			->setParameter('los_id', $losId);

		$result = $q->getQuery()->getOneOrNullResult();

		return $result;
	}

    /**
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $site
     * @param $nmls
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @author Dan Alvare
     */
    public function findOneBySiteAndNmlsId(Site $site, $nmls)
    {
        $siteIds = $site->getParentSiteIds();
        $childSiteIds = $site->getChildSiteIds();
        foreach($childSiteIds as $childSiteId) {
            array_push($siteIds, $childSiteId);
        }
        array_push($siteIds, $site->getId());

        $q = $this->createQueryBuilder('b')
            ->where('b.site IN (:site_ids)')
            ->andWhere('b.deleted = 0')
            ->andWhere('b.active = 1')
            ->andWhere('b.nmls_id = :nmls_id')
            ->setParameter('site_ids', $siteIds)
            ->setParameter('nmls_id', $nmls);

        $results = $q->getQuery()->getResult();

		if(count($results) > 0) {
			$result = $results[0];
		} else {
			$result = null;
		}

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

        $q = $this->createQueryBuilder('b')
            ->where('b.site IN (:site_ids)')
            ->andWhere('b.deleted = 0')
            ->setParameter('site_ids', $siteIds);

        $siteBranch = $site->getSettings()->getBranch();
        if(isset($siteBranch)) {
            //$q->andWhere('b.id = :id OR b.id = :branch_id');
			// @todo - fix security issue from child branch site access to edit other branches
			$q->andWhere('b.id = :id');
            $q->setParameter('id', $id);
            //$q->setParameter('branch_id', $siteBranch->getId());
        } else {
            $q->andWhere('b.id = :id');
            $q->setParameter('id', $id);
        }

        $result = $q->getQuery()->getOneOrNullResult();

        return $result;
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

		$q = $this->createQueryBuilder('b')
			->select('count(b.id)')
			->where('b.site IN (:site_ids)')
			->setParameter('site_ids', $siteIds)
			->getQuery();

		return $q->getSingleScalarResult();
	}
}
