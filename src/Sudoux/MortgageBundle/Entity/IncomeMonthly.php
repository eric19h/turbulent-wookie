<?php

namespace Sudoux\MortgageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class IncomeMonthly
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class IncomeMonthly
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
    private $base;

    /**
     * @var float
     * @Expose()
     */
    private $overtime;

    /**
     * @var float
     * @Expose()
     */
    private $bonus;

    /**
     * @var float
     * @Expose()
     */
    private $commission;

    /**
     * @var float
     * @Expose()
     */
    private $interest;

    /**
     * @var float
     * @Expose()
     */
    private $rent_net;

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

    /**
     * Set base
     *
     * @param float $base
     * @return IncomeMonthly
     */
    public function setBase($base)
    {
        $this->base = $base;
    
        return $this;
    }

    /*
    public function __clone(){

        if ($this->id) {

            $this->id = NULL;

        }

    }
    */

    /**
     * Get base
     *
     * @return float 
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Set overtime
     *
     * @param float $overtime
     * @return IncomeMonthly
     */
    public function setOvertime($overtime)
    {
        $this->overtime = $overtime;
    
        return $this;
    }

    /**
     * Get overtime
     *
     * @return float 
     */
    public function getOvertime()
    {
        return $this->overtime;
    }

    /**
     * Set bonus
     *
     * @param float $bonus
     * @return IncomeMonthly
     */
    public function setBonus($bonus)
    {
        $this->bonus = $bonus;
    
        return $this;
    }

    /**
     * Get bonus
     *
     * @return float 
     */
    public function getBonus()
    {
        return $this->bonus;
    }

    /**
     * Set commission
     *
     * @param float $commission
     * @return IncomeMonthly
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;
    
        return $this;
    }

    /**
     * Get commission
     *
     * @return float 
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * Set interest
     *
     * @param float $interest
     * @return IncomeMonthly
     */
    public function setInterest($interest)
    {
        $this->interest = $interest;
    
        return $this;
    }

    /**
     * Get interest
     *
     * @return float 
     */
    public function getInterest()
    {
        return $this->interest;
    }

    /**
     * Set rent_net
     *
     * @param float $rentNet
     * @return IncomeMonthly
     */
    public function setRentNet($rentNet)
    {
        $this->rent_net = $rentNet;
    
        return $this;
    }

    /**
     * Get rent_net
     *
     * @return float 
     */
    public function getRentNet()
    {
        return $this->rent_net;
    }

    /**
     * Set other
     *
     * @param float $other
     * @return IncomeMonthly
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
    /**
     * @var \Sudoux\MortgageBundle\Entity\Borrower
     */
    private $borrower;


    /**
     * Set borrower
     *
     * @param \Sudoux\MortgageBundle\Entity\Borrower $borrower
     * @return IncomeMonthly
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