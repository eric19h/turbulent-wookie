<?php

namespace Sudoux\MortgageBundle\Model\LoanFormat;

use Sudoux\MortgageBundle\Entity\LoanApplication;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Sudoux\MortgageBundle\Model\DeepCopy\DeepCopy;
use Sudoux\MortgageBundle\Model\DeepCopy\Filter\Doctrine\DoctrineCollectionFilter;
use Sudoux\MortgageBundle\Model\DeepCopy\Filter\KeepFilter;
use Sudoux\MortgageBundle\Model\DeepCopy\Filter\ReplaceFilter;
use Sudoux\MortgageBundle\Model\DeepCopy\Filter\SetNullFilter;
use Sudoux\MortgageBundle\Model\DeepCopy\Matcher\PropertyMatcher;
use Sudoux\MortgageBundle\Model\DeepCopy\Matcher\PropertyNameMatcher;
use Sudoux\MortgageBundle\Model\DeepCopy\Matcher\PropertyTypeMatcher;

/**
 * Class MismoFormat
 * @package Sudoux\MortgageBundle\Model\LoanFormat
 * @author Eric Haynes
 */
class Mismo231Format implements iLoanFormat
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

    public function upsertLoanFromFormat($dataIn, Site $site = null, LoanApplication $application = null){



    }


    public function convertToLoanApp($dataIn){



    }


    public function convertFromLoanApp( LoanApplication $application ){


        $this->em->detach($application);
        $mismo = $this->formatMismo231($application);
        $applicationXml = $this->container->get('templating')->render('SudouxMortgageBundle:LoanApplicationAdmin/formats:mismo231.xml.twig', array('application' => $mismo), 'text/xml');

        return $applicationXml;



    }

    protected function formatMismo231(LoanApplication $application)
    {


        $borrower = $application->getBorrower();
        $maritalStatusBorrower = $borrower->getMaritalStatus();
        $coBorrowers = $application->getCoBorrower();
        $type = $application->getLoanType();
        $propertyType = $application->getPropertyType();
        $resType = $application->getResidencyType();

        switch ($propertyType) {

            case '1':
                $application->setPropertyType("twotofourunitproperty");
                break;
            case '2':
                $application->setPropertyType("condominium");
                break;
            case '3':
                $application->setPropertyType("twotofourunitproperty");
                break;
            case '4':
                $application->setPropertyType("twotofourunitproperty");
                break;
            case '5':
                $application->setPropertyType("condominium");
                break;
            case '7':
                $application->setPropertyType("condominium");
                break;

            default:
                $application->setPropertyType("singlefamilyresidence");

        }



        if($type == 0){
            $application->setLoanType("Purchase");
        }else{
            if($application->getRefinancePurpose() == 3){
                $application->setLoanType("Refinance");
            }else{
                $application->setLoanType("Refinance");
            }
        }



        $appTitleManner = $application->getTitleManner();

        switch ($appTitleManner) {

            case 'Community property':
                $application->setTitleManner("Community Property");
                break;
            case 'Joint Tenants':
                $application->setTitleManner("Joint Tenancy");
                break;
            case 'Single Man':
                $application->setTitleManner("Single");
                break;
            case 'Single Woman':
                $application->setTitleManner("Single");
                break;
            case 'Married Man':
                $application->setTitleManner("A Married Man As His Separate Estate");
                break;
            case 'Married Woman':
                $application->setTitleManner("A Married Woman As Her Separate Estate");
                break;
            case 'Tenants in common':
                $application->setTitleManner("Tenancy in Common");
                break;
            case 'To be decided in escrow':
                $application->setTitleManner("");
                break;
            case 'Unmarried man':
                $application->setTitleManner("An Unmarried Person");
                break;
            case 'Unmarried woman':
                $application->setTitleManner("An Unmarried Person");
                break;
            case 'Other':
                $application->setTitleManner("");
                break;
            default:
                $application->setTitleManner("");

        }




        $refiPurpose = $application->getRefinancePurpose();

        switch ($refiPurpose) {

            case '0':
                $application->setRefinancePurpose("cashoutdebtconsolidation");
                break;
            case '1':
                $application->setRefinancePurpose("cashouthomeimprovement");
                break;
            case '2':
                $application->setRefinancePurpose("cashoutother");
                break;
            case '3':
                $application->setRefinancePurpose("nocashoutother");
                break;
            case '4':
                $application->setRefinancePurpose("cashoutlimited");
                break;

            default:
                $application->setRefinancePurpose("cashoutdebtconsolidation");

        }



        switch ($resType) {
            case '0':
                $application->setResidencyType("PrimaryResidence");
                break;
            case '1':
                $application->setResidencyType("SecondHome");
                break;
            case '2':
                $application->setResidencyType("Investor");
                break;

            default:
                $application->setResidencyType("PrimaryResidence");
        }


        if ($borrower->getDeclarationUsCitizen()) {

            $borrower->setDeclarationUsCitizen("USCitizen");

        }elseif($borrower->getDeclarationResidentAlien()) {

            $borrower->setDeclarationUsCitizen("PermanentResidentAlien");

        }else{

            $borrower->setDeclarationUsCitizen("Unknown");

        }


        if($borrower->getDeclarationOwnershipWithinThreeYears()){

            switch ($borrower->getDeclarationOwnershipWithinThreeYearsPropertyTitle()) {
                case '0':
                    $borrower->setDeclarationOwnershipWithinThreeYearsPropertyTitle("Sole");
                    break;
                case '1':
                    $borrower->setDeclarationOwnershipWithinThreeYearsPropertyTitle("JointWithSpouse");
                    break;
                case '2':
                    $borrower->setDeclarationOwnershipWithinThreeYearsPropertyTitle("JointWithOtherThanSpouse");
                    break;
                default:


            }

            switch ($borrower->getDeclarationOwnershipWithinThreeYearsPropertyType()) {
                case '0':
                    $borrower->setDeclarationOwnershipWithinThreeYearsPropertyType("PrimaryResidence");
                    break;
                case '1':
                    $borrower->setDeclarationOwnershipWithinThreeYearsPropertyType("SecondaryResidence");
                    break;
                case '2':
                    $borrower->setDeclarationOwnershipWithinThreeYearsPropertyType("Investment");
                    break;
                default:


            }


        }


        if(!$borrower->getGovtMonitoringOptOut()){

            switch ($borrower->getEthnicity()) {
                case '0':
                    $borrower->setEthnicity('HispanicOrLatino');
                    break;
                case '1':
                    $borrower->setEthnicity('NotHispanicOrLatino');
                    break;
                case '2':
                    $borrower->setEthnicity('NotApplicable');
                    break;
                default:


            }

            switch ($borrower->getRace()) {
                case '0':
                    $borrower->setRace('AmericanIndianOrAlaskanNative');
                    break;
                case '1':
                    $borrower->setRace('AsianOrPacificIslander');
                    break;
                case '2':
                    $borrower->setRace('BlackNotOfHispanicOrigin');
                    break;
                case '3':
                    $borrower->setRace('AsianOrPacificIslander');
                    break;
                case '4':
                    $borrower->setRace('WhiteNotOfHispanicOrigin');
                    break;
                default:
                    $borrower->setRace('Other');
            }


            if($borrower->getIsMale()){
                $borrower->setIsMale("Male");
            }else{
                $borrower->setIsMale("Female");
            }

        }




        $borrowerAssetsA = $borrower->getAssetAccount();


        foreach($borrowerAssetsA as $a){

            $aType = $a->getType();

            switch ($aType) {
                case '0':
                    $a->setType('CheckingAccount');
                    break;
                case '1':
                    $a->setType('SavingsAccount');
                    break;
                case '2':
                    $a->setType('MoneyMarketFund');
                    break;
                case '3':
                    $a->setType('CertificateOfDepositTimeDeposit');
                    break;
                case '4':
                    $a->setType('MutualFund');
                    break;
                case '5':
                    $a->setType('RetirementFund');
                    break;
                default:
                    $a->setType('OtherLiquidAssets');

            }


        }




        switch ($maritalStatusBorrower) {
            case '0':
                $borrower->setMaritalStatus('Married');
                break;
            case '1':
                $borrower->setMaritalStatus('Unmarried');
                break;
            case '2':
                $borrower->setMaritalStatus('Separated');
                break;

            default:

        }



        if($borrower->getDependents()){

            $BorrowerDependentsAges = explode(",", $application->getBorrower()->getDependentsAges());
            $application->getBorrower()->setDependentsAges($BorrowerDependentsAges);

        }


        $borrowerEmployment = $borrower->getEmployment()->getValues();


        foreach($borrowerEmployment as $job){

            $borEmpPhone = null;

            if($job->getEndDate()->add(new \DateInterval('P1D')) >= $application->getCreated() && $application->getBorrower()->getEmployed()){

                $job->setCurrent("Y");

            }else{

                $job->setCurrent("N");

            }


            switch ($job->getSelfEmployed()) {
                case '0':
                    $job->setSelfEmployed("N");
                    break;
                case '1':
                    $job->setSelfEmployed("Y");
                    break;

                default:

            }


            $borEmpPhone = $job->getEmployerPhone();
            if(isset($borEmpPhone)){
                $borEmpPhone = preg_replace("/[^0-9]/", "", $borEmpPhone);
                $job->setEmployerPhone($borEmpPhone);
            }



        }
////////////////////////////////////////////////////////////////////////////
        //
        // Co Borrowers
        //
        ////////////////////////////////////////////////////////////////////

        foreach( $coBorrowers as $coBorrower) {

            $maritalStatusCoBorrower = $coBorrower->getMaritalStatus();

            $coBorrowerDependentsAges = NULL;


            if ($coBorrower->getDeclarationUsCitizen()) {

                $coBorrower->setDeclarationUsCitizen("USCitizen");

            } elseif ($coBorrower->getDeclarationResidentAlien()) {

                $coBorrower->setDeclarationUsCitizen("PermanentResidentAlien");

            } else {

                $coBorrower->setDeclarationUsCitizen("Unknown");

            }


            if ($coBorrower->getDeclarationOwnershipWithinThreeYears()) {

                switch ($coBorrower->getDeclarationOwnershipWithinThreeYearsPropertyTitle()) {
                    case '0':
                        $coBorrower->setDeclarationOwnershipWithinThreeYearsPropertyTitle("Sole");
                        break;
                    case '1':
                        $coBorrower->setDeclarationOwnershipWithinThreeYearsPropertyTitle("JointWithSpouse");
                        break;
                    case '2':
                        $coBorrower->setDeclarationOwnershipWithinThreeYearsPropertyTitle("JointWithOtherThanSpouse");
                        break;
                    default:


                }

                switch ($coBorrower->getDeclarationOwnershipWithinThreeYearsPropertyType()) {
                    case '0':
                        $coBorrower->setDeclarationOwnershipWithinThreeYearsPropertyType("PrimaryResidence");
                        break;
                    case '1':
                        $coBorrower->setDeclarationOwnershipWithinThreeYearsPropertyType("SecondaryResidence");
                        break;
                    case '2':
                        $coBorrower->setDeclarationOwnershipWithinThreeYearsPropertyType("Investment");
                        break;
                    default:


                }


            }


            if (!$coBorrower->getGovtMonitoringOptOut()) {

                switch ($coBorrower->getEthnicity()) {
                    case '0':
                        $coBorrower->setEthnicity('HispanicOrLatino');
                        break;
                    case '1':
                        $coBorrower->setEthnicity('NotHispanicOrLatino');
                        break;
                    case '2':
                        $coBorrower->setEthnicity('NotApplicable');
                        break;
                    default:


                }

                switch ($coBorrower->getRace()) {
                    case '0':
                        $coBorrower->setRace('AmericanIndianOrAlaskanNative');
                        break;
                    case '1':
                        $coBorrower->setRace('AsianOrPacificIslander');
                        break;
                    case '2':
                        $coBorrower->setRace('BlackNotOfHispanicOrigin');
                        break;
                    case '3':
                        $coBorrower->setRace('AsianOrPacificIslander');
                        break;
                    case '4':
                        $coBorrower->setRace('WhiteNotOfHispanicOrigin');
                        break;
                    default:
                        $coBorrower->setRace('Other');
                }


                if ($coBorrower->getIsMale()) {
                    $coBorrower->setIsMale("Male");
                } else {
                    $coBorrower->setIsMale("Female");
                }

            }


            switch ($maritalStatusCoBorrower) {
                case '0':
                    $coBorrower->setMaritalStatus('Married');
                    break;
                case '1':
                    $coBorrower->setMaritalStatus('Unmarried');
                    break;
                case '2':
                    $coBorrower->setMaritalStatus('Separated');
                    break;

                default:

            }

            if ($coBorrower->getDependents()) {

                $coBorrowerDependentsAges = explode(",", $coBorrower->getDependentsAges());
                $coBorrower->setDependentsAges($coBorrowerDependentsAges);

            }


            $coBorrowerEmployment = $coBorrower->getEmployment()->getValues();

            foreach ($coBorrowerEmployment as $job) {

                $coBorEmpPhone = null;

                if ($job->getEndDate()->add(new \DateInterval('P1D')) >= $application->getCreated() && $application->getBorrower()->getEmployed()) {

                    $job->setCurrent("Y");

                } else {

                    $job->setCurrent("N");

                }


                switch ($job->getSelfEmployed()) {
                    case '0':
                        $job->setSelfEmployed("N");
                        break;
                    case '1':
                        $job->setSelfEmployed("Y");
                        break;

                    default:

                }


                $coBorEmpPhone = $job->getEmployerPhone();
                if(isset($coBorEmpPhone)){
                    $borEmpPhone = preg_replace("/[^0-9]/", "", $coBorEmpPhone);
                    $job->setEmployerPhone($borEmpPhone);
                }

            }


            $coBborrowerAssetsA = $coBorrower->getAssetAccount();


            foreach ($coBborrowerAssetsA as $a) {

                $aType = $a->getType();

                switch ($aType) {
                    case '0':
                        $a->setType('CheckingAccount');
                        break;
                    case '1':
                        $a->setType('SavingsAccount');
                        break;
                    case '2':
                        $a->setType('MoneyMarketFund');
                        break;
                    case '3':
                        $a->setType('CertificateOfDepositTimeDeposit');
                        break;
                    case '4':
                        $a->setType('MutualFund');
                        break;
                    case '5':
                        $a->setType('RetirementFund');
                        break;
                    default:
                        $a->setType('OtherLiquidAssets');

                }


            }

        }

        return $application;

    }

    /**
     * @param $loanTerm
     * @return float
     */
    protected function getLoanTermInYears($loanTerm)
    {
        if(isset($loanTerm)) {
            $loanTerm = $loanTerm / 12;
        }

        return $loanTerm;
    }

    /**
     * @param $dateString
     * @return \DateTime|null
     */
    protected function convertToDateTime($dateString)
    {
        $date = null;
        if(!empty($dateString)) {
            //$dateString = strtotime($dateString);
            $date = new \DateTime($dateString);
        }
        return $date;
    }


    /**
     * @param $value
     * @return int
     */
    protected function toInt($value)
    {
        if(isset($value)) {
            $value = (int)$value;
        }

        return $value;
    }

    /**
     * @param $value
     * @return int
     */
    protected function toZipcode($value)
    {
        if(isset($value)) {
            $value = (int)$value;
            $value = str_pad($value, 5, '0', STR_PAD_LEFT);
        }

        return $value;
    }

    /**
     * @param $value
     * @return float
     */
    protected function toFloat($value)
    {
        if(isset($value)) {
            $value = (float)$value;
        }

        return $value;
    }

}