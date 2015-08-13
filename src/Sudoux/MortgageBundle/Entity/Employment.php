<?php

namespace Sudoux\MortgageBundle\Entity;

use Sudoux\Cms\LocationBundle\Entity\Location;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
/**
 * Class Employment
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class Employment
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
    private $employer_name;

    /**
     * @var string
     * @Expose()
     */
    private $employer_phone;

    /**
     * @var string
     * @Expose()
     */
    private $employer_phone_ext;

    /**
     * @var boolean
     * @Expose()
     */
    private $current;

    /**
     * @var string
     * @Expose()
     */
    private $title;

    /**
     * @var \DateTime
     * @Expose()
     */
    private $start_date;

    /**
     * @var boolean
     * @Expose()
     */
    private $self_employed;

    /**
     * @var integer
     * @Expose()
     */
    private $years_employed;

    public function __construct()
    {
    	$this->location = new Location();
    }

    /*
    public function __clone(){

        if ($this->id) {

            $this->id = NULL;

        }

    }
    */
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
     * Set employer_name
     *
     * @param string $employerName
     * @return Employment
     */
    public function setEmployerName($employerName)
    {
        $this->employer_name = $employerName;
    	
        return $this;
    }

    /**
     * Get employer_name
     *
     * @return string 
     */
    public function getEmployerName()
    {
        return $this->employer_name;
    }

    /**
     * Set employer_phone
     *
     * @param string $employerPhone
     * @return Employment
     */
    public function setEmployerPhone($employerPhone)
    {
        $this->employer_phone = $employerPhone;
    
        return $this;
    }

    /**
     * Get employer_phone
     *
     * @return string 
     */
    public function getEmployerPhone()
    {
        return $this->employer_phone;
    }

    /**
     * Set employer_phone_ext
     *
     * @param string $employerPhoneExt
     * @return Employment
     */
    public function setEmployerPhoneExt($employerPhoneExt)
    {
        $this->employer_phone_ext = $employerPhoneExt;
    
        return $this;
    }

    /**
     * Get employer_phone_ext
     *
     * @return string 
     */
    public function getEmployerPhoneExt()
    {
        return $this->employer_phone_ext;
    }

    /**
     * Set current
     *
     * @param boolean $current
     * @return Employment
     */
    public function setCurrent($current)
    {
        $this->current = $current;
    
        return $this;
    }

    /**
     * Get current
     *
     * @return boolean 
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Employment
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
     * Set start_date
     *
     * @param \DateTime $startDate
     * @return Employment
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;
    
        return $this;
    }

    /**
     * Get start_date
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set self_employed
     *
     * @param boolean $selfEmployed
     * @return Employment
     */
    public function setSelfEmployed($selfEmployed)
    {
        $this->self_employed = $selfEmployed;
    
        return $this;
    }

    /**
     * Get self_employed
     *
     * @return boolean 
     */
    public function getSelfEmployed()
    {
        return $this->self_employed;
    }

    /**
     * Set years_employed
     *
     * @param integer $yearsEmployed
     * @return Employment
     */
    public function setYearsEmployed($yearsEmployed)
    {
        $this->years_employed = $yearsEmployed;
    
        return $this;
    }

    /**
     * Get years_employed
     *
     * @return integer 
     */
    public function getYearsEmployed()
    {
        return $this->years_employed;
    }
    /**
     * @var \DateTime
     * @Expose()
     */
    private $end_date;


    /**
     * Set end_date
     *
     * @param \DateTime $endDate
     * @return Employment
     */
    public function setEndDate($endDate)
    {
        $this->end_date = $endDate;
    
        return $this;
    }

    /**
     * Get end_date
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->end_date;
    }
    /**
     * @var \Sudoux\Cms\LocationBundle\Entity\Location
     * @Expose()
     */
    private $location;


    /**
     * Set location
     *
     * @param \Sudoux\Cms\LocationBundle\Entity\Location $location
     * @return Employment
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
    
    public function getTimeAtJob()
    {
    	$timeAtJob = new \stdClass();
    	$timeAtJob->years = null;
    	$timeAtJob->months = null;
    	if(isset($this->start_date) && isset($this->end_date)) {
	    	$interval = $this->start_date->diff($this->end_date);
	    	$timeAtJob->years = $interval->format('%y');
	    	$timeAtJob->months = $interval->format('%m');
    	}

    	return $timeAtJob;
    }
    /**
     * @var integer
     * @Expose()
     */
    private $years_on_job;


    /**
     * Set years_on_job
     *
     * @param integer $yearsOnJob
     * @return Employment
     */
    public function setYearsOnJob($yearsOnJob)
    {
        $this->years_on_job = $yearsOnJob;
    
        return $this;
    }

    /**
     * Get years_on_job
     *
     * @return integer 
     */
    public function getYearsOnJob()
    {
        return $this->years_on_job;
    }


}