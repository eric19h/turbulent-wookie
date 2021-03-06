<?php

namespace Sudoux\EagleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Sudoux\Cms\LocationBundle\Entity\Location;
use Sudoux\Cms\TaxonomyBundle\Entity\Taxonomy;
use Sudoux\Cms\UserBundle\Entity\User;
use Sudoux\MortgageBundle\Entity\ExpenseHousing;

/**
 * Class EagleLoanApplication
 * @package Sudoux\EagleBundle\Entity
 * @author Eric Haynes
 * @ExclusionPolicy("all")
 */
class EagleLoanApplication
{
    ##################################################
    ##Public Properties
    ##
    ###################################################

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

    ##################################################
    ##Private Properties
    ##
    ###################################################

    /**
     * @var integer
     * @Expose()
     */
    private $id;

    /**
     * @var guid
     * @Expose()
     */
    private $guid;

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
     * @var string
     * @Expose()
     */
    private $los_id;

    /**
     * @var string
     * @Expose()
     */
    private $los_loan_number;

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
    private $property_year_built;

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
     * @var string
     * @Expose()
     */
    private $title_company3;

    /**
     * @var string
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
     * @var boolean
     * @Expose()
     */
    private $is_prequal;

    /**
     * @var integer
     * @Expose()
     */
    private $refinance_year_acquired;

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
     * @var string
     * @Expose()
     */
    private $refinance_current_lender;

    /**
     * @var integer
     * @Expose()
     */
    private $refinance_purpose;

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
     * @var string
     * @Expose()
     */
    private $comments;

    /**
     * @var boolean
     * @Expose()
     */
    private $completed;

    /**
     * @var \DateTime
     * @Expose()
     */
    private $completed_date;

    /**
     * @var integer
     * @Expose()
     */
    private $lock_status;

    /**
     * @var integer
     * @Expose()
     */
    private $last_step_completed;

    /**
     * @var integer
     * @Expose()
     */
    private $status;

    /**
     * @var \DateTime
     * @Expose()
     */
    private $status_date;

    /**
     * @var boolean
     * @Accessor(getter="getDeleted",setter="setDeleted")
     * @Expose()
     *
     */
    private $deleted;

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
     * @var \DateTime
     * @Expose()
     */
    private $los_modified;

    /**
     * @var boolean
     * @Expose()
     */
    private $sent_to_los;

    /**
     * @var integer
     * @Expose()
     */
    private $source;

    /**
     * @var boolean
     * @Expose()
     */
    private $no_property_location;

    /**
     * @var boolean
     * @Expose()
     */
    private $is_lennar_home;

    /**
     * @var integer
     * @Expose()
     */
    private $loan_purpose;

    /**
     * @var boolean
     * @Expose()
     */
    private $need_to_sell;

    /**
     * @var integer
     * @Expose()
     */
    private $rent_own_status;

    /**
     * @var boolean
     * @Expose()
     */
    private $are_joint_borrowers;

    /**
     * @var float
     * @Expose()
     */
    private $down_payment_amount;

    /**
     * @var integer
     * @Expose()
     */
    private $down_payment_source;

    /**
     * @var string
     * @Expose()
     */
    private $lennar_community_name;

    /**
     * @var string
     * @Expose()
     */
    private $lennar_builder_name;

    /**
     * @var boolean
     * @Expose()
     */
    private $has_communities;

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
     * @var \Sudoux\Cms\UserBundle\Entity\User
     * @Expose()
     */
    private $admin_user;

    /**
     * @var \Sudoux\Cms\SiteBundle\Entity\Site
     * @Expose()
     */
    private $site;

    /**
     * @var \Sudoux\EagleBundle\Entity\EagleBorrower
     * @Expose()
     */
    private $borrower;

    /**
     * @var \Sudoux\MortgageBundle\Entity\ExpenseHousing
     * @Expose()
     */
    private $expense_housing;

    /**
     * @var \Sudoux\MortgageBundle\Entity\LoanOfficer
     * @Expose()
     */
    private $loan_officer;

    /**
     * @var \Sudoux\Cms\MessageBundle\Entity\Thread
     * @Expose()
     */
    private $message_thread;

    /**
     * @var \Sudoux\MortgageBundle\Entity\LoanMilestoneGroup
     * @Expose()
     */
    private $milestone_group;

    /**
     * @var \Sudoux\MortgageBundle\Entity\LoanMilestone
     * @Expose()
     */
    private $milestone;


    ##################################################
    ##Private Array Collections
    ##
    ###################################################

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $referral_source;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $client_user;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $additional_site;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $pricing_scenario;

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
     * @var  \Doctrine\Common\Collections\Collection
     * @author Eric Haynes
     * @Expose()
     */
    private $email;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $document;



    ##################################################
    ##Functions
    ##
    ###################################################


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->is_prequal = false;
        $this->borrower = new EagleBorrower();
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
     * @author Eric Haynes
     */
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

    /**
     * @param \Sudoux\Cms\TaxonomyBundle\Entity\Taxonomy $taxonomy
     * @param null $status
     * @return bool
     * @author Eric Haynes
     */
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
     * @author Eric Haynes
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

    /**
     * @param \Sudoux\Cms\UserBundle\Entity\User $user
     * @return array
     * @author Eric Haynes
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
     * @return bool
     * @author Eric Haynes
     */
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
     * Set completed
     *
     * @param boolean $completed
     * @return EagleLoanApplication
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
     * @return int
     * @author Eric Haynes
     */
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
     * @param \Sudoux\Cms\UserBundle\Entity\User $user
     * @return string
     * @author Eric Haynes
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
     * @param \Sudoux\Cms\UserBundle\Entity\User $user
     * @return bool
     * @author Eric Haynes
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


    public function preUpdate()
    {
        $this->modified = new \DateTime();
        if($this->no_property_location) {
            $this->removePropertyData();
        }
    }


    ##################################################
    ##Getters and Setters
    ##Adders and Removers
    ###################################################


    /**
     * @return int
     * @author Eric Haynes
     */
    public function getLoanPurposeText()
    {

        if(isset($this->loan_purpose)) {
            return $this->loanPurposes[$this->loan_purpose];
        }

        return $this->loan_purpose;
    }

    /**
     * @return int
     * @author Eric Haynes
     */
    public function getLoanTypeText()
    {

        if(isset($this->loan_type)) {
            return $this->loan_type[$this->loan_type];
        }

        return $this->loan_type;
    }

    /**
     * @return int
     * @author Eric Haynes
     */
    public function getPropertyTypeText()
    {
        if(isset($this->property_type)) {
            return $this->propertyTypes[$this->property_type];
        }

        return $this->property_type;
    }

    /**
     * @return string
     * @author Eric Haynes
     */
    public function getTitleMannerText()
    {
        if(isset($this->title_manner)) {
            return $this->titleManners[$this->title_manner];
        }

        return $this->title_manner;
    }

    /**
     * @return null
     * @author Eric Haynes
     */
    public function getResidencyTypeText()
    {
        if(isset($this->residency_type)) {
            return $this->residencyTypes[$this->residency_type];
        }

        return null;
    }

    /**
     * @return float|int
     * @author Eric Haynes
     */
    public function getLoanTermsFromMonthsToYears()
    {
        if(isset($this->loan_term)) {
            $this->loan_term = ($this->loan_term / 12);
        }

        return $this->loan_term;
    }


    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @author Eric Haynes
     */
    public function getIncomeMonthly()
    {
        return $this->income_monthly;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @author Eric Haynes
     */
    public function getCoBorrower()
    {
        return $this->co_borrower;
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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

        return $this->refinance_purpose;

    }


    /**
     * Set agreement_one
     *
     * @param boolean $agreementOne
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
     */
    public function setStatus($status)
    {
        $this->status = $status;

        $this->status_date = new \DateTime();
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * Set created
     *
     * @param \DateTime $created
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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

    /**
     * Set sent_to_los
     *
     * @param boolean $sentToLos
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @param \Sudoux\EagleBundle\Entity\EagleBorrower|null $borrower
     * @return $this
     * @author Eric Haynes
     */
    public function setBorrower(\Sudoux\EagleBundle\Entity\EagleBorrower $borrower = null)
    {
        $this->borrower = $borrower;

        return $this;
    }

    /**
     * Get borrower
     *
     * @return \Sudoux\EagleBundle\Entity\EagleBorrower
     */
    public function getBorrower()
    {
        return $this->borrower;
    }

    /**
     * Set expense_housing
     *
     * @param \Sudoux\MortgageBundle\Entity\ExpenseHousing $expenseHousing
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @return EagleLoanApplication
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
     * @param \Sudoux\EagleBundle\Entity\EagleIncomeMonthly|null $incomeMonthly
     * @return $this
     * @author Eric Haynes
     */
    public function setIncomeMonthly(\Sudoux\EagleBundle\Entity\EagleIncomeMonthly $incomeMonthly = null)
    {
        $this->income_monthly = $incomeMonthly;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @author Eric Haynes
     */
    public function getIncomeOther()
    {
        return $this->income_other;
    }



    /**
     * @param \Sudoux\EagleBundle\Entity\EagleBorrower $borrower
     * @author Eric Haynes
     */
    public function removeIncomeMonthlyByBorrower(\Sudoux\EagleBundle\Entity\EagleBorrower $borrower)
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
     * @param \Sudoux\EagleBundle\Entity\EagleIncomeMonthly $incomeMonthly
     * @author Eric Haynes
     */
    public function removeIncomeMonthly(\Sudoux\EagleBundle\Entity\EagleIncomeMonthly $incomeMonthly)
    {
        $this->income_monthly->removeElement($incomeMonthly);
    }

    /**
     * @param \Sudoux\EagleBundle\Entity\EagleBorrower $coBorrower
     * @return $this
     * @author Eric Haynes
     */
    public function addCoBorrower(\Sudoux\EagleBundle\Entity\EagleBorrower $coBorrower)
    {
        $this->co_borrower[] = $coBorrower;

        return $this;
    }

    /**
     * @param \Sudoux\EagleBundle\Entity\EagleBorrower $coBorrower
     * @author Eric Haynes
     */
    public function removeCoBorrower(\Sudoux\EagleBundle\Entity\EagleBorrower $coBorrower)
    {
        $this->co_borrower->removeElement($coBorrower);
    }



    /**
     * @param \Sudoux\EagleBundle\Entity\EagleAssetAccount $assetAccount
     * @return $this
     * @author Eric Haynes
     */
    public function addAssetAccount(\Sudoux\EagleBundle\Entity\EagleAssetAccount $assetAccount)
    {
        $this->asset_account[] = $assetAccount;

        return $this;
    }

    /**
     * @param \Sudoux\EagleBundle\Entity\EagleAssetAccount $assetAccount
     * @author Eric Haynes
     */
    public function removeAssetAccount(\Sudoux\EagleBundle\Entity\EagleAssetAccount $assetAccount)
    {
        $this->asset_account->removeElement($assetAccount);
    }

    public function removeAllAssetAccount()
    {
        if(count($this->asset_account) > 0) {
            $this->asset_account->clear();
        }
    }

    /**
     * @param \Sudoux\EagleBundle\Entity\EagleBorrower $borrower
     * @author Eric Haynes
     */
    public function removeAllAssetAccountByBorrower(EagleBorrower $borrower)
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
     * @param \Sudoux\EagleBundle\Entity\EagleAssetRealEstate $assetRealEstate
     * @return $this
     * @author Eric Haynes
     */
    public function addAssetRealEstate(\Sudoux\EagleBundle\Entity\EagleAssetRealEstate $assetRealEstate)
    {
        $this->asset_real_estate[] = $assetRealEstate;

        return $this;
    }

    /**
     * @param \Sudoux\EagleBundle\Entity\EagleAssetRealEstate $assetRealEstate
     * @author Eric Haynes
     */
    public function removeAssetRealEstate(\Sudoux\EagleBundle\Entity\EagleAssetRealEstate $assetRealEstate)
    {
        $this->asset_real_estate->removeElement($assetRealEstate);
    }

    /**
     * @param \Sudoux\EagleBundle\Entity\EagleBorrower $borrower
     * @author Eric Haynes
     */
    public function removeAllAssetRealEstateByBorrower(EagleBorrower $borrower)
    {
        if(count($this->asset_real_estate) > 0) {
            foreach($this->asset_real_estate as $asset) {
                if($borrower->getId() == $asset->getBorrower()->getId()) {
                    $this->removeAssetRealEstate($asset);
                }
            }
        }
    }

    /**
     * @author Eric Haynes
     */
    public function removeAllAssetRealEstate()
    {
        if(count($this->asset_real_estate) > 0) {
            $this->asset_real_estate->clear();
        }
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     * @author Eric Haynes
     */
    public function getAssetRealEstate()
    {
        return $this->asset_real_estate;
    }

    /**
     * @param \Sudoux\EagleBundle\Entity\EagleIncomeMonthly $incomeMonthly
     * @return $this
     * @author Eric Haynes
     */
    public function addIncomeMonthly(\Sudoux\EagleBundle\Entity\EagleIncomeMonthly $incomeMonthly)
    {
        $this->income_monthly[] = $incomeMonthly;

        return $this;
    }

    /**
     * @param \Sudoux\EagleBundle\Entity\EagleIncomeOther $incomeOther
     * @return $this
     * @author Eric Haynes
     */
    public function addIncomeOther(\Sudoux\EagleBundle\Entity\EagleIncomeOther $incomeOther)
    {
        $this->income_other[] = $incomeOther;

        return $this;
    }

    /**
     * @param \Sudoux\EagleBundle\Entity\EagleIncomeOther $incomeOther
     * @author Eric Haynes
     */
    public function removeIncomeOther(\Sudoux\EagleBundle\Entity\EagleIncomeOther $incomeOther)
    {
        $this->income_other->removeElement($incomeOther);
    }

    /**
     * @param \Sudoux\EagleBundle\Entity\EagleLoanDocument $document
     * @return $this
     * @author Eric Haynes
     */
    public function addDocument(\Sudoux\EagleBundle\Entity\EagleLoanDocument $document)
    {
        $this->document[] = $document;

        return $this;
    }

    /**
     * @param \Sudoux\EagleBundle\Entity\EagleLoanDocument $document
     * @author Eric Haynes
     */
    public function removeDocument(\Sudoux\EagleBundle\Entity\EagleLoanDocument $document)
    {
        $this->document->removeElement($document);
    }

    /**
     * @param \Sudoux\Cms\MessageBundle\Entity\Email $email
     * @return $this
     * @author Eric Haynes
     */
    public function addEmail(\Sudoux\Cms\MessageBundle\Entity\Email $email)
    {
        $this->email[] = $email;

        return $this;
    }

    /**
     * @param \Sudoux\Cms\MessageBundle\Entity\Email $email
     * @author Eric Haynes
     */
    public function removeEmail(\Sudoux\Cms\MessageBundle\Entity\Email $email)
    {
        $this->email->removeElement($email);
    }


    /**
     * Add additional_site
     *
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $additionalSite
     * @return EagleLoanApplication
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
     * @param \Sudoux\Cms\UserBundle\Entity\User $clientUser
     * @author Eric Haynes
     */
    public function setClientUser(\Sudoux\Cms\UserBundle\Entity\User $clientUser)
    {
        $this->addClientUser($clientUser);
    }

    /**
     * @param \Sudoux\Cms\UserBundle\Entity\User $clientUser
     * @return $this
     * @author Eric Haynes
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
     * @param \Sudoux\MortgageBundle\Entity\PricingScenario $pricingScenario
     * @return $this
     * @author Eric Haynes
     */
    public function addPricingScenario(\Sudoux\MortgageBundle\Entity\PricingScenario $pricingScenario)
    {
        $this->pricing_scenario[] = $pricingScenario;

        return $this;
    }

    /**
     * @param \Sudoux\MortgageBundle\Entity\PricingScenario $pricingScenario
     * @author Eric Haynes
     */
    public function removePricingScenario(\Sudoux\MortgageBundle\Entity\PricingScenario $pricingScenario)
    {
        $this->pricing_scenario->removeElement($pricingScenario);
    }

    /**
     * @author Eric Haynes
     */
    public function removeAllPricingScenarios()
    {
        $this->pricing_scenario->clear();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     * @author Eric Haynes
     */
    public function getPricingScenario()
    {
        return $this->pricing_scenario;
    }

    /**
     * @param \Sudoux\Cms\FormBundle\Entity\ReferralSource $referralSource
     * @return $this
     * @author Eric Haynes
     */
    public function addReferralSource(\Sudoux\Cms\FormBundle\Entity\ReferralSource $referralSource)
    {
        $this->referral_source[] = $referralSource;

        return $this;
    }

    /**
     * @param \Sudoux\Cms\FormBundle\Entity\ReferralSource $referralSource
     * @author Eric Haynes
     */
    public function removeReferralSource(\Sudoux\Cms\FormBundle\Entity\ReferralSource $referralSource)
    {
        $this->referral_source->removeElement($referralSource);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     * @author Eric Haynes
     */
    public function getReferralSource()
    {
        return $this->referral_source;
    }

}