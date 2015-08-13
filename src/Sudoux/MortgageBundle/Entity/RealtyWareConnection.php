<?php

namespace Sudoux\MortgageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
/**
 * Class RealtyWareConnection
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class RealtyWareConnection
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $client_id;

    /**
     * @var string
     */
    private $client_secret;


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
     * Set domain
     *
     * @param string $domain
     * @return RealtyWareConnection
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    
        return $this;
    }

    /**
     * Get domain
     *
     * @return string 
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set client_id
     *
     * @param string $clientId
     * @return RealtyWareConnection
     */
    public function setClientId($clientId)
    {
        $this->client_id = $clientId;
    
        return $this;
    }

    /**
     * Get client_id
     *
     * @return string 
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * Set client_secret
     *
     * @param string $clientSecret
     * @return RealtyWareConnection
     */
    public function setClientSecret($clientSecret)
    {
        $this->client_secret = $clientSecret;
    
        return $this;
    }

    /**
     * Get client_secret
     *
     * @return string 
     */
    public function getClientSecret()
    {
        return $this->client_secret;
    }
    /**
     * @var \DateTime
     */
    private $created;


    /**
     * Set created
     *
     * @param \DateTime $created
     * @return RealtyWareConnection
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
     * @var string
     */
    private $api_key;


    /**
     * Set api_key
     *
     * @param string $apiKey
     * @return RealtyWareConnection
     */
    public function setApiKey($apiKey)
    {
        $this->api_key = $apiKey;
    
        return $this;
    }

    /**
     * Get api_key
     *
     * @return string 
     */
    public function getApiKey()
    {
        return $this->api_key;
    }
}