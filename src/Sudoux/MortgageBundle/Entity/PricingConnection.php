<?php

namespace Sudoux\MortgageBundle\Entity;

use Sudoux\Cms\SecurityBundle\DependencyInjection\EncryptionUtil;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
/**
 * Class PricingConnection
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class PricingConnection
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
     * @return PricingConnection
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
     * @return PricingConnection
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
     * @return PricingConnection
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
     * @return PricingConnection
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
     * @var \Sudoux\MortgageBundle\Entity\PricingProvider
     */
    private $pricing_provider;


    /**
     * Set pricing_provider
     *
     * @param \Sudoux\MortgageBundle\Entity\PricingProvider $pricingProvider
     * @return PricingConnection
     */
    public function setPricingProvider(\Sudoux\MortgageBundle\Entity\PricingProvider $pricingProvider = null)
    {
        $this->pricing_provider = $pricingProvider;
    
        return $this;
    }

    /**
     * Get pricing_provider
     *
     * @return \Sudoux\MortgageBundle\Entity\PricingProvider 
     */
    public function getPricingProvider()
    {
        return $this->pricing_provider;
    }
}