<?php

namespace Sudoux\MortgageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
/**
 * Class AssetAccount
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class AssetAccount
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


    /*
    public function __clone(){

        if ($this->id) {

            $this->id = NULL;

        }

    }

    */


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
     * @return AssetAccount
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
     * @return AssetAccount
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

    public function getTypeText()
    {
    	$types = array(
	        0 => 'Checking',		
	        1 => 'Savings',		
	        2 => 'Money Market',		
	        3 => 'CD',		
	        4 => 'Mutual Fund',		
	        5 => 'Retirement',		
			6 => 'Other',		
        );
        return $types[$this->type];
    }

    /**
     * Set account_number
     *
     * @param string $accountNumber
     * @return AssetAccount
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
     * @return AssetAccount
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
     * @var \Sudoux\MortgageBundle\Entity\Borrower
     */
    private $borrower;


    /**
     * Set borrower
     *
     * @param \Sudoux\MortgageBundle\Entity\Borrower $borrower
     * @return AssetAccount
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