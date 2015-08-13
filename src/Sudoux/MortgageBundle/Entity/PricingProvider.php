<?php

namespace Sudoux\MortgageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
/**
 * Class PricingProvider
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class PricingProvider
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $class_name;

    /**
     * @var string
     */
    private $created;

    public function __construct()
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
     * Set name
     *
     * @param string $name
     * @return PricingProvider
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
     * Set class_name
     *
     * @param string $className
     * @return PricingProvider
     */
    public function setClassName($className)
    {
        $this->class_name = $className;
    
        return $this;
    }

    /**
     * Get class_name
     *
     * @return string 
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * Set created
     *
     * @param string $created
     * @return PricingProvider
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return string 
     */
    public function getCreated()
    {
        return $this->created;
    }
}