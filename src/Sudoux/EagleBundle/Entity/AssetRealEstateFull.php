<?php

namespace Sudoux\EagleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AssetRealEstateFull
 * @package Sudoux\EagleBundle\Entity
 * @author Eric Haynes
 */
class AssetRealEstateFull
{
    /**
     * @var integer
     */
    private $id;


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
     * @var float
     */
    private $market_value;

    /**
     * @var float
     */
    private $mortgage_amount;

    /**
     * @var float
     */
    private $original_cost;

    /**
     * @var float
     */
    private $mortgage_payment;

    /**
     * @var float
     */
    private $rent_gross_income;

    /**
     * @var float
     */
    private $ins_tax_exp;

    /**
     * @var float
     */
    private $rent_net_income;

    /**
     * @var \DateTime
     */
    private $date_aquired;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var \Sudoux\Cms\LocationBundle\Entity\Location
     */
    private $location;

    /**
     * @var \Sudoux\EagleBundle\Entity\BorrowerFull
     */
    private $borrower;


    /**
     * Set market_value
     *
     * @param float $marketValue
     * @return AssetRealEstateFull
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
     * @return AssetRealEstateFull
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
     * Set original_cost
     *
     * @param float $originalCost
     * @return AssetRealEstateFull
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
     * Set mortgage_payment
     *
     * @param float $mortgagePayment
     * @return AssetRealEstateFull
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
     * Set rent_gross_income
     *
     * @param float $rentGrossIncome
     * @return AssetRealEstateFull
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
     * Set ins_tax_exp
     *
     * @param float $insTaxExp
     * @return AssetRealEstateFull
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
     * Set rent_net_income
     *
     * @param float $rentNetIncome
     * @return AssetRealEstateFull
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
     * Set date_aquired
     *
     * @param \DateTime $dateAquired
     * @return AssetRealEstateFull
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
     * @return AssetRealEstateFull
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
     * Set location
     *
     * @param \Sudoux\Cms\LocationBundle\Entity\Location $location
     * @return AssetRealEstateFull
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
     * Set borrower
     *
     * @param \Sudoux\EagleBundle\Entity\BorrowerFull $borrower
     * @return AssetRealEstateFull
     */
    public function setBorrower(\Sudoux\EagleBundle\Entity\BorrowerFull $borrower = null)
    {
        $this->borrower = $borrower;
    
        return $this;
    }

    /**
     * Get borrower
     *
     * @return \Sudoux\EagleBundle\Entity\BorrowerFull 
     */
    public function getBorrower()
    {
        return $this->borrower;
    }
}