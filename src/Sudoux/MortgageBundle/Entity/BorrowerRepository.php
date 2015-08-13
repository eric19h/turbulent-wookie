<?php

namespace Sudoux\MortgageBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\Collection;

/**
 * Class BorrowerRepository
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 */
class BorrowerRepository extends EntityRepository
{
    /**
     * @param $loanId
     * @return \Doctrine\ORM\QueryBuilder
     */
	public function findByLoanApplication($loanId) {
		$q = $this
			->createQueryBuilder('b')
			->leftJoin('b.loan', 'l')
			->leftJoin('b.loan_coborrower', 'lc')
			->where('l.id = :loan_id')
			->orWhere('lc.id = :loan_id')
			->setParameter('loan_id', $loanId);
	
		return $q;
	}
}
