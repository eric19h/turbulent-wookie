<?php

namespace Sudoux\EagleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class LoanDocumentFull
 * @package Sudoux\EagleBundle\Entity
 * @author Eric Haynes
 */
class LoanDocumentFull
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
    private $extension;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var boolean
     */
    private $required;

    /**
     * @var string
     */
    private $los_id;

    /**
     * @var integer
     */
    private $los_status;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \Sudoux\Cms\FileBundle\Entity\File
     */
    private $file;

    /**
     * @var \Sudoux\Cms\TaxonomyBundle\Entity\Taxonomy
     */
    private $type;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $loan;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->loan = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return LoanDocumentFull
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
     * Set extension
     *
     * @param string $extension
     * @return LoanDocumentFull
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    
        return $this;
    }

    /**
     * Get extension
     *
     * @return string 
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return LoanDocumentFull
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set required
     *
     * @param boolean $required
     * @return LoanDocumentFull
     */
    public function setRequired($required)
    {
        $this->required = $required;
    
        return $this;
    }

    /**
     * Get required
     *
     * @return boolean 
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set los_id
     *
     * @param string $losId
     * @return LoanDocumentFull
     */
    public function setLosId($losId)
    {
        $this->los_id = $losId;
    
        return $this;
    }

    /**
     * Get los_id
     *
     * @return string 
     */
    public function getLosId()
    {
        return $this->los_id;
    }

    /**
     * Set los_status
     *
     * @param integer $losStatus
     * @return LoanDocumentFull
     */
    public function setLosStatus($losStatus)
    {
        $this->los_status = $losStatus;
    
        return $this;
    }

    /**
     * Get los_status
     *
     * @return integer 
     */
    public function getLosStatus()
    {
        return $this->los_status;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return LoanDocumentFull
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
     * Set file
     *
     * @param \Sudoux\Cms\FileBundle\Entity\File $file
     * @return LoanDocumentFull
     */
    public function setFile(\Sudoux\Cms\FileBundle\Entity\File $file = null)
    {
        $this->file = $file;
    
        return $this;
    }

    /**
     * Get file
     *
     * @return \Sudoux\Cms\FileBundle\Entity\File 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set type
     *
     * @param \Sudoux\Cms\TaxonomyBundle\Entity\Taxonomy $type
     * @return LoanDocumentFull
     */
    public function setType(\Sudoux\Cms\TaxonomyBundle\Entity\Taxonomy $type = null)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return \Sudoux\Cms\TaxonomyBundle\Entity\Taxonomy 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add loan
     *
     * @param \Sudoux\EagleBundle\Entity\LoanApplicationFull $loan
     * @return LoanDocumentFull
     */
    public function addLoan(\Sudoux\EagleBundle\Entity\LoanApplicationFull $loan)
    {
        $this->loan[] = $loan;
    
        return $this;
    }

    /**
     * Remove loan
     *
     * @param \Sudoux\EagleBundle\Entity\LoanApplicationFull $loan
     */
    public function removeLoan(\Sudoux\EagleBundle\Entity\LoanApplicationFull $loan)
    {
        $this->loan->removeElement($loan);
    }

    /**
     * Get loan
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLoan()
    {
        return $this->loan;
    }
}