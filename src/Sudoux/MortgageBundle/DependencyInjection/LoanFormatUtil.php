<?php

namespace Sudoux\MortgageBundle\DependencyInjection;

use Sudoux\MortgageBundle\Model\LoanFormat;
use Sudoux\MortgageBundle\Model\LoanFormat\iLoanFormat;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sudoux\MortgageBundle\Entity\LoanApplication;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Doctrine\ORM\EntityManager;
/**
 * Class LoanFormatUtil
 * @package Sudoux\MortgageBundle\DependencyInjection
 * @author Eric Haynes
 */
class LoanFormatUtil implements iLoanFormat
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var
     * @author Eric Haynes
     */
    protected $container;

    /**
     * @var
     * @author Eric Haynes
     */
    protected $type;

    /**
     * @var array
     * @author Eric Haynes
     */
    protected $types = array(
        0 => '\Sudoux\MortgageBundle\Model\LoanFormat\VantageFormat',
        1 => '\Sudoux\MortgageBundle\Model\LoanFormat\DestinyFormat',
        2 => '\Sudoux\MortgageBundle\Model\LoanFormat\Mismo231Format',
    );

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
	public function __construct(EntityManager $em, ContainerInterface $container)
	{
		$this->em = $em;
        $this->container = $container;
	}


    /**
     * @param $type
     * @throws \Exception
     * @author Eric Haynes
     */
    public function setType($type){

        if(is_numeric($type)){

            $this->type = $type;

        }else{

            switch ($type){

                case 'mismo231':
                    $this->type = 2;
                    break;

                case 'vantage':
                    $this->type = 0;
                    break;

                case 'destiny':
                    $this->type = 1;
                    break;
                default:
                    throw new \Exception();

            }

        }

    }

    /**
     * @param $dataIn
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $site
     * @return mixed
     * @author Eric Haynes
     */
    public function createLoanFromFormat( $dataIn, Site $site = null){

            $newLoanApp = New $this->types[$this->type]($this->em, $this->container);
            $a = $newLoanApp->createLoanFromFormat($dataIn, $site);

            return $a;

    }

    /**
     * @param $dataIn
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $site
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $application
     * @return mixed
     * @author Eric Haynes
     */
    public function upsertLoanFromFormat( $dataIn, Site $site = null, LoanApplication $application = null){

        $newLoanApp = New $this->types[$this->type]($this->em, $this->container);
        $a = $newLoanApp->upsertLoanFromFormat($dataIn, $site, $application);

        return $a;

    }


    /**
     * @param $dataIn
     * @return mixed
     * @author Eric Haynes
     */
    public function convertToLoanApp($dataIn){


        $newLoanApp = New $this->types[$this->type]($this->em, $this->container);
        $a = $newLoanApp->convertToLoanApp($dataIn);

        return $a;


    }


    /**
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $application
     * @return mixed
     * @author Eric Haynes
     */
    public function convertFromLoanApp( LoanApplication $application ){

        $newLoanApp = New $this->types[$this->type]($this->em, $this->container);
        $a = $newLoanApp->convertFromLoanApp($application);

        return $a;


    }


}