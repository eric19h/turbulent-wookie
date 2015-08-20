<?php

namespace Sudoux\EagleBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class BorrowerFullRepository
 * @package Sudoux\EagleBundle\Entity
 * @author Eric Haynes
 */
class BorrowerFullRepository extends EntityRepository
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
