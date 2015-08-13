<?php

namespace Sudoux\MortgageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class IncomeSource
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class IncomeSource
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
    private $name;

    /**
     * @var boolean
     * @Expose()
     */
    private $public;

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
     * Set name
     *
     * @param string $name
     * @return IncomeSource
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
     * Set public
     *
     * @param boolean $public
     * @return IncomeSource
     */
    public function setPublic($public)
    {
        $this->public = $public;
    
        return $this;
    }

    /**
     * Get public
     *
     * @return boolean 
     */
    public function getPublic()
    {
        return $this->public;
    }


}
