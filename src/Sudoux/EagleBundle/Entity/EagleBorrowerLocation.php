<?php

namespace Sudoux\EagleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Sudoux\Cms\LocationBundle\Entity\Location;

/**
 * Class EagleBorrowerLocation
 * @package Sudoux\EagleBundle\Entity
 * @author Eric Haynes
 * @ExclusionPolicy("all")
 */
class EagleBorrowerLocation
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
     * @var integer
     * @Expose()
     */
    private $years_at_location;

    /**
     * @var integer
     * @Expose()
     */
    private $months_at_location;

    /**
     * @var boolean
     * @Expose()
     */
    private $own_residence;

    /**
     * @var boolean
     * @Expose()
     */
    private $has_foreign_address;

    /**
     * @var \Sudoux\Cms\LocationBundle\Entity\Location
     * @Expose()
     */
    private $location;


    public function __construct()
    {
        $this->created = new \DateTime();
        $this->location = new Location();
    }

    public function setApi()
    {
        $this->created = new \DateTime();
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
     * @return EagleBorrowerLocation
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
     * Set years_at_location
     *
     * @param integer $yearsAtLocation
     * @return EagleBorrowerLocation
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
     * Set months_at_location
     *
     * @param integer $monthsAtLocation
     * @return EagleBorrowerLocation
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
     * Set own_residence
     *
     * @param boolean $ownResidence
     * @return EagleBorrowerLocation
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

    /**
     * Set has_foreign_address
     *
     * @param boolean $hasForeignAddress
     * @return EagleBorrowerLocation
     */
    public function setHasForeignAddress($hasForeignAddress)
    {
        $this->has_foreign_address = $hasForeignAddress;
    
        return $this;
    }

    /**
     * Get has_foreign_address
     *
     * @return boolean 
     */
    public function getHasForeignAddress()
    {
        return $this->has_foreign_address;
    }

    /**
     * Set location
     *
     * @param \Sudoux\Cms\LocationBundle\Entity\Location $location
     * @return EagleBorrowerLocation
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
}