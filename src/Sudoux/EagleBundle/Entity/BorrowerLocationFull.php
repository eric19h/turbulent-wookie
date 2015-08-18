<?php

namespace Sudoux\EagleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BorrowerLocationFull
 * @package Sudoux\EagleBundle\Entity
 * @author Eric Haynes
 */
class BorrowerLocationFull
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var integer
     */
    private $years_at_location;

    /**
     * @var integer
     */
    private $months_at_location;

    /**
     * @var boolean
     */
    private $own_residence;

    /**
     * @var boolean
     */
    private $has_foreign_address;

    /**
     * @var \Sudoux\Cms\LocationBundle\Entity\Location
     */
    private $location;

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
     * @return BorrowerLocationFull
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
     * @return BorrowerLocationFull
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
     * @return BorrowerLocationFull
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
     * @return BorrowerLocationFull
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
     * @return BorrowerLocationFull
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
     * @return BorrowerLocationFull
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