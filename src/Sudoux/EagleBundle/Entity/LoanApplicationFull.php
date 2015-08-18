<?php

namespace Sudoux\EagleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class LoanApplicationFull
 * @package Sudoux\EagleBundle\Entity
 * @author Eric Haynes
 */
class LoanApplicationFull
{


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
        0 => 'FHA',
        1 => 'VA',
        2 => 'CONV',
        3 => 'ARM',
        4 => 'JUMBO'
    );

    /**
     * @var array
     * @author Eric Haynes
     */
    public $loanPurposes = array(
        0 => 'Purchase',
        1 => 'Refinance'
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

    /**
     * @var array
     */
    public $downPaymentSources = array(
        0 => 'Sale',
        1 => 'Savings',
        2 => 'Gift',
        3 => 'Stock',
        4 => 'Other'
    );

    /**
     * @var integer
     */
    private $id;

    /**
     * @var guid
     */
    private $guid;

    /**
     * @var integer
     */
    private $sale_price;

    /**
     * @var integer
     */
    private $loan_amount;

    /**
     * @var string
     */
    private $los_id;

    /**
     * @var string
     */
    private $los_loan_number;

    /**
     * @var integer
     */
    private $loan_term;

    /**
     * @var integer
     */
    private $loan_type;

    /**
     * @var integer
     */
    private $num_units;

    /**
     * @var integer
     */
    private $property_type;

    /**
     * @var integer
     */
    private $property_year_built;

    /**
     * @var integer
     */
    private $residency_type;

    /**
     * @var string
     */
    private $title_company1;

    /**
     * @var string
     */
    private $title_company2;

    /**
     * @var string
     */
    private $title_company3;

    /**
     * @var string
     */
    private $title_manner;

    /**
     * @var boolean
     */
    private $has_realtor;

    /**
     * @var string
     */
    private $realtor_name;

    /**
     * @var string
     */
    private $realtor_company;

    /**
     * @var string
     */
    private $realtor_phone;

    /**
     * @var boolean
     */
    private $is_prequal;

    /**
     * @var integer
     */
    private $refinance_year_acquired;

    /**
     * @var float
     */
    private $refinance_original_cost;

    /**
     * @var float
     */
    private $refinance_existing_liens;

    /**
     * @var float
     */
    private $refinance_current_rate;

    /**
     * @var string
     */
    private $refinance_current_loan_type;

    /**
     * @var string
     */
    private $refinance_current_lender;

    /**
     * @var integer
     */
    private $refinance_purpose;

    /**
     * @var boolean
     */
    private $agreement_one;

    /**
     * @var boolean
     */
    private $agreement_two;

    /**
     * @var boolean
     */
    private $agreement_three;

    /**
     * @var string
     */
    private $comments;

    /**
     * @var boolean
     */
    private $completed;

    /**
     * @var \DateTime
     */
    private $completed_date;

    /**
     * @var integer
     */
    private $lock_status;

    /**
     * @var integer
     */
    private $last_step_completed;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var \DateTime
     */
    private $status_date;

    /**
     * @var boolean
     */
    private $deleted;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $modified;

    /**
     * @var \DateTime
     */
    private $los_modified;

    /**
     * @var boolean
     */
    private $sent_to_los;

    /**
     * @var integer
     */
    private $source;

    /**
     * @var boolean
     */
    private $no_property_location;

    /**
     * @var boolean
     */
    private $is_lennar_home;

    /**
     * @var integer
     */
    private $loan_purpose;

    /**
     * @var boolean
     */
    private $need_to_sell;

    /**
     * @var integer
     */
    private $rent_own_status;

    /**
     * @var boolean
     */
    private $are_joint_borrowers;

    /**
     * @var float
     */
    private $down_payment_amount;

    /**
     * @var integer
     */
    private $down_payment_source;

    /**
     * @var string
     */
    private $lennar_community_name;

    /**
     * @var string
     */
    private $lennar_builder_name;

    /**
     * @var boolean
     */
    private $has_communities;

    /**
     * @var \Sudoux\Cms\LocationBundle\Entity\Location
     */
    private $property_location;

    /**
     * @var \Sudoux\Cms\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var \Sudoux\Cms\UserBundle\Entity\User
     */
    private $admin_user;

    /**
     * @var \Sudoux\Cms\SiteBundle\Entity\Site
     */
    private $site;

    /**
     * @var \Sudoux\EagleBundle\Entity\BorrowerFull
     */
    private $borrower;

    /**
     * @var \Sudoux\MortgageBundle\Entity\ExpenseHousing
     */
    private $expense_housing;

    /**
     * @var \Sudoux\MortgageBundle\Entity\LoanOfficer
     */
    private $loan_officer;

    /**
     * @var \Sudoux\Cms\MessageBundle\Entity\Thread
     */
    private $message_thread;

    /**
     * @var \Sudoux\MortgageBundle\Entity\LoanMilestoneGroup
     */
    private $milestone_group;

    /**
     * @var \Sudoux\MortgageBundle\Entity\LoanMilestone
     */
    private $milestone;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->is_prequal = false;
        $this->borrower = new BorrowerFull();
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
     * Set guid
     *
     * @param guid $guid
     * @return LoanApplicationFull
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

    /**
     * Set sale_price
     *
     * @param integer $salePrice
     * @return LoanApplicationFull
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
     * @return LoanApplicationFull
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
     * Set los_id
     *
     * @param string $losId
     * @return LoanApplicationFull
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
     * Set los_loan_number
     *
     * @param string $losLoanNumber
     * @return LoanApplicationFull
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
     * Set loan_term
     *
     * @param integer $loanTerm
     * @return LoanApplicationFull
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

    /**
     * Set loan_type
     *
     * @param integer $loanType
     * @return LoanApplicationFull
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

    /**
     * Set num_units
     *
     * @param integer $numUnits
     * @return LoanApplicationFull
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
     * @return LoanApplicationFull
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

    /**
     * Set property_year_built
     *
     * @param integer $propertyYearBuilt
     * @return LoanApplicationFull
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
     * Set residency_type
     *
     * @param integer $residencyType
     * @return LoanApplicationFull
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

    /**
     * Set title_company1
     *
     * @param string $titleCompany1
     * @return LoanApplicationFull
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
     * @return LoanApplicationFull
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
     * Set title_company3
     *
     * @param string $titleCompany3
     * @return LoanApplicationFull
     */
    public function setTitleCompany3($titleCompany3)
    {
        $this->title_company3 = $titleCompany3;
    
        return $this;
    }

    /**
     * Get title_company3
     *
     * @return string 
     */
    public function getTitleCompany3()
    {
        return $this->title_company3;
    }

    /**
     * Set title_manner
     *
     * @param string $titleManner
     * @return LoanApplicationFull
     */
    public function setTitleManner($titleManner)
    {
        $this->title_manner = $titleManner;
    
        return $this;
    }

    /**
     * Get title_manner
     *
     * @return string 
     */
    public function getTitleManner()
    {
        return $this->title_manner;
    }

    /**
     * Set has_realtor
     *
     * @param boolean $hasRealtor
     * @return LoanApplicationFull
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
     * @return LoanApplicationFull
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
     * @return LoanApplicationFull
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
     * @return LoanApplicationFull
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
     * Set is_prequal
     *
     * @param boolean $isPrequal
     * @return LoanApplicationFull
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
     * Set refinance_year_acquired
     *
     * @param integer $refinanceYearAcquired
     * @return LoanApplicationFull
     */
    public function setRefinanceYearAcquired($refinanceYearAcquired)
    {
        $this->refinance_year_acquired = $refinanceYearAcquired;
    
        return $this;
    }

    /**
     * Get refinance_year_acquired
     *
     * @return integer 
     */
    public function getRefinanceYearAcquired()
    {
        return $this->refinance_year_acquired;
    }

    /**
     * Set refinance_original_cost
     *
     * @param float $refinanceOriginalCost
     * @return LoanApplicationFull
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
     * @return LoanApplicationFull
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
     * @return LoanApplicationFull
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
     * @return LoanApplicationFull
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
     * Set refinance_current_lender
     *
     * @param string $refinanceCurrentLender
     * @return LoanApplicationFull
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
     * Set refinance_purpose
     *
     * @param integer $refinancePurpose
     * @return LoanApplicationFull
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

    /**
     * Set agreement_one
     *
     * @param boolean $agreementOne
     * @return LoanApplicationFull
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
     * @return LoanApplicationFull
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
     * @return LoanApplicationFull
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
     * Set comments
     *
     * @param string $comments
     * @return LoanApplicationFull
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
     * Set completed
     *
     * @param boolean $completed
     * @return LoanApplicationFull
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;
    
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
     * Set completed_date
     *
     * @param \DateTime $completedDate
     * @return LoanApplicationFull
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
     * Set lock_status
     *
     * @param integer $lockStatus
     * @return LoanApplicationFull
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
     * Set last_step_completed
     *
     * @param integer $lastStepCompleted
     * @return LoanApplicationFull
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

    /**
     * Set status
     *
     * @param integer $status
     * @return LoanApplicationFull
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status_date
     *
     * @param \DateTime $statusDate
     * @return LoanApplicationFull
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
     * Set deleted
     *
     * @param boolean $deleted
     * @return LoanApplicationFull
     */
    public function setDeleted($deleted)
    {
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
     * Set created
     *
     * @param \DateTime $created
     * @return LoanApplicationFull
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
     * @return LoanApplicationFull
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
     * Set los_modified
     *
     * @param \DateTime $losModified
     * @return LoanApplicationFull
     */
    public function setLosModified($losModified)
    {
        $this->los_modified = $losModified;
    
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

    /**
     * Set sent_to_los
     *
     * @param boolean $sentToLos
     * @return LoanApplicationFull
     */
    public function setSentToLos($sentToLos)
    {
        $this->sent_to_los = $sentToLos;
    
        return $this;
    }

    /**
     * Get sent_to_los
     *
     * @return boolean 
     */
    public function getSentToLos()
    {
        return $this->sent_to_los;
    }

    /**
     * Set source
     *
     * @param integer $source
     * @return LoanApplicationFull
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

    /**
     * Set no_property_location
     *
     * @param boolean $noPropertyLocation
     * @return LoanApplicationFull
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
     * Set is_lennar_home
     *
     * @param boolean $isLennarHome
     * @return LoanApplicationFull
     */
    public function setIsLennarHome($isLennarHome)
    {
        $this->is_lennar_home = $isLennarHome;
    
        return $this;
    }

    /**
     * Get is_lennar_home
     *
     * @return boolean 
     */
    public function getIsLennarHome()
    {
        return $this->is_lennar_home;
    }

    /**
     * Set loan_purpose
     *
     * @param integer $loanPurpose
     * @return LoanApplicationFull
     */
    public function setLoanPurpose($loanPurpose)
    {
        $this->loan_purpose = $loanPurpose;
    
        return $this;
    }

    /**
     * Get loan_purpose
     *
     * @return integer 
     */
    public function getLoanPurpose()
    {
        return $this->loan_purpose;
    }

    /**
     * Set need_to_sell
     *
     * @param boolean $needToSell
     * @return LoanApplicationFull
     */
    public function setNeedToSell($needToSell)
    {
        $this->need_to_sell = $needToSell;
    
        return $this;
    }

    /**
     * Get need_to_sell
     *
     * @return boolean 
     */
    public function getNeedToSell()
    {
        return $this->need_to_sell;
    }

    /**
     * Set rent_own_status
     *
     * @param integer $rentOwnStatus
     * @return LoanApplicationFull
     */
    public function setRentOwnStatus($rentOwnStatus)
    {
        $this->rent_own_status = $rentOwnStatus;
    
        return $this;
    }

    /**
     * Get rent_own_status
     *
     * @return integer 
     */
    public function getRentOwnStatus()
    {
        return $this->rent_own_status;
    }

    /**
     * Set are_joint_borrowers
     *
     * @param boolean $areJointBorrowers
     * @return LoanApplicationFull
     */
    public function setAreJointBorrowers($areJointBorrowers)
    {
        $this->are_joint_borrowers = $areJointBorrowers;
    
        return $this;
    }

    /**
     * Get are_joint_borrowers
     *
     * @return boolean 
     */
    public function getAreJointBorrowers()
    {
        return $this->are_joint_borrowers;
    }

    /**
     * Set down_payment_amount
     *
     * @param float $downPaymentAmount
     * @return LoanApplicationFull
     */
    public function setDownPaymentAmount($downPaymentAmount)
    {
        $this->down_payment_amount = $downPaymentAmount;
    
        return $this;
    }

    /**
     * Get down_payment_amount
     *
     * @return float 
     */
    public function getDownPaymentAmount()
    {
        return $this->down_payment_amount;
    }

    /**
     * Set down_payment_source
     *
     * @param integer $downPaymentSource
     * @return LoanApplicationFull
     */
    public function setDownPaymentSource($downPaymentSource)
    {
        $this->down_payment_source = $downPaymentSource;
    
        return $this;
    }

    /**
     * Get down_payment_source
     *
     * @return integer 
     */
    public function getDownPaymentSource()
    {
        return $this->down_payment_source;
    }

    /**
     * Set lennar_community_name
     *
     * @param string $lennarCommunityName
     * @return LoanApplicationFull
     */
    public function setLennarCommunityName($lennarCommunityName)
    {
        $this->lennar_community_name = $lennarCommunityName;
    
        return $this;
    }

    /**
     * Get lennar_community_name
     *
     * @return string 
     */
    public function getLennarCommunityName()
    {
        return $this->lennar_community_name;
    }

    /**
     * Set lennar_builder_name
     *
     * @param string $lennarBuilderName
     * @return LoanApplicationFull
     */
    public function setLennarBuilderName($lennarBuilderName)
    {
        $this->lennar_builder_name = $lennarBuilderName;
    
        return $this;
    }

    /**
     * Get lennar_builder_name
     *
     * @return string 
     */
    public function getLennarBuilderName()
    {
        return $this->lennar_builder_name;
    }

    /**
     * Set has_communities
     *
     * @param boolean $hasCommunities
     * @return LoanApplicationFull
     */
    public function setHasCommunities($hasCommunities)
    {
        $this->has_communities = $hasCommunities;
    
        return $this;
    }

    /**
     * Get has_communities
     *
     * @return boolean 
     */
    public function getHasCommunities()
    {
        return $this->has_communities;
    }

    /**
     * Set property_location
     *
     * @param \Sudoux\Cms\LocationBundle\Entity\Location $propertyLocation
     * @return LoanApplicationFull
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
     * @return LoanApplicationFull
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
     * Set admin_user
     *
     * @param \Sudoux\Cms\UserBundle\Entity\User $adminUser
     * @return LoanApplicationFull
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
     * Set site
     *
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $site
     * @return LoanApplicationFull
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
     * Set borrower
     *
     * @param \Sudoux\EagleBundle\Entity\BorrowerFull $borrower
     * @return LoanApplicationFull
     */
    public function setBorrower(\Sudoux\EagleBundle\Entity\BorrowerFull $borrower = null)
    {
        $this->borrower = $borrower;
    
        return $this;
    }

    /**
     * Get borrower
     *
     * @return \Sudoux\EagleBundle\Entity\BorrowerFull 
     */
    public function getBorrower()
    {
        return $this->borrower;
    }

    /**
     * Set expense_housing
     *
     * @param \Sudoux\MortgageBundle\Entity\ExpenseHousing $expenseHousing
     * @return LoanApplicationFull
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
     * Set loan_officer
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanOfficer $loanOfficer
     * @return LoanApplicationFull
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

    /**
     * Set message_thread
     *
     * @param \Sudoux\Cms\MessageBundle\Entity\Thread $messageThread
     * @return LoanApplicationFull
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
     * Set milestone_group
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanMilestoneGroup $milestoneGroup
     * @return LoanApplicationFull
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
     * Set milestone
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanMilestone $milestone
     * @return LoanApplicationFull
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
}