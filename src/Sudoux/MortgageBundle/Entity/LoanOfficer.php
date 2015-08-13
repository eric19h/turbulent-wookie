<?php

namespace Sudoux\MortgageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\ExecutionContext;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
/**
 * Class LoanOfficer
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class LoanOfficer
{
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
     * @var integer
     * @Expose()
     */
    private $los_id;

    /**
     * @var integer
     * @Expose()
     */
    private $nmls_id;

    /**
     * @var string
     * @Expose()
     */
    private $title;

    /**
     * @var string
     * @Expose()
     */
    private $phone_office;

    /**
     * @var string
     * @Expose()
     */
    private $phone_mobile;

    /**
     * @var string
     * @Expose()
     */
    private $phone_tollfree;

    /**
     * @var string
     * @Expose()
     */
    private $fax;

    /**
     * @var string
     * @Expose()
     */
    private $signature;

    /**
     * @var string
     * @Expose()
     */
    private $bio;
    
    public $officer_photo_file;

    public function __construct()
    {
    	$this->created = new \DateTime();
    	$this->modified = new \DateTime();
    	$this->active = true;
    	$this->weight = 0;
    	$this->deleted = 0;
        $this->auto_create_user = false;
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
     * @return LoanOfficer
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
     * @return LoanOfficer
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
    	return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return LoanOfficer
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
     * Set los_id
     *
     * @param integer $losId
     * @return LoanOfficer
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
     * Set nmls_id
     *
     * @param integer $nmlsId
     * @return LoanOfficer
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
     * Set title
     *
     * @param string $title
     * @return LoanOfficer
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set phone_office
     *
     * @param string $phoneOffice
     * @return LoanOfficer
     */
    public function setPhoneOffice($phoneOffice)
    {
        $this->phone_office = $phoneOffice;
    
        return $this;
    }

    /**
     * Get phone_office
     *
     * @return string 
     */
    public function getPhoneOffice()
    {
        return $this->phone_office;
    }

    /**
     * Set phone_mobile
     *
     * @param string $phoneMobile
     * @return LoanOfficer
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
     * Set phone_tollfree
     *
     * @param string $phoneTollfree
     * @return LoanOfficer
     */
    public function setPhoneTollfree($phoneTollfree)
    {
        $this->phone_tollfree = $phoneTollfree;
    
        return $this;
    }

    /**
     * Get phone_tollfree
     *
     * @return string 
     */
    public function getPhoneTollfree()
    {
        return $this->phone_tollfree;
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return LoanOfficer
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
     * Set signature
     *
     * @param string $signature
     * @return LoanOfficer
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
     * Set bio
     *
     * @param string $bio
     * @return LoanOfficer
     */
    public function setBio($bio)
    {
        $this->bio = $bio;
    
        return $this;
    }

    /**
     * Get bio
     *
     * @return string 
     */
    public function getBio()
    {
        return $this->bio;
    }
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
     * @var \Sudoux\Cms\SiteBundle\Entity\Site
     * @Expose()
     */
    private $site;


    /**
     * Set created
     *
     * @param \DateTime $created
     * @return LoanOfficer
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
     * @return LoanOfficer
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
     * @return LoanOfficer
     */
    public function setSite(\Sudoux\Cms\SiteBundle\Entity\Site $site)
    {
        $this->site = $site;

        $autoCreateUser = $site->getSettings()->getAutoCreateLoanOfficerUser();
        if($autoCreateUser) {
            $this->auto_create_user = $autoCreateUser;
        }
    
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
     * @Expose()
     */
    private $officer_photo;


    /**
     * Set officer_photo
     *
     * @param \Sudoux\Cms\FileBundle\Entity\File $officerPhoto
     * @return LoanOfficer
     */
    public function setOfficerPhoto(\Sudoux\Cms\FileBundle\Entity\File $officerPhoto = null)
    {
        $this->officer_photo = $officerPhoto;
    
        return $this;
    }

    /**
     * Get officer_photo
     *
     * @return \Sudoux\Cms\FileBundle\Entity\File 
     */
    public function getOfficerPhoto()
    {
        return $this->officer_photo;
    }
    /**
     * @var boolean
     * @Expose()
     */
    private $active;


    /**
     * Set active
     *
     * @param boolean $active
     * @return LoanOfficer
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
     * @var \Sudoux\MortgageBundle\Entity\Branch
     * @Expose()
     */
    private $branch;


    /**
     * Set branch
     *
     * @param \Sudoux\MortgageBundle\Entity\Branch $branch
     * @return LoanOfficer
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
     * @var \Sudoux\Cms\SiteBundle\Entity\Site
     * @Expose()
     */
    private $officer_site;


    /**
     * Set officer_site
     *
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $officerSite
     * @return LoanOfficer
     */
    public function setOfficerSite(\Sudoux\Cms\SiteBundle\Entity\Site $officerSite = null)
    {
        $this->officer_site = $officerSite;
    
        return $this;
    }

    /**
     * Get officer_site
     *
     * @return \Sudoux\Cms\SiteBundle\Entity\Site 
     */
    public function getOfficerSite()
    {
        return $this->officer_site;
    }
    /**
     * @var string
     * @Expose()
     */
    private $website;


    /**
     * Set website
     *
     * @param string $website
     * @return LoanOfficer
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
     * @var boolean
     * @Expose()
     */
    private $deleted;


    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return LoanOfficer
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
     * @Expose()
     */
    private $weight;


    /**
     * Set weight
     *
     * @param integer $weight
     * @return LoanOfficer
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
    
    	if(isset($this->officer_site)) {
    		$active = $this->officer_site->getActive();
    		$deleted = $this->officer_site->getDeleted();
    		if($active && !$deleted) {
    			$hasSite = true;
    		}
    	}
    
    	return $hasSite;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $testimonial;


    /**
     * Add testimonial
     *
     * @param \Sudoux\Cms\NodeBundle\Entity\Testimonial $testimonial
     * @return LoanOfficer
     */
    public function addTestimonial(\Sudoux\Cms\NodeBundle\Entity\Testimonial $testimonial)
    {
        $this->testimonial[] = $testimonial;
    
        return $this;
    }

    /**
     * Remove testimonial
     *
     * @param \Sudoux\Cms\NodeBundle\Entity\Testimonial $testimonial
     */
    public function removeTestimonial(\Sudoux\Cms\NodeBundle\Entity\Testimonial $testimonial)
    {
        $this->testimonial->removeElement($testimonial);
    }

    /**
     * Get testimonial
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTestimonial()
    {
        return $this->testimonial;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return preg_replace("/[^a-z0-9]/", '-', strtolower($this->first_name)) . '-' . preg_replace("/[^a-z0-9]/", '-', strtolower($this->last_name));
    }

    /**
     * @param ExecutionContext $context
     */
    public function isLoanOfficerValid(ExecutionContext $context)
    {
        $losConn = $this->site->getSettings()->getInheritedLos();
        if(isset($losConn)) {
            // make sure they enter an los id
            if(empty($this->los_id)) {
                $context->addViolationAtSubPath('los_id', 'This field is required.', array(), null);
            }
        }
    }
    /**
     * @var \Sudoux\Cms\UserBundle\Entity\User
     *
     */
    private $user;


    /**
     * Set user
     *
     * @param \Sudoux\Cms\UserBundle\Entity\User $user
     * @return LoanOfficer
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
     * @var boolean
     * @Expose()
     */
    private $auto_create_user;


    /**
     * Set auto_create_user
     *
     * @param boolean $autoCreateUser
     * @return LoanOfficer
     */
    public function setAutoCreateUser($autoCreateUser)
    {
        $this->auto_create_user = $autoCreateUser;
    
        return $this;
    }

    /**
     * Get auto_create_user
     *
     * @return boolean 
     */
    public function getAutoCreateUser()
    {
        return $this->auto_create_user;
    }


}