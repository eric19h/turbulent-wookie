<?php

namespace Sudoux\EagleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Sudoux\Cms\SecurityBundle\DependencyInjection\EncryptionUtil;

/**
 * Class EagleBorrower
 * @package Sudoux\EagleBundle\Entity
 * @author Eric Haynes
 * @ExclusionPolicy("all")
 */
class EagleBorrower
{

    const SSN_PASSPHRASE = "d72e62a6efc36";

    ##################################################
    ##public properties
    ##
    ###################################################

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

    ##################################################
    ##private properties
    ##
    ###################################################


    /**
     * @var integer
     * @Expose()
     */
    private $id;

    /**
     * @var string
     * @Expose()
     */
    private $first_name;

    /**
     * @var string
     * @Expose()
     */
    private $last_name;

    /**
     * @var string
     * @Expose()
     */
    private $email;

    /**
     * @var string
     * @Expose()
     */
    private $middle_name;

    /**
     * @var string
     * @Expose()
     */
    private $suffix;

    /**
     * @var string
     * @Expose()
     */
    private $phone_home;

    /**
     * @var string
     * @Expose()
     */
    private $phone_mobile;

    /**
     * @var string
     * @Accessor(getter="getSsn",setter="setSsn")
     * @Expose()
     */
    private $ssn;

    /**
     * @var \DateTime
     * @Expose()
     */
    private $birth_date;

    /**
     * @var integer
     * @Expose()
     */
    private $years_of_school;

    /**
     * @var integer
     * @Expose()
     */
    private $marital_status;

    /**
     * @var integer
     * @Expose()
     */
    private $ethnicity;

    /**
     * @var integer
     * @Expose()
     */
    private $race;

    /**
     * @var boolean
     * @Expose()
     */
    private $is_male;

    /**
     * @var boolean
     * @Expose()
     */
    private $employed;

    /**
     * @var boolean
     * @Expose()
     */
    private $dependents;

    /**
     * @var integer
     * @Expose()
     */
    private $dependents_number;

    /**
     * @var string
     * @Expose()
     */
    private $dependents_ages;

    /**
     * @var boolean
     * @Expose()
     */
    private $govt_monitoring_opt_out;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_outstanding_judgement;

    /**
     * @var string
     * @Expose()
     */
    private $declaration_outstanding_judgement_details;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_bankruptcy;

    /**
     * @var string
     * @Expose()
     */
    private $declaration_bankruptcy_details;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_forclosure;

    /**
     * @var string
     * @Expose()
     */
    private $declaration_forclosure_details;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_lawsuit;

    /**
     * @var string
     * @Expose()
     */
    private $declaration_lawsuit_details;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_forclosure_obligation;

    /**
     * @var string
     * @Expose()
     */
    private $declaration_forclosure_obligation_details;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_in_default;

    /**
     * @var string
     * @Expose()
     */
    private $declaration_in_default_details;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_alimony_child_support;

    /**
     * @var string
     * @Expose()
     */
    private $declaration_alimony_child_support_details;

    /**
     * @var integer
     * @Expose()
     */
    private $declaration_alimony_child_support_alimony;

    /**
     * @var integer
     * @Expose()
     */
    private $declaration_alimony_child_support_child_support;

    /**
     * @var integer
     * @Expose()
     */
    private $declaration_alimony_child_support_separate_maintenance;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_down_payment_borrowed;

    /**
     * @var string
     * @Expose()
     */
    private $declaration_down_payment_borrowed_details;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_note_endorser;

    /**
     * @var string
     * @Expose()
     */
    private $declaration_note_endorser_details;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_us_citizen;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_resident_alien;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_primary_residence;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_ownership_within_three_years;

    /**
     * @var integer
     * @Expose()
     */
    private $declaration_ownership_within_three_years_property_type;

    /**
     * @var integer
     * @Expose()
     */
    private $declaration_ownership_within_three_years_property_title;

    /**
     * @var string
     * @Expose()
     */
    private $initials;

    /**
     * @var string
     * @Expose()
     */
    private $signature;

    /**
     * @var string
     * @Expose()
     */
    private $los_id;

    /**
     * @var integer
     * @Expose()
     */
    private $citizen_status;

    /**
     * @var integer
     * @Expose()
     */
    private $preferred_contact_time;

    /**
     * @var integer
     * @Expose()
     */
    private $preferred_contact_method;

    /**
     * @var boolean
     * @Expose()
     */
    private $credit_report_authorized;

    /**
     * @var boolean
     * @Expose()
     */
    private $consent_to_contact;

    /**
     * @var boolean
     * @Expose()
     */
    private $electronic_delivery_consent;

    /**
     * @var boolean
     * @Expose()
     */
    private $consent_to_share_info;

    /**
     * @var \Sudoux\MortgageBundle\Entity\CreditReport
     * @Expose()
     */
    private $credit_report;

    /**
     * @var
     * @author Eric Haynes
     */
    private $location;

    /**
     * @var \Sudoux\Cms\LocationBundle\Entity\Location
     * @Expose()
     */
    private $mailing_location;

    ##################################################
    ##Private Array Collections
    ##
    ###################################################

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $loan;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $loan_coborrower;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $asset_account;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $asset_realestate;

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
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $previous_location;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $employment;

    ##################################################
    ##Functions
    ##
    ###################################################



    /**
     * Constructor
     */
    public function __construct()
    {

        //This block was generated by doctrine and NOT in Dan's code
        //so I am still determining the impact of it
        //TODO: IS THIS NEEDED?
     //   $this->loan = new \Doctrine\Common\Collections\ArrayCollection();
        $this->loan_coborrower = new \Doctrine\Common\Collections\ArrayCollection();
        $this->asset_account = new \Doctrine\Common\Collections\ArrayCollection();
        $this->asset_realestate = new \Doctrine\Common\Collections\ArrayCollection();
        $this->income_monthly = new \Doctrine\Common\Collections\ArrayCollection();
        $this->income_other = new \Doctrine\Common\Collections\ArrayCollection();
        $this->previous_location = new \Doctrine\Common\Collections\ArrayCollection();
        $this->employment = new \Doctrine\Common\Collections\ArrayCollection();
        //
        $this->setDefaults();
    }


    public function setDefaults()
    {
        $this->loan = new \Doctrine\Common\Collections\ArrayCollection();
        $this->employed = true;
        $this->declaration_outstanding_judgement = false;
        $this->declaration_bankruptcy = false;
        $this->declaration_forclosure = false;
        $this->declaration_forclosure_obligation = false;
        $this->declaration_lawsuit = false;
        $this->declaration_in_default = false;
        $this->declaration_alimony_child_support = false;
        $this->declaration_down_payment_borrowed = false;
        $this->declaration_note_endorser = false;
        $this->declaration_us_citizen = true;
        $this->declaration_resident_alien = false;
        $this->declaration_primary_residence = false;
        $this->declaration_ownership_within_three_years = false;
        $this->govt_monitoring_opt_out = false;
        if(!isset($this->location)) {
            $this->location = new EagleBorrowerLocation();
        }
    }

    /**
     * @return bool
     * @author Eric Haynes
     */
    public function validate()
    {
        $isValid = true;
        if(!$this->govt_monitoring_opt_out) {
            $govtRequiredProperties = array($this->race, $this->ethnicity, $this->is_male);
            foreach($govtRequiredProperties as $property) {
                if(!isset($property)) {
                    $isValid = false;
                }
            }
        }

        $declarationRequiredProperties = array(
            $this->declaration_outstanding_judgement,
            $this->declaration_bankruptcy,
            $this->declaration_forclosure,
            $this->declaration_lawsuit,
            $this->declaration_forclosure_obligation,
            $this->declaration_in_default,
            $this->declaration_alimony_child_support,
            $this->declaration_down_payment_borrowed,
            $this->declaration_note_endorser,
            $this->declaration_us_citizen,
            $this->declaration_resident_alien,
            $this->declaration_primary_residence,
            $this->declaration_ownership_within_three_years,
        );

        foreach($declarationRequiredProperties as $property) {
            if(!isset($property)) {
                $isValid = false;
            }
        }


        if($this->declaration_alimony_child_support) {
            $declarationAlimonyRequiredProperties = array(
                $this->declaration_alimony_child_support_alimony,
                $this->declaration_alimony_child_support_child_support,
                $this->declaration_alimony_child_support_separate_maintenance,
            );

            foreach($declarationAlimonyRequiredProperties as $property) {
                if(!isset($property)) {
                    $isValid = false;
                }
            }
        }

        return $isValid;
    }

    /**
     * @author Eric Haynes
     */
    public function prePersist()
    {

        if(!$this->declaration_outstanding_judgement) {
            $this->declaration_outstanding_judgement_details = null;
        }

        if(!$this->declaration_bankruptcy) {
            $this->declaration_bankruptcy_details = null;
        }

        if(!$this->declaration_forclosure) {
            $this->declaration_forclosure_details = null;
        }

        if(!$this->declaration_lawsuit) {
            $this->declaration_lawsuit_details = null;
        }

        if(!$this->declaration_forclosure_obligation) {
            $this->declaration_forclosure_obligation_details = null;
        }

        if(!$this->declaration_in_default) {
            $this->declaration_in_default_details = null;
        }

        if($this->declaration_alimony_child_support) {
            // generate the details data
            $alimonyChildSupportDetails = "";
            if(isset($this->declaration_alimony_child_support_alimony)) {
                $alimonyChildSupportDetails .= "Alimony: $" . $this->declaration_alimony_child_support_alimony . " ";
            }

            if(isset($this->declaration_alimony_child_support_child_support)) {
                $alimonyChildSupportDetails .= "Child Support: $" . $this->declaration_alimony_child_support_child_support . " ";
            }

            if(isset($this->declaration_alimony_child_support_separate_maintenance)) {
                $alimonyChildSupportDetails .= "Separate Maintenance: $" . $this->declaration_alimony_child_support_separate_maintenance;
            }

            $this->declaration_alimony_child_support_details = $alimonyChildSupportDetails;

        } else {
            $this->declaration_alimony_child_support_details = null;
            $this->declaration_alimony_child_support_alimony = null;
            $this->declaration_alimony_child_support_child_support = null;
            $this->declaration_alimony_child_support_separate_maintenance = null;
        }

        if(!$this->declaration_down_payment_borrowed) {
            $this->declaration_down_payment_borrowed_details = null;
        }

        if(!$this->declaration_note_endorser) {
            $this->declaration_note_endorser_details = null;
        }

        if(!$this->declaration_ownership_within_three_years) {
            $this->declaration_ownership_within_three_years_property_title = null;
            $this->declaration_ownership_within_three_years_property_type = null;
        }

        if($this->govt_monitoring_opt_out) {
            $this->ethnicity = null;
            $this->race = null;
            $this->is_male = null;
        }

        if(!$this->dependents) {
            $this->dependents_ages = null;
            $this->dependents_number = 0;
        }
    }

    /**
     * @return int
     * @author Eric Haynes
     */
    public function getAge()
    {
        $now = new \DateTime('today');
        return $this->birth_date->diff($now)->y;
    }

    ##################################################
    ##Getters Setters
    ##Adders Removers
    ###################################################



    /**
     * @return string
     * @author Eric Haynes
     */
    public function getFullName()
    {
        $fullName = $this->first_name . ' ';
        if(!empty($this->middle_initial)) {
            $fullName .= $this->middle_initial . '. ';
        }
        $fullName .= $this->last_name;
        if(!empty($this->suffix)) {
            $fullName .= ' ' . $this->suffix . '.';
        }

        return $fullName;
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
     */
    public function setSsn($ssn)
    {
        $encryptionUtil = new EncryptionUtil();
        $encryptedSSN = $encryptionUtil->encrypt($ssn, $this::SSN_PASSPHRASE);
        $this->ssn = $encryptedSSN;

        return $this;
    }

    /**
     * Get ssn
     *
     * @return string 
     */
    public function getSsn()
    {
        $encryptionUtil = new EncryptionUtil();
        return $encryptionUtil->decrypt($this->ssn, $this::SSN_PASSPHRASE);
    }

    /**
     * Set birth_date
     *
     * @param \DateTime $birthDate
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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

    public function getEthnicityText()
    {
        if(isset($this->ethnicity)) {
            return $this->ethnicities[$this->ethnicity];
        }

        return null;
    }

    /**
     * Set race
     *
     * @param integer $race
     * @return EagleBorrower
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
     * @return null
     * @author Eric Haynes
     */
    public function getRaceText()
    {
        if(isset($this->race)) {
            return $this->races[$this->race];
        }

        return null;
    }

    /**
     * Set is_male
     *
     * @param boolean $isMale
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
     */
    public function setDependentsNumber($dependentsNumber)
    {
        $this->dependents = false;
        if(isset($dependentsNumber)) {
            if($dependentsNumber > 0) {
                $this->dependents = true;
            }
        }

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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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

    public function getDeclarationOwnershipWithinThreeYearsPropertyTypeName()
    {
        if(isset($this->declaration_ownership_within_three_years_property_type)) {
            return $this->property_ownership_types[$this->declaration_ownership_within_three_years_property_type];
        }
        return null;
    }

    /**
     * Set declaration_ownership_within_three_years_property_title
     *
     * @param integer $declarationOwnershipWithinThreeYearsPropertyTitle
     * @return EagleBorrower
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

    public function getDeclarationOwnershipWithinThreeYearsPropertyTitleName()
    {
        if(isset($this->declaration_ownership_within_three_years_property_title)) {
            return $this->property_ownership_title_types[$this->declaration_ownership_within_three_years_property_title];
        }

        return null;
    }


    /**
     * Set initials
     *
     * @param string $initials
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @return EagleBorrower
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
     * @param \Sudoux\EagleBundle\Entity\EagleLoanApplication $loan
     * @return $this
     * @author Eric Haynes
     */
    public function addLoan(\Sudoux\EagleBundle\Entity\EagleLoanApplication $loan)
    {
        $this->loan[] = $loan;
    
        return $this;
    }

    /**
     * Remove loan
     *
     * @param \Sudoux\EagleBundle\Entity\EagleLoanApplication $loan
     */
    public function removeLoan(\Sudoux\EagleBundle\Entity\EagleLoanApplication $loan)
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
     * @param \Sudoux\EagleBundle\Entity\EagleLoanApplication $loanCoborrower
     * @return EagleBorrower
     */
    public function addLoanCoborrower(\Sudoux\EagleBundle\Entity\EagleLoanApplication $loanCoborrower)
    {
        $this->loan_coborrower[] = $loanCoborrower;
    
        return $this;
    }

    /**
     * Remove loan_coborrower
     *
     * @param \Sudoux\EagleBundle\Entity\EagleLoanApplication $loanCoborrower
     */
    public function removeLoanCoborrower(\Sudoux\EagleBundle\Entity\EagleLoanApplication $loanCoborrower)
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
     * @return EagleBorrower
     */
    public function addAssetAccount(\Sudoux\EagleBundle\Entity\EagleAssetAccount $assetAccount)
    {
        $this->asset_account[] = $assetAccount;
    
        return $this;
    }

    /**
     * Remove asset_account
     *
     * @param \Sudoux\MortgageBundle\Entity\AssetAccount $assetAccount
     */
    public function removeAssetAccount(\Sudoux\EagleBundle\Entity\EagleAssetAccount $assetAccount)
    {
        $this->asset_account->removeElement($assetAccount);
    }

    /**
     * @author Eric Haynes
     */
    public function removeAllAssetAccount()
    {
        if(isset($this->asset_account)) {
            $this->asset_account->clear();
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
     * Add asset_realestate
     *
     * @param \Sudoux\MortgageBundle\Entity\AssetRealEstate $assetRealestate
     * @return EagleBorrower
     */
    public function addAssetRealestate(\Sudoux\EagleBundle\Entity\EagleAssetRealEstate $assetRealestate)
    {
        $this->asset_realestate[] = $assetRealestate;
    
        return $this;
    }

    /**
     * Remove asset_realestate
     *
     * @param \Sudoux\MortgageBundle\Entity\AssetRealEstate $assetRealestate
     */
    public function removeAssetRealestate(\Sudoux\EagleBundle\Entity\EagleAssetRealEstate $assetRealestate)
    {
        $this->asset_realestate->removeElement($assetRealestate);
    }

    /**
     * @author Eric Haynes
     */
    public function removeAllAssetRealestate()
    {
        if(isset($this->asset_realestate)) {
            $this->asset_realestate->clear();
        }

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
     * @return EagleBorrower
     */
    public function addIncomeMonthly(\Sudoux\EagleBundle\Entity\EagleIncomeMonthly $incomeMonthly)
    {
        $this->income_monthly[] = $incomeMonthly;
    
        return $this;
    }

    /**
     * Remove income_monthly
     *
     * @param \Sudoux\MortgageBundle\Entity\IncomeMonthly $incomeMonthly
     */
    public function removeIncomeMonthly(\Sudoux\EagleBundle\Entity\EagleIncomeMonthly $incomeMonthly)
    {
        $this->income_monthly->removeElement($incomeMonthly);
    }

    /**
     * @author Eric Haynes
     */
    public function removeAllIncomeMonthly()
    {
        if(isset($this->income_monthly)) {
            $this->income_monthly->clear();
        }

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
     * @return EagleBorrower
     */
    public function addIncomeOther(\Sudoux\EagleBundle\Entity\EagleIncomeOther $incomeOther)
    {
        $this->income_other[] = $incomeOther;
    
        return $this;
    }

    /**
     * Remove income_other
     *
     * @param \Sudoux\MortgageBundle\Entity\IncomeOther $incomeOther
     */
    public function removeIncomeOther(\Sudoux\EagleBundle\Entity\EagleIncomeOther $incomeOther)
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
     * @param \Sudoux\EagleBundle\Entity\EagleBorrowerLocation $location
     * @return EagleBorrower
     */
    public function setLocation(\Sudoux\EagleBundle\Entity\EagleBorrowerLocation $location)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return \Sudoux\EagleBundle\Entity\EagleBorrowerLocation
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set mailing_location
     *
     * @param \Sudoux\Cms\LocationBundle\Entity\Location $mailingLocation
     * @return EagleBorrower
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
     * @param \Sudoux\EagleBundle\Entity\EagleBorrowerLocation $previousLocation
     * @return EagleBorrower
     */
    public function addPreviousLocation(\Sudoux\EagleBundle\Entity\EagleBorrowerLocation $previousLocation)
    {
        $this->previous_location[] = $previousLocation;
    
        return $this;
    }

    /**
     * Remove previous_location
     *
     * @param \Sudoux\EagleBundle\Entity\EagleBorrowerLocation $previousLocation
     */
    public function removePreviousLocation(\Sudoux\EagleBundle\Entity\EagleBorrowerLocation $previousLocation)
    {
        $this->previous_location->removeElement($previousLocation);
    }

    /**
     * @author Eric Haynes
     */
    public function removeAllPreviousLocations()
    {
        if(isset($this->previous_location)) {
            $this->previous_location->clear();
        }
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
     * @return EagleBorrower
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

    public function removeAllEmployment()
    {
        if(isset($this->employment)) {
            $this->employment->clear();
        }
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
     * @return EagleBorrower
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