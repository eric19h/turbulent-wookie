<?php

namespace Sudoux\MortgageBundle\DependencyInjection;

use Sudoux\MortgageBundle\Entity\AssetRealEstate;

use Sudoux\MortgageBundle\Entity\AssetAccount;

use Sudoux\MortgageBundle\Entity\IncomeMonthly;

use Sudoux\MortgageBundle\Entity\ExpenseHousing;

use Sudoux\MortgageBundle\Entity\Employment;

use Sudoux\MortgageBundle\Entity\BorrowerLocation;

use Sudoux\MortgageBundle\Entity\Borrower;

use Sudoux\Cms\SiteBundle\Entity\Site;

use Doctrine\ORM\EntityManager;

use Sudoux\MortgageBundle\Entity\LoanApplication;
use Symfony\Component\Yaml\Yaml;
use Sudoux\Cms\SiteBundle\DependencyInjection\StringUtil;

/**
 * Class FannieMaeUtil
 * @package Sudoux\MortgageBundle\DependencyInjection
 * @author Dan Alvare
 */
class FannieMaeUtil 
{
    /**
     * @var EntityManager
     */
	protected $em;

    /**
     * @param EntityManager $em
     */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;	
	}

    /**
     * @param Site $site
     * @param $fannieMaeData
     */
	public function createLoanApplicationFromFannieMae2(Site $site, $fannieMaeData)
	{
		$application = $this->convertFannieMaeToLoanApplication($fannieMaeData);
		$application->setSite($site);
		$loanOfficer = $site->getSettings()->getLoanOfficer();
		if(isset($loanOfficer)) {
			$application->setLoanOfficer($loanOfficer);
		}
		
		$this->em->persist($application);
		$this->em->flush();
	}

    /**
     *
     */
	public function convertLoanApplicationToFannieMae()
	{
		
	}

    /**
     * @param Site $site
     * @param $fannieMaeData
     * @param LoanApplication $application
     */
	public function upsertLoanFromFanniemaeFormat(Site $site, $fannieMaeData, LoanApplication $application = null)
	{
		$application = $this->convertFannieMaeToLoanApplication($fannieMaeData, $application);
		
		$application->setSite($site);
		$loanOfficer = $site->getSettings()->getLoanOfficer();
		
		if(isset($loanOfficer)) {
			$application->setLoanOfficer($loanOfficer);
		}
		
		$this->em->persist($application);
		$this->em->flush($application);
	}

    /**
     * @param $fannieMaeData
     * @param LoanApplication $application
     * @param int $source
     * @return LoanApplication
     */
	public function convertFannieMaeToLoanApplication($fannieMaeData, LoanApplication $application = null, $source = 0)
	{
		if(isset($application)) {
			// remove data for update
			$application->removeAllAssetAccount();
			$application->removeAllAssetRealEstate();
			// reset housing expense
			
		} else {
			$application = new LoanApplication();
			$application->setLastStepCompleted(6);
			$application->setHasRealtor(false);
			$application->setSource($source);
			$housingExpense = new ExpenseHousing();
			$application->setExpenseHousing($housingExpense);
		}
		
		$loanAmount = StringUtil::find($fannieMaeData, "01A", 131, 15);
		$application->setLoanAmount($this->toFloat($loanAmount));
		
		$loanTerm = StringUtil::find($fannieMaeData, "01A", 153, 3);
		$application->setLoanTerm($this->getLoanTerm($loanTerm));
		
		$loanType = StringUtil::find($fannieMaeData, "02B", 6, 2);
		$application->setLoanType($this->getLoanType($loanType));
		// refi data
		$refinanceYearAquired = StringUtil::find($fannieMaeData, "02D", 4, 4);
		$application->setRefinanceYearAquired($this->toInt($refinanceYearAquired));
		
		$refinanceOriginalCost = StringUtil::find($fannieMaeData, "02D", 8, 15);
		$application->setRefinanceOriginalCost($this->toFloat($refinanceOriginalCost));
		
		$refinanceExistingLiens = StringUtil::find($fannieMaeData, "02D", 23, 15);
		$application->setRefinanceExistingLiens($this->toFloat($refinanceExistingLiens));
		
		$refinancePurpose = StringUtil::find($fannieMaeData, "02D", 68, 2);
		$application->setRefinancePurpose($this->getRefinancePurpose($refinancePurpose));
		
		/*$refinancePurpose = StringUtil::find($fannieMaeData, "02D", 68, 2);	
		$application->setRefinanceCurrentLender($refinanceCurrentLender);
		$application->setRefinanceCurrentLoanType($refinanceCurrentLoanType);
		$application->setRefinanceCurrentRate($refinanceCurrentRate);*/
			
		$salePrice = StringUtil::find($fannieMaeData, "07A", 4, 15);
		$application->setSalePrice($this->toFloat($salePrice));
		
		$numUnits = StringUtil::find($fannieMaeData, "02A", 100, 3);
		$application->setNumUnits($this->toInt($numUnits));
		
		$propertyYearBuilt = StringUtil::find($fannieMaeData, "02A", 185, 4);
		$application->setPropertyYearBuilt($this->toInt($propertyYearBuilt));
		
		$propertyType = StringUtil::find($fannieMaeData, "LNC", 6, 2);
		$application->setPropertyType($this->getPropertyType($propertyType));

		$residencyType = StringUtil::find($fannieMaeData, "02B", 88, 1);
		$application->setResidencyType($this->getResidencyType($residencyType));

		$titleManner = StringUtil::find($fannieMaeData, "02B", 89, 60);
		$application->setTitleManner($titleManner);
		
		$titleCompany = StringUtil::find($fannieMaeData, "02C", 4, 60);
		$application->setTitleCompany1($titleCompany);

		// property_location \Sudoux\Cms\LocationBundle\Entity\Location
		$propertyLocation = $application->getPropertyLocation();
		
		$propertyAddress = StringUtil::find($fannieMaeData, "02A", 4, 50);
		$propertyLocation->setAddress1($propertyAddress);
		
		$propertyCity = StringUtil::find($fannieMaeData, "02A", 54, 35);
		$propertyLocation->setCity($propertyCity);
		//echo $propertyCity; exit;
		
		$propertyState = StringUtil::find($fannieMaeData, "02A", 89, 2);
		$propertyStateEntity = $this->em->getRepository('SudouxCmsLocationBundle:State')->findStateByAbbreviation($propertyState);
		$propertyLocation->setState($propertyStateEntity);
		
		$propertyZipcode = StringUtil::find($fannieMaeData, "02A", 91, 5);
		$propertyLocation->setZipcode($this->toZipcode($propertyZipcode));
		
		$propertyUnit = StringUtil::find($fannieMaeData, "02A", 55, 11);
		$propertyLocation->setUnit($propertyUnit);

        // reset the housing expense
        $housingExpense = $application->getExpenseHousing();
        if(isset($housingExpense)) {
            $housingExpense->reset();
        }

		$borrower = $application->getBorrower();
		$this->convertFannieMaeToBorrower($application, $borrower, $fannieMaeData, array('03A_suffix' => 'BW'));
		
		$fnmCoBorrowers = StringUtil::findLinesStartingWith($fannieMaeData, "03AQZ");
		$coBorrowers = $application->getCoBorrower();

		foreach($fnmCoBorrowers as $fnmCoBorrower) {
			$coBorrowerSsn = StringUtil::find($fnmCoBorrower, "03AQZ", 6, 9);
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
			
			$this->convertFannieMaeToBorrower($application, $coBorrower, $fannieMaeData, array('03A_suffix' => 'QZ'));
		}
		//exit;
		return $application;
	}

    /**
     * @param LoanApplication $application
     * @param Borrower $borrower
     * @param $fannieMaeData
     * @param $options
     */
	protected function convertFannieMaeToBorrower(LoanApplication $application, Borrower $borrower, $fannieMaeData, $options)
	{
		// clear for update
		$incomeMonthly = $borrower->getIncomeMonthly();
		if(count($borrower->getIncomeMonthly()) > 0) {
			$borrower->removeAllIncomeMonthly();
		}
		
		$borrowerSsn = StringUtil::find($fannieMaeData, "03A" . $options['03A_suffix'], 6, 9);
		$borrower->setSsn($borrowerSsn);
		
		$borrowerFirstName = StringUtil::find($fannieMaeData, "03A" . $options['03A_suffix'], 15, 35);
		$borrower->setFirstName($borrowerFirstName);
		
		$borrowerLastName = StringUtil::find($fannieMaeData, "03A" . $options['03A_suffix'], 85, 35);
		$borrower->setLastName($borrowerLastName);
		
		$borrowerMiddleInitial = StringUtil::find($fannieMaeData, "03A" . $options['03A_suffix'], 50, 35);
		$borrower->setMiddleInitial($borrowerMiddleInitial);
		
		$borrowerSuffix = StringUtil::find($fannieMaeData, "03A" . $options['03A_suffix'], 120, 4);
		$borrower->setSuffix($borrowerSuffix);
		
		$borrowerEmail = StringUtil::find($fannieMaeData, "03A" . $options['03A_suffix'], 160, 80);
		$borrower->setEmail($borrowerEmail);
		
		$borrowerPhoneHome = StringUtil::find($fannieMaeData, "03A" . $options['03A_suffix'], 124, 10);
		$borrower->setPhoneHome($borrowerPhoneHome);
		
		$borrowerMaritialStatus = StringUtil::find($fannieMaeData, "03A" . $options['03A_suffix'], 139, 1);
		$borrower->setMaritalStatus($this->getBorrowerMaritalStatus($borrowerMaritialStatus));
		
		$borrowerYearsOfSchool = StringUtil::find($fannieMaeData, "03A" . $options['03A_suffix'], 137, 2);
		$borrower->setYearsOfSchool($this->toInt($borrowerYearsOfSchool));
		
		$borrowerBirthDate = StringUtil::find($fannieMaeData, "03A" . $options['03A_suffix'], 152, 8);
		$borrower->setBirthDate($this->convertToDateTime($borrowerBirthDate));
		
		$borrowerDependentsNumber = StringUtil::find($fannieMaeData, "03A" . $options['03A_suffix'], 140, 2);
		$borrower->setDependentsNumber($this->toInt($borrowerDependentsNumber));
		$borrowerDependentsAges = StringUtil::findAll($fannieMaeData, "03B" . $borrowerSsn, 13, 3);
		if(count($borrowerDependentsAges) == 1) {
			$borrower->setDependentsAges($borrowerDependentsAges[0]);
		} else if(count($borrowerDependentsAges) > 1) {			
			$borrower->setDependentsAges(implode(',', $borrowerDependentsAges));
		}
		
		// BorrowerLocation
		$borrowerLocation = $borrower->getLocation();
		$borrowerLocationYears = StringUtil::find($fannieMaeData, "03C" . $borrowerSsn . "ZG", 112, 2);
		$borrowerLocation->setYearsAtLocation($this->toInt($borrowerLocationYears));
		
		$borrowerLocationMonths = StringUtil::find($fannieMaeData, "03C" . $borrowerSsn . "ZG", 114, 2);
		$borrowerLocation->setMonthsAtLocation($this->toInt($borrowerLocationMonths));
		
		$borrowerLocationOwnResidence = StringUtil::find($fannieMaeData, "03C" . $borrowerSsn . "ZG", 111, 1);
		$borrowerLocation->setOwnResidence($this->getBorrowerLocationOwnResidence($borrowerLocationOwnResidence));
		
		// BorrowerLocation Location
		$borrowerLocationLocation = $borrowerLocation->getLocation();
		
		$borrowerLocationLocationAddress = StringUtil::find($fannieMaeData, "03C" . $borrowerSsn . "ZG", 15, 50);
		$borrowerLocationLocation->setAddress1($borrowerLocationLocationAddress);
		
		$borrowerLocationLocationCity = StringUtil::find($fannieMaeData, "03C" . $borrowerSsn . "ZG", 65, 35);
		$borrowerLocationLocation->setCity($borrowerLocationLocationCity);
		
		$borrowerLocationLocationState = StringUtil::find($fannieMaeData, "03C" . $borrowerSsn . "ZG", 100, 2);
		$borrowerLocationLocationStateEntity = $this->em->getRepository('SudouxCmsLocationBundle:State')->findStateByAbbreviation($borrowerLocationLocationState);
		$borrowerLocationLocation->setState($borrowerLocationLocationStateEntity);
		
		$borrowerLocationLocationZipcode = StringUtil::find($fannieMaeData, "03C" . $borrowerSsn . "ZG", 102, 5);
		$borrowerLocationLocation->setZipcode($this->toZipcode($borrowerLocationLocationZipcode));
		
		// Borrower Previous Locations
		$fnmBorrowerPreviousLocations = StringUtil::findLinesStartingWith($fannieMaeData, "03C" . $borrowerSsn . "F4");

		$borrower->removeAllPreviousLocations(); // clear prev locations for update
		
		if(count($fnmBorrowerPreviousLocations) > 0) {
			
			foreach($fnmBorrowerPreviousLocations as $fnmLocation) {
					
				$borrowerPreviousLocation = new BorrowerLocation();
				$borrower->addPreviousLocation($borrowerPreviousLocation);
					
				$borrowerPreviousLocationYears = StringUtil::find($fannieMaeData, "03C" . $borrowerSsn . "F4", 112, 2);
				$borrowerPreviousLocation->setYearsAtLocation($this->toInt($borrowerPreviousLocationYears));
					
				$borrowerPreviousLocationMonths = StringUtil::find($fannieMaeData, "03C" . $borrowerSsn . "F4", 114, 2);
				$borrowerPreviousLocation->setMonthsAtLocation($this->toInt($borrowerPreviousLocationMonths));
					
				$borrowerPreviousLocationOwnResidence = StringUtil::find($fannieMaeData, "03C" . $borrowerSsn . "F4", 111, 1);
				$borrowerPreviousLocation->setOwnResidence($this->getBorrowerLocationOwnResidence($borrowerPreviousLocationOwnResidence));
					
				$borrowerPreviousLocationLocation = $borrowerPreviousLocation->getLocation();
					
				$borrowerPreviousLocationLocationAddress = StringUtil::find($fannieMaeData, "03C" . $borrowerSsn . "F4", 15, 50);
				$borrowerPreviousLocationLocation->setAddress1($borrowerPreviousLocationLocationAddress);
					
				$borrowerPreviousLocationLocationCity = StringUtil::find($fannieMaeData, "03C" . $borrowerSsn . "F4", 65, 35);
				$borrowerPreviousLocationLocation->setCity($borrowerLocationLocationCity);
					
				$borrowerPreviousLocationLocationState = StringUtil::find($fannieMaeData, "03C" . $borrowerSsn . "F4", 100, 2);
				$borrowerPreviousLocationLocationStateEntity = $this->em->getRepository('SudouxCmsLocationBundle:State')->findStateByAbbreviation($borrowerPreviousLocationLocationState);
				$borrowerPreviousLocationLocation->setState($borrowerPreviousLocationLocationStateEntity);
					
				$borrowerPreviousLocationLocationZipcode = StringUtil::find($fannieMaeData, "03C" . $borrowerSsn . "F4", 102, 5);
				$borrowerPreviousLocationLocation->setZipcode($this->toZipcode($borrowerPreviousLocationLocationZipcode));
			}
		}
		
		// borrower employment
		$borrowerEmployment = $borrower->getEmployment();
		$fnmBorrowerEmployment = StringUtil::findLinesStartingWith($fannieMaeData, "04A" . $borrowerSsn);
		$borrower->removeAllEmployment(); // remove employment for update
		
		if(count($fnmBorrowerEmployment)) {
			
			foreach($fnmBorrowerEmployment as $fnmEmployment) {
			
				$employment = new Employment();
				$borrower->addEmployment($employment);
					
				$employerName = StringUtil::find($fnmEmployment, "04A" . $borrowerSsn, 13, 35);
				$employment->setEmployerName($employerName);
					
				$selfEmployed = StringUtil::find($fnmEmployment, "04A" . $borrowerSsn, 129, 1);
				$employment->setSelfEmployed($this->convertToBoolean($selfEmployed));
					
				$yearsInProfession = StringUtil::find($fnmEmployment, "04A" . $borrowerSsn, 134, 2);
				$employment->setYearsEmployed($this->toInt($yearsInProfession));
					
				$yearsOnJob = StringUtil::find($fnmEmployment, "04A" . $borrowerSsn, 130, 2);
				$employment->setYearsOnJob($this->toInt($yearsOnJob));
			
				$position = StringUtil::find($fnmEmployment, "04A" . $borrowerSsn, 136, 25);
				$employment->setTitle($position);
					
				$employerPhone = StringUtil::find($fnmEmployment, "04A" . $borrowerSsn, 161, 10);
				$employment->setEmployerPhone($employerPhone);
					
				$location = $employment->getLocation();
					
				$locationAddress = StringUtil::find($fnmEmployment, "04A" . $borrowerSsn, 48, 35);
				$location->setAddress1($locationAddress);
					
				$locationCity = StringUtil::find($fnmEmployment, "04A" . $borrowerSsn, 83, 35);
				$location->setCity($locationCity);
					
				$locationState = StringUtil::find($fnmEmployment, "04A" . $borrowerSsn, 118, 2);
				$locationStateEntity = $this->em->getRepository('SudouxCmsLocationBundle:State')->findStateByAbbreviation($locationState);
				$location->setState($locationStateEntity);
					
				$locationZipcode = StringUtil::find($fnmEmployment, "04A" . $borrowerSsn, 120, 5);
				$location->setZipcode($this->toZipcode($locationZipcode));
			}
		}

		// previous employment
		$fnmBorrowerEmployment = StringUtil::findLinesStartingWith($fannieMaeData, "04B" . $borrowerSsn);

		if(count($fnmBorrowerEmployment)) {
			
			foreach($fnmBorrowerEmployment as $fnmEmployment) {
			
				$employment = new Employment();
				$borrower->addEmployment($employment);
					
				$employerName = StringUtil::find($fnmEmployment, "04B" . $borrowerSsn, 13, 35);
				$employment->setEmployerName($employerName);
					
				$selfEmployed = StringUtil::find($fnmEmployment, "04B" . $borrowerSsn, 129, 1);
				$employment->setSelfEmployed($this->convertToBoolean($selfEmployed));
					
				$startDateString = StringUtil::find($fnmEmployment, "04B" . $borrowerSsn, 131, 8);
                $startDate = $this->convertToDateTime($startDateString);
                if(isset($startDate)) {
                    $employment->setStartDate($startDate);
                }

					
				$endDateString = StringUtil::find($fnmEmployment, "04B" . $borrowerSsn, 139, 8);
                $endDate = $this->convertToDateTime($endDateString);
                if(isset($endDate)) {
                    $employment->setEndDate($endDate);
                }

			
				$position = StringUtil::find($fnmEmployment, "04B" . $borrowerSsn, 162, 25);
				$employment->setTitle($position);
					
				$employerPhone = StringUtil::find($fnmEmployment, "04B" . $borrowerSsn, 187, 10);
				$employment->setEmployerPhone($employerPhone);
					
				$location = $employment->getLocation();
					
				$locationAddress = StringUtil::find($fnmEmployment, "04B" . $borrowerSsn, 48, 35);
				$location->setAddress1($locationAddress);
					
				$locationCity = StringUtil::find($fnmEmployment, "04B" . $borrowerSsn, 83, 35);
				$location->setCity($locationCity);
					
				$locationState = StringUtil::find($fnmEmployment, "04B" . $borrowerSsn, 118, 2);
				$locationStateEntity = $this->em->getRepository('SudouxCmsLocationBundle:State')->findStateByAbbreviation($locationState);
				$location->setState($locationStateEntity);
					
				$locationZipcode = StringUtil::find($fnmEmployment, "04B" . $borrowerSsn, 120, 5);
				$location->setZipcode($this->toZipcode($locationZipcode));
			}
		}
		
		// account assets
		$fnmBorrowerAccountAsset = StringUtil::findLinesStartingWith($fannieMaeData, "06C" . $borrowerSsn);
        $borrower->removeAllAssetAccount();

		if(count($fnmBorrowerAccountAsset)) {
			foreach($fnmBorrowerAccountAsset as $fnmAsset) {
					
				$accountAsset = new AssetAccount();
				$accountAsset->setBorrower($borrower);
				$application->addAssetAccount($accountAsset);
					
				$type = StringUtil::find($fnmAsset, "06C" . $borrowerSsn, 13, 3);
				$accountAsset->setType($this->getAccountType($type));
					
				$accountInstitutionName = StringUtil::find($fnmAsset, "06C" . $borrowerSsn, 16, 35);
				$accountAsset->setInstitutionName($accountInstitutionName);
					
				$accountNumber = StringUtil::find($fnmAsset, "06C" . $borrowerSsn, 132, 30);
				$accountAsset->setAccountNumber($accountNumber);
					
				$balance = StringUtil::find($fnmAsset, "06C" . $borrowerSsn, 162, 15);
				$accountAsset->setBalance($this->toFloat($balance));
					
			}
		}
		
		// real estate assets
		$fnmBorrowerRealEstateAsset = StringUtil::findLinesStartingWith($fannieMaeData, "06G" . $borrowerSsn);
		$borrower->removeAllAssetRealestate();

		if(count($fnmBorrowerRealEstateAsset)) {
			
			foreach($fnmBorrowerRealEstateAsset as $fnmAsset) {
					
				$asset = new AssetRealEstate();
				$asset->setBorrower($borrower);
				$application->addAssetRealEstate($asset);
					
				$assetLocation = $asset->getLocation();
			
				$streetAddress = StringUtil::find($fnmAsset, "06G" . $borrowerSsn, 13, 35);
				$assetLocation->setAddress1($streetAddress);
			
				$city = StringUtil::find($fnmAsset, "06G" . $borrowerSsn, 48, 35);
				$assetLocation->setCity($city);
			
				$state = StringUtil::find($fnmAsset, "06G" . $borrowerSsn, 83, 2);
				$stateEntity = $this->em->getRepository('SudouxCmsLocationBundle:State')->findStateByAbbreviation($state);
				$assetLocation->setState($stateEntity);
			
				$zipcode = StringUtil::find($fnmAsset, "06G" . $borrowerSsn, 85, 5);
				$assetLocation->setZipcode($this->toZipcode($zipcode));
			
				$marketValue = StringUtil::find($fnmAsset, "06G" . $borrowerSsn, 97, 15);
				$asset->setMarketValue($this->toFloat($marketValue));
			
				$mortgageAmount = StringUtil::find($fnmAsset, "06G" . $borrowerSsn, 112, 15);
				$asset->setMortgageAmount($this->toFloat($mortgageAmount));
			
				$rentGrossIncome = StringUtil::find($fnmAsset, "06G" . $borrowerSsn, 127, 15);
				$asset->setRentGrossIncome($this->toFloat($rentGrossIncome));
			
				$mortgagePayment = StringUtil::find($fnmAsset, "06G" . $borrowerSsn, 142, 15);
				$asset->setMortgagePayment($this->toFloat($mortgagePayment));
			
				$insuranceTax = StringUtil::find($fnmAsset, "06G" . $borrowerSsn, 157, 15);
				$asset->setInsTaxExp($this->toFloat($insuranceTax));
			
				$rentNetIncome = StringUtil::find($fnmAsset, "06G" . $borrowerSsn, 172, 15);
				$asset->setRentNetIncome($this->toFloat($rentNetIncome));
			}
		}
		
		// combined housing expenses
		$housingExpense = $application->getExpenseHousing();
		if(!isset($housingExpense)) {
			$housingExpense = new ExpenseHousing();
			$application->setExpenseHousing($housingExpense);
		}
		
		$housingExpenseRent = StringUtil::find($fannieMaeData, "05H" . $borrowerSsn . "125", 16, 15);
		$housingExpense->setRent($this->getHousingExpenseTotal($housingExpense->getRent(), $housingExpenseRent));
		
		$housingExpenseMortgage = StringUtil::find($fannieMaeData, "05H" . $borrowerSsn . "126", 16, 15);
		$housingExpense->setMortgage($this->getHousingExpenseTotal($housingExpense->getMortgage(), $housingExpenseMortgage));
		
		$housingExpenseHazardInsurance = StringUtil::find($fannieMaeData, "05H" . $borrowerSsn . "101", 16, 15);
		$housingExpense->setInsuranceHazard($this->getHousingExpenseTotal($housingExpense->getInsuranceHazard(),$housingExpenseHazardInsurance));
		
		$housingExpenseOtherFinancial = StringUtil::find($fannieMaeData, "05H" . $borrowerSsn . "122", 16, 15);
		$housingExpense->setOtherFinancial($this->getHousingExpenseTotal($housingExpense->getOtherFinancial(), $housingExpenseOtherFinancial));
		
		$housingExpenseMortgageInsurance = StringUtil::find($fannieMaeData, "05H" . $borrowerSsn . "102", 16, 15);
		$housingExpense->setInsuranceMortgage($this->getHousingExpenseTotal($housingExpense->getInsuranceMortgage(), $housingExpenseMortgageInsurance));
		
		$housingExpenseRealEstateTax = StringUtil::find($fannieMaeData, "05H" . $borrowerSsn . "114", 16, 15);
		$housingExpense->setTaxRealEstate($this->getHousingExpenseTotal($housingExpense->getTaxRealEstate(), $housingExpenseRealEstateTax));
		
		$housingExpenseHoaDues = StringUtil::find($fannieMaeData, "05H" . $borrowerSsn . "106", 16, 15);
		$housingExpense->setHoaDues($this->getHousingExpenseTotal($housingExpense->getHoaDues(), $housingExpenseHoaDues));
		
		$housingExpenseOther = StringUtil::find($fannieMaeData, "05H" . $borrowerSsn . "123", 16, 15);
		$housingExpense->setOther($this->getHousingExpenseTotal($housingExpense->getOther(), $housingExpenseOther));
			
		// monthly income
        $application->removeIncomeMonthlyByBorrower($borrower);

		$borrowerMonthlyIncome = new IncomeMonthly();
		$borrowerMonthlyIncome->setBorrower($borrower);
		$application->addIncomeMonthly($borrowerMonthlyIncome);
		
		$borrowerMonthlyIncomeBase = StringUtil::find($fannieMaeData, "05I" . $borrowerSsn . "20", 15, 15);
		$borrowerMonthlyIncome->setBase($this->toFloat($borrowerMonthlyIncomeBase));
		
		$borrowerMonthlyIncomeOvertime = StringUtil::find($fannieMaeData, "05I" . $borrowerSsn . "09", 15, 15);
		$borrowerMonthlyIncome->setOvertime($this->toFloat($borrowerMonthlyIncomeOvertime));
		
		$borrowerMonthlyIncomeBonus = StringUtil::find($fannieMaeData, "05I" . $borrowerSsn . "08", 15, 15);
		$borrowerMonthlyIncome->setBonus($this->toFloat($borrowerMonthlyIncomeBonus));
		
		$borrowerMonthlyIncomeCommission = StringUtil::find($fannieMaeData, "05I" . $borrowerSsn . "10", 15, 15);
		$borrowerMonthlyIncome->setCommission($this->toFloat($borrowerMonthlyIncomeCommission));
		
		$borrowerMonthlyIncomeInterest = StringUtil::find($fannieMaeData, "05I" . $borrowerSsn . "17", 15, 15);
		$borrowerMonthlyIncome->setInterest($this->toFloat($borrowerMonthlyIncomeInterest));
		
		$borrowerMonthlyIncomeRentNet = StringUtil::find($fannieMaeData, "05I" . $borrowerSsn . "33", 15, 15);
		$borrowerMonthlyIncome->setRentNet($this->toFloat($borrowerMonthlyIncomeRentNet));
		
		$borrowerMonthlyIncomeOther = StringUtil::find($fannieMaeData, "05I" . $borrowerSsn . "45", 15, 15);
		$borrowerMonthlyIncome->setOther($this->toFloat($borrowerMonthlyIncomeOther));
		
		// declarations
		$declarationOutstandingJudgement = StringUtil::find($fannieMaeData, "08A" . $borrowerSsn, 13, 1);
		$borrower->setDeclarationOutstandingJudgement($this->convertToBoolean($declarationOutstandingJudgement));
		
		$declarationOutstandingJudgementDetails = StringUtil::find($fannieMaeData, "08B" . $borrowerSsn . "91", 15, 255);
		$borrower->setDeclarationOutstandingJudgementDetails($this->getStringValue($declarationOutstandingJudgementDetails));
		
		$declarationBankruptcy = StringUtil::find($fannieMaeData, "08A" . $borrowerSsn, 14, 1);
		$borrower->setDeclarationBankruptcy($this->convertToBoolean($declarationBankruptcy));
		
		$declarationBankruptcyDetails = StringUtil::find($fannieMaeData, "08B" . $borrowerSsn . "92", 15, 255);
		$borrower->setDeclarationBankruptcyDetails($this->getStringValue($declarationBankruptcyDetails));
		
		$declarationForclosure = StringUtil::find($fannieMaeData, "08A" . $borrowerSsn, 15, 1);
		$borrower->setDeclarationForclosure($this->convertToBoolean($declarationForclosure));
		
		$declarationForclosureDetails = StringUtil::find($fannieMaeData, "08B" . $borrowerSsn . "93", 15, 255);
		$borrower->setDeclarationForclosureDetails($this->getStringValue($declarationForclosureDetails));
		
		$declarationLawsuit = StringUtil::find($fannieMaeData, "08A" . $borrowerSsn, 16, 1);
		$borrower->setDeclarationLawsuit($this->convertToBoolean($declarationLawsuit));
		
		$declarationLawsuitDetails = StringUtil::find($fannieMaeData, "08B" . $borrowerSsn . "94", 15, 255);
		$borrower->setDeclarationLawsuitDetails($this->getStringValue($declarationLawsuitDetails));
		
		$declarationForclosureObligation = StringUtil::find($fannieMaeData, "08A" . $borrowerSsn, 17, 1);
		$borrower->setDeclarationForclosureObligation($this->convertToBoolean($declarationForclosureObligation));
		
		$declarationForclosureObligationDetails = StringUtil::find($fannieMaeData, "08B" . $borrowerSsn . "95", 15, 255);
		$borrower->setDeclarationForclosureObligationDetails($this->getStringValue($declarationForclosureObligationDetails));
		
		$declarationInDefault = StringUtil::find($fannieMaeData, "08A" . $borrowerSsn, 18, 1);
		$borrower->setDeclarationInDefault($this->convertToBoolean($declarationInDefault));
		
		$declarationInDefaultDetails = StringUtil::find($fannieMaeData, "08B" . $borrowerSsn . "96", 15, 255);
		$borrower->setDeclarationInDefaultDetails($this->getStringValue($declarationInDefaultDetails));
		
		$declarationAlimonyChildSupport = StringUtil::find($fannieMaeData, "08A" . $borrowerSsn, 19, 1);
		$borrower->setDeclarationAlimonyChildSupport($this->convertToBoolean($declarationAlimonyChildSupport));
		
		$declarationAlimonyChildSupportDetails = StringUtil::find($fannieMaeData, "08B" . $borrowerSsn . "97", 15, 255);
		$borrower->setDeclarationAlimonyChildSupportDetails($this->getStringValue($declarationAlimonyChildSupportDetails));
		
		$declarationDownPaymentBorrowed = StringUtil::find($fannieMaeData, "08A" . $borrowerSsn, 20, 1);
		$borrower->setDeclarationDownPaymentBorrowed($this->convertToBoolean($declarationDownPaymentBorrowed));
		
		$declarationDownPaymentBorrowedDetails = StringUtil::find($fannieMaeData, "08B" . $borrowerSsn . "98", 15, 255);
		$borrower->setDeclarationDownPaymentBorrowedDetails($this->getStringValue($declarationDownPaymentBorrowedDetails));
		
		$declarationNoteEndorser = StringUtil::find($fannieMaeData, "08A" . $borrowerSsn, 21, 1);
		$borrower->setDeclarationNoteEndorser($this->convertToBoolean($declarationNoteEndorser));
		
		$declarationNoteEndorserDetails = StringUtil::find($fannieMaeData, "08B" . $borrowerSsn . "99", 15, 255);
		$borrower->setDeclarationNoteEndorserDetails($this->getStringValue($declarationNoteEndorserDetails));
		
		$declarationUsCitizen = StringUtil::find($fannieMaeData, "08A" . $borrowerSsn, 22, 2);
		$borrower->setDeclarationUsCitizen($this->getUsCitizen($declarationUsCitizen));

		$declarationResidentAlien = StringUtil::find($fannieMaeData, "08A" . $borrowerSsn, 22, 2);
		$borrower->setDeclarationResidentAlien($this->getResidentAlien($declarationResidentAlien));
		
		$declarationPrimaryResidence = StringUtil::find($fannieMaeData, "08A" . $borrowerSsn, 24, 1);
		$borrower->setDeclarationPrimaryResidence($this->convertToBoolean($declarationPrimaryResidence));
		
		$declarationOwnershipWithinThreeYears = StringUtil::find($fannieMaeData, "08A" . $borrowerSsn, 25, 1);
		$borrower->setDeclarationOwnershipWithinThreeYears($this->convertToBoolean($declarationOwnershipWithinThreeYears));
		
		$declarationOwnershipWithinThreeYearsPropertyType = StringUtil::find($fannieMaeData, "08A" . $borrowerSsn, 26, 1);
		$borrower->setDeclarationOwnershipWithinThreeYearsPropertyType($this->getOwnershipPropertyType($declarationOwnershipWithinThreeYearsPropertyType));
		
		$declarationOwnershipWithinThreeYearsPropertyTitle = StringUtil::find($fannieMaeData, "08A" . $borrowerSsn, 27, 2);
		$borrower->setDeclarationOwnershipWithinThreeYearsPropertyTitle($this->getOwnershipTitle($declarationOwnershipWithinThreeYearsPropertyTitle));
		
		// govt monitoring
		$borrowerGovtMonitoringOptOut = StringUtil::find($fannieMaeData, "10A" . $borrowerSsn, 13, 1);
		$borrower->setGovtMonitoringOptOut($this->convertToBoolean($borrowerGovtMonitoringOptOut));
		
		$borrowerRace = StringUtil::find($fannieMaeData, "10R" . $borrowerSsn, 13, 2);
		$borrower->setRace($this->getBorrowerRace($borrowerRace));
		
		$borrowerEthnicity = StringUtil::find($fannieMaeData, "10A" . $borrowerSsn, 14, 1);
		$borrower->setEthnicity($this->getBorrowerEthnicity($borrowerEthnicity));
		
		$borrowerSex = StringUtil::find($fannieMaeData, "10A" . $borrowerSsn, 45, 1);
		$borrower->setIsMale($this->getBorrowerIsMale($borrowerSex));
		
		// expenses
		$declarationAlimonyChildSupportAlimony = StringUtil::find($fannieMaeData, "06F" . $borrowerSsn . "DR", 16, 15);
		$borrower->setDeclarationAlimonyChildSupportAlimony($this->toFloat($declarationAlimonyChildSupportAlimony));
		
		$declarationAlimonyChildSupportChildSupport = StringUtil::find($fannieMaeData, "06F" . $borrowerSsn . "DT", 16, 15);
		$borrower->setDeclarationAlimonyChildSupportChildSupport($this->toFloat($declarationAlimonyChildSupportChildSupport));
		
		$declarationAlimonyChildSupportSeparateMaintenance = StringUtil::find($fannieMaeData, "06F" . $borrowerSsn . "DV", 16, 15);
		$borrower->setDeclarationAlimonyChildSupportSeparateMaintenance($this->toFloat($declarationAlimonyChildSupportSeparateMaintenance));
	}

    /**
     * @param $loanTerm
     * @return float
     */
	protected function getLoanTerm($loanTerm)
	{
		if(isset($loanTerm)) {
			$loanTerm = $loanTerm / 12;			
		}
		
		return $loanTerm;
	}

    /**
     * @param $loanType
     * @return int
     */
	protected function getLoanType($loanType)
	{
		if(isset($loanType)) {
			switch($loanType) {
				case '16':
					$loanType = 0;
					break;
				case '05':
					$loanType = 1;
					break;
			}
		}
		
		return $loanType;
	}

    /**
     * @param $borrowerMaritialStatus
     * @return int
     */
	protected function getBorrowerMaritalStatus($borrowerMaritialStatus)
	{
		if(isset($borrowerMaritialStatus)) {
			switch($borrowerMaritialStatus) {
				case 'M';
				$borrowerMaritialStatus = 0;
				break;
				case 'U';
				$borrowerMaritialStatus = 1;
				break;
				case 'S';
				$borrowerMaritialStatus = 2;
				break;
			}	
		}
		
		return $borrowerMaritialStatus;
	}

    /**
     * @param $borrowerLocationOwnResidence
     * @return int|null
     */
	protected function getBorrowerLocationOwnResidence($borrowerLocationOwnResidence) {
		if(isset($borrowerLocationOwnResidence)) {
			switch($borrowerLocationOwnResidence) {
				case 'R':
					$borrowerLocationOwnResidence = 0;
					break;
				case 'O':
					$borrowerLocationOwnResidence = 1;
					break;
				default:
					$borrowerLocationOwnResidence = null;
			}
		}
		
		return $borrowerLocationOwnResidence;
	}

    /**
     * @param $propertyType
     * @return int
     */
	protected function getPropertyType($propertyType)
	{
		if(isset($propertyType)) {
			switch($propertyType) {
				case '01':
					$propertyType = 0; // single family
					break;
				case '02':
					$propertyType = 1; // attached
					break;
				case '03':
					$propertyType = 2; // condo
					break;
				case '04':
					$propertyType = 3; // pud
					break;
				case '05':
					$propertyType = 4; // multifamily
					break;
				case '07':
					$propertyType = 5; // high rise condo
					break;
				case '08':
					$propertyType = 6; // manufactured
					break;
				case '09':
					$propertyType = 7; // detached condo
					break;
				case '10':
					$propertyType = 8; // manufactured pud
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
	public function getResidencyType($residencyType)
	{
		if(isset($residencyType)) {
			switch($residencyType) {
				case '1';
					$residencyType = 0;
					break;
				case '2';
					$residencyType = 1;
					break;
				case 'D';
					$residencyType = 2;
					break;
				default:
					$residencyType = null;
			}
		}
		
		return $residencyType;
	}

    /**
     * @param $ethnicity
     * @return int|null
     */
	protected function getBorrowerEthnicity($ethnicity)
	{
		if(isset($ethnicity)) {
			switch($ethnicity) {
				case '1':
					$ethnicity = 0;
					break;
				case '2':
					$ethnicity = 1;
					break;
				case '4':
					$ethnicity = 2;
					break;
				default:
					$ethnicity = null;
			}
		}
		
		return $ethnicity;
	}

    /**
     * @param $sex
     * @return bool|null
     */
	protected function getBorrowerIsMale($sex)
	{
		if(isset($sex)) {
			switch($sex) {
				case 'M':
					$sex = true;
					break;
				case 'F':
					$sex = false;
					break;
				case 'N':
					$sex = null;
					break;
				default:
					$sex = null;
			}
		}
		
		return $sex;
	}

    /**
     * @param $race
     * @return int
     */
	protected function getBorrowerRace($race) 
	{
		if(isset($race)) {
			
			switch($race) {
				case '1':
					$race = 0;
					break;
				case '2':
					$race = 1;
					break;
				case '3':
					$race = 2;
					break;
				case '4':
					$race = 3;
					break;
				case '5':
					$race = 4;
					break;
				case '7':
					$race = 5;
					break;
				default:
					$race = null;
			}
		}
		
		return $race;
	}

    /**
     * @param $type
     * @return int|string
     */
	protected function getAccountType($type)
	{
		if(isset($type)) {
			$type = trim($type);
			switch($type) {
				case '03';
					$type = 0; // chceking
					break;
				case 'SG';
					$type = 1; // savings
					break;
				case 'F3';
					$type = 2; // money market
					break;
				case '01';
					$type = 3; // cd
					break;
				case 'F4';
					$type = 4; // mutual fund
					break;
				case '08';
					$type = 5; // retirement
					break;
				default:
					$type = 6; // other
			}
		}
		
		return $type;
	}

    /**
     * @param $dateString
     * @return \DateTime|null
     */
	protected function convertToDateTime($dateString)
	{
		$date = null;
		if(!empty($dateString)) {
			$dateString = sprintf('%s-%s-%s', substr($dateString, 0, 4), substr($dateString, 4, 2), substr($dateString, 6, 2));
			$date = new \DateTime($dateString);
		}
		return $date;
	}

    /**
     * @param $value
     * @return bool|null
     */
	protected function convertToBoolean($value)
	{
		$bool = null;
		
		if(isset($value)) {
			$falseValues = array('N', 'n', 'no', 'No', 'false', '0');
			if(in_array($value, $falseValues)) {
				$bool = false;
			} else {
				$bool = true;				
			}
		}
		
		return $bool;
	}

    /**
     * @param $existingValue
     * @param $newValue
     * @return float|int
     */
	public function getHousingExpenseTotal($existingValue, $newValue)
	{	
		$newValue = (float)$newValue;
		$expenseTotal = 0;
		
		if(isset($existingValue)) {
			$expenseTotal += $existingValue;
		}
		
		if(!empty($newValue)) {
			$expenseTotal += $newValue;
		}
		
		return $expenseTotal;
	}

    /**
     * @param $status
     * @return bool|null
     */
	public function getUsCitizen($status)
	{
		$retVal = null;
		if(isset($status)) {
			if($status == '01') {
				$retVal = true;
			} else {
				$retVal = false;
			}
		}
		return $retVal;
	}

    /**
     * @param $status
     * @return bool|null
     */
	public function getResidentAlien($status)
	{
		$retVal = null;
		if(isset($status)) {
			if($status == '05') {
				$retVal = true;
			} else {
				$retVal = false;
			}
		}
		return $retVal;
	}

    /**
     * @param $value
     * @return null
     */
	public function getStringValue($value)
	{
		if(empty($value)) {
			$value = null;
		}
		
		return $value;
	}

    /**
     * @param $purpose
     * @return int|null
     */
	public function getRefinancePurpose($purpose)
	{
		if(!empty($purpose)) {
			switch($purpose) {
				case 'F1';
					$purpose = 3;
					break;
				case '01';
					$purpose = 2;
					break;
				case '04';
					$purpose = 1;
					break;
				case '11';
					$purpose = 0;
					break;
				case '13';
					$purpose = 4;
					break;
				default:
					$purpose = null;
			}
		} else {
			$purpose = null;
		}
		
		return $purpose;
	}

    /**
     * @param $type
     * @return int|null
     */
	public function getOwnershipPropertyType($type)
	{
		if(isset($type)) {
			switch($type) {
				case '1':
					$type = 0;
					break;
				case '2':
					$type = 1;
					break;
				case 'D':
					$type = 2;
					break;
				default:
					$type = null;
			}
		}
		
		return $type;
	}

    /**
     * @param $value
     * @return int|null
     */
	public function getOwnershipTitle($value)
	{
		if(isset($value)) {
			switch($value) {
				case '01':
					$value = 0;
					break;
				case '25':
					$value = 1;
					break;
				case '26':
					$value = 2;
					break;
				default:
					$value = null;
			}
		}	

		return $value;
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