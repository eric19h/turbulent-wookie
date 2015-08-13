<?php

namespace Sudoux\MortgageBundle\Entity;

use Sudoux\Cms\SecurityBundle\DependencyInjection\EncryptionUtil;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
/**
 * Class CreditConnection
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class CreditConnection
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $company;

    /**
     * @var \DateTime
     */
    private $created;

    public function __construct()
    {
    	$this->created = new \DateTime();
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
     * Set username
     *
     * @param string $username
     * @return CreditConnection
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return CreditConnection
     */
    public function setPassword($password)
    {
    	if(!empty($password)) {
    		$encryptionUtil = new EncryptionUtil();
    		$encryptedPassword = $encryptionUtil->encrypt($password);
    		$this->password = $encryptedPassword;
    	}
    	    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
    	$encryptionUtil = new EncryptionUtil();
    	return $encryptionUtil->decrypt($this->password);
    }

    /**
     * Set company
     *
     * @param string $company
     * @return CreditConnection
     */
    public function setCompany($company)
    {
        $this->company = $company;
    
        return $this;
    }

    /**
     * Get company
     *
     * @return string 
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return CreditConnection
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }
    /**
     * @var \Sudoux\MortgageBundle\Entity\CreditProvider
     */
    private $credit_provider;


    /**
     * Set credit_provider
     *
     * @param \Sudoux\MortgageBundle\Entity\CreditProvider $creditProvider
     * @return CreditConnection
     */
    public function setCreditProvider(\Sudoux\MortgageBundle\Entity\CreditProvider $creditProvider = null)
    {
        $this->credit_provider = $creditProvider;
    
        return $this;
    }

    /**
     * Get credit_provider
     *
     * @return \Sudoux\MortgageBundle\Entity\CreditProvider 
     */
    public function getCreditProvider()
    {
        return $this->credit_provider;
    }
}