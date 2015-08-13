<?php

namespace Sudoux\MortgageBundle\Model\LoanFormat;

use Sudoux\MortgageBundle\Entity\LoanApplication;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

/**
 * Class VantageFormat
 * @package Sudoux\MortgageBundle\Model\LoanFormat
 * @author Eric Haynes
 */
class VantageFormat implements iLoanFormat
{


    /**
     * @var \Doctrine\ORM\EntityManager
     * @author Eric Haynes
     */
    protected $em;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     * @author Eric Haynes
     */
    protected $container;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container){

        $this->em = $em;
        $this->container = $container;

    }


    public function createLoanFromFormat($dataIn, Site $site = null){



    }

    public function upsertLoanFromFormat( $dataIn, Site $site = null, LoanApplication $application = null){



    }



    public function convertFromLoanApp( LoanApplication $application){

        $this->em->detach($application);
        $vantage = $this->formatVantage($application);
        $applicationXml = $this->container->get('templating')->render('SudouxMortgageBundle:LoanApplicationAdmin/formats:vantageFull.xml.twig', array('application' => $vantage), 'text/xml');

        return $applicationXml;


    }


    public function convertToLoanApp($dataIn){





    }

    protected function formatVantage(LoanApplication $application)
    {
        $site = $application->getSite();
        $a = New \stdClass();
        $coBorrowers            = $application->getCoBorrower();
        $borrower               = $application->getBorrower();
        $a->isCurrentlyEmployed = False;
        $a->coIsCurrentlyEmployed = False;


        $a->firstName           = $borrower->getFirstName();
        $a->lastName            = $borrower->getLastName();
        $a->address1            = $borrower->getLocation()->getLocation()->getAddress1();
        $a->address2            = $borrower->getLocation()->getLocation()->getAddress2();
        $a->city                = $borrower->getLocation()->getLocation()->getCity();
        $a->state               = $borrower->getLocation()->getLocation()->getState()->getAbbreviation();
        $a->zipcode             = $borrower->getLocation()->getLocation()->getZipcode();
        $a->email               = $borrower->getEmail();
        $a->phoneHome           = $borrower->getPhoneHome();
        $a->phoneCell           = $borrower->getPhoneMobile();
        $a->ssn                 = $borrower->getSsn();
        $a->dependants          = $borrower->getDependentsNumber();
        $a->dependantsAges      = $borrower->getDependentsAges();
        $a->yearsOfResidence    = $borrower->getLocation()->getYearsAtLocation();
        $a->monthsOfResidence   = $borrower->getLocation()->getMonthsAtLocation();
        $a->isRenting           = !$borrower->getLocation()->getOwnResidence();
        $a->hasCoborrower       = !$coBorrowers->isEmpty();
        $a->yearsOfSchool       = $borrower->getYearsOfSchool();


        switch ($borrower->getMaritalStatus()) {
            case '0':
                $a->maritalStatus       =  3;
                break;
            case '1':
                $a->maritalStatus       =  1;
                break;
            case '2':
                $a->maritalStatus       =  4;
                break;

            default:
                $a->maritalStatus       =  0;
        }



        foreach($borrower->getEmployment() as $job){

            if($job->getEndDate()->add(new \DateInterval('P1D')) >= $application->getCreated() && $application->getBorrower()->getEmployed()){

                $a->isCurrentlyEmployed = True;
                $job->setCurrent(True);

                $a->employer                = $job->getEmployerName();
                $a->employerPhone           = $job->getEmployerPhone();
                $a->employerAddress1        = $job->getLocation()->getAddress1();
                $a->employerAddress2        = $job->getLocation()->getAddress2();
                $a->employerCity            = $job->getLocation()->getCity();
                $a->employerState           = $job->getLocation()->getState()->getAbbreviation();
                $a->employerZipCode         = $job->getLocation()->getZipcode();
                $a->employmentYearsOnJob    = $job->getTimeAtJob()->years;
                $a->employmentMonthsOnJob   = $job->getTimeAtJob()->months;
                $a->employmentTitle         = $job->getTitle();


            }else{

                $job->setCurrent(False);

            }


        }

        foreach( $coBorrowers as $coBorrower) {

            $a->coFirstName     = $coBorrower->getFirstName();
            $a->coLastName      = $coBorrower->getLastName();
            $a->coAddress1      = $coBorrower->getLocation()->getLocation()->getAddress1();
            $a->coAddress2      = $coBorrower->getLocation()->getLocation()->getAddress2();
            $a->coCity          = $coBorrower->getLocation()->getLocation()->getCity();
            $a->coState         = $coBorrower->getLocation()->getLocation()->getstate()->getAbbreviation();
            $a->coZip           = $coBorrower->getLocation()->getLocation()->getZipcode();
            $a->coPhoneHome     = $coBorrower->getPhoneHome();
            $a->coPhoneCell     = $coBorrower->getPhoneMobile();
            $a->coSSN           = $coBorrower->getSsn();
            $a->coEmail         = $coBorrower->getEmail();
            $a->coDependants    = $coBorrower->getDependentsNumber();
            $a->coDependantsAges      = $coBorrower->getDependentsAges();
            $a->coYearsOfResidence    = $coBorrower->getLocation()->getYearsAtLocation();
            $a->coMonthsOfResidence   = $coBorrower->getLocation()->getMonthsAtLocation();
            $a->coIsRenting           = !$coBorrower->getLocation()->getOwnResidence();



            foreach($coBorrower->getEmployment() as $job){

                if($job->getEndDate()->add(new \DateInterval('P1D')) >= $application->getCreated() && $application->getBorrower()->getEmployed()){

                    $a->coIsCurrentlyEmployed = True;
                    $job->setCurrent(True);

                    $a->CoEmployer                = $job->getEmployerName();
                    $a->CoEmployerPhone           = $job->getEmployerPhone();
                    $a->CoEmployerAddress1        = $job->getLocation()->getAddress1();
                    $a->CoEmployerAddress2        = $job->getLocation()->getAddress2();
                    $a->CoEmployerCity            = $job->getLocation()->getCity();
                    $a->CoEmployerState           = $job->getLocation()->getState()->getAbbreviation();
                    $a->CoEmployerZipCode         = $job->getLocation()->getZipcode();
                    $a->CoEmploymentYearsOnJob    = $job->getTimeAtJob()->years;
                    $a->CoEmploymentMonthsOnJob   = $job->getTimeAtJob()->months;
                    $a->CoEmploymentTitle         = $job->getTitle();


                }else{

                    $job->setCurrent(False);

                }


            }

            switch ($coBorrower->getMaritalStatus()) {
                case '0':
                    $a->coMaritalStatus       =  3;
                    break;
                case '1':
                    $a->coMaritalStatus       =  1;
                    break;
                case '2':
                    $a->coMaritalStatus       =  4;
                    break;

                default:
                    $a->coMaritalStatus       =  0;
            }


        }


        $a->loanAmount          = $application->getLoanAmount();
        $a->propertyMarketValue = $application->getSalePrice();
        $a->propertyAddress1    = $application->getPropertyLocation()->getAddress1();
        $a->propertyAddress2    = $application->getPropertyLocation()->getAddress2();
        $a->propertyCity        = $application->getPropertyLocation()->getCity();
        $a->propertyState       = $application->getPropertyLocation()->getState()->getAbbreviation();
        $a->propertyZipCode     = $application->getPropertyLocation()->getZipcode();
        $a->realEstateBrokerPhone = $application->getRealtorPhone();


        $a->propertyUse             = $application->getResidencyType()+1;
        $a->vendorLeadPrice         = '$5.00';
        $a->vendorLeadId            = $application->getId();
        $a->vendorId                = $site->getSettings()->getLos()->getUsername();
        $a->vendorPassword          = $site->getSettings()->getLos()->getPassword();
        $a->orgId                   = $site->getSettings()->getLos()->getLicenseKey();
        $a->customerIp              = $ip = getenv('HTTP_CLIENT_IP')?:
            getenv('HTTP_X_FORWARDED_FOR')?:
                getenv('HTTP_X_FORWARDED')?:
                    getenv('HTTP_FORWARDED_FOR')?:
                        getenv('HTTP_FORWARDED')?:
                            getenv('REMOTE_ADDR');
        $a->vendorWebProperty       = $site->getPrimaryDomain()->getDomain();
        $a->vendorCampaign          = $site->getSettings()->getLos()->getSettings();

        return $a;

    }


}