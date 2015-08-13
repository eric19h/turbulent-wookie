<?php

namespace Sudoux\MortgageBundle\Model\LoanFormat;

use Sudoux\MortgageBundle\Entity\LoanApplication;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;


/**
 * Interface iLoanFormat
 * @package Sudoux\MortgageBundle\Model\LoanFormat
 * @author Eric Haynes
 */
interface iLoanFormat
{

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container);

    /**
     * @param $dataIn
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $site
     * @return mixed
     * @author Eric Haynes
     */
    public function createLoanFromFormat( $dataIn, Site $site = null);

    /**
     * @param $dataIn
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $site
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $application
     * @return mixed
     * @author Eric Haynes
     */
    public function upsertLoanFromFormat( $dataIn, Site $site = null, LoanApplication $application = null);

    /**
     * @param $dataIn
     * @return mixed
     * @author Eric Haynes
     */
    public function convertToLoanApp( $dataIn);

    /**
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $application
     * @return mixed
     * @author Eric Haynes
     */
    public function convertFromLoanApp( LoanApplication $application );


}