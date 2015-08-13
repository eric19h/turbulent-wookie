<?php

namespace Sudoux\MortgageBundle\Entity;

use Sudoux\Cms\LocationBundle\Entity\Location;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AssetRealEstate
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class AssetRealEstate
{
    /**
     * @var integer
     * @Expose()
     */
    private $id;

    /**
     * @var float
     * @Expose()
     */
    private $market_value;

    /**
     * @var float
     * @Expose()
     */
    private $mortgage_amount;

    /**
     * @var float
     * @Expose()
     */
    private $mortgage_payment;

    /**
     * @var float
     * @Expose()
     */
    private $gross_rent_income;

    /**
     * @var float
     * @Expose()
     */
    private $ins_tax_exp;

    /**
     * @var float
     * @Expose()
     */
    private $net_rent;
/*
    public function __clone(){

        if ($this->id) {

            $this->id = NULL;

        }

    }
  */
    public function __construct()
    {
    	$this->location = new Location();
    	$this->status = 0;
    }

    public $statuses = array(
    	0 => 'Retained',	
    	1 => 'Need the rest',	
    );

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
     * Set market_value
     *
     * @param float $marketValue
     * @return AssetRealEstate
     */
    public function setMarketValue($marketValue)
    {
        $this->market_value = $marketValue;
    
        return $this;
    }

    /**
     * Get market_value
     *
     * @return float 
     */
    public function getMarketValue()
    {
        return $this->market_value;
    }

    /**
     * Set mortgage_amount
     *
     * @param float $mortgageAmount
     * @return AssetRealEstate
     */
    public function setMortgageAmount($mortgageAmount)
    {
        $this->mortgage_amount = $mortgageAmount;
    
        return $this;
    }

    /**
     * Get mortgage_amount
     *
     * @return float 
     */
    public function getMortgageAmount()
    {
        return $this->mortgage_amount;
    }

    /**
     * Set mortgage_payment
     *
     * @param float $mortgagePayment
     * @return AssetRealEstate
     */
    public function setMortgagePayment($mortgagePayment)
    {
        $this->mortgage_payment = $mortgagePayment;
    
        return $this;
    }

    /**
     * Get mortgage_payment
     *
     * @return float 
     */
    public function getMortgagePayment()
    {
        return $this->mortgage_payment;
    }

    /**
     * Set gross_rent_income
     *
     * @param float $grossRentIncome
     * @return AssetRealEstate
     */
    public function setGrossRentIncome($grossRentIncome)
    {
        $this->gross_rent_income = $grossRentIncome;
    
        return $this;
    }

    /**
     * Get gross_rent_income
     *
     * @return float 
     */
    public function getGrossRentIncome()
    {
        return $this->gross_rent_income;
    }

    /**
     * Set ins_tax_exp
     *
     * @param float $insTaxExp
     * @return AssetRealEstate
     */
    public function setInsTaxExp($insTaxExp)
    {
        $this->ins_tax_exp = $insTaxExp;
    
        return $this;
    }

    /**
     * Get ins_tax_exp
     *
     * @return float 
     */
    public function getInsTaxExp()
    {
        return $this->ins_tax_exp;
    }

    /**
     * Set net_rent
     *
     * @param float $netRent
     * @return AssetRealEstate
     */
    public function setNetRent($netRent)
    {
        $this->net_rent = $netRent;
    
        return $this;
    }

    /**
     * Get net_rent
     *
     * @return float 
     */
    public function getNetRent()
    {
        return $this->net_rent;
    }
    /**
     * @var float
     */
    private $rent_gross_income;

    /**
     * @var float
     */
    private $rent_net_income;


    /**
     * Set rent_gross_income
     *
     * @param float $rentGrossIncome
     * @return AssetRealEstate
     */
    public function setRentGrossIncome($rentGrossIncome)
    {
        $this->rent_gross_income = $rentGrossIncome;
    
        return $this;
    }

    /**
     * Get rent_gross_income
     *
     * @return float 
     */
    public function getRentGrossIncome()
    {
        return $this->rent_gross_income;
    }

    /**
     * Set rent_net_income
     *
     * @param float $rentNetIncome
     * @return AssetRealEstate
     */
    public function setRentNetIncome($rentNetIncome)
    {
        $this->rent_net_income = $rentNetIncome;
    
        return $this;
    }

    /**
     * Get rent_net_income
     *
     * @return float 
     */
    public function getRentNetIncome()
    {
        return $this->rent_net_income;
    }
    /**
     * @var \Sudoux\Cms\LocationBundle\Entity\Location
     */
    private $location;


    /**
     * Set location
     *
     * @param \Sudoux\Cms\LocationBundle\Entity\Location $location
     * @return AssetRealEstate
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
    /**
     * @var float
     * @Expose()
     */
    private $original_cost;

    /**
     * @var \DateTime
     * @Expose()
     */
    private $date_aquired;

    /**
     * @var integer
     * @Expose()
     */
    private $status;


    /**
     * Set original_cost
     *
     * @param float $originalCost
     * @return AssetRealEstate
     */
    public function setOriginalCost($originalCost)
    {
        $this->original_cost = $originalCost;
    
        return $this;
    }

    /**
     * Get original_cost
     *
     * @return float 
     */
    public function getOriginalCost()
    {
        return $this->original_cost;
    }

    /**
     * Set date_aquired
     *
     * @param \DateTime $dateAquired
     * @return AssetRealEstate
     */
    public function setDateAquired($dateAquired)
    {
        $this->date_aquired = $dateAquired;
    
        return $this;
    }

    /**
     * Get date_aquired
     *
     * @return \DateTime 
     */
    public function getDateAquired()
    {
        return $this->date_aquired;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return AssetRealEstate
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * @var \Sudoux\MortgageBundle\Entity\Borrower
     */
    private $borrower;


    /**
     * Set borrower
     *
     * @param \Sudoux\MortgageBundle\Entity\Borrower $borrower
     * @return AssetRealEstate
     */
    public function setBorrower(\Sudoux\MortgageBundle\Entity\Borrower $borrower = null)
    {
        $this->borrower = $borrower;
    
        return $this;
    }

    /**
     * Get borrower
     *
     * @return \Sudoux\MortgageBundle\Entity\Borrower 
     */
    public function getBorrower()
    {
        return $this->borrower;
    }


}