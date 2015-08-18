<?php

namespace Sudoux\EagleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class IncomeMonthlyFull
 * @package Sudoux\EagleBundle\Entity
 * @author Eric Haynes
 */
class IncomeMonthlyFull
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
    private $base;

    /**
     * @var float
     */
    private $overtime;

    /**
     * @var float
     */
    private $bonus;

    /**
     * @var float
     */
    private $commission;

    /**
     * @var float
     */
    private $interest;

    /**
     * @var float
     */
    private $rent_net;

    /**
     * @var float
     */
    private $other;

    /**
     * @var \Sudoux\EagleBundle\Entity\BorrowerFull
     */
    private $borrower;


    /**
     * Set base
     *
     * @param float $base
     * @return IncomeMonthlyFull
     */
    public function setBase($base)
    {
        $this->base = $base;
    
        return $this;
    }

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
     * @return IncomeMonthlyFull
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
     * @return IncomeMonthlyFull
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
     * @return IncomeMonthlyFull
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
     * @return IncomeMonthlyFull
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
     * @return IncomeMonthlyFull
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
     * @return IncomeMonthlyFull
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
     * Set borrower
     *
     * @param \Sudoux\EagleBundle\Entity\BorrowerFull $borrower
     * @return IncomeMonthlyFull
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