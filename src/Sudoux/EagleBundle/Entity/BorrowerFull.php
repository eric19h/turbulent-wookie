<?php

namespace Sudoux\EagleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BorrowerFull
 * @package Sudoux\EagleBundle\Entity
 * @author Eric Haynes
 */
class BorrowerFull
{

    const SSN_PASSPHRASE = "d72e62a6efc36";

    public $property_ownership_types =  array(
        0 => 'Primary Residence',
        1 => 'Second Home',
        2 => 'Investment Property',
    );

    public $property_ownership_title_types = array(
        0 => 'Solely',
        1 => 'Jointly with Spouse',
        2 => 'Jointly with Another Person',
    );

    public $maritial_statuses = array(
        0 => 'Married',
        1 => 'Unmarried',
        2 => 'Separated'
    );

    public $ethnicities = array(
        0 => 'Hispanic or Latino',
        1 => 'Not Hispanic or Latino',
        2 => 'Not applicable',
    );

    public $races = array(
        0 => 'American Indian or Alaskan Native',
        1 => 'Asian',
        2 => 'Black or African American',
        3 => 'Native Hawaiian or Other Pac. Islander',
        4 => 'White',
        5 => 'Not applicable',
    );

    public $preferred_contact_times = array(
        0 => 'Morning',
        1 => 'Afternoon'
    );

    public $preferred_contact_methods = array(
        0 => 'Primary Phone',
        1 => 'Email'
    );

    public $citizen_statuses = array(
        0 => 'US',
        1 => 'Permanent Alien',
        2 => 'Non-Resident Alien'
    );


    public $living_statuses = array(
        0 => 'Own',
        1 => 'Rent',
        2 => 'Rent Free'
    );




    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $first_name;

    /**
     * @var string
     */
    private $last_name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $middle_name;

    /**
     * @var string
     */
    private $suffix;

    /**
     * @var string
     */
    private $phone_home;

    /**
     * @var string
     */
    private $phone_mobile;

    /**
     * @var string
     */
    private $ssn;

    /**
     * @var \DateTime
     */
    private $birth_date;

    /**
     * @var integer
     */
    private $years_of_school;

    /**
     * @var integer
     */
    private $marital_status;

    /**
     * @var integer
     */
    private $ethnicity;

    /**
     * @var integer
     */
    private $race;

    /**
     * @var boolean
     */
    private $is_male;

    /**
     * @var boolean
     */
    private $employed;

    /**
     * @var boolean
     */
    private $dependents;

    /**
     * @var integer
     */
    private $dependents_number;

    /**
     * @var string
     */
    private $dependents_ages;

    /**
     * @var boolean
     */
    private $govt_monitoring_opt_out;

    /**
     * @var boolean
     */
    private $declaration_outstanding_judgement;

    /**
     * @var string
     */
    private $declaration_outstanding_judgement_details;

    /**
     * @var boolean
     */
    private $declaration_bankruptcy;

    /**
     * @var string
     */
    private $declaration_bankruptcy_details;

    /**
     * @var boolean
     */
    private $declaration_forclosure;

    /**
     * @var string
     */
    private $declaration_forclosure_details;

    /**
     * @var boolean
     */
    private $declaration_lawsuit;

    /**
     * @var string
     */
    private $declaration_lawsuit_details;

    /**
     * @var boolean
     */
    private $declaration_forclosure_obligation;

    /**
     * @var string
     */
    private $declaration_forclosure_obligation_details;

    /**
     * @var boolean
     */
    private $declaration_in_default;

    /**
     * @var string
     */
    private $declaration_in_default_details;

    /**
     * @var boolean
     */
    private $declaration_alimony_child_support;

    /**
     * @var string
     */
    private $declaration_alimony_child_support_details;

    /**
     * @var integer
     */
    private $declaration_alimony_child_support_alimony;

    /**
     * @var integer
     */
    private $declaration_alimony_child_support_child_support;

    /**
     * @var integer
     */
    private $declaration_alimony_child_support_separate_maintenance;

    /**
     * @var boolean
     */
    private $declaration_down_payment_borrowed;

    /**
     * @var string
     */
    private $declaration_down_payment_borrowed_details;

    /**
     * @var boolean
     */
    private $declaration_note_endorser;

    /**
     * @var string
     */
    private $declaration_note_endorser_details;

    /**
     * @var boolean
     */
    private $declaration_us_citizen;

    /**
     * @var boolean
     */
    private $declaration_resident_alien;

    /**
     * @var boolean
     */
    private $declaration_primary_residence;

    /**
     * @var boolean
     */
    private $declaration_ownership_within_three_years;

    /**
     * @var integer
     */
    private $declaration_ownership_within_three_years_property_type;

    /**
     * @var integer
     */
    private $declaration_ownership_within_three_years_property_title;

    /**
     * @var string
     */
    private $initials;

    /**
     * @var string
     */
    private $signature;

    /**
     * @var string
     */
    private $los_id;

    /**
     * @var integer
     */
    private $citizen_status;

    /**
     * @var integer
     */
    private $preferred_contact_time;

    /**
     * @var integer
     */
    private $preferred_contact_method;

    /**
     * @var boolean
     */
    private $credit_report_authorized;

    /**
     * @var boolean
     */
    private $consent_to_contact;

    /**
     * @var boolean
     */
    private $electronic_delivery_consent;

    /**
     * @var boolean
     */
    private $consent_to_share_info;

    /**
     * @var \Sudoux\MortgageBundle\Entity\CreditReport
     */
    private $credit_report;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $loan;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $loan_coborrower;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $asset_account;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $asset_realestate;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $income_monthly;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $income_other;

    /**
     * @var \Sudoux\EagleBundle\Entity\BorrowerLocationFull
     */
    private $location;

    /**
     * @var \Sudoux\Cms\LocationBundle\Entity\Location
     */
    private $mailing_location;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $previous_location;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $employment;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->loan = new \Doctrine\Common\Collections\ArrayCollection();
        $this->loan_coborrower = new \Doctrine\Common\Collections\ArrayCollection();
        $this->asset_account = new \Doctrine\Common\Collections\ArrayCollection();
        $this->asset_realestate = new \Doctrine\Common\Collections\ArrayCollection();
        $this->income_monthly = new \Doctrine\Common\Collections\ArrayCollection();
        $this->income_other = new \Doctrine\Common\Collections\ArrayCollection();
        $this->previous_location = new \Doctrine\Common\Collections\ArrayCollection();
        $this->employment = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set first_name
     *
     * @param string $firstName
     * @return BorrowerFull
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;
    
        return $this;
    }

    /**
     * Get first_name
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set last_name
     *
     * @param string $lastName
     * @return BorrowerFull
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;
    
        return $this;
    }

    /**
     * Get last_name
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return BorrowerFull
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set middle_name
     *
     * @param string $middleName
     * @return BorrowerFull
     */
    public function setMiddleName($middleName)
    {
        $this->middle_name = $middleName;
    
        return $this;
    }

    /**
     * Get middle_name
     *
     * @return string 
     */
    public function getMiddleName()
    {
        return $this->middle_name;
    }

    /**
     * Set suffix
     *
     * @param string $suffix
     * @return BorrowerFull
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    
        return $this;
    }

    /**
     * Get suffix
     *
     * @return string 
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * Set phone_home
     *
     * @param string $phoneHome
     * @return BorrowerFull
     */
    public function setPhoneHome($phoneHome)
    {
        $this->phone_home = $phoneHome;
    
        return $this;
    }

    /**
     * Get phone_home
     *
     * @return string 
     */
    public function getPhoneHome()
    {
        return $this->phone_home;
    }

    /**
     * Set phone_mobile
     *
     * @param string $phoneMobile
     * @return BorrowerFull
     */
    public function setPhoneMobile($phoneMobile)
    {
        $this->phone_mobile = $phoneMobile;
    
        return $this;
    }

    /**
     * Get phone_mobile
     *
     * @return string 
     */
    public function getPhoneMobile()
    {
        return $this->phone_mobile;
    }

    /**
     * Set ssn
     *
     * @param string $ssn
     * @return BorrowerFull
     */
    public function setSsn($ssn)
    {
        $this->ssn = $ssn;
    
        return $this;
    }

    /**
     * Get ssn
     *
     * @return string 
     */
    public function getSsn()
    {
        return $this->ssn;
    }

    /**
     * Set birth_date
     *
     * @param \DateTime $birthDate
     * @return BorrowerFull
     */
    public function setBirthDate($birthDate)
    {
        $this->birth_date = $birthDate;
    
        return $this;
    }

    /**
     * Get birth_date
     *
     * @return \DateTime 
     */
    public function getBirthDate()
    {
        return $this->birth_date;
    }

    /**
     * Set years_of_school
     *
     * @param integer $yearsOfSchool
     * @return BorrowerFull
     */
    public function setYearsOfSchool($yearsOfSchool)
    {
        $this->years_of_school = $yearsOfSchool;
    
        return $this;
    }

    /**
     * Get years_of_school
     *
     * @return integer 
     */
    public function getYearsOfSchool()
    {
        return $this->years_of_school;
    }

    /**
     * Set marital_status
     *
     * @param integer $maritalStatus
     * @return BorrowerFull
     */
    public function setMaritalStatus($maritalStatus)
    {
        $this->marital_status = $maritalStatus;
    
        return $this;
    }

    /**
     * Get marital_status
     *
     * @return integer 
     */
    public function getMaritalStatus()
    {
        return $this->marital_status;
    }

    /**
     * Set ethnicity
     *
     * @param integer $ethnicity
     * @return BorrowerFull
     */
    public function setEthnicity($ethnicity)
    {
        $this->ethnicity = $ethnicity;
    
        return $this;
    }

    /**
     * Get ethnicity
     *
     * @return integer 
     */
    public function getEthnicity()
    {
        return $this->ethnicity;
    }

    /**
     * Set race
     *
     * @param integer $race
     * @return BorrowerFull
     */
    public function setRace($race)
    {
        $this->race = $race;
    
        return $this;
    }

    /**
     * Get race
     *
     * @return integer 
     */
    public function getRace()
    {
        return $this->race;
    }

    /**
     * Set is_male
     *
     * @param boolean $isMale
     * @return BorrowerFull
     */
    public function setIsMale($isMale)
    {
        $this->is_male = $isMale;
    
        return $this;
    }

    /**
     * Get is_male
     *
     * @return boolean 
     */
    public function getIsMale()
    {
        return $this->is_male;
    }

    /**
     * Set employed
     *
     * @param boolean $employed
     * @return BorrowerFull
     */
    public function setEmployed($employed)
    {
        $this->employed = $employed;
    
        return $this;
    }

    /**
     * Get employed
     *
     * @return boolean 
     */
    public function getEmployed()
    {
        return $this->employed;
    }

    /**
     * Set dependents
     *
     * @param boolean $dependents
     * @return BorrowerFull
     */
    public function setDependents($dependents)
    {
        $this->dependents = $dependents;
    
        return $this;
    }

    /**
     * Get dependents
     *
     * @return boolean 
     */
    public function getDependents()
    {
        return $this->dependents;
    }

    /**
     * Set dependents_number
     *
     * @param integer $dependentsNumber
     * @return BorrowerFull
     */
    public function setDependentsNumber($dependentsNumber)
    {
        $this->dependents_number = $dependentsNumber;
    
        return $this;
    }

    /**
     * Get dependents_number
     *
     * @return integer 
     */
    public function getDependentsNumber()
    {
        return $this->dependents_number;
    }

    /**
     * Set dependents_ages
     *
     * @param string $dependentsAges
     * @return BorrowerFull
     */
    public function setDependentsAges($dependentsAges)
    {
        $this->dependents_ages = $dependentsAges;
    
        return $this;
    }

    /**
     * Get dependents_ages
     *
     * @return string 
     */
    public function getDependentsAges()
    {
        return $this->dependents_ages;
    }

    /**
     * Set govt_monitoring_opt_out
     *
     * @param boolean $govtMonitoringOptOut
     * @return BorrowerFull
     */
    public function setGovtMonitoringOptOut($govtMonitoringOptOut)
    {
        $this->govt_monitoring_opt_out = $govtMonitoringOptOut;
    
        return $this;
    }

    /**
     * Get govt_monitoring_opt_out
     *
     * @return boolean 
     */
    public function getGovtMonitoringOptOut()
    {
        return $this->govt_monitoring_opt_out;
    }

    /**
     * Set declaration_outstanding_judgement
     *
     * @param boolean $declarationOutstandingJudgement
     * @return BorrowerFull
     */
    public function setDeclarationOutstandingJudgement($declarationOutstandingJudgement)
    {
        $this->declaration_outstanding_judgement = $declarationOutstandingJudgement;
    
        return $this;
    }

    /**
     * Get declaration_outstanding_judgement
     *
     * @return boolean 
     */
    public function getDeclarationOutstandingJudgement()
    {
        return $this->declaration_outstanding_judgement;
    }

    /**
     * Set declaration_outstanding_judgement_details
     *
     * @param string $declarationOutstandingJudgementDetails
     * @return BorrowerFull
     */
    public function setDeclarationOutstandingJudgementDetails($declarationOutstandingJudgementDetails)
    {
        $this->declaration_outstanding_judgement_details = $declarationOutstandingJudgementDetails;
    
        return $this;
    }

    /**
     * Get declaration_outstanding_judgement_details
     *
     * @return string 
     */
    public function getDeclarationOutstandingJudgementDetails()
    {
        return $this->declaration_outstanding_judgement_details;
    }

    /**
     * Set declaration_bankruptcy
     *
     * @param boolean $declarationBankruptcy
     * @return BorrowerFull
     */
    public function setDeclarationBankruptcy($declarationBankruptcy)
    {
        $this->declaration_bankruptcy = $declarationBankruptcy;
    
        return $this;
    }

    /**
     * Get declaration_bankruptcy
     *
     * @return boolean 
     */
    public function getDeclarationBankruptcy()
    {
        return $this->declaration_bankruptcy;
    }

    /**
     * Set declaration_bankruptcy_details
     *
     * @param string $declarationBankruptcyDetails
     * @return BorrowerFull
     */
    public function setDeclarationBankruptcyDetails($declarationBankruptcyDetails)
    {
        $this->declaration_bankruptcy_details = $declarationBankruptcyDetails;
    
        return $this;
    }

    /**
     * Get declaration_bankruptcy_details
     *
     * @return string 
     */
    public function getDeclarationBankruptcyDetails()
    {
        return $this->declaration_bankruptcy_details;
    }

    /**
     * Set declaration_forclosure
     *
     * @param boolean $declarationForclosure
     * @return BorrowerFull
     */
    public function setDeclarationForclosure($declarationForclosure)
    {
        $this->declaration_forclosure = $declarationForclosure;
    
        return $this;
    }

    /**
     * Get declaration_forclosure
     *
     * @return boolean 
     */
    public function getDeclarationForclosure()
    {
        return $this->declaration_forclosure;
    }

    /**
     * Set declaration_forclosure_details
     *
     * @param string $declarationForclosureDetails
     * @return BorrowerFull
     */
    public function setDeclarationForclosureDetails($declarationForclosureDetails)
    {
        $this->declaration_forclosure_details = $declarationForclosureDetails;
    
        return $this;
    }

    /**
     * Get declaration_forclosure_details
     *
     * @return string 
     */
    public function getDeclarationForclosureDetails()
    {
        return $this->declaration_forclosure_details;
    }

    /**
     * Set declaration_lawsuit
     *
     * @param boolean $declarationLawsuit
     * @return BorrowerFull
     */
    public function setDeclarationLawsuit($declarationLawsuit)
    {
        $this->declaration_lawsuit = $declarationLawsuit;
    
        return $this;
    }

    /**
     * Get declaration_lawsuit
     *
     * @return boolean 
     */
    public function getDeclarationLawsuit()
    {
        return $this->declaration_lawsuit;
    }

    /**
     * Set declaration_lawsuit_details
     *
     * @param string $declarationLawsuitDetails
     * @return BorrowerFull
     */
    public function setDeclarationLawsuitDetails($declarationLawsuitDetails)
    {
        $this->declaration_lawsuit_details = $declarationLawsuitDetails;
    
        return $this;
    }

    /**
     * Get declaration_lawsuit_details
     *
     * @return string 
     */
    public function getDeclarationLawsuitDetails()
    {
        return $this->declaration_lawsuit_details;
    }

    /**
     * Set declaration_forclosure_obligation
     *
     * @param boolean $declarationForclosureObligation
     * @return BorrowerFull
     */
    public function setDeclarationForclosureObligation($declarationForclosureObligation)
    {
        $this->declaration_forclosure_obligation = $declarationForclosureObligation;
    
        return $this;
    }

    /**
     * Get declaration_forclosure_obligation
     *
     * @return boolean 
     */
    public function getDeclarationForclosureObligation()
    {
        return $this->declaration_forclosure_obligation;
    }

    /**
     * Set declaration_forclosure_obligation_details
     *
     * @param string $declarationForclosureObligationDetails
     * @return BorrowerFull
     */
    public function setDeclarationForclosureObligationDetails($declarationForclosureObligationDetails)
    {
        $this->declaration_forclosure_obligation_details = $declarationForclosureObligationDetails;
    
        return $this;
    }

    /**
     * Get declaration_forclosure_obligation_details
     *
     * @return string 
     */
    public function getDeclarationForclosureObligationDetails()
    {
        return $this->declaration_forclosure_obligation_details;
    }

    /**
     * Set declaration_in_default
     *
     * @param boolean $declarationInDefault
     * @return BorrowerFull
     */
    public function setDeclarationInDefault($declarationInDefault)
    {
        $this->declaration_in_default = $declarationInDefault;
    
        return $this;
    }

    /**
     * Get declaration_in_default
     *
     * @return boolean 
     */
    public function getDeclarationInDefault()
    {
        return $this->declaration_in_default;
    }

    /**
     * Set declaration_in_default_details
     *
     * @param string $declarationInDefaultDetails
     * @return BorrowerFull
     */
    public function setDeclarationInDefaultDetails($declarationInDefaultDetails)
    {
        $this->declaration_in_default_details = $declarationInDefaultDetails;
    
        return $this;
    }

    /**
     * Get declaration_in_default_details
     *
     * @return string 
     */
    public function getDeclarationInDefaultDetails()
    {
        return $this->declaration_in_default_details;
    }

    /**
     * Set declaration_alimony_child_support
     *
     * @param boolean $declarationAlimonyChildSupport
     * @return BorrowerFull
     */
    public function setDeclarationAlimonyChildSupport($declarationAlimonyChildSupport)
    {
        $this->declaration_alimony_child_support = $declarationAlimonyChildSupport;
    
        return $this;
    }

    /**
     * Get declaration_alimony_child_support
     *
     * @return boolean 
     */
    public function getDeclarationAlimonyChildSupport()
    {
        return $this->declaration_alimony_child_support;
    }

    /**
     * Set declaration_alimony_child_support_details
     *
     * @param string $declarationAlimonyChildSupportDetails
     * @return BorrowerFull
     */
    public function setDeclarationAlimonyChildSupportDetails($declarationAlimonyChildSupportDetails)
    {
        $this->declaration_alimony_child_support_details = $declarationAlimonyChildSupportDetails;
    
        return $this;
    }

    /**
     * Get declaration_alimony_child_support_details
     *
     * @return string 
     */
    public function getDeclarationAlimonyChildSupportDetails()
    {
        return $this->declaration_alimony_child_support_details;
    }

    /**
     * Set declaration_alimony_child_support_alimony
     *
     * @param integer $declarationAlimonyChildSupportAlimony
     * @return BorrowerFull
     */
    public function setDeclarationAlimonyChildSupportAlimony($declarationAlimonyChildSupportAlimony)
    {
        $this->declaration_alimony_child_support_alimony = $declarationAlimonyChildSupportAlimony;
    
        return $this;
    }

    /**
     * Get declaration_alimony_child_support_alimony
     *
     * @return integer 
     */
    public function getDeclarationAlimonyChildSupportAlimony()
    {
        return $this->declaration_alimony_child_support_alimony;
    }

    /**
     * Set declaration_alimony_child_support_child_support
     *
     * @param integer $declarationAlimonyChildSupportChildSupport
     * @return BorrowerFull
     */
    public function setDeclarationAlimonyChildSupportChildSupport($declarationAlimonyChildSupportChildSupport)
    {
        $this->declaration_alimony_child_support_child_support = $declarationAlimonyChildSupportChildSupport;
    
        return $this;
    }

    /**
     * Get declaration_alimony_child_support_child_support
     *
     * @return integer 
     */
    public function getDeclarationAlimonyChildSupportChildSupport()
    {
        return $this->declaration_alimony_child_support_child_support;
    }

    /**
     * Set declaration_alimony_child_support_separate_maintenance
     *
     * @param integer $declarationAlimonyChildSupportSeparateMaintenance
     * @return BorrowerFull
     */
    public function setDeclarationAlimonyChildSupportSeparateMaintenance($declarationAlimonyChildSupportSeparateMaintenance)
    {
        $this->declaration_alimony_child_support_separate_maintenance = $declarationAlimonyChildSupportSeparateMaintenance;
    
        return $this;
    }

    /**
     * Get declaration_alimony_child_support_separate_maintenance
     *
     * @return integer 
     */
    public function getDeclarationAlimonyChildSupportSeparateMaintenance()
    {
        return $this->declaration_alimony_child_support_separate_maintenance;
    }

    /**
     * Set declaration_down_payment_borrowed
     *
     * @param boolean $declarationDownPaymentBorrowed
     * @return BorrowerFull
     */
    public function setDeclarationDownPaymentBorrowed($declarationDownPaymentBorrowed)
    {
        $this->declaration_down_payment_borrowed = $declarationDownPaymentBorrowed;
    
        return $this;
    }

    /**
     * Get declaration_down_payment_borrowed
     *
     * @return boolean 
     */
    public function getDeclarationDownPaymentBorrowed()
    {
        return $this->declaration_down_payment_borrowed;
    }

    /**
     * Set declaration_down_payment_borrowed_details
     *
     * @param string $declarationDownPaymentBorrowedDetails
     * @return BorrowerFull
     */
    public function setDeclarationDownPaymentBorrowedDetails($declarationDownPaymentBorrowedDetails)
    {
        $this->declaration_down_payment_borrowed_details = $declarationDownPaymentBorrowedDetails;
    
        return $this;
    }

    /**
     * Get declaration_down_payment_borrowed_details
     *
     * @return string 
     */
    public function getDeclarationDownPaymentBorrowedDetails()
    {
        return $this->declaration_down_payment_borrowed_details;
    }

    /**
     * Set declaration_note_endorser
     *
     * @param boolean $declarationNoteEndorser
     * @return BorrowerFull
     */
    public function setDeclarationNoteEndorser($declarationNoteEndorser)
    {
        $this->declaration_note_endorser = $declarationNoteEndorser;
    
        return $this;
    }

    /**
     * Get declaration_note_endorser
     *
     * @return boolean 
     */
    public function getDeclarationNoteEndorser()
    {
        return $this->declaration_note_endorser;
    }

    /**
     * Set declaration_note_endorser_details
     *
     * @param string $declarationNoteEndorserDetails
     * @return BorrowerFull
     */
    public function setDeclarationNoteEndorserDetails($declarationNoteEndorserDetails)
    {
        $this->declaration_note_endorser_details = $declarationNoteEndorserDetails;
    
        return $this;
    }

    /**
     * Get declaration_note_endorser_details
     *
     * @return string 
     */
    public function getDeclarationNoteEndorserDetails()
    {
        return $this->declaration_note_endorser_details;
    }

    /**
     * Set declaration_us_citizen
     *
     * @param boolean $declarationUsCitizen
     * @return BorrowerFull
     */
    public function setDeclarationUsCitizen($declarationUsCitizen)
    {
        $this->declaration_us_citizen = $declarationUsCitizen;
    
        return $this;
    }

    /**
     * Get declaration_us_citizen
     *
     * @return boolean 
     */
    public function getDeclarationUsCitizen()
    {
        return $this->declaration_us_citizen;
    }

    /**
     * Set declaration_resident_alien
     *
     * @param boolean $declarationResidentAlien
     * @return BorrowerFull
     */
    public function setDeclarationResidentAlien($declarationResidentAlien)
    {
        $this->declaration_resident_alien = $declarationResidentAlien;
    
        return $this;
    }

    /**
     * Get declaration_resident_alien
     *
     * @return boolean 
     */
    public function getDeclarationResidentAlien()
    {
        return $this->declaration_resident_alien;
    }

    /**
     * Set declaration_primary_residence
     *
     * @param boolean $declarationPrimaryResidence
     * @return BorrowerFull
     */
    public function setDeclarationPrimaryResidence($declarationPrimaryResidence)
    {
        $this->declaration_primary_residence = $declarationPrimaryResidence;
    
        return $this;
    }

    /**
     * Get declaration_primary_residence
     *
     * @return boolean 
     */
    public function getDeclarationPrimaryResidence()
    {
        return $this->declaration_primary_residence;
    }

    /**
     * Set declaration_ownership_within_three_years
     *
     * @param boolean $declarationOwnershipWithinThreeYears
     * @return BorrowerFull
     */
    public function setDeclarationOwnershipWithinThreeYears($declarationOwnershipWithinThreeYears)
    {
        $this->declaration_ownership_within_three_years = $declarationOwnershipWithinThreeYears;
    
        return $this;
    }

    /**
     * Get declaration_ownership_within_three_years
     *
     * @return boolean 
     */
    public function getDeclarationOwnershipWithinThreeYears()
    {
        return $this->declaration_ownership_within_three_years;
    }

    /**
     * Set declaration_ownership_within_three_years_property_type
     *
     * @param integer $declarationOwnershipWithinThreeYearsPropertyType
     * @return BorrowerFull
     */
    public function setDeclarationOwnershipWithinThreeYearsPropertyType($declarationOwnershipWithinThreeYearsPropertyType)
    {
        $this->declaration_ownership_within_three_years_property_type = $declarationOwnershipWithinThreeYearsPropertyType;
    
        return $this;
    }

    /**
     * Get declaration_ownership_within_three_years_property_type
     *
     * @return integer 
     */
    public function getDeclarationOwnershipWithinThreeYearsPropertyType()
    {
        return $this->declaration_ownership_within_three_years_property_type;
    }

    /**
     * Set declaration_ownership_within_three_years_property_title
     *
     * @param integer $declarationOwnershipWithinThreeYearsPropertyTitle
     * @return BorrowerFull
     */
    public function setDeclarationOwnershipWithinThreeYearsPropertyTitle($declarationOwnershipWithinThreeYearsPropertyTitle)
    {
        $this->declaration_ownership_within_three_years_property_title = $declarationOwnershipWithinThreeYearsPropertyTitle;
    
        return $this;
    }

    /**
     * Get declaration_ownership_within_three_years_property_title
     *
     * @return integer 
     */
    public function getDeclarationOwnershipWithinThreeYearsPropertyTitle()
    {
        return $this->declaration_ownership_within_three_years_property_title;
    }

    /**
     * Set initials
     *
     * @param string $initials
     * @return BorrowerFull
     */
    public function setInitials($initials)
    {
        $this->initials = $initials;
    
        return $this;
    }

    /**
     * Get initials
     *
     * @return string 
     */
    public function getInitials()
    {
        return $this->initials;
    }

    /**
     * Set signature
     *
     * @param string $signature
     * @return BorrowerFull
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
    
        return $this;
    }

    /**
     * Get signature
     *
     * @return string 
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Set los_id
     *
     * @param string $losId
     * @return BorrowerFull
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
     * Set citizen_status
     *
     * @param integer $citizenStatus
     * @return BorrowerFull
     */
    public function setCitizenStatus($citizenStatus)
    {
        $this->citizen_status = $citizenStatus;
    
        return $this;
    }

    /**
     * Get citizen_status
     *
     * @return integer 
     */
    public function getCitizenStatus()
    {
        return $this->citizen_status;
    }

    /**
     * Set preferred_contact_time
     *
     * @param integer $preferredContactTime
     * @return BorrowerFull
     */
    public function setPreferredContactTime($preferredContactTime)
    {
        $this->preferred_contact_time = $preferredContactTime;
    
        return $this;
    }

    /**
     * Get preferred_contact_time
     *
     * @return integer 
     */
    public function getPreferredContactTime()
    {
        return $this->preferred_contact_time;
    }

    /**
     * Set preferred_contact_method
     *
     * @param integer $preferredContactMethod
     * @return BorrowerFull
     */
    public function setPreferredContactMethod($preferredContactMethod)
    {
        $this->preferred_contact_method = $preferredContactMethod;
    
        return $this;
    }

    /**
     * Get preferred_contact_method
     *
     * @return integer 
     */
    public function getPreferredContactMethod()
    {
        return $this->preferred_contact_method;
    }

    /**
     * Set credit_report_authorized
     *
     * @param boolean $creditReportAuthorized
     * @return BorrowerFull
     */
    public function setCreditReportAuthorized($creditReportAuthorized)
    {
        $this->credit_report_authorized = $creditReportAuthorized;
    
        return $this;
    }

    /**
     * Get credit_report_authorized
     *
     * @return boolean 
     */
    public function getCreditReportAuthorized()
    {
        return $this->credit_report_authorized;
    }

    /**
     * Set consent_to_contact
     *
     * @param boolean $consentToContact
     * @return BorrowerFull
     */
    public function setConsentToContact($consentToContact)
    {
        $this->consent_to_contact = $consentToContact;
    
        return $this;
    }

    /**
     * Get consent_to_contact
     *
     * @return boolean 
     */
    public function getConsentToContact()
    {
        return $this->consent_to_contact;
    }

    /**
     * Set electronic_delivery_consent
     *
     * @param boolean $electronicDeliveryConsent
     * @return BorrowerFull
     */
    public function setElectronicDeliveryConsent($electronicDeliveryConsent)
    {
        $this->electronic_delivery_consent = $electronicDeliveryConsent;
    
        return $this;
    }

    /**
     * Get electronic_delivery_consent
     *
     * @return boolean 
     */
    public function getElectronicDeliveryConsent()
    {
        return $this->electronic_delivery_consent;
    }

    /**
     * Set consent_to_share_info
     *
     * @param boolean $consentToShareInfo
     * @return BorrowerFull
     */
    public function setConsentToShareInfo($consentToShareInfo)
    {
        $this->consent_to_share_info = $consentToShareInfo;
    
        return $this;
    }

    /**
     * Get consent_to_share_info
     *
     * @return boolean 
     */
    public function getConsentToShareInfo()
    {
        return $this->consent_to_share_info;
    }

    /**
     * Set credit_report
     *
     * @param \Sudoux\MortgageBundle\Entity\CreditReport $creditReport
     * @return BorrowerFull
     */
    public function setCreditReport(\Sudoux\MortgageBundle\Entity\CreditReport $creditReport = null)
    {
        $this->credit_report = $creditReport;
    
        return $this;
    }

    /**
     * Get credit_report
     *
     * @return \Sudoux\MortgageBundle\Entity\CreditReport 
     */
    public function getCreditReport()
    {
        return $this->credit_report;
    }

    /**
     * Add loan
     *
     * @param \Sudoux\EagleBundle\Entity\LoanApplicationFull $loan
     * @return BorrowerFull
     */
    public function addLoan(\Sudoux\EagleBundle\Entity\LoanApplicationFull $loan)
    {
        $this->loan[] = $loan;
    
        return $this;
    }

    /**
     * Remove loan
     *
     * @param \Sudoux\EagleBundle\Entity\LoanApplicationFull $loan
     */
    public function removeLoan(\Sudoux\EagleBundle\Entity\LoanApplicationFull $loan)
    {
        $this->loan->removeElement($loan);
    }

    /**
     * Get loan
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLoan()
    {
        return $this->loan;
    }

    /**
     * Add loan_coborrower
     *
     * @param \Sudoux\EagleBundle\Entity\LoanApplicationFull $loanCoborrower
     * @return BorrowerFull
     */
    public function addLoanCoborrower(\Sudoux\EagleBundle\Entity\LoanApplicationFull $loanCoborrower)
    {
        $this->loan_coborrower[] = $loanCoborrower;
    
        return $this;
    }

    /**
     * Remove loan_coborrower
     *
     * @param \Sudoux\EagleBundle\Entity\LoanApplicationFull $loanCoborrower
     */
    public function removeLoanCoborrower(\Sudoux\EagleBundle\Entity\LoanApplicationFull $loanCoborrower)
    {
        $this->loan_coborrower->removeElement($loanCoborrower);
    }

    /**
     * Get loan_coborrower
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLoanCoborrower()
    {
        return $this->loan_coborrower;
    }

    /**
     * Add asset_account
     *
     * @param \Sudoux\MortgageBundle\Entity\AssetAccount $assetAccount
     * @return BorrowerFull
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
     * Add asset_realestate
     *
     * @param \Sudoux\MortgageBundle\Entity\AssetRealEstate $assetRealestate
     * @return BorrowerFull
     */
    public function addAssetRealestate(\Sudoux\MortgageBundle\Entity\AssetRealEstate $assetRealestate)
    {
        $this->asset_realestate[] = $assetRealestate;
    
        return $this;
    }

    /**
     * Remove asset_realestate
     *
     * @param \Sudoux\MortgageBundle\Entity\AssetRealEstate $assetRealestate
     */
    public function removeAssetRealestate(\Sudoux\MortgageBundle\Entity\AssetRealEstate $assetRealestate)
    {
        $this->asset_realestate->removeElement($assetRealestate);
    }

    /**
     * Get asset_realestate
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAssetRealestate()
    {
        return $this->asset_realestate;
    }

    /**
     * Add income_monthly
     *
     * @param \Sudoux\MortgageBundle\Entity\IncomeMonthly $incomeMonthly
     * @return BorrowerFull
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

    /**
     * Add income_other
     *
     * @param \Sudoux\MortgageBundle\Entity\IncomeOther $incomeOther
     * @return BorrowerFull
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
     * Set location
     *
     * @param \Sudoux\EagleBundle\Entity\BorrowerLocationFull $location
     * @return BorrowerFull
     */
    public function setLocation(\Sudoux\EagleBundle\Entity\BorrowerLocationFull $location)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return \Sudoux\EagleBundle\Entity\BorrowerLocationFull 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set mailing_location
     *
     * @param \Sudoux\Cms\LocationBundle\Entity\Location $mailingLocation
     * @return BorrowerFull
     */
    public function setMailingLocation(\Sudoux\Cms\LocationBundle\Entity\Location $mailingLocation = null)
    {
        $this->mailing_location = $mailingLocation;
    
        return $this;
    }

    /**
     * Get mailing_location
     *
     * @return \Sudoux\Cms\LocationBundle\Entity\Location 
     */
    public function getMailingLocation()
    {
        return $this->mailing_location;
    }

    /**
     * Add previous_location
     *
     * @param \Sudoux\EagleBundle\Entity\BorrowerLocationFull $previousLocation
     * @return BorrowerFull
     */
    public function addPreviousLocation(\Sudoux\EagleBundle\Entity\BorrowerLocationFull $previousLocation)
    {
        $this->previous_location[] = $previousLocation;
    
        return $this;
    }

    /**
     * Remove previous_location
     *
     * @param \Sudoux\EagleBundle\Entity\BorrowerLocationFull $previousLocation
     */
    public function removePreviousLocation(\Sudoux\EagleBundle\Entity\BorrowerLocationFull $previousLocation)
    {
        $this->previous_location->removeElement($previousLocation);
    }

    /**
     * Get previous_location
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPreviousLocation()
    {
        return $this->previous_location;
    }

    /**
     * Add employment
     *
     * @param \Sudoux\MortgageBundle\Entity\Employment $employment
     * @return BorrowerFull
     */
    public function addEmployment(\Sudoux\MortgageBundle\Entity\Employment $employment)
    {
        $this->employment[] = $employment;
    
        return $this;
    }

    /**
     * Remove employment
     *
     * @param \Sudoux\MortgageBundle\Entity\Employment $employment
     */
    public function removeEmployment(\Sudoux\MortgageBundle\Entity\Employment $employment)
    {
        $this->employment->removeElement($employment);
    }

    /**
     * Get employment
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEmployment()
    {
        return $this->employment;
    }
    /**
     * @var integer
     */
    private $living_status;


    /**
     * Set living_status
     *
     * @param integer $livingStatus
     * @return BorrowerFull
     */
    public function setLivingStatus($livingStatus)
    {
        $this->living_status = $livingStatus;
    
        return $this;
    }

    /**
     * Get living_status
     *
     * @return integer 
     */
    public function getLivingStatus()
    {
        return $this->living_status;
    }
}