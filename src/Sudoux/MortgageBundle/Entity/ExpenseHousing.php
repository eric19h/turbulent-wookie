<?php

namespace Sudoux\MortgageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
/**
 * Class ExpenseHousing
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class ExpenseHousing
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
    private $rent;

    /**
     * @var float
     * @Expose()
     */
    private $mortgage;

    /**
     * @var float
     * @Expose()
     */
    private $other_financial;

    /**
     * @var float
     * @Expose()
     */
    private $insurance_hazard;

    /**
     * @var float
     * @Expose()
     */
    private $insurance_mortgage;

    /**
     * @var float
     * @Expose()
     */
    private $tax_real_estate;

    /**
     * @var float
     * @Expose()
     */
    private $hoa_dues;

    /**
     * @var float
     * @Expose()
     */
    private $other;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function __clone(){

        if ($this->id) {

            $this->id = NULL;

        }

    }
    /**
     * Set rent
     *
     * @param float $rent
     * @return ExpenseHousing
     */
    public function setRent($rent)
    {
        $this->rent = $rent;
    
        return $this;
    }

    /**
     * Get rent
     *
     * @return float 
     */
    public function getRent()
    {
        return $this->rent;
    }

    /**
     * Set mortgage
     *
     * @param float $mortgage
     * @return ExpenseHousing
     */
    public function setMortgage($mortgage)
    {
        $this->mortgage = $mortgage;
    
        return $this;
    }

    /**
     * Get mortgage
     *
     * @return float 
     */
    public function getMortgage()
    {
        return $this->mortgage;
    }

    /**
     * Set other_financial
     *
     * @param float $otherFinancial
     * @return ExpenseHousing
     */
    public function setOtherFinancial($otherFinancial)
    {
        $this->other_financial = $otherFinancial;
    
        return $this;
    }

    /**
     * Get other_financial
     *
     * @return float 
     */
    public function getOtherFinancial()
    {
        return $this->other_financial;
    }

    /**
     * Set insurance_hazard
     *
     * @param float $insuranceHazard
     * @return ExpenseHousing
     */
    public function setInsuranceHazard($insuranceHazard)
    {
        $this->insurance_hazard = $insuranceHazard;
    
        return $this;
    }

    /**
     * Get insurance_hazard
     *
     * @return float 
     */
    public function getInsuranceHazard()
    {
        return $this->insurance_hazard;
    }

    /**
     * Set insurance_mortgage
     *
     * @param float $insuranceMortgage
     * @return ExpenseHousing
     */
    public function setInsuranceMortgage($insuranceMortgage)
    {
        $this->insurance_mortgage = $insuranceMortgage;
    
        return $this;
    }

    /**
     * Get insurance_mortgage
     *
     * @return float 
     */
    public function getInsuranceMortgage()
    {
        return $this->insurance_mortgage;
    }

    /**
     * Set tax_real_estate
     *
     * @param float $taxRealEstate
     * @return ExpenseHousing
     */
    public function setTaxRealEstate($taxRealEstate)
    {
        $this->tax_real_estate = $taxRealEstate;
    
        return $this;
    }

    /**
     * Get tax_real_estate
     *
     * @return float 
     */
    public function getTaxRealEstate()
    {
        return $this->tax_real_estate;
    }

    /**
     * Set hoa_dues
     *
     * @param float $hoaDues
     * @return ExpenseHousing
     */
    public function setHoaDues($hoaDues)
    {
        $this->hoa_dues = $hoaDues;
    
        return $this;
    }

    /**
     * Get hoa_dues
     *
     * @return float 
     */
    public function getHoaDues()
    {
        return $this->hoa_dues;
    }

    /**
     * Set other
     *
     * @param float $other
     * @return ExpenseHousing
     */
    public function setOther($other)
    {
        $this->other = $other;
    
        return $this;
    }

    /**
     * Get other
     *
     * @return float 
     */
    public function getOther()
    {
        return $this->other;
    }

    public function reset()
    {
        $this->hoa_dues = null;
        $this->insurance_hazard = null;
        $this->insurance_mortgage = null;
        $this->rent = null;
        $this->mortgage = null;
        $this->other = null;
        $this->other_financial = null;
        $this->tax_real_estate = null;
    }


}