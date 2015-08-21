<?php

namespace Sudoux\EagleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class EagleAssetAccount
 * @package Sudoux\EagleBundle\Entity
 * @author Eric Haynes
 * @ExclusionPolicy("all")
 */
class EagleAssetAccount
{
    public  $types = array(
        0 => 'Checking',
        1 => 'Savings',
        2 => 'Money Market',
        3 => 'CD',
        4 => 'Mutual Fund',
        5 => 'Retirement',
        6 => 'Other',
    );



    /**
     * @var integer
     * @Expose()
     */
    private $id;

    /**
     * @var string
     * @Expose()
     */
    private $institution_name;

    /**
     * @var integer
     * @Expose()
     */
    private $type;

    /**
     * @var string
     * @Expose()
     */
    private $account_number;

    /**
     * @var float
     * @Expose()
     */
    private $balance;

    /**
     * @var
     * @author Eric Haynes
     */
    private $borrower;

    public function getTypeText()
    {
        if(isset($this->types)){
            return $this->types[$this->type];
        }
        return null;

    }

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
     * Set institution_name
     *
     * @param string $institutionName
     * @return EagleAssetAccount
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
     * @return EagleAssetAccount
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
     * @return EagleAssetAccount
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
     * @return EagleAssetAccount
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
     * @param \Sudoux\EagleBundle\Entity\EagleBorrower $borrower
     * @return EagleAssetAccount
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