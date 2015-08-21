<?php

namespace Sudoux\EagleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class EagleIncomeOther
 * @package Sudoux\EagleBundle\Entity
 * @author Eric Haynes
 */
class EagleIncomeOther
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
     * @var string
     */
    private $description;

    /**
     * @var float
     */
    private $income;

    /**
     * @var
     * @author Eric Haynes
     */
    private $borrower;


    /**
     * Set description
     *
     * @param string $description
     * @return EagleIncomeOther
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
     * @return EagleIncomeOther
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
     * Set borrower
     *
     * @param \Sudoux\EagleBundle\Entity\EagleBorrower $borrower
     * @return EagleIncomeOther
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