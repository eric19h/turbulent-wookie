<?php

namespace Sudoux\MortgageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
/**
 * Class Branch
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class Branch
{
	/* non persisted site creation fields */
    /**
     * @var string
     */
	public $site_domain;

    /**
     * @var string
     */
	public $site_menu;
	
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $nmls_id;

    /**
     * @var integer
     */
    private $los_id;

    /**
     * @var string
     */
    private $website;

    /**
     * @var string
     */
    private $directions;

    /**
     * @var string
     */
    private $description;

    public $branch_photo_file;
    
    public function __construct()
    {
    	$this->created = new \DateTime();
    	$this->modified = new \DateTime();
    	$this->active  = true;
    	$this->weight = 0;
    	$this->deleted = false;
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
     * Set name
     *
     * @param string $name
     * @return Branch
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set nmls_id
     *
     * @param integer $nmlsId
     * @return Branch
     */
    public function setNmlsId($nmlsId)
    {
        $this->nmls_id = $nmlsId;
    
        return $this;
    }

    /**
     * Get nmls_id
     *
     * @return integer 
     */
    public function getNmlsId()
    {
        return $this->nmls_id;
    }

    /**
     * Set los_id
     *
     * @param integer $losId
     * @return Branch
     */
    public function setLosId($losId)
    {
        $this->los_id = $losId;
    
        return $this;
    }

    /**
     * Get los_id
     *
     * @return integer 
     */
    public function getLosId()
    {
        return $this->los_id;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return Branch
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    
        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set directions
     *
     * @param string $directions
     * @return Branch
     */
    public function setDirections($directions)
    {
        $this->directions = $directions;
    
        return $this;
    }

    /**
     * Get directions
     *
     * @return string 
     */
    public function getDirections()
    {
        return $this->directions;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Branch
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $modified;

    /**
     * @var \Sudoux\Cms\SiteBundle\Entity\Site
     */
    private $site;


    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Branch
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
     * @return Branch
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
     * Set site
     *
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $site
     * @return Branch
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
     * @var \Sudoux\Cms\FileBundle\Entity\File
     */
    private $branch_photo;

    /**
     * @var \Sudoux\Cms\LocationBundle\Entity\Location
     */
    private $location;


    /**
     * Set branch_photo
     *
     * @param \Sudoux\Cms\FileBundle\Entity\File $branchPhoto
     * @return Branch
     */
    public function setBranchPhoto(\Sudoux\Cms\FileBundle\Entity\File $branchPhoto = null)
    {
        $this->branch_photo = $branchPhoto;
    
        return $this;
    }

    /**
     * Get branch_photo
     *
     * @return \Sudoux\Cms\FileBundle\Entity\File 
     */
    public function getBranchPhoto()
    {
        return $this->branch_photo;
    }

    /**
     * Set location
     *
     * @param \Sudoux\Cms\LocationBundle\Entity\Location $location
     * @return Branch
     */
    public function setLocation(\Sudoux\Cms\LocationBundle\Entity\Location $location = null)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return \Sudoux\Cms\LocationBundle\Entity\Location 
     */
    public function getLocation()
    {
        return $this->location;
    }
    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $fax;

    /**
     * @var string
     */
    private $email;


    /**
     * Set phone
     *
     * @param string $phone
     * @return Branch
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return Branch
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    
        return $this;
    }

    /**
     * Get fax
     *
     * @return string 
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Branch
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
     * @var boolean
     */
    private $active;


    /**
     * Set active
     *
     * @param boolean $active
     * @return Branch
     */
    public function setActive($active)
    {
        $this->active = $active;
    
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }
    /**
     * @var \Sudoux\Cms\SiteBundle\Entity\Site
     */
    private $branch_site;


    /**
     * Set branch_site
     *
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $branchSite
     * @return Branch
     */
    public function setBranchSite(\Sudoux\Cms\SiteBundle\Entity\Site $branchSite = null)
    {
        $this->branch_site = $branchSite;
    
        return $this;
    }

    /**
     * Get branch_site
     *
     * @return \Sudoux\Cms\SiteBundle\Entity\Site 
     */
    public function getBranchSite()
    {
        return $this->branch_site;
    }
    /**
     * @var boolean
     */
    private $deleted;


    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Branch
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
     * @var integer
     */
    private $weight;


    /**
     * Set weight
     *
     * @param integer $weight
     * @return Branch
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    
        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }
    
    public function hasSite()
    {
    	$hasSite = false;
    
    	if(isset($this->branch_site)) {
    		$active = $this->branch_site->getActive();
    		$deleted = $this->branch_site->getDeleted();
    		if($active && !$deleted) {
    			$hasSite = true;
    		}
    	}
    
    	return $hasSite;
    }
    /**
     * @var \Sudoux\MortgageBundle\Entity\LoanOfficer
     */
    private $branch_manager;


    /**
     * Set branch_manager
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanOfficer $branchManager
     * @return Branch
     */
    public function setBranchManager(\Sudoux\MortgageBundle\Entity\LoanOfficer $branchManager = null)
    {
        $this->branch_manager = $branchManager;
    
        return $this;
    }

    /**
     * Get branch_manager
     *
     * @return \Sudoux\MortgageBundle\Entity\LoanOfficer 
     */
    public function getBranchManager()
    {
        return $this->branch_manager;
    }

    public function getAlias()
    {
        $alias = preg_replace("/[^a-zA-Z0-9]/", '-', strtolower($this->name));
        if(isset($this->location)) {
            $address = $this->location->getAddress1();
            $city = $this->location->getCity();
            $state = $this->location->getState();
            $zipcode = $this->location->getZipcode();

            if(isset($address)) {
                $alias .= '-' . preg_replace("/[^a-z0-9]/", '-', strtolower($address));
            }

            if(isset($city)) {
                $alias .= '-' . preg_replace("/[^a-z0-9]/", '-', strtolower($city));
            }

            if(isset($state)) {
                $alias .= '-' . preg_replace("/[^a-z0-9]/", '-', strtolower($state->getName()));
            }

            if(isset($zipcode)) {
                $alias .= '-' . preg_replace("/[^a-z0-9]/", '-', strtolower($zipcode));
            }
        }

        return $alias;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $loan_officer;


    /**
     * Add loan_officer
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanOfficer $loanOfficer
     * @return Branch
     */
    public function addLoanOfficer(\Sudoux\MortgageBundle\Entity\LoanOfficer $loanOfficer)
    {
        $this->loan_officer[] = $loanOfficer;
    
        return $this;
    }

    /**
     * Remove loan_officer
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanOfficer $loanOfficer
     */
    public function removeLoanOfficer(\Sudoux\MortgageBundle\Entity\LoanOfficer $loanOfficer)
    {
        $this->loan_officer->removeElement($loanOfficer);
    }

    /**
     * Get loan_officer
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLoanOfficer()
    {
        return $this->loan_officer;
    }
}