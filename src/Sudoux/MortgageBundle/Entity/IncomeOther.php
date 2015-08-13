<?php

namespace Sudoux\MortgageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class IncomeOther
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class IncomeOther
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
    private $description;

    /**
     * @var float
     * @Expose()
     */
    private $income;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /*
    public function __clone(){

        if ($this->id) {

            $this->id = NULL;

        }

    }
    */

    /**
     * Set description
     *
     * @param string $description
     * @return IncomeOther
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set income
     *
     * @param float $income
     * @return IncomeOther
     */
    public function setIncome($income)
    {
        $this->income = $income;
    
        return $this;
    }

    /**
     * Get income
     *
     * @return float 
     */
    public function getIncome()
    {
        return $this->income;
    }
    /**
     * @var \Sudoux\MortgageBundle\Entity\Borrower
     *
     */
    private $borrower;


    /**
     * Set borrower
     *
     * @param \Sudoux\MortgageBundle\Entity\Borrower $borrower
     * @return IncomeOther
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