<?php

namespace Sudoux\MortgageBundle\Entity;


use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

use Doctrine\Common\Collections\ArrayCollection;

use Sudoux\Cms\LocationBundle\Entity\Location;
use JMS\Serializer\Annotation\Accessor;

use Doctrine\ORM\Mapping as ORM;
use Sudoux\Cms\SecurityBundle\DependencyInjection\EncryptionUtil;

/**
 * Class Borrower
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class Borrower
{
    const SSN_PASSPHRASE = "d72e62a6efc36";
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
    private $middle_initial;

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
     * @var boolean
     * @Expose()
     */
    private $primaryBorrower;

    /**
     * @var \Sudoux\Cms\LocationBundle\Entity\Location
     * @Expose()
     */
    private $location;

/*
    public function __clone(){

        if ($this->id) {

            $this->id = NULL;
            $this->income_monthly = clone $this->income_monthly;
            $this->income_other = clone $this->income_other;
            $this->asset_account = clone $this->asset_account;
            $this->asset_realestate = clone $this->asset_realestate;
            $this->employment = clone $this->employment;
        }

    }
*/
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
     * @return Borrower
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
     * @return Borrower
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
     * Set email
     *
     * @param string $email
     * @return Borrower
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
     * Set middle_initial
     *
     * @param string $middleInitial
     * @return Borrower
     */
    public function setMiddleInitial($middleInitial)
    {
        $this->middle_initial = substr($middleInitial, 0, 1);
    
        return $this;
    }

    /**
     * Get middle_initial
     *
     * @return string 
     */
    public function getMiddleInitial()
    {
        return $this->middle_initial;
    }

    /**
     * Set suffix
     *
     * @param string $suffix
     * @return Borrower
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
     * @return Borrower
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
     * @return Borrower
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
     * @return Borrower
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
     * @return Borrower
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
    
    public function getAge()
    {
    	$now = new \DateTime('today');
    	return $this->birth_date->diff($now)->y;
    }

    /**
     * Set years_of_school
     *
     * @param integer $yearsOfSchool
     * @return Borrower
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
     * @return Borrower
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

    public function getMaritalStatusName()
    {
        return $this->maritial_statuses[$this->marital_status];
    }

    /**
     * Set primaryBorrower
     *
     * @param boolean $primaryBorrower
     * @return Borrower
     */
    public function setPrimaryBorrower($primaryBorrower)
    {
        $this->primaryBorrower = $primaryBorrower;
    
        return $this;
    }

    /**
     * Get primaryBorrower
     *
     * @return boolean 
     */
    public function getPrimaryBorrower()
    {
        return $this->primaryBorrower;
    }

    /**
     * Set location
     *
     * @param \Sudoux\MortgageBundle\Entity\BorrowerLocation $location
     * @return Borrower
     */
    public function setLocation(\Sudoux\MortgageBundle\Entity\BorrowerLocation $location)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return \Sudoux\MortgageBundle\Entity\BorrowerLocation
     */
    public function getLocation()
    {
        return $this->location;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $loan;

    /**
     * Constructor
     */
    public function __construct()
    {
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
	    	$this->location = new BorrowerLocation();
        }
    }

    /**
     * Add loan
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $loan
     * @return Borrower
     */
    public function addLoan(\Sudoux\MortgageBundle\Entity\LoanApplication $loan)
    {
        $this->loan[] = $loan;
    
        return $this;
    }

    /**
     * Remove loan
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $loan
     */
    public function removeLoan(\Sudoux\MortgageBundle\Entity\LoanApplication $loan)
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
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $previous_location;


    /**
     * Add previous_location
     *
     * @param \Sudoux\MortgageBundle\Entity\BorrowerLocation $previousLocation
     * @return Borrower
     */
    public function addPreviousLocation(\Sudoux\MortgageBundle\Entity\BorrowerLocation $previousLocation)
    {
        $this->previous_location[] = $previousLocation;
    
        return $this;
    }

    /**
     * Remove previous_location
     *
     * @param \Sudoux\MortgageBundle\Entity\BorrowerLocation $previousLocation
     */
    public function removePreviousLocation(\Sudoux\MortgageBundle\Entity\BorrowerLocation $previousLocation)
    {
        $this->previous_location->removeElement($previousLocation);
    }
    
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
     * Set ethnicity
     *
     * @param integer $ethnicity
     * @return Borrower
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
     * @return Borrower
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
     * @return Borrower
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
     * @var boolean
     * @Expose()
     */
    private $govt_monitoring_opt_out;


    /**
     * Set govt_monitoring_opt_out
     *
     * @param boolean $govtMonitoringOptOut
     * @return Borrower
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
     * @var \Doctrine\Common\Collections\Collection
     *
     */
    private $loan_coborrower;


    /**
     * Add loan_coborrower
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $loanCoborrower
     * @return Borrower
     */
    public function addLoanCoborrower(\Sudoux\MortgageBundle\Entity\LoanApplication $loanCoborrower)
    {
        $this->loan_coborrower[] = $loanCoborrower;
    
        return $this;
    }

    /**
     * Remove loan_coborrower
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $loanCoborrower
     */
    public function removeLoanCoborrower(\Sudoux\MortgageBundle\Entity\LoanApplication $loanCoborrower)
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
     * @var integer
     * @Expose()
     */
    private $months_at_residence;


    /**
     * Set months_at_residence
     *
     * @param integer $monthsAtResidence
     * @return Borrower
     */
    public function setMonthsAtResidence($monthsAtResidence)
    {
        $this->months_at_residence = $monthsAtResidence;
    
        return $this;
    }

    /**
     * Get months_at_residence
     *
     * @return integer 
     */
    public function getMonthsAtResidence()
    {
        return $this->months_at_residence;
    }
    /**
     * @var \Sudoux\Cms\LocationBundle\Entity\Location
     * @Expose()
     */
    private $mailing_location;


    /**
     * Set mailing_location
     *
     * @param \Sudoux\Cms\LocationBundle\Entity\Location $mailingLocation
     * @return Borrower
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
     * @var boolean
     * @Expose()
     */
    private $declaration_outstanding_judgement;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_bankruptcy;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_forclosure;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_lawsuit;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_forclosure_obligation;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_in_default;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_alimony_child_support;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_down_payment_borrowed;

    /**
     * @var boolean
     * @Expose()
     */
    private $declaration_note_endorser;

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
     * Set declaration_outstanding_judgement
     *
     * @param boolean $declarationOutstandingJudgement
     * @return Borrower
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
     * Set declaration_bankruptcy
     *
     * @param boolean $declarationBankruptcy
     * @return Borrower
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
     * Set declaration_forclosure
     *
     * @param boolean $declarationForclosure
     * @return Borrower
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
     * Set declaration_lawsuit
     *
     * @param boolean $declarationLawsuit
     * @return Borrower
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
     * Set declaration_forclosure_obligation
     *
     * @param boolean $declarationForclosureObligation
     * @return Borrower
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
     * Set declaration_in_default
     *
     * @param boolean $declarationInDefault
     * @return Borrower
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
     * Set declaration_alimony_child_support
     *
     * @param boolean $declarationAlimonyChildSupport
     * @return Borrower
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
     * Set declaration_down_payment_borrowed
     *
     * @param boolean $declarationDownPaymentBorrowed
     * @return Borrower
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
     * Set declaration_note_endorser
     *
     * @param boolean $declarationNoteEndorser
     * @return Borrower
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
     * Set declaration_us_citizen
     *
     * @param boolean $declarationUsCitizen
     * @return Borrower
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
     * @return Borrower
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
     * @return Borrower
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
     * @return Borrower
     */
    public function setDeclarationOwnershipWithinThreeYears($declarationOwnershipWithinThreeYears)
    {
        $this->declaration_ownership_within_three_years = $declarationOwnershipWithinThreeYears;
    
        return $this;
    }

    /**J
     * Get declaration_ownership_within_three_years
     *
     * @return boolean 
     */
    public function getDeclarationOwnershipWithinThreeYears()
    {
        return $this->declaration_ownership_within_three_years;
    }
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
     * Set initials
     *
     * @param string $initials
     * @return Borrower
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
     * @return Borrower
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
     * @var \Sudoux\MortgageBundle\Entity\CreditReport
     */
    private $credit_report;


    /**
     * Set credit_report
     *
     * @param \Sudoux\MortgageBundle\Entity\CreditReport $creditReport
     * @return Borrower
     */
    public function setCreditReport(\Sudoux\MortgageBundle\Entity\CreditReport $creditReport)
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
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $employment;


    /**
     * Add employment
     *
     * @param \Sudoux\MortgageBundle\Entity\Employment $employment
     * @return Borrower
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
     * @var boolean
     * @Expose()
     */
    private $employed;


    /**
     * Set employed
     *
     * @param boolean $employed
     * @return Borrower
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
     * Set declaration_ownership_within_three_years_property_type
     *
     * @param integer $declarationOwnershipWithinThreeYearsPropertyType
     * @return Borrower
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
    	return;
    }

    /**
     * Set declaration_ownership_within_three_years_property_title
     *
     * @param integer $declarationOwnershipWithinThreeYearsPropertyTitle
     * @return Borrower
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
    	
    	return;
    }
    /**
     * @var string
     * @Expose()
     */
    private $declaration_outstanding_judgement_details;

    /**
     * @var string
     * @Expose()
     */
    private $declaration_bankruptcy_details;

    /**
     * @var string
     * @Expose()
     */
    private $declaration_forclosure_details;

    /**
     * @var string
     * @Expose()
     */
    private $declaration_lawsuit_details;

    /**
     * @var string
     * @Expose()
     */
    private $declaration_forclosure_obligation_details;

    /**
     * @var string
     * @Expose()
     */
    private $declaration_in_default_details;

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
     * @var string
     * @Expose()
     */
    private $declaration_down_payment_borrowed_details;

    /**
     * @var string
     * @Expose()
     */
    private $declaration_note_endorser_details;


    /**
     * Set declaration_outstanding_judgement_details
     *
     * @param string $declarationOutstandingJudgementDetails
     * @return Borrower
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
     * Set declaration_bankruptcy_details
     *
     * @param string $declarationBankruptcyDetails
     * @return Borrower
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
     * Set declaration_forclosure_details
     *
     * @param string $declarationForclosureDetails
     * @return Borrower
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
     * Set declaration_lawsuit_details
     *
     * @param string $declarationLawsuitDetails
     * @return Borrower
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
     * Set declaration_forclosure_obligation_details
     *
     * @param string $declarationForclosureObligationDetails
     * @return Borrower
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
     * Set declaration_in_default_details
     *
     * @param string $declarationInDefaultDetails
     * @return Borrower
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
     * Set declaration_alimony_child_support_alimony
     *
     * @param integer $declarationAlimonyChildSupportAlimony
     * @return Borrower
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
     * @return Borrower
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
     * @return Borrower
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
     * Set declaration_down_payment_borrowed_details
     *
     * @param string $declarationDownPaymentBorrowedDetails
     * @return Borrower
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
     * Set declaration_note_endorser_details
     *
     * @param string $declarationNoteEndorserDetails
     * @return Borrower
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
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $asset_account;


    /**
     * Add asset_account
     *
     * @param \Sudoux\MortgageBundle\Entity\AssetAccount $assetAccount
     * @return Borrower
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
     *
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
     * Add asset_realestate
     *
     * @param \Sudoux\MortgageBundle\Entity\AssetRealEstate $assetRealestate
     * @return Borrower
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
     *
     */
    public function removeAllAssetRealestate()
    {
        if(isset($this->asset_realestate)) {
            $this->asset_realestate->clear();
        }

    }

    /**
     * Add income_monthly
     *
     * @param \Sudoux\MortgageBundle\Entity\IncomeMonthly $incomeMonthly
     * @return Borrower
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
     * @return Borrower
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
     * Set dependents
     *
     * @param boolean $dependents
     * @return Borrower
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
     * @return Borrower
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
     * @return Borrower
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
     * @var string
     * @Expose()
     */
    private $los_id;


    /**
     * Set los_id
     *
     * @param string $losId
     * @return Borrower
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
     * @var string
     * @Expose()
     */
    private $declaration_alimony_child_support_details;


    /**
     * Set declaration_alimony_child_support_details
     *
     * @param string $declarationAlimonyChildSupportDetails
     * @return Borrower
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




}