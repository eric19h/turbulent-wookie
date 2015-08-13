<?php

namespace Sudoux\MortgageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class LoanMilestone
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class LoanMilestone
{
    /*
     *
     */
    public function __construct()
    {
        $this->active = true;
    }

    /**
     * @var integer
     * @Expose()
     */
    private $id;

    /**
     * @var string
     * @Expose()
     */
    private $name;


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
     * @return LoanMilestone
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
     * @var \Sudoux\Cms\SiteBundle\Entity\Site
     */
    private $site;


    /**
     * Set site
     *
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $site
     * @return LoanMilestone
     */
    public function setSite(\Sudoux\Cms\SiteBundle\Entity\Site $site = null)
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
     * @var integer
     * @Expose()
     */
    private $weight;


    /**
     * Set weight
     *
     * @param integer $weight
     * @return LoanMilestone
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
    /**
     * @var \Sudoux\MortgageBundle\Entity\LoanMilestoneGroup
     * @Expose()
     */
    private $milestone_group;


    /**
     * Set milestone_group
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanMilestoneGroup $milestoneGroup
     * @return LoanMilestone
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
     * @var string
     * @Expose()
     */
    private $los_id;


    /**
     * Set los_id
     *
     * @param string $losId
     * @return LoanMilestone
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
    private $display_name;

    /**
     * @var boolean
     * @Expose()
     */
    private $active;


    /**
     * Set display_name
     *
     * @param string $displayName
     * @return LoanMilestone
     */
    public function setDisplayName($displayName)
    {
        $this->display_name = $displayName;
    
        return $this;
    }

    /**
     * Get display_name
     *
     * @return string 
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }


    public function getMilestoneName()
    {
        $name = $this->name;
        if(!empty($this->display_name)) {
            $name = $this->display_name;
        }

        return $name;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return LoanMilestone
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
}