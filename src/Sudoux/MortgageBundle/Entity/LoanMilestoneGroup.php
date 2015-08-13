<?php

namespace Sudoux\MortgageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class LoanMilestoneGroup
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class LoanMilestoneGroup
{

    /**
     * @var integer
     * @Expose()
     */
    private $id;

    /**
     * @var \DateTime
     * @Expose()
     */
    private $created;

    /**
     * @var string
     * @Expose()
     */
    private $los_id;


    public function __construct()
    {
    	$this->created = new \DateTime();
        $this->active = true;
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
     * Set created
     *
     * @param \DateTime $created
     * @return LoanMilestoneGroup
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
     * Set los_id
     *
     * @param string $losId
     * @return LoanMilestoneGroup
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
     * @var \Sudoux\Cms\SiteBundle\Entity\Site
     * @Expose()
     */
    private $site;


    /**
     * Set site
     *
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $site
     * @return LoanMilestoneGroup
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
     * @var string
     * @Expose()
     */
    private $name;


    /**
     * Set name
     *
     * @param string $name
     * @return LoanMilestoneGroup
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
     * @var \Doctrine\Common\Collections\Collection
     * @Expose()
     */
    private $milestone;


    /**
     * Add milestone
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanMilestone $milestone
     * @return LoanMilestoneGroup
     */
    public function addMilestone(\Sudoux\MortgageBundle\Entity\LoanMilestone $milestone)
    {
        $this->milestone[] = $milestone;
    
        return $this;
    }

    /**
     * Remove milestone
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanMilestone $milestone
     */
    public function removeMilestone(\Sudoux\MortgageBundle\Entity\LoanMilestone $milestone)
    {
        $this->milestone->removeElement($milestone);
    }

    /**
     * Get milestone
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMilestone()
    {
        return $this->milestone;
    }

    /**
     *
     */
    public function getActiveMilestones()
    {
        $milestones = array();
        if($this->milestone->count() > 0) {
            foreach($this->milestone as $milestone) {
                $active = $milestone->getActive();
                if($active) {
                    $milestones[$milestone->getWeight()] = $milestone;
                    //array_push($milestones, $milestone);
                }
            }
        }

        ksort($milestones);

        return $milestones;
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
     * @return LoanMilestoneGroup
     */
    public function setActive($active)
    {
        $this->active = $active;

        if(!$this->active && $this->milestone->count() > 0) {
            foreach($this->milestone as $milestone) {
                $milestone->setActive(false);
            }
        }
    
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