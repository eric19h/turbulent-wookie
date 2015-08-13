<?php

namespace Sudoux\MortgageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
/**
 * Class PricingScenario
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class PricingScenario
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
     * @var float
     */
    private $rate;

    /**
     * @var float
     */
    private $margin;

    /**
     * @var float
     */
    private $apr;

    /**
     * @var integer
     */
    private $pi;

    /**
     * @var float
     */
    private $closing_cost;

    /**
     * @var float
     */
    private $discount;


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
     * @return PricingScenario
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
     * Set rate
     *
     * @param float $rate
     * @return PricingScenario
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    
        return $this;
    }

    /**
     * Get rate
     *
     * @return float 
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set margin
     *
     * @param float $margin
     * @return PricingScenario
     */
    public function setMargin($margin)
    {
        $this->margin = $margin;
    
        return $this;
    }

    /**
     * Get margin
     *
     * @return float 
     */
    public function getMargin()
    {
        return $this->margin;
    }

    /**
     * Set apr
     *
     * @param float $apr
     * @return PricingScenario
     */
    public function setApr($apr)
    {
        $this->apr = $apr;
    
        return $this;
    }

    /**
     * Get apr
     *
     * @return float 
     */
    public function getApr()
    {
        return $this->apr;
    }

    /**
     * Set pi
     *
     * @param integer $pi
     * @return PricingScenario
     */
    public function setPi($pi)
    {
        $this->pi = $pi;
    
        return $this;
    }

    /**
     * Get pi
     *
     * @return integer 
     */
    public function getPi()
    {
        return $this->pi;
    }

    /**
     * Set closing_cost
     *
     * @param float $closingCost
     * @return PricingScenario
     */
    public function setClosingCost($closingCost)
    {
        $this->closing_cost = $closingCost;
    
        return $this;
    }

    /**
     * Get closing_cost
     *
     * @return float 
     */
    public function getClosingCost()
    {
        return $this->closing_cost;
    }

    /**
     * Set discount
     *
     * @param float $discount
     * @return PricingScenario
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    
        return $this;
    }

    /**
     * Get discount
     *
     * @return float 
     */
    public function getDiscount()
    {
        return $this->discount;
    }
    /**
     * @var array
     */
    private $options;


    /**
     * Set options
     *
     * @param array $options
     * @return PricingScenario
     */
    public function setOptions($options)
    {
        $this->options = $options;
    
        return $this;
    }

    /**
     * Get options
     *
     * @return array 
     */
    public function getOptions()
    {
        return $this->options;
    }
}