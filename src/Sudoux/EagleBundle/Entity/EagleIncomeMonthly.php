<?php

namespace Sudoux\EagleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class EagleIncomeMonthly
 * @package Sudoux\EagleBundle\Entity
 * @author Eric Haynes
 * @ExclusionPolicy()
 */
class EagleIncomeMonthly
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
     * @var
     * @author Eric Haynes
     */
    private $borrower;



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
     * @return EagleIncomeMonthly
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
     * @return EagleIncomeMonthly
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
     * @return EagleIncomeMonthly
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
     * @return EagleIncomeMonthly
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
     * @return EagleIncomeMonthly
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
     * @return EagleIncomeMonthly
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
     * @return EagleIncomeMonthly
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
     * @param \Sudoux\EagleBundle\Entity\EagleBorrower $borrower
     * @return EagleIncomeMonthly
     */
    public function setBorrower(\Sudoux\EagleBundle\Entity\EagleBorrower $borrower = null)
    {
        $this->borrower = $borrower;
    
        return $this;
    }

    /**
     * Get borrower
     *
     * @return \Sudoux\EagleBundle\Entity\EagleBorrower
     */
    public function getBorrower()
    {
        return $this->borrower;
    }
}