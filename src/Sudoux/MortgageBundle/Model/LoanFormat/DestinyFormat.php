<?php

namespace Sudoux\MortgageBundle\Model\LoanFormat;

use Sudoux\MortgageBundle\Entity\LoanApplication;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Sudoux\MortgageBundle\Model\LosSync;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Sudoux\MortgageBundle\Entity\Borrower;
use Sudoux\MortgageBundle\Entity\ExpenseHousing;
/**
 * Class DestinyFormat
 * @package Sudoux\MortgageBundle\Model\LoanFormat
 * @author Eric Haynes
 */
class DestinyFormat extends Mismo231Format implements iLoanFormat
{

    /**
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $application
     * @return string
     * @author Eric Haynes
     */
    public function convertFromLoanApp(LoanApplication $application){

      //  $this->container->get('');
        $copyApp = clone($application);
        $mismo = $this->formatDestiny($copyApp);
        $applicationXml = $this->container->get('templating')->render('SudouxMortgageBundle:LoanApplicationAdmin/formats:destiny.xml.twig', array('application' => $mismo), 'text/xml');

        return $applicationXml;


    }


    /**
     * @param $dataIn
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $application
     * @param int $source
     * @return \Sudoux\MortgageBundle\Entity\LoanApplication
     * @author Eric Haynes
     */
    public function convertToLoanApp($dataIn, LoanApplication $application = null, $source = 4){

        $xml = simplexml_load_string($dataIn);
        $json = json_encode($xml);
        $destinyData = json_decode($json,TRUE);


        if(!isset($application)) {

            $application = new LoanApplication();
            $application->setLastStepCompleted(6);
            $application->setHasRealtor(false); //add section to grab realtor info if in xml
            $application->setNumUnits(1);
            $application->setSource($source);
            $housingExpense = new ExpenseHousing();
            $application->setExpenseHousing($housingExpense);

        }
        $application->setStatus(6);

        // loan-info
        //
        ////////////////////////////////

        $loanAmount = $destinyData['loan-info']['@attributes']['base-loan-amount'];
        $application->setLoanAmount($this->toFloat($loanAmount));

        $loanTerm = $destinyData['loan-info']['@attributes']['loan-term'];
        $application->setLoanTerm($this->getLoanTermInYears($loanTerm));

        $loanType = $destinyData['loan-info']['@attributes']['loan-purpose'];
        $newLoanType = $this->getLoanTypeDestiny($loanType);
        $application->setLoanType($newLoanType);

        if($newLoanType == 1){

            $refiPurpose = $destinyData['loan-info']['@attributes']['purpose-of-refinance'];
            $application->setRefinancePurpose($this->getLoanRefiPurposeDestiny($refiPurpose));

            $refiFirstMortgageAmount = $destinyData['loan-info']['@attributes']['first-mortgage-amount'];
            $application->setRefinanceOriginalCost($refiFirstMortgageAmount);

            $refiFirstMortgageRate = $destinyData['loan-info']['@attributes']['interest-rate'];
            $application->setRefinanceCurrentRate($refiFirstMortgageRate);

            $loanProgram = $destinyData['loan-info']['@attributes']['loan-program'];
            $application->setRefinanceCurrentLoanType($loanProgram);

        }



        $residencyType =$destinyData['loan-info']['@attributes']['occupancy-type'];
        $application->setResidencyType($this->getResidencyTypeDestiny($residencyType));

        $salePrice = $destinyData['loan-info']['@attributes']['sale-price'];
        $application->setSalePrice($this->toFloat($salePrice));


        // property-info
        //
        /////////////////////////////////
        $propertyLocation = $application->getPropertyLocation();

        $propertyAddress = ucwords(strtolower($destinyData['property-info']['@attributes']['street-address']));
        $propertyLocation->setAddress1($propertyAddress);

        $propertyCity = ucwords(strtolower($destinyData['property-info']['@attributes']['city']));
        $propertyLocation->setCity($propertyCity);
        //echo $propertyCity; exit;

        $propertyState = $destinyData['property-info']['@attributes']['state'];
        $propertyStateEntity = $this->em->getRepository('SudouxCmsLocationBundle:State')->findStateByAbbreviation($propertyState);
        $propertyLocation->setState($propertyStateEntity);

        $propertyZipcode = $destinyData['property-info']['@attributes']['zip'];
        $propertyLocation->setZipcode($this->toZipcode($propertyZipcode));

        $propertyType = $destinyData['property-info']['@attributes']['property-type-code'];
        $application->setPropertyType($this->getPropertyTypeDestiny($propertyType));

        // borrower  info
        //
        ////////////////////////////////

        $marriageStatus = $this->guessMarriedTypeDestiny($destinyData['borrower-info']);


        foreach($destinyData['borrower-info'] as $destinyBorrower){


            if($destinyBorrower['@attributes']['co-borrower'] == 'false'){

                $borrower = $application->getBorrower();
                $this->convertDestinyToBorrower($application, $borrower , $destinyBorrower, false);
                $borrower->setMaritalStatus($marriageStatus);

            }else{

                $coBorrowers = $application->getCoBorrower();

                $coBorrowerSsn = $destinyBorrower['@attributes']['ssn'];
                $coBorrower = null;
                if(count($coBorrowers) > 0) {
                    // check if coborrower exists
                    foreach($coBorrowers as $cb) {
                        $existingSsn = preg_replace("/[^0-9]/", "", $cb->getSsn());
                        if($existingSsn == $coBorrowerSsn) {
                            $coBorrower = $cb;
                            break;
                        }
                    }
                }

                if(!isset($coBorrower)) {
                    $coBorrower = new Borrower();
                    $application->addCoBorrower($coBorrower);
                    $coBorrower->setSsn($coBorrowerSsn);

                }

                $this->convertDestinyToBorrower($application, $coBorrower , $destinyBorrower, true);
                $coBorrower->setMaritalStatus($marriageStatus);

            }

        }


        // loan status
        //
        ////////////////////////////////




        return $application;

    }



    /**
     * @param $dataIn
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $site
     * @return \Sudoux\MortgageBundle\Entity\LoanApplication
     * @author Eric Haynes
     */
    public function createLoanFromFormat($dataIn, Site $site = null){

        $xml = simplexml_load_string($dataIn);
        $json = json_encode($xml);
        $destinyData = json_decode($json,TRUE);

        $application = $this->convertToLoanApp($dataIn ,null, 4);
        $application->setApi();

        If(isset($site)){

            $application->setSite($site);
            $loanOfficer = $site->getSettings()->getLoanOfficer();
            if(isset($loanOfficer)) {
                $application->setLoanOfficer($loanOfficer);
            }

        }else{

            $destinyLo = $this->getDestinyLo($destinyData);

            if(isset($destinyLo)){

                $destinySite = $this->getDestinySite($destinyData);
                $application->setLoanOfficer($destinyLo);

            }else{

                $siteRequest = $this->container->get('sudoux.cms.site');
                $destinySite = $siteRequest->getSite()->getParentSite();

                if(!isset($destinySite)){
                    $destinySite = $siteRequest->getSite();
                }

            }


            $application->setSite($destinySite);
            $application->setLosLoanNumber($destinyData['loan-info']['@attributes']['destiny-loan-id']);

        }


        $this->em->persist($application);
        $this->em->flush();

        return $application;

    }


    /**
     * @param $dataIn
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $site
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $application
     * @return \Sudoux\MortgageBundle\Entity\LoanApplication
     * @author Eric Haynes
     */
    public function upsertLoanFromFormat($dataIn, Site $site = null, LoanApplication $application = null){

        $xml = simplexml_load_string($dataIn);
        $json = json_encode($xml);
        $destinyData = json_decode($json,TRUE);

        $webmaxRefId = $destinyData['loan-info']['@attributes']['webmax-reference-id'];
        $loanToBeUpdated = $this->em->getRepository('SudouxMortgageBundle:LoanApplication')->find($webmaxRefId);


        if(isset($loanToBeUpdated)){

            $application = $this->convertToLoanApp($dataIn, $loanToBeUpdated);
            $application->setLosLoanNumber($destinyData['loan-info']['@attributes']['destiny-loan-id']);


        }else{

            $application = $this->createLoanFromFormat($dataIn);

        }

        $this->getMilestoneFromLoanStatus($application, $destinyData['loan-status']['@attributes']);
        $application->setStatus(6);
        $this->em->persist($application);
        $this->em->flush($application);

        return $application;

    }


    /**
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $application
     * @param \Sudoux\MortgageBundle\Entity\Borrower $borrower
     * @param $destinyBorrower
     * @param bool $isCoBorrower
     * @author Eric Haynes
     */
    protected function convertDestinyToBorrower(LoanApplication $application, Borrower $borrower, $destinyBorrower, $isCoBorrower = false)
    {

        $borrowerSsn = $destinyBorrower['@attributes']['ssn'];
        $borrower->setSsn($borrowerSsn);

        $borrowerFirstName = ucwords(strtolower($destinyBorrower['@attributes']['first-name']));
        $borrower->setFirstName($borrowerFirstName);

        $borrowerLastName = ucwords(strtolower($destinyBorrower['@attributes']['last-name']));
        $borrower->setLastName($borrowerLastName);

        $borrowerMiddleInitial = ucwords(strtolower($destinyBorrower['@attributes']['middle-name']));
        if(strlen($borrowerMiddleInitial) > 0){
            $borrower->setMiddleInitial($borrowerMiddleInitial[0]);
        }else{
            $borrower->setMiddleInitial('');
        }


        $borrowerSuffix = ucwords(strtolower($destinyBorrower['@attributes']['generation']));
        $borrower->setSuffix($borrowerSuffix);

        $borrowerEmail = $destinyBorrower['@attributes']['email'];
        $borrower->setEmail($borrowerEmail);

        if(isset($destinyBorrower['@attributes']['home-phone'])){
            $borrower->setPhoneHome($destinyBorrower['@attributes']['home-phone']);
        }
        if(isset($destinyBorrower['@attributes']['work-phone'])){
            $borrower->setPhoneHome($destinyBorrower['@attributes']['work-phone']);
        }


        $borrowerBirthDate = $destinyBorrower['@attributes']['birthdate'];
        $borrower->setBirthDate($this->convertToDateTime($borrowerBirthDate));

        // BorrowerLocation Location
        $borrowerLocation = $borrower->getLocation();

        $borrowerLocationLocation = $borrowerLocation->getLocation();

        $borrowerLocationLocationAddress = ucwords(strtolower($destinyBorrower['@attributes']['street-address']));
        $borrowerLocationLocation->setAddress1($borrowerLocationLocationAddress);

        $borrowerLocationLocationCity = ucwords(strtolower($destinyBorrower['@attributes']['city']));
        $borrowerLocationLocation->setCity($borrowerLocationLocationCity);

        $borrowerLocationLocationState = $destinyBorrower['@attributes']['state'];
        $borrowerLocationLocationStateEntity = $this->em->getRepository('SudouxCmsLocationBundle:State')->findStateByAbbreviation($borrowerLocationLocationState);
        $borrowerLocationLocation->setState($borrowerLocationLocationStateEntity);

        $borrowerLocationLocationZipcode = $destinyBorrower['@attributes']['zip'];
        $borrowerLocationLocation->setZipcode($this->toZipcode($borrowerLocationLocationZipcode));


        if($isCoBorrower){

            $borrower->setPrimaryBorrower(false);


        }else{

            $borrower->setPrimaryBorrower(true);

        }


    }

    /**
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $application
     * @param $loanStatus
     * @author Eric Haynes
     */
    protected function getMilestoneFromLoanStatus(LoanApplication $application, $loanStatus)
    {
        $los = $this->container->get('sudoux_mortgage.los_util');


        $appReceived = $loanStatus['loan-application-received'];
        $appReceivedDate= strtotime($appReceived);

        $appUW = $loanStatus['loan-application-submitted-UW'];
        $appUWDate= strtotime($appUW);

        $appConApp = $loanStatus['loan-conditionally-approved'];
        $appConAppDate= strtotime($appConApp);

        $appApp = $loanStatus['final-loan-approval'];
        $appAppDate= strtotime($appApp);

        if($appAppDate && $appAppDate < time()){
            $los->setLoanMilestone($application,'45','Destiny_1');
        }
        elseif($appConAppDate && $appConAppDate< time()){
            $los->setLoanMilestone($application,'34','Destiny_1');
        }
        elseif($appUWDate && $appUWDate < time()){
            $los->setLoanMilestone($application,'12','Destiny_1');
        }
        elseif($appReceivedDate && $appReceivedDate < time()){
            $los->setLoanMilestone($application,'1','Destiny_1');
        }



    }



    /**
     * @param $borrowerData
     * @return int
     * @author Eric Haynes
     */
    protected function guessMarriedTypeDestiny($borrowerData)
    {

       if(count($borrowerData) === 1){
           return 1;
       }elseif(count($borrowerData) === 2){

           if (false === strripos($borrowerData[0]['@attributes']['last-name'], $borrowerData[1]['@attributes']['last-name']) && false === strripos($borrowerData[1]['@attributes']['last-name'], $borrowerData[0]['@attributes']['last-name'])){
               return 1;
           }else{
               return 0;
           }

       }else{
           return 2;
       }

    }

    /**
     * @param $loanType
     * @return int
     */
    protected function getLoanRefiPurposeDestiny($loanType)
    {
        if(isset($loanType)) {
            switch($loanType) {
                case 'NoCashOutOther':
                    $loanType = 3;
                    break;
                case 'CashOutDebtConsolidation':
                    $loanType = 0;
                    break;
                case 'CashOutOther':
                    $loanType = 2;
                    break;
                case 'CashOutLimited':
                    $loanType = 4;
                    break;
                case 'NoCashOutStreamlineRefinance':
                    $loanType = 3;
                    break;
                case 'CashOutHomeImprovement':
                    $loanType = 1;
                    break;
                default:
                    $loanType = 0 ;

            }
        }

        return $loanType;
    }

    /**
     * @param $loanType
     * @return int
     */
    protected function getLoanTypeDestiny($loanType)
    {
        if(isset($loanType)) {
            switch($loanType) {
                case 'Purchase':
                    $loanType = 0;
                    break;
                case 'Refinance':
                    $loanType = 1;
                    break;
                default:
                    $loanType = 1;

            }
        }

        return $loanType;
    }

    /**
     * @param $propertyType
     * @return int
     */
    protected function getPropertyTypeDestiny($propertyType)
    {
        if(isset($propertyType)) {
            switch($propertyType) {
                case 'SingleFamilyResidence':
                    $propertyType = 0; // single family
                    break;
                case 'TwoToFourUnitProperty':
                    $propertyType = 1; // attached
                    break;
                case 'Condominium':
                    $propertyType = 2; // condo
                    break;
                case 'Multifamily':
                    $propertyType = 4; // multifamily
                    break;
                default:
                    $propertyType = 9; // other

            }

        }

        return $propertyType;
    }

    /**
     * @param $residencyType
     * @return int|null
     */
    public function getResidencyTypeDestiny($residencyType)
    {
        if(isset($residencyType)) {
            switch($residencyType) {
                case 'PrimaryResidence';
                    $residencyType = 0;
                    break;
                case 'SecondHome';
                    $residencyType = 1;
                    break;
                case 'Investor';
                    $residencyType = 2;
                    break;
                default:
                    $residencyType = 0;
            }
        }

        return $residencyType;
    }

    /**
     * @param $DestinyApp
     * @return mixed
     * @author Eric Haynes
     */
    protected function getDestinyLo($DestinyApp){

        $foundLo = NULL;

        if(isset($DestinyApp['loan-officer-ref']['@attributes']['destiny-loan-officer-id'])){

            $destinyLo = $DestinyApp['loan-officer-ref']['@attributes']['destiny-loan-officer-id'];
            $foundLo = $this->em->getRepository('SudouxMortgageBundle:LoanOfficer')->findOneByLosId($destinyLo);

        }

        return $foundLo;

    }

    /**
     * @param $DestinyApp
     * @return mixed
     * @author Eric Haynes
     */
    protected function getDestinySite($DestinyApp){

        $site = NULL;

        if(NULL === $foundLo = $this->getDestinyLo($DestinyApp)){

            $site = $this->container->get('sudoux.cms.site')->getSite()->getParentSite();

            if(!isset($site)){
                $site = $this->container->get('sudoux.cms.site')->getSite();
            }

        }else{
            $site = $foundLo->getSite();
        }

        return $site;

    }


    protected function formatDestiny(LoanApplication $app){


                $formattedMismo = $this->formatMismo231($app);

                return $formattedMismo;




    }


}