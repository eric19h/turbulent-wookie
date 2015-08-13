<?php

namespace Sudoux\MortgageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
/**
 * Class LosProvider
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class LosProvider
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $className;

    /**
     * @var \DateTime
     */
    private $created;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->documents = true;
        $this->messages = false;
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
     * Set name
     *
     * @param string $name
     * @return LosProvider
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set className
     *
     * @param string $className
     * @return LosProvider
     */
    public function setClassName($className)
    {
        $this->className = $className;
    
        return $this;
    }

    /**
     * Get className
     *
     * @return string 
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return LosProvider
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
     * @var boolean
     */
    private $documents;

    /**
     * @var boolean
     */
    private $messages;


    /**
     * Set documents
     *
     * @param boolean $documents
     * @return LosProvider
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;
    
        return $this;
    }

    /**
     * Get documents
     *
     * @return boolean 
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Set messages
     *
     * @param boolean $messages
     * @return LosProvider
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    
        return $this;
    }

    /**
     * Get messages
     *
     * @return boolean 
     */
    public function getMessages()
    {
        return $this->messages;
    }
}