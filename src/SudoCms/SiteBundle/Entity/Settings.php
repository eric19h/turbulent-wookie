<?php

namespace SudoCms\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\ExecutionContext;

/**
 * Class Settings
 * @package SudoCms\SiteBundle\Entity
 * @author Dan Alvare
 */
class Settings extends \Sudoux\Cms\SiteBundle\Entity\Settings
{
    /**
     *
     */
	public function __construct()
	{
		parent::__construct();
		$this->show_los_milestones = true;
        $this->send_milestones_notifications = true;
        $this->member_portal_enabled = true;
	}
	
    /**
     * @var integer
     */
    private $id;


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
     * @var \Sudoux\Cms\TaxonomyBundle\Entity\Vocabulary
     */
    private $loan_document_vocab;


    /**
     * Set loan_document_vocab
     *
     * @param \Sudoux\Cms\TaxonomyBundle\Entity\Vocabulary $loanDocumentVocab
     * @return Settings
     */
    public function setLoanDocumentVocab(\Sudoux\Cms\TaxonomyBundle\Entity\Vocabulary $loanDocumentVocab = null)
    {
        $this->loan_document_vocab = $loanDocumentVocab;

        return $this;
    }

    /**
     * Get loan_document_vocab
     *
     * @return \Sudoux\Cms\TaxonomyBundle\Entity\Vocabulary 
     */
    public function getLoanDocumentVocab()
    {
        return $this->loan_document_vocab;
    }

    /**
     * @return \Sudoux\Cms\TaxonomyBundle\Entity\Vocabulary
     */
    public function getInheritedLoanDocumentVocab()
    {
    	if(!isset($this->loan_document_vocab)) {
    		$this->getInheritedProperty('loan_document_vocab', $this->site->getParentSite());
    	}
        return $this->loan_document_vocab;
    }

    /**
     * @var \Sudoux\MortgageBundle\Entity\LosConnection
     */
    private $los;


    /**
     * Set los
     *
     * @param \Sudoux\MortgageBundle\Entity\LosConnection $los
     * @return Settings
     */
    public function setLos(\Sudoux\MortgageBundle\Entity\LosConnection $los = null)
    {
        $this->los = $los;
        
        return $this;
    }

    /**
     * Get los
     *
     * @return \Sudoux\MortgageBundle\Entity\LosConnection 
     */
    public function getLos()
    {
        return $this->los;
    }

    /**
     * @return \Sudoux\MortgageBundle\Entity\LosConnection
     */
    public function getInheritedLos()
    {
    	if(!isset($this->los)) {
    		$this->getInheritedProperty('los', $this->site->getParentSite());
    	}
    	
        return $this->los;
    }

    /**
     * @var \Sudoux\MortgageBundle\Entity\CreditConnection
     */
    private $credit_connection;


    /**
     * Set credit_connection
     *
     * @param \Sudoux\MortgageBundle\Entity\CreditConnection $creditConnection
     * @return Settings
     */
    public function setCreditConnection(\Sudoux\MortgageBundle\Entity\CreditConnection $creditConnection = null)
    {	    
    	$this->credit_connection = $creditConnection;
    	
        return $this;
    }

    /**
     * Get credit_connection
     *
     * @return \Sudoux\MortgageBundle\Entity\CreditConnection 
     */
    public function getCreditConnection()
    {
        return $this->credit_connection;
    }

    /**
     * @return \Sudoux\MortgageBundle\Entity\CreditConnection
     */
    public function getInheritedCreditConnection()
    {
    	if(!isset($this->credit_connection)) {
    		$this->getInheritedProperty('credit_connection', $this->site->getParentSite());
    	}
    	
        return $this->credit_connection;
    }
    
    
    /**
     * @var \Sudoux\MortgageBundle\Entity\PricingConnection
     */
    private $pricing_connection;


    /**
     * Set pricing_connection
     *
     * @param \Sudoux\MortgageBundle\Entity\PricingConnection $pricingConnection
     * @return Settings
     */
    public function setPricingConnection(\Sudoux\MortgageBundle\Entity\PricingConnection $pricingConnection = null)
    {
    	$this->pricing_connection = $pricingConnection;
    	    
        return $this;
    }

    /**
     * Get pricing_connection
     *
     * @return \Sudoux\MortgageBundle\Entity\PricingConnection 
     */
    public function getPricingConnection()
    {
        return $this->pricing_connection;
    }

    public function getInheritedPricingConnection()
    {
    	if(!isset($this->pricing_connection)) {
    		$this->getInheritedProperty('pricing_connection', $this->site->getParentSite());
    	}
    	
        return $this->pricing_connection;
    }

    /**
     * @var \Sudoux\MortgageBundle\Entity\LoanOfficer
     */
    private $loan_officer;

    /**
     * @var \Sudoux\MortgageBundle\Entity\Branch
     */
    private $branch;


    /**
     * Set loan_officer
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanOfficer $loanOfficer
     * @return Settings
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
     * Set branch
     *
     * @param \Sudoux\MortgageBundle\Entity\Branch $branch
     * @return Settings
     */
    public function setBranch(\Sudoux\MortgageBundle\Entity\Branch $branch = null)
    {
        $this->branch = $branch;
    
        return $this;
    }

    /**
     * Get branch
     *
     * @return \Sudoux\MortgageBundle\Entity\Branch 
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @var boolean
     */
    private $show_los_milestones;

    /**
     * @var \Sudoux\Cms\SiteBundle\Entity\Site
     */

    /**
     * Set show_los_milestones
     *
     * @param boolean $showLosMilestones
     * @return Settings
     */
    public function setShowLosMilestones($showLosMilestones)
    {
        $this->show_los_milestones = $showLosMilestones;
    
        return $this;
    }

    /**
     * Get show_los_milestones
     *
     * @return boolean 
     */
    public function getShowLosMilestones()
    {
        return $this->show_los_milestones;
    }

    /**
     * @return bool
     */
    public function getInheritedShowLosMilestones()
    {
    	if(!isset($this->show_los_milestones)) {
    		$this->getInheritedProperty('show_los_milestones', $this->site->getParentSite());
    	}
    	
        return $this->show_los_milestones;
    }

    /**
     * @var \Sudoux\MortgageBundle\Entity\LoanMilestoneGroup
     */
    private $milestone_group;

    /**
     * Set milestone_group
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanMilestoneGroup $milestoneGroup
     * @return Settings
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
     * @return \Sudoux\MortgageBundle\Entity\LoanMilestoneGroup
     */
    public function getInheritedMilestoneGroup()
    {
    	if(!isset($this->milestone_group)) {
    		$this->getInheritedProperty('milestone_group', $this->site->getParentSite());
    	}
    	
        return $this->milestone_group;
    }


    /**
     * @var \Sudoux\MortgageBundle\Entity\RealtywareConnection
     */
    private $realtyware_connection;


    /**
     * Set realtyware_connection
     *
     * @param \Sudoux\MortgageBundle\Entity\RealtywareConnection $realtywareConnection
     * @return Settings
     */
    public function setRealtywareConnection(\Sudoux\MortgageBundle\Entity\RealtywareConnection $realtywareConnection = null)
    {
        $this->realtyware_connection = $realtywareConnection;
    
        return $this;
    }

    /**
     * Get realtyware_connection
     *
     * @return \Sudoux\MortgageBundle\Entity\RealtywareConnection 
     */
    public function getRealtywareConnection()
    {
        return $this->realtyware_connection;
    }

    /**
     * @var boolean
     */
    private $send_milestones_notifications;

    /**
     * Set send_milestones_notifications
     *
     * @param boolean $sendMilestonesNotifications
     * @return Settings
     */
    public function setSendMilestonesNotifications($sendMilestonesNotifications)
    {
        $this->send_milestones_notifications = $sendMilestonesNotifications;
    
        return $this;
    }

    /**
     * Get send_milestones_notifications
     *
     * @return boolean 
     */
    public function getSendMilestonesNotifications()
    {
        return $this->send_milestones_notifications;
    }

    public function getInheritedSendMilestonesNotifications()
    {
        if(!isset($this->send_milestones_notifications)) {
            $this->getInheritedProperty('send_milestones_notifications', $this->site->getParentSite());
        }

        return $this->send_milestones_notifications;
    }

    /**
     * @var string
     */
    private $loan_complete_url;

    /**
     * @var string
     */
    private $prequal_complete_url;


    /**
     * Set loan_complete_url
     *
     * @param string $loanCompleteUrl
     * @return Settings
     */
    public function setLoanCompleteUrl($loanCompleteUrl)
    {
        $this->loan_complete_url = $loanCompleteUrl;
    
        return $this;
    }

    /**
     * Get loan_complete_url
     *
     * @return string 
     */
    public function getLoanCompleteUrl()
    {
        return $this->loan_complete_url;
    }

    /**
     * @return string
     */
    public function getInheritedLoanCompleteUrl()
    {
        if(!isset($this->loan_complete_url)) {
            $this->getInheritedProperty('loan_complete_url', $this->site->getParentSite());
        }

        return $this->loan_complete_url;
    }

    /**
     * Set prequal_complete_url
     *
     * @param string $prequalCompleteUrl
     * @return Settings
     */
    public function setPrequalCompleteUrl($prequalCompleteUrl)
    {
        $this->prequal_complete_url = $prequalCompleteUrl;
    
        return $this;
    }

    /**
     * Get prequal_complete_url
     *
     * @return string 
     */
    public function getPrequalCompleteUrl()
    {
        return $this->prequal_complete_url;
    }

    /**
     * @return string
     */
    public function getInheritedPrequalCompleteUrl()
    {
        if(!isset($this->prequal_complete_url)) {
            $this->getInheritedProperty('prequal_complete_url', $this->site->getParentSite());
        }

        return $this->prequal_complete_url;
    }

    public function isMortgageSettingsValid(ExecutionContext $context)
    {
        // field requirements for sites without parents
        $parentSite = $this->site->getParentSite();
        if(!isset($parentSite)) {
            $requiredFields = array('loan_document_vocab');
            foreach($requiredFields as $field) {
                if(empty($this->{$field})) {
                    $context->addViolationAtSubPath($field, 'This value is required.', array(), null);
                }
            }
        }
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $state_license;


    /**
     * Add state_license
     *
     * @param \Sudoux\Cms\LocationBundle\Entity\State $stateLicense
     * @return Settings
     */
    public function addStateLicense(\Sudoux\Cms\LocationBundle\Entity\State $stateLicense)
    {
        $this->state_license[] = $stateLicense;
    
        return $this;
    }

    /**
     * Remove state_license
     *
     * @param \Sudoux\Cms\LocationBundle\Entity\State $stateLicense
     */
    public function removeStateLicense(\Sudoux\Cms\LocationBundle\Entity\State $stateLicense)
    {
        $this->state_license->removeElement($stateLicense);
    }

    /**
     * Get state_license
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStateLicense()
    {
        return $this->state_license;
    }

    public function setStateLicense(\Doctrine\Common\Collections\Collection $stateLicenses)
    {
        $this->state_license = $stateLicenses;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInheritedStateLicense()
    {
        if($this->state_license->count() == 0) {
            $this->getInheritedCollection('state_license', $this->site->getParentSite());
        }

        return $this->state_license;
    }

    /**
     * @var boolean
     */
    private $member_portal_enabled;

    /**
     * Set member_portal_enabled
     *
     * @param boolean $memberPortalEnabled
     * @return Settings
     */
    public function setMemberPortalEnabled($memberPortalEnabled)
    {
        $this->member_portal_enabled = $memberPortalEnabled;

        $this->send_milestones_notifications = $this->member_portal_enabled;
    
        return $this;
    }

    /**
     * Get member_portal_enabled
     *
     * @return boolean 
     */
    public function getMemberPortalEnabled()
    {
        return $this->member_portal_enabled;
    }

    /**
     * @return bool
     */
    public function getInheritedMemberPortalEnabled()
    {
        if(!isset($this->member_portal_enabled)) {
            $this->getInheritedProperty('member_portal_enabled', $this->site->getParentSite());
        }

        return $this->member_portal_enabled;
    }

    /**
     * @var boolean
     */
    private $member_portal_documents_enabled;


    /**
     * Set member_portal_documents_enabled
     *
     * @param boolean $memberPortalDocumentsEnabled
     * @return Settings
     */
    public function setMemberPortalDocumentsEnabled($memberPortalDocumentsEnabled)
    {
        $this->member_portal_documents_enabled = $memberPortalDocumentsEnabled;
    
        return $this;
    }

    /**
     * Get member_portal_documents_enabled
     *
     * @return boolean 
     */
    public function getMemberPortalDocumentsEnabled()
    {
        return $this->member_portal_documents_enabled;
    }

    /**
     * @return bool
     */
    public function getInheritedMemberPortalDocumentsEnabled()
    {
        if(!isset($this->member_portal_documents_enabled)) {
            $this->getInheritedProperty('member_portal_documents_enabled', $this->site->getParentSite());
        }

        return $this->member_portal_documents_enabled;
    }

    /**
     * @var boolean
     */
    private $auto_create_loan_officer_user;

    /**
     * Set auto_create_loan_officer_user
     *
     * @param boolean $autoCreateLoanOfficerUser
     * @return Settings
     */
    public function setAutoCreateLoanOfficerUser($autoCreateLoanOfficerUser)
    {
        $this->auto_create_loan_officer_user = $autoCreateLoanOfficerUser;
    
        return $this;
    }

    /**
     * Get auto_create_loan_officer_user
     *
     * @return boolean 
     */
    public function getAutoCreateLoanOfficerUser()
    {
        return $this->auto_create_loan_officer_user;
    }

    /**
     * @return bool
     */
    public function getInheritedAutoCreateLoanOfficerUser()
    {
        if(!isset($this->auto_create_loan_officer_user)) {
            $this->getInheritedProperty('auto_create_loan_officer_user', $this->site->getParentSite());
        }

        return $this->auto_create_loan_officer_user;
    }

    public function getLeadEmail()
    {
        $email = $this->getInheritedWebsiteEmail();

        $loanOfficer = $this->loan_officer;
        if(isset($loanOfficer)) {
            $email = $loanOfficer->getEmail();
        }

        return $email;
    }

}