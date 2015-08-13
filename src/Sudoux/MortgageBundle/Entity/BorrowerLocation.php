<?php

namespace Sudoux\MortgageBundle\Entity;

use Sudoux\Cms\LocationBundle\Entity\Location;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class BorrowerLocation
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class BorrowerLocation
{
    /**
     * @var integer
     * @Expose()
     */
    private $id;

    /**
     * @var integer
     * @Expose()
     */
    private $months_at_location;

    /**
     * @var \DateTime
     * @Expose()
     */
    private $created;
    
    
    public function __construct()
    {
    	$this->created = new \DateTime();
    	$this->location = new Location();
    }

    public function setApi()
    {
        $this->created = new \DateTime();
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
     * Set months_at_location
     *
     * @param integer $monthsAtLocation
     * @return BorrowerLocation
     */
    public function setMonthsAtLocation($monthsAtLocation)
    {
        $this->months_at_location = $monthsAtLocation;
    
        return $this;
    }

    /**
     * Get months_at_location
     *
     * @return integer 
     */
    public function getMonthsAtLocation()
    {
        return $this->months_at_location;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return BorrowerLocation
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
     * @var \Sudoux\Cms\LocationBundle\Entity\Location
     * @Expose()
     */
    private $location;


    /**
     * Set location
     *
     * @param \Sudoux\Cms\LocationBundle\Entity\Location $location
     * @return BorrowerLocation
     */
    public function setLocation(\Sudoux\Cms\LocationBundle\Entity\Location $location)
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
     * Set years_at_location
     *
     * @param integer $yearsAtLocation
     * @return BorrowerLocation
     */
    public function setYearsAtLocation($yearsAtLocation)
    {
        $this->years_at_location = $yearsAtLocation;
    
        return $this;
    }

    /**
     * Get years_at_location
     *
     * @return integer 
     */
    public function getYearsAtLocation()
    {
        return $this->years_at_location;
    }
    /**
     * @var integer
     * @Expose()
     */
    private $years_at_location;


    /**
     * @var boolean
     * @Expose()
     */
    private $own_residence;


    /**
     * Set own_residence
     *
     * @param boolean $ownResidence
     * @return BorrowerLocation
     */
    public function setOwnResidence($ownResidence)
    {
        $this->own_residence = $ownResidence;
    
        return $this;
    }

    /**
     * Get own_residence
     *
     * @return boolean 
     */
    public function getOwnResidence()
    {
        return $this->own_residence;
    }


}