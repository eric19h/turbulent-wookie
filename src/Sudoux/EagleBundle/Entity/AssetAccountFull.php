<?php

namespace Sudoux\EagleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AssetAccountFull
 * @package Sudoux\EagleBundle\Entity
 * @author Eric Haynes
 */
class AssetAccountFull
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
    private $institution_name;

    /**
     * @var integer
     */
    private $type;

    /**
     * @var string
     */
    private $account_number;

    /**
     * @var float
     */
    private $balance;

    /**
     * @var \Sudoux\EagleBundle\Entity\BorrowerFull
     */
    private $borrower;


    /**
     * Set institution_name
     *
     * @param string $institutionName
     * @return AssetAccountFull
     */
    public function setInstitutionName($institutionName)
    {
        $this->institution_name = $institutionName;
    
        return $this;
    }

    /**
     * Get institution_name
     *
     * @return string 
     */
    public function getInstitutionName()
    {
        return $this->institution_name;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return AssetAccountFull
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set account_number
     *
     * @param string $accountNumber
     * @return AssetAccountFull
     */
    public function setAccountNumber($accountNumber)
    {
        $this->account_number = $accountNumber;
    
        return $this;
    }

    /**
     * Get account_number
     *
     * @return string 
     */
    public function getAccountNumber()
    {
        return $this->account_number;
    }

    /**
     * Set balance
     *
     * @param float $balance
     * @return AssetAccountFull
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    
        return $this;
    }

    /**
     * Get balance
     *
     * @return float 
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set borrower
     *
     * @param \Sudoux\EagleBundle\Entity\BorrowerFull $borrower
     * @return AssetAccountFull
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