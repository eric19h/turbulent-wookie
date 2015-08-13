<?php

namespace Sudoux\MortgageBundle\Entity;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Doctrine\Common\Collections\ArrayCollection;
use Sudoux\Cms\TaxonomyBundle\Entity\Taxonomy;
use JMS\Serializer\Annotation\Accessor;
use Sudoux\Cms\LocationBundle\Entity\Location;

use Sudoux\Cms\UserBundle\Entity\User;

use Sudoux\MortgageBundle\Model\Encompass;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class LoanApplication
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class LoanApplication
{

    
    /**
     * @Expose()
     * @var integer
     */
    private $id;

    /**
     * @var integer
     * @Expose()
     */
    private $sale_price;

    /**
     * @var integer
     * @Expose()
     */
    private $loan_amount;

    /**
     * @var integer
     * @Expose()
     */
    private $loan_term;

    /**
     * @var integer
     * @Expose()
     */
    private $loan_type;

    /**
     * @var integer
     * @Expose()
     */
    private $num_units;

    /**
     * @var integer
     * @Expose()
     */
    private $property_type;

    /**
     * @var integer
     * @Expose()
     */
    private $residency_type;

    /**
     * @var string
     * @Expose()
     */
    private $title_company1;

    /**
     * @var string
     * @Expose()
     */
    private $title_company2;

    /**
     * @var integer
     * @Expose()
     */
    private $title_manner;

    /**
     * @var boolean
     * @Expose()
     */
    private $has_realtor;

    /**
     * @var string
     * @Expose()
     */
    private $realtor_name;

    /**
     * @var string
     * @Expose()
     */
    private $realtor_company;

    /**
     * @var string
     * @Expose()
     */
    private $realtor_phone;

    /**
     * @var \Sudoux\Cms\LocationBundle\Entity\Location
     * @Expose()
     */
    private $property_location;

    /**
     * @var \Sudoux\Cms\UserBundle\Entity\User
     * @Expose()
     */
    private $user;

    /**
     * @var \Sudoux\MortgageBundle\Entity\Borrower
     * @Expose()
     */
    private $borrower;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $co_borrower;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $asset_account;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $asset_real_estate;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $income_monthly;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $income_other;

    /**
     * @var array
     */
    public $loanStatuses = array(
    	0 => 'Incomplete',
    	1 => 'New',
    	2 => 'Viewed',
    	3 => 'In Process',
    	4 => 'Junk',
    	5 => 'Closed',	
    	6 => 'Upserted from Destiny',
        7 => 'Imported From Velma',
        8 => 'Imported From API'
    );

    /**
     * @var array
     */
    public $refinancePurposes = array(
    	0 => 'Cash-Out Debt Consolidation',
    	1 => 'Cash-Out Home Improvement',
    	2 => 'Cash-Out Other',
    	3 => 'No Cash-Out',
    	4 => 'Limited Cash-Out',
    );

    /**
     * @var array
     */
    public $titleManners = array(
       	'Community property' => 'Community property',
		'Joint tenants' => 'Joint tenants',
		'Single man' => 'Single man',
		'Single woman' => 'Single woman',
		'Married man' => 'Married man',
		'Married woman' => 'Married woman',
		'Tenants in common' => 'Tenants in common',
		'To be decided in escrow' => 'To be decided in escrow',
		'Unmarried man' => 'Unmarried man',
		'Unmarried woman' => 'Unmarried woman',
		'Other' => 'Other',
   	);

    /**
     * @var array
     */
    public $loanTypes = array(
		0 => 'Purchase',
		1 => 'Refinance',
	);

    /**
     * @var array
     */
    public $propertyTypes = array(
		0 => 'Single Family',
		1 => 'Attached',
		2 => 'Condominium',
		3 => 'Planned Unit Development (PUD)',
		4 => 'Multi-Family (2 - 4 Units)',
		5 => 'High Rise Condo',
		6 => 'Manufactured Home',
		7 => 'Detached Condo',
		8 => 'Manufactured Home: Condo/PUD/Co-Op',
		9 => 'Other',
	);

    /**
     * @var array
     */
    public $residencyTypes = array(
		0 => 'Primary',
		1 => 'Second Home',
		2 => 'Investment'
	);

    /**
     * @var array
     */
    public $lockStatuses = array(
		0 => 'Unlocked',
		1 => 'Applicant Locked',
		2 => 'Locked'
	);

    /**
     * @var array
     */
    public $sources = array(
    	0 => 'Website',
    	1 => 'LOS',
    	2 => 'Fannie Mae 3.2 File',
        3 => 'API',
        4 => 'POST'
    );


/*
    public function __clone(){

        if ($this->id) {

           // $this->id = NULL;
            $this->borrower = clone $this->borrower;
            $this->co_borrower = clone $this->co_borrower;
            $this->user = NULL;
            $this->client_user = NULL;
            $this->pricing_scenario = NULL;
            $this->additional_site = NULL;

        }

    }
*/

    /**
     * Constructor
     */
    public function __construct()
    {
    	$this->is_prequal = false;
    	$this->borrower = new Borrower();
        $this->co_borrower = new \Doctrine\Common\Collections\ArrayCollection();
        $this->asset_account = new \Doctrine\Common\Collections\ArrayCollection();
        $this->asset_real_estate = new \Doctrine\Common\Collections\ArrayCollection();
        $this->income_monthly = new \Doctrine\Common\Collections\ArrayCollection();
        $this->income_other = new \Doctrine\Common\Collections\ArrayCollection();
        $this->completed = false;
        $this->modified = new \DateTime();
        $this->created = new \DateTime();
        $this->setStatus(0);
        $this->deleted = false;
        $this->lock_status = 0;
        $this->message_thread = new \Sudoux\Cms\MessageBundle\Entity\Thread();
        $this->message_thread->setSubject('Loan Application Thread');
        $this->last_step_completed = 1;
        $this->sent_to_los = false;
        $this->property_location = new Location();
        $this->document = new \Doctrine\Common\Collections\ArrayCollection();
        $this->loan_type = 0;
        $this->source = 0;
        $this->no_property_location = false;
    }

    public function setApi(){


        if(!isset($this->is_prequal)){
            $this->is_prequal = false;
        }
        if(!isset($this->co_borrower)){
            $this->co_borrower = new \Doctrine\Common\Collections\ArrayCollection();
        }
        if(!isset($this->asset_account)){
            $this->asset_account = new \Doctrine\Common\Collections\ArrayCollection();
        }
        if(!isset($this->asset_real_estate)){
            $this->asset_real_estate = new \Doctrine\Common\Collections\ArrayCollection();
        }
        if(!isset($this->income_monthly)){
            $this->income_monthly = new \Doctrine\Common\Collections\ArrayCollection();
        }
        if(!isset($this->income_other)){
            $this->income_other = new \Doctrine\Common\Collections\ArrayCollection();
        }
        if(!isset($this->expense_housing)){
            $this->expense_housing = new ExpenseHousing();
        }
        if(!isset($this->created)){
            $this->created = new \DateTime();
        }
        if(!isset($this->modified)){
            $this->modified = new \DateTime();
        }
        if(!isset($this->deleted)){
            $this->deleted = false;
        }
        if(!isset($this->message_thread)){
            $this->message_thread = new \Sudoux\Cms\MessageBundle\Entity\Thread();
            $this->message_thread->setSubject('Loan Application Thread');
        }
        if(!isset($this->last_step_completed) || $this->last_step_completed >= 7){
            $this->last_step_completed = 7;
            $this->completed=true;
            $this->completed_date=new \DateTime();
        }else{
            $this->completed=false;
        }
        if(!isset($this->sent_to_los)){
            $this->sent_to_los = false;
        }
        if(!isset($this->property_location)){
            $this->property_location = new Location();
        }else{
            $propertyCreated = $this->property_location->getCreated();
            if(!isset($propertyCreated)){
                $this->property_location->setCreated(new \DateTime());
            }
        }
        if(!isset($this->document)){
            $this->document = new \Doctrine\Common\Collections\ArrayCollection();
        }
        if(!isset($this->loan_type)){
            $this->loan_type = 0;
        }
        if(!isset($this->source)){
            $this->source = 3;
        }
        if(!isset($this->lock_status )){
            $this->lock_status = 0;
        }
        if(!isset($this->no_property_location )){
            $this->no_property_location = false;
        }

        $this->setStatus(8);

        ///////////////////////
        //
        //Everything below is so that the user doesn't have to set the created dates
        //for anything coming into the API
        ////////////////////////

        $blLocationCreated = $this->borrower->getLocation()->getLocation()->getCreated();
        $bLocationCreated = $this->borrower->getLocation()->getCreated();
        $eLocationCreated = $this->borrower->getEmployment();

        if(!isset($this->status_date)){
            $this->status_date = new \DateTime();
        }


        if(isset($eLocationCreated)){

            foreach($eLocationCreated as $emp){

                $bEmpLoc = $emp->getLocation()->getCreated();

                if(!isset($bEmpLoc)){
                    $emp->getLocation()->setApi();
                }

            }
        }

        if(!isset($bLocationCreated)){
            $this->borrower->getLocation()->getLocation()->setApi();
        }

        if(!isset($blLocationCreated)){
            $this->borrower->getLocation()->setApi();
        }

        foreach($this->co_borrower as $coB){

            $colbLocationCreated = $coB->getLocation()->getLocation()->getCreated();
            $cobLocationCreated = $coB->getLocation()->getCreated();
            $eCoLocationCreated = $coB->getEmployment();

            if(isset($eCoLocationCreated)){
                foreach($eCoLocationCreated as $coEmp){

                    $bCoEmpLoc = $coEmp->getLocation()->getCreated();

                    if(!isset($bCoEmpLoc)){
                        $coEmp->getLocation()->setApi();
                    }

                }
            }



            if(!isset($cobLocationCreated)){
                $coB->getLocation()->getLocation()->setApi();
            }
            if(!isset($colbLocationCreated)){
                $coB->getLocation()->setApi();
            }

        }
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sale_price
     *
     * @param integer $salePrice
     * @return LoanApplication
     */
    public function setSalePrice($salePrice)
    {
        $this->sale_price = $salePrice;
    
        return $this;
    }

    /**
     * Get sale_price
     *
     * @return integer 
     */
    public function getSalePrice()
    {
        return $this->sale_price;
    }

    /**
     * Set loan_amount
     *
     * @param integer $loanAmount
     * @return LoanApplication
     */
    public function setLoanAmount($loanAmount)
    {
        $this->loan_amount = $loanAmount;
    
        return $this;
    }

    /**
     * Get loan_amount
     *
     * @return integer 
     */
    public function getLoanAmount()
    {
        return $this->loan_amount;
    }

    /**
     * Set loan_term
     *
     * @param integer $loanTerm
     * @return LoanApplication
     */
    public function setLoanTerm($loanTerm)
    {
        $this->loan_term = $loanTerm;
    
        return $this;
    }


    /**
     * Get loan_term
     *
     * @return integer 
     */
    public function getLoanTerm()
    {
        return $this->loan_term;
    }
    
    public function getLoanTermsFromMonthsToYears()
    {
    	if(isset($this->loan_term)) {
    		$this->loan_term = ($this->loan_term / 12);
    	}
    	
    	return $this->loan_term;
    }

    /**
     * Set loan_type
     *
     * @param integer $loanType
     * @return LoanApplication
     */
    public function setLoanType($loanType)
    {
        $this->loan_type = $loanType;
    
        return $this;
    }

    /**
     * Get loan_type
     *
     * @return integer 
     */
    public function getLoanType()
    {
        return $this->loan_type;
    }

    public function getLoanTypeText()
    {
    	$types = array(
            0 => 'Purchase',
            1 => 'Refinance',
        );
        return $types[$this->loan_type];
    }

    /**
     * Set num_units
     *
     * @param integer $numUnits
     * @return LoanApplication
     */
    public function setNumUnits($numUnits)
    {
        $this->num_units = $numUnits;
    
        return $this;
    }

    /**
     * Get num_units
     *
     * @return integer 
     */
    public function getNumUnits()
    {
        return $this->num_units;
    }

    /**
     * Set property_type
     *
     * @param integer $propertyType
     * @return LoanApplication
     */
    public function setPropertyType($propertyType)
    {
        $this->property_type = $propertyType;
    
        return $this;
    }

    /**
     * Get property_type
     *
     * @return integer 
     */
    public function getPropertyType()
    {
        return $this->property_type;
    }

    public function getPropertyTypeText()
    {
    	if(isset($this->property_type)) {
	        return $this->propertyTypes[$this->property_type];
    	}
    	
    	return $this->property_type;
    }
    

    /**
     * Set residency_type
     *
     * @param integer $residencyType
     * @return LoanApplication
     */
    public function setResidencyType($residencyType)
    {
        $this->residency_type = $residencyType;
    
        return $this;
    }

    /**
     * Get residency_type
     *
     * @return integer 
     */
    public function getResidencyType()
    {
        return $this->residency_type;
    }

    public function getResidencyTypeText()
    {
    	if(isset($this->residency_type)) {
	        return $this->residencyTypes[$this->residency_type];
    	}
    	
    	return null;
    }

    /**
     * Set title_company1
     *
     * @param string $titleCompany1
     * @return LoanApplication
     */
    public function setTitleCompany1($titleCompany1)
    {
        $this->title_company1 = $titleCompany1;
    
        return $this;
    }

    /**
     * Get title_company1
     *
     * @return string 
     */
    public function getTitleCompany1()
    {
        return $this->title_company1;
    }

    /**
     * Set title_company2
     *
     * @param string $titleCompany2
     * @return LoanApplication
     */
    public function setTitleCompany2($titleCompany2)
    {
        $this->title_company2 = $titleCompany2;
    
        return $this;
    }

    /**
     * Get title_company2
     *
     * @return string 
     */
    public function getTitleCompany2()
    {
        return $this->title_company2;
    }

    /**
     * Set title_manner
     *
     * @param integer $titleManner
     * @return LoanApplication
     */
    public function setTitleManner($titleManner)
    {
        $this->title_manner = $titleManner;
    
        return $this;
    }

    /**
     * Get title_manner
     *
     * @return integer 
     */
    public function getTitleManner()
    {
        return $this->title_manner;
    }
    
    public function getTitleMannerText()
    {
    	if(isset($this->title_manner)) {
	    	return $this->titleManners[$this->title_manner];
    	}
    	
    	return $this->title_manner;
    }

    /**
     * Set has_realtor
     *
     * @param boolean $hasRealtor
     * @return LoanApplication
     */
    public function setHasRealtor($hasRealtor)
    {
        $this->has_realtor = $hasRealtor;
    
        return $this;
    }

    /**
     * Get has_realtor
     *
     * @return boolean 
     */
    public function getHasRealtor()
    {
        return $this->has_realtor;
    }

    /**
     * Set realtor_name
     *
     * @param string $realtorName
     * @return LoanApplication
     */
    public function setRealtorName($realtorName)
    {
        $this->realtor_name = $realtorName;
    
        return $this;
    }

    /**
     * Get realtor_name
     *
     * @return string 
     */
    public function getRealtorName()
    {
        return $this->realtor_name;
    }

    /**
     * Set realtor_company
     *
     * @param string $realtorCompany
     * @return LoanApplication
     */
    public function setRealtorCompany($realtorCompany)
    {
        $this->realtor_company = $realtorCompany;
    
        return $this;
    }

    /**
     * Get realtor_company
     *
     * @return string 
     */
    public function getRealtorCompany()
    {
        return $this->realtor_company;
    }

    /**
     * Set realtor_phone
     *
     * @param string $realtorPhone
     * @return LoanApplication
     */
    public function setRealtorPhone($realtorPhone)
    {
        $this->realtor_phone = $realtorPhone;
    
        return $this;
    }

    /**
     * Get realtor_phone
     *
     * @return string 
     */
    public function getRealtorPhone()
    {
        return $this->realtor_phone;
    }

    /**
     * Set property_location
     *
     * @param \Sudoux\Cms\LocationBundle\Entity\Location $propertyLocation
     * @return LoanApplication
     */
    public function setPropertyLocation(\Sudoux\Cms\LocationBundle\Entity\Location $propertyLocation = null)
    {
        $this->property_location = $propertyLocation;
    
        return $this;
    }

    /**
     * Get property_location
     *
     * @return \Sudoux\Cms\LocationBundle\Entity\Location 
     */
    public function getPropertyLocation()
    {
        return $this->property_location;
    }

    /**
     * Set user
     *
     * @param \Sudoux\Cms\UserBundle\Entity\User $user
     * @return LoanApplication
     */
    public function setUser(\Sudoux\Cms\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Sudoux\Cms\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set borrower
     *
     * @param \Sudoux\MortgageBundle\Entity\Borrower $borrower
     * @return LoanApplication
     */
    public function setBorrower(\Sudoux\MortgageBundle\Entity\Borrower $borrower = null)
    {
        $this->borrower = $borrower;
    
        return $this;
    }

    /**
     * Get borrower
     *
     * @return \Sudoux\MortgageBundle\Entity\Borrower 
     */
    public function getBorrower()
    {
        return $this->borrower;
    }

    /**
     * Add co_borrower
     *
     * @param \Sudoux\MortgageBundle\Entity\Borrower $coBorrower
     * @return LoanApplication
     */
    public function addCoBorrower(\Sudoux\MortgageBundle\Entity\Borrower $coBorrower)
    {
        $this->co_borrower[] = $coBorrower;
    
        return $this;
    }

    /**
     * Remove co_borrower
     *
     * @param \Sudoux\MortgageBundle\Entity\Borrower $coBorrower
     */
    public function removeCoBorrower(\Sudoux\MortgageBundle\Entity\Borrower $coBorrower)
    {
        $this->co_borrower->removeElement($coBorrower);
    }

    /**
     * Get co_borrower
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCoBorrower()
    {
        return $this->co_borrower;
    }

    /**
     * Add asset_account
     *
     * @param \Sudoux\MortgageBundle\Entity\AssetAccount $assetAccount
     * @return LoanApplication
     */
    public function addAssetAccount(\Sudoux\MortgageBundle\Entity\AssetAccount $assetAccount)
    {
        $this->asset_account[] = $assetAccount;
    
        return $this;
    }

    /**
     * Remove asset_account
     *
     * @param \Sudoux\MortgageBundle\Entity\AssetAccount $assetAccount
     */
    public function removeAssetAccount(\Sudoux\MortgageBundle\Entity\AssetAccount $assetAccount)
    {
        $this->asset_account->removeElement($assetAccount);
    }
    
    public function removeAllAssetAccount()
    {
    	if(count($this->asset_account) > 0) {
    		$this->asset_account->clear();
    	}
    }
    
    public function removeAllAssetAccountByBorrower(Borrower $borrower)
    {
    	if(count($this->asset_account) > 0) {
    		foreach($this->asset_account as $asset) {
    			if($borrower->getId() == $asset->getBorrower()->getId()) {
    				$this->removeAssetAccount($asset);
    			}
    		}
    	}
    }

    /**
     * Get asset_account
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAssetAccount()
    {
        return $this->asset_account;
    }

    /**
     * Add asset_real_estate
     *
     * @param \Sudoux\MortgageBundle\Entity\AssetRealEstate $assetRealEstate
     * @return LoanApplication
     */
    public function addAssetRealEstate(\Sudoux\MortgageBundle\Entity\AssetRealEstate $assetRealEstate)
    {
        $this->asset_real_estate[] = $assetRealEstate;
    
        return $this;
    }

    /**
     * Remove asset_real_estate
     *
     * @param \Sudoux\MortgageBundle\Entity\AssetRealEstate $assetRealEstate
     */
    public function removeAssetRealEstate(\Sudoux\MortgageBundle\Entity\AssetRealEstate $assetRealEstate)
    {
        $this->asset_real_estate->removeElement($assetRealEstate);
    }
    
    public function removeAllAssetRealEstateByBorrower(Borrower $borrower)
    {
    	if(count($this->asset_real_estate) > 0) {
    		foreach($this->asset_real_estate as $asset) {
    			if($borrower->getId() == $asset->getBorrower()->getId()) {
    				$this->removeAssetRealEstate($asset);
    			}
    		}
    	}
    }
    
    public function removeAllAssetRealEstate()
    {
    	if(count($this->asset_real_estate) > 0) {
	    	$this->asset_real_estate->clear();
    	}
    }

    /**
     * Get asset_real_estate
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAssetRealEstate()
    {
        return $this->asset_real_estate;
    }

    /**
     * Add income_monthly
     *
     * @param \Sudoux\MortgageBundle\Entity\IncomeMonthly $incomeMonthly
     * @return LoanApplication
     */
    public function addIncomeMonthly(\Sudoux\MortgageBundle\Entity\IncomeMonthly $incomeMonthly)
    {
        $this->income_monthly[] = $incomeMonthly;
    
        return $this;
    }

    /**
     * Remove income_monthly
     *
     * @param \Sudoux\MortgageBundle\Entity\IncomeMonthly $incomeMonthly
     */
    public function removeIncomeMonthly(\Sudoux\MortgageBundle\Entity\IncomeMonthly $incomeMonthly)
    {
        $this->income_monthly->removeElement($incomeMonthly);
    }

    /**
     * Get income_monthly
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIncomeMonthly()
    {
        return $this->income_monthly;
    }

    public function removeAllIncomeMonthly()
    {
        $this->income_monthly->clear();
    }

    public function removeIncomeMonthlyByBorrower(\Sudoux\MortgageBundle\Entity\Borrower $borrower)
    {
        if($this->income_monthly->count() > 0) {
            foreach($this->income_monthly as $income) {
                if($income->getBorrower()->getId() == $borrower->getId()) {
                    $this->removeIncomeMonthly($income);
                }
            }
        }

    }

    /**
     * Add income_other
     *
     * @param \Sudoux\MortgageBundle\Entity\IncomeOther $incomeOther
     * @return LoanApplication
     */
    public function addIncomeOther(\Sudoux\MortgageBundle\Entity\IncomeOther $incomeOther)
    {
        $this->income_other[] = $incomeOther;
    
        return $this;
    }

    /**
     * Remove income_other
     *
     * @param \Sudoux\MortgageBundle\Entity\IncomeOther $incomeOther
     */
    public function removeIncomeOther(\Sudoux\MortgageBundle\Entity\IncomeOther $incomeOther)
    {
        $this->income_other->removeElement($incomeOther);
    }

    /**
     * Get income_other
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIncomeOther()
    {
        return $this->income_other;
    }
    /**
     * @var boolean
     * @Expose()
     */
    private $completed;

    /**
     * @var \DateTime
     * @Expose()
     */
    private $created;

    /**
     * @var \DateTime
     * @Expose()
     */
    private $modified;


    /**
     * Set completed
     *
     * @param boolean $completed
     * @return LoanApplication
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;
    	$this->completed_date = new \DateTime();
    	$this->lock_status = 1;
    	$this->last_step_completed = 7;
    	$this->is_prequal = false;
    	
        return $this;
    }

    /**
     * Get completed
     *
     * @return boolean 
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return LoanApplication
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return LoanApplication
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    
        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime 
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set income_monthly
     *
     * @param \Sudoux\MortgageBundle\Entity\IncomeMonthly $incomeMonthly
     * @return LoanApplication
     */
    public function setIncomeMonthly(\Sudoux\MortgageBundle\Entity\IncomeMonthly $incomeMonthly = null)
    {
        $this->income_monthly = $incomeMonthly;
    
        return $this;
    }


    /**
     * @var boolean
     * @Expose()
     */
    private $is_prequal;


    /**
     * Set is_prequal
     *
     * @param boolean $isPrequal
     * @return LoanApplication
     */
    public function setIsPrequal($isPrequal)
    {
        $this->is_prequal = $isPrequal;
    
        return $this;
    }

    /**
     * Get is_prequal
     *
     * @return boolean 
     */
    public function getIsPrequal()
    {
        return $this->is_prequal;
    }

    /**
     * @var \DateTime
     */
    private $completed_date;

    /**
     * Set completed_date
     *
     * @param \DateTime $completedDate
     * @return LoanApplication
     */
    public function setCompletedDate($completedDate)
    {
        $this->completed_date = $completedDate;
    
        return $this;
    }

    /**
     * Get completed_date
     *
     * @return \DateTime 
     */
    public function getCompletedDate()
    {
        return $this->completed_date;
    }
    /**
     * @var boolean
     * @Expose()
     */
    private $agreement_one;

    /**
     * @var boolean
     * @Expose()
     */
    private $agreement_two;

    /**
     * @var boolean
     * @Expose()
     */
    private $agreement_three;


    /**
     * Set agreement_one
     *
     * @param boolean $agreementOne
     * @return LoanApplication
     */
    public function setAgreementOne($agreementOne)
    {
        $this->agreement_one = $agreementOne;
    
        return $this;
    }

    /**
     * Get agreement_one
     *
     * @return boolean 
     */
    public function getAgreementOne()
    {
        return $this->agreement_one;
    }

    /**
     * Set agreement_two
     *
     * @param boolean $agreementTwo
     * @return LoanApplication
     */
    public function setAgreementTwo($agreementTwo)
    {
        $this->agreement_two = $agreementTwo;
    
        return $this;
    }

    /**
     * Get agreement_two
     *
     * @return boolean 
     */
    public function getAgreementTwo()
    {
        return $this->agreement_two;
    }

    /**
     * Set agreement_three
     *
     * @param boolean $agreementThree
     * @return LoanApplication
     */
    public function setAgreementThree($agreementThree)
    {
        $this->agreement_three = $agreementThree;
    
        return $this;
    }

    /**
     * Get agreement_three
     *
     * @return boolean 
     */
    public function getAgreementThree()
    {
        return $this->agreement_three;
    }
    /**
     * @var \Sudoux\MortgageBundle\Entity\ExpenseHousing
     */
    private $expense_housing;


    /**
     * Set expense_housing
     *
     * @param \Sudoux\MortgageBundle\Entity\ExpenseHousing $expenseHousing
     * @return LoanApplication
     */
    public function setExpenseHousing(\Sudoux\MortgageBundle\Entity\ExpenseHousing $expenseHousing = null)
    {
        $this->expense_housing = $expenseHousing;
    
        return $this;
    }

    /**
     * Get expense_housing
     *
     * @return \Sudoux\MortgageBundle\Entity\ExpenseHousing 
     */
    public function getExpenseHousing()
    {
        return $this->expense_housing;
    }
    /**
     * @var string
     * @Expose()
     */
    private $comments;


    /**
     * Set comments
     *
     * @param string $comments
     * @return LoanApplication
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    
        return $this;
    }

    /**
     * Get comments
     *
     * @return string 
     */
    public function getComments()
    {
        return $this->comments;
    }
    /**
     * @var \Sudoux\Cms\SiteBundle\Entity\Site
     * @Expose()
     */
    private $site;


    /**
     * Set site
     *
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $site
     * @return LoanApplication
     */
    public function setSite(\Sudoux\Cms\SiteBundle\Entity\Site $site)
    {
        $this->site = $site;
    
        return $this;
    }

    /**
     * Get site
     *
     * @return \Sudoux\Cms\SiteBundle\Entity\Site 
     */
    public function getSite()
    {
        return $this->site;
    }
    
    /**
     * @var \Sudoux\MortgageBundle\Entity\LoanOfficer
     * @Expose()
     */
    private $loan_officer;


    /**
     * Set loan_officer
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanOfficer $loanOfficer
     * @return LoanApplication
     */
    public function setLoanOfficer(\Sudoux\MortgageBundle\Entity\LoanOfficer $loanOfficer = null)
    {
        $this->loan_officer = $loanOfficer;
    
        return $this;
    }

    /**
     * Get loan_officer
     *
     * @return \Sudoux\MortgageBundle\Entity\LoanOfficer 
     */
    public function getLoanOfficer()
    {
        return $this->loan_officer;
    }
    
    public function getStatus()
    {
    	return $this->status;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $document;


    /**
     * Add document
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanDocument $document
     * @return LoanApplication
     */
    public function addDocument(\Sudoux\MortgageBundle\Entity\LoanDocument $document)
    {
        $this->document[] = $document;
    
        return $this;
    }

    /**
     * Remove document
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanDocument $document
     */
    public function removeDocument(\Sudoux\MortgageBundle\Entity\LoanDocument $document)
    {
        $this->document->removeElement($document);
    }

    /**
     * Get document
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDocument()
    {
        return $this->document;
    }

    public function getNewBorrowerDocumentCount()
    {
        $newDocCount = 0;

        if(count($this->document) > 0) {
            foreach($this->document as $document) {
                $docFile = $document->getFile();
                if(isset($docFile) && $document->getStatus() == 0) {
                    if($docFile->getUser()->getId() == $this->user->getId()) {
                        $newDocCount++;
                    }
                }
            }
        }

        return $newDocCount;
    }
    
    /**
     * @var \Sudoux\Cms\MessageBundle\Entity\Thread
     */
    private $message_thread;


    /**
     * Set message_thread
     *
     * @param \Sudoux\Cms\MessageBundle\Entity\Thread $messageThread
     * @return LoanApplication
     */
    public function setMessageThread(\Sudoux\Cms\MessageBundle\Entity\Thread $messageThread = null)
    {
        $this->message_thread = $messageThread;
    
        return $this;
    }

    /**
     * Get message_thread
     *
     * @return \Sudoux\Cms\MessageBundle\Entity\Thread 
     */
    public function getMessageThread()
    {
        return $this->message_thread;
    }

    /**
     * @var integer
     * @Expose()
     */
    private $status;


    /**
     * Set status
     *
     * @param integer $status
     * @return LoanApplication
     */
    public function setStatus($status)
    {
        $this->status = $status;
    	
        $this->status_date = new \DateTime();
        return $this;
    }
    
    public function getStatusName()
    {
    	return $this->loanStatuses[$this->status];
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $email;


    /**
     * Add email
     *
     * @param \Sudoux\Cms\MessageBundle\Entity\Email $email
     * @return LoanApplication
     */
    public function addEmail(\Sudoux\Cms\MessageBundle\Entity\Email $email)
    {
        $this->email[] = $email;
    
        return $this;
    }

    /**
     * Remove email
     *
     * @param \Sudoux\Cms\MessageBundle\Entity\Email $email
     */
    public function removeEmail(\Sudoux\Cms\MessageBundle\Entity\Email $email)
    {
        $this->email->removeElement($email);
    }

    /**
     * Get email
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEmail()
    {
        return $this->email;
    }
    /**
     * @var boolean
     * @Accessor(getter="getDeleted",setter="setDeleted")
     * @Expose()
     *
     */
    private $deleted;


    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return LoanApplication
     */
    public function setDeleted($deleted)
    {
    	$this->los_modified = new \DateTime();
        $this->deleted = $deleted;
    
        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
    /**
     * @var \DateTime
     * @Expose()
     */
    private $status_date;


    /**
     * Set status_date
     *
     * @param \DateTime $statusDate
     * @return LoanApplication
     */
    public function setStatusDate($statusDate)
    {
        $this->status_date = $statusDate;
    
        return $this;
    }

    /**
     * Get status_date
     *
     * @return \DateTime 
     */
    public function getStatusDate()
    {
        return $this->status_date;
    }
    /**
     * @var integer
     * @Expose()
     */
    private $refinance_year_aquired;

    /**
     * @var float
     * @Expose()
     */
    private $refinance_original_cost;

    /**
     * @var float
     * @Expose()
     */
    private $refinance_existing_liens;

    /**
     * @var float
     * @Expose()
     */
    private $refinance_current_rate;

    /**
     * @var string
     * @Expose()
     */
    private $refinance_current_loan_type;

    /**
     * @var integer
     * @Expose()
     */
    private $refinance_purpose;


    /**
     * Set refinance_year_aquired
     *
     * @param integer $refinanceYearAquired
     * @return LoanApplication
     */
    public function setRefinanceYearAquired($refinanceYearAquired)
    {
        $this->refinance_year_aquired = $refinanceYearAquired;
    
        return $this;
    }

    /**
     * Get refinance_year_aquired
     *
     * @return integer 
     */
    public function getRefinanceYearAquired()
    {
        return $this->refinance_year_aquired;
    }

    /**
     * Set refinance_original_cost
     *
     * @param float $refinanceOriginalCost
     * @return LoanApplication
     */
    public function setRefinanceOriginalCost($refinanceOriginalCost)
    {
        $this->refinance_original_cost = $refinanceOriginalCost;
    
        return $this;
    }

    /**
     * Get refinance_original_cost
     *
     * @return float 
     */
    public function getRefinanceOriginalCost()
    {
        return $this->refinance_original_cost;
    }

    /**
     * Set refinance_existing_liens
     *
     * @param float $refinanceExistingLiens
     * @return LoanApplication
     */
    public function setRefinanceExistingLiens($refinanceExistingLiens)
    {
        $this->refinance_existing_liens = $refinanceExistingLiens;
    
        return $this;
    }

    /**
     * Get refinance_existing_liens
     *
     * @return float 
     */
    public function getRefinanceExistingLiens()
    {
        return $this->refinance_existing_liens;
    }

    /**
     * Set refinance_current_rate
     *
     * @param float $refinanceCurrentRate
     * @return LoanApplication
     */
    public function setRefinanceCurrentRate($refinanceCurrentRate)
    {
        $this->refinance_current_rate = $refinanceCurrentRate;
    
        return $this;
    }

    /**
     * Get refinance_current_rate
     *
     * @return float 
     */
    public function getRefinanceCurrentRate()
    {
        return $this->refinance_current_rate;
    }

    /**
     * Set refinance_current_loan_type
     *
     * @param string $refinanceCurrentLoanType
     * @return LoanApplication
     */
    public function setRefinanceCurrentLoanType($refinanceCurrentLoanType)
    {
        $this->refinance_current_loan_type = $refinanceCurrentLoanType;
    
        return $this;
    }

    /**
     * Get refinance_current_loan_type
     *
     * @return string 
     */
    public function getRefinanceCurrentLoanType()
    {
        return $this->refinance_current_loan_type;
    }

    /**
     * Set refinance_purpose
     *
     * @param integer $refinancePurpose
     * @return LoanApplication
     */
    public function setRefinancePurpose($refinancePurpose)
    {
        $this->refinance_purpose = $refinancePurpose;
    
        return $this;
    }

    /**
     * Get refinance_purpose
     *
     * @return integer 
     */
    public function getRefinancePurpose()
    {
        return $this->refinance_purpose;
    }

    public function getRefinancePurposeName()
    {
    	if(isset($this->refinance_purpose)) {
	        return $this->refinancePurposes[$this->refinance_purpose];
    	}
    	
    	return;
    }
    /**
     * @var string
     * @Expose()
     */
    private $refinance_current_lender;


    /**
     * Set refinance_current_lender
     *
     * @param string $refinanceCurrentLender
     * @return LoanApplication
     */
    public function setRefinanceCurrentLender($refinanceCurrentLender)
    {
        $this->refinance_current_lender = $refinanceCurrentLender;
    
        return $this;
    }

    /**
     * Get refinance_current_lender
     *
     * @return string 
     */
    public function getRefinanceCurrentLender()
    {
        return $this->refinance_current_lender;
    }
    /**
     * @var integer
     * @Expose()
     */
    private $lock_status;


    /**
     * Set lock_status
     *
     * @param integer $lockStatus
     * @return LoanApplication
     */
    public function setLockStatus($lockStatus)
    {
        $this->lock_status = $lockStatus;
    
        return $this;
    }

    /**
     * Get lock_status
     *
     * @return integer 
     */
    public function getLockStatus()
    {
        return $this->lock_status;
    }
    /**
     * @var \Sudoux\MortgageBundle\Entity\LoanMilestoneGroup
     * @Expose()
     */
    private $milestone_group;


    /**
     * Set milestone_group
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanMilestoneGroup $milestoneGroup
     * @return LoanApplication
     */
    public function setMilestoneGroup(\Sudoux\MortgageBundle\Entity\LoanMilestoneGroup $milestoneGroup = null)
    {
        $this->milestone_group = $milestoneGroup;
    
        return $this;
    }

    /**
     * Get milestone_group
     *
     * @return \Sudoux\MortgageBundle\Entity\LoanMilestoneGroup 
     */
    public function getMilestoneGroup()
    {
        return $this->milestone_group;
    }
    /**
     * @var \Sudoux\MortgageBundle\Entity\LoanMilestone
     * @Expose()
     */
    private $milestone;


    /**
     * Set milestone
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanMilestone $milestone
     * @return LoanApplication
     */
    public function setMilestone(\Sudoux\MortgageBundle\Entity\LoanMilestone $milestone = null)
    {
        $this->milestone = $milestone;
    
        return $this;
    }

    /**
     * Get milestone
     *
     * @return \Sudoux\MortgageBundle\Entity\LoanMilestone 
     */
    public function getMilestone()
    {
        return $this->milestone;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $client_user;

    public function setClientUser(\Sudoux\Cms\UserBundle\Entity\User $clientUser)
    {
    	$this->addClientUser($clientUser);
    }

    /**
     * Add client_user
     *
     * @param \Sudoux\Cms\UserBundle\Entity\User $clientUser
     * @return LoanApplication
     */
    public function addClientUser(\Sudoux\Cms\UserBundle\Entity\User $clientUser)
    {
        $this->client_user[] = $clientUser;
    
        return $this;
    }

    /**
     * Remove client_user
     *
     * @param \Sudoux\Cms\UserBundle\Entity\User $clientUser
     */
    public function removeClientUser(\Sudoux\Cms\UserBundle\Entity\User $clientUser)
    {
        $this->client_user->removeElement($clientUser);
    }

    /**
     * Get client_user
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClientUser()
    {
        return $this->client_user;
    }
    /**
     * @var string
     * @Expose()
     */
    private $los_id;


    /**
     * Set los_id
     *
     * @param string $losId
     * @return LoanApplication
     */
    public function setLosId($losId)
    {
        $this->los_id = $losId;
    
        return $this;
    }

    /**
     * Get los_id
     *
     * @return string 
     */
    public function getLosId()
    {
        return $this->los_id;
    }
    /**
     * @var integer
     * @Expose()
     */
    private $last_step_completed;


    /**
     * Set last_step_completed
     *
     * @param integer $lastStepCompleted
     * @return LoanApplication
     */
    public function setLastStepCompleted($lastStepCompleted)
    {
        $this->last_step_completed = $lastStepCompleted;
    
        return $this;
    }

    /**
     * Get last_step_completed
     *
     * @return integer 
     */
    public function getLastStepCompleted()
    {
        return $this->last_step_completed;
    }
    
    public function validate()
    {
    	$isValid = true;
    	// validate co-borrowers. borrower should always be valid
    	$coBorrowers = $this->getCoBorrower();
    	if(count($coBorrowers) > 0) {
    		foreach($coBorrowers as $coBorrower) {
    			if(!$coBorrower->validate()) {
    				$isValid = false;
    			}
    		}
    	}
    	
    	return $isValid;
    }
    /**
     * @var integer
     * @Expose()
     */
    private $property_year_built;


    /**
     * Set property_year_built
     *
     * @param integer $propertyYearBuilt
     * @return LoanApplication
     */
    public function setPropertyYearBuilt($propertyYearBuilt)
    {
        $this->property_year_built = $propertyYearBuilt;
    
        return $this;
    }

    /**
     * Get property_year_built
     *
     * @return integer 
     */
    public function getPropertyYearBuilt()
    {
        return $this->property_year_built;
    }
    /**
     * @var \DateTime
     * @Expose()
     */
    private $los_modified;


    /**
     * Set los_modified
     *
     * @param \DateTime $losModified
     * @return LoanApplication
     */
    public function setLosModified($losModified)
    {
        $this->los_modified = $losModified;
    	$this->sent_to_los = true;
        return $this;
    }

    /**
     * Get los_modified
     *
     * @return \DateTime 
     */
    public function getLosModified()
    {
        return $this->los_modified;
    }
    
    public function preUpdate()
    {
    	$this->modified = new \DateTime();
        if($this->no_property_location) {
            $this->removePropertyData();
        }
    }

    /**
     * Set sentToLos
     *
     * @param boolean $sentToLos
     * @return LoanApplication
     */
    public function setSentToLos($sentToLos)
    {
        $this->sent_to_los = $sentToLos;
    
        return $this;
    }

    /**
     * Get sentToLos
     *
     * @return boolean 
     */
    public function getSentToLos()
    {
        return $this->sent_to_los;
    }
    /**
     * @var boolean
     * @Expose()
     */
    private $sent_to_los;

    /**
     * @param User $user
     * @return array
     */
    public function getNewUserMessages(User $user)
    {
    	$messages = array();
    	$newMessages = $this->message_thread->getNewMessages();
    	if(count($this->message_thread->getNewMessages()) > 0) {
    		foreach($newMessages as $message) {
    			if($message->getStatus() == 0 && $user->getId() != $message->getUser()->getId()) {
    				array_push($messages, $message);
    			}
    		}
    	}
    	return $messages;
    }


    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $pricing_scenario;


    /**
     * Add pricing_scenario
     *
     * @param \Sudoux\MortgageBundle\Entity\PricingScenario $pricingScenario
     * @return LoanApplication
     */
    public function addPricingScenario(\Sudoux\MortgageBundle\Entity\PricingScenario $pricingScenario)
    {
        $this->pricing_scenario[] = $pricingScenario;
    
        return $this;
    }

    /**
     * Remove pricing_scenario
     *
     * @param \Sudoux\MortgageBundle\Entity\PricingScenario $pricingScenario
     */
    public function removePricingScenario(\Sudoux\MortgageBundle\Entity\PricingScenario $pricingScenario)
    {
        $this->pricing_scenario->removeElement($pricingScenario);
    }
    
    public function removeAllPricingScenarios()
    {
    	$this->pricing_scenario->clear();
    }

    /**
     * Get pricing_scenario
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPricingScenario()
    {
        return $this->pricing_scenario;
    }
    /**
     * @var integer
     * @Expose()
     */
    private $source;


    /**
     * Set source
     *
     * @param integer $source
     * @return LoanApplication
     */
    public function setSource($source)
    {
        $this->source = $source;
    
        return $this;
    }

    /**
     * Get source
     *
     * @return integer 
     */
    public function getSource()
    {
        return $this->source;
    }

    public function getSourceName()
    {
        return $this->sources[$this->source];
    }
    /**
     * @var guid
     * @Expose()
     */
    private $guid;


    /**
     * Set guid
     *
     * @param guid $guid
     * @return LoanApplication
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;
    
        return $this;
    }

    /**
     * Get guid
     *
     * @return guid 
     */
    public function getGuid()
    {
        return $this->guid;
    }
    
    public function prePersist()
    {
    	if (function_exists('com_create_guid') === true)
    	{
    		$this->guid = trim(com_create_guid(), '{}');
    	}
    	 
    	$this->guid = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));

        if($this->no_property_location) {
            $this->removePropertyData();
        }
    }
    
    public function hasDocumentTaxonomy(Taxonomy $taxonomy, $status = null)
    {
    	$hasTaxonomy = false;
    	if(count($this->document) > 0) {
	    	foreach($this->document as $doc) {
	    		$docTaxonomy = $doc->getType();
	    		if(isset($docTaxonomy)) {
	    			if($docTaxonomy->getId() == $taxonomy->getId()) {
	    				if(isset($status)) {
	    					if($status == $doc->getStatus()) {
						    	$hasTaxonomy = true;	    					
	    					}
	    				} else {
					    	$hasTaxonomy = true;
	    				}
	    				break;
	    			}
	    		}	
	    	}
    	}
    	
    	return $hasTaxonomy;
    }
    /**
     * @var string
     * @Expose()
     */
    private $los_loan_number;


    /**
     * Set los_loan_number
     *
     * @param string $losLoanNumber
     * @return LoanApplication
     */
    public function setLosLoanNumber($losLoanNumber)
    {
        $this->los_loan_number = $losLoanNumber;
    
        return $this;
    }

    /**
     * Get los_loan_number
     *
     * @return string 
     */
    public function getLosLoanNumber()
    {
        return $this->los_loan_number;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $additional_site;


    /**
     * Add additional_site
     *
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $additionalSite
     * @return LoanApplication
     */
    public function addAdditionalSite(\Sudoux\Cms\SiteBundle\Entity\Site $additionalSite)
    {
        $this->additional_site[] = $additionalSite;
    
        return $this;
    }

    /**
     * Remove additional_site
     *
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $additionalSite
     */
    public function removeAdditionalSite(\Sudoux\Cms\SiteBundle\Entity\Site $additionalSite)
    {
        $this->additional_site->removeElement($additionalSite);
    }

    /**
     * Get additional_site
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdditionalSite()
    {
        return $this->additional_site;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $referral_source;


    /**
     * Add referral_source
     *
     * @param \Sudoux\Cms\FormBundle\Entity\ReferralSource $referralSource
     * @return LoanApplication
     */
    public function addReferralSource(\Sudoux\Cms\FormBundle\Entity\ReferralSource $referralSource)
    {
        $this->referral_source[] = $referralSource;
    
        return $this;
    }

    /**
     * Remove referral_source
     *
     * @param \Sudoux\Cms\FormBundle\Entity\ReferralSource $referralSource
     */
    public function removeReferralSource(\Sudoux\Cms\FormBundle\Entity\ReferralSource $referralSource)
    {
        $this->referral_source->removeElement($referralSource);
    }

    /**
     * Get referral_source
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReferralSource()
    {
        return $this->referral_source;
    }

    /**
     * @var \Sudoux\Cms\UserBundle\Entity\User
     * @Expose()
     */
    private $admin_user;


    /**
     * Set admin_user
     *
     * @param \Sudoux\Cms\UserBundle\Entity\User $adminUser
     * @return LoanApplication
     */
    public function setAdminUser(\Sudoux\Cms\UserBundle\Entity\User $adminUser = null)
    {
        $this->admin_user = $adminUser;
    
        return $this;
    }

    /**
     * Get admin_user
     *
     * @return \Sudoux\Cms\UserBundle\Entity\User 
     */
    public function getAdminUser()
    {
        return $this->admin_user;
    }

    /**
     *
     */
    public function isClientUser(User $user)
    {
        $isClientUser = false;
        if(isset($this->client_user)) {
            if($this->client_user->count() > 0) {
                foreach($this->client_user as $clientUser) {
                    if($clientUser->getId() == $user->getId()) {
                        $isClientUser = true;
                        break;
                    }
                }
            }
        }

        return $isClientUser;
    }

    /**
     * @param User $user
     * @return string
     */
    public function getLoanUserRoleType(User $user)
    {
        $roleType = 'Website Admin';
        if($this->isClientUser($user)) {
            $roleType = 'Additional User';
        } elseif(isset($this->user)) {
            if($user->getId() == $this->user->getId()) {
                if($user->hasRole('ROLE_LOAN_OFFICER')) {
                    $roleType = 'Loan Officer';
                } else {
                    $roleType = 'Applicant';
                }
            }
        }

        return $roleType;
    }

    /**
     * @var boolean
     */
    private $no_property_location;


    /**
     * Set no_property_location
     *
     * @param boolean $noPropertyLocation
     * @return LoanApplication
     */
    public function setNoPropertyLocation($noPropertyLocation)
    {
        $this->no_property_location = $noPropertyLocation;
    
        return $this;
    }

    /**
     * Get no_property_location
     *
     * @return boolean 
     */
    public function getNoPropertyLocation()
    {
        return $this->no_property_location;
    }

    /**
     *
     */
    public function removePropertyData() {
        $this->property_location = null;
        $this->property_type = null;
        $this->residency_type = null;
        $this->sale_price = null;
        $this->loan_amount = null;
        $this->loan_term = null;
        $this->num_units = null;
        $this->property_year_built = null;

    }
}