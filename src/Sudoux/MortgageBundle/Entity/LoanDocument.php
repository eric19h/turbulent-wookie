<?php

namespace Sudoux\MortgageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
/**
 * Class LoanDocument
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class LoanDocument
{
    /**
     * @var integer
     * @Expose()
     */
    private $id;

    /**
     * @var integer
     * @Expose()
     */
    private $status;

    /**
     * @var object
     * @Expose()
     */
    public $file_field;

    /**
     * @var string
     * @Expose()
     */
    public $name;

    /**
     * @var array
     */
    public $statusValues = array( 
    	0 => 'New', 
    	1 => 'Not Submitted', 
    	2 => 'Awaiting Review', 
    	3 => 'Accepted', 
    	4 => 'Rejected'
    );

    /**
     * @var array
     */
    public $losStatusValues = array(
    	0 => 'Not Sent',
    	1 => 'Sent',
    	2 => 'Pending Download',
    	3 => 'Pending Upload',
    	4 => 'Received',
    	5 => 'Failed',
        6 => 'Queued',
    );

    /**
     *
     */
    public function __construct()
    {
    	$this->status = 0;
    	$this->created = new \DateTime();
    	$this->required = false;
    	$this->los_status = 0;
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
     * Set status
     *
     * @param integer $status
     * @return LoanDocument
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

    public function getStatusName()
    {
        return $this->statusValues[$this->status];
    }
    /**
     * @var \DateTime
     */
    private $created;


    /**
     * Set created
     *
     * @param \DateTime $created
     * @return LoanDocument
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
     * @var \Sudoux\Cms\TaxonomyBundle\Entity\Taxonomy
     */
    private $type;


    /**
     * Set type
     *
     * @param \Sudoux\Cms\TaxonomyBundle\Entity\Taxonomy $type
     * @return LoanDocument
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
     * @var \Sudoux\Cms\FileBundle\Entity\File
     * @Expose()
     */
    private $file;


    /**
     * Set file
     *
     * @param \Sudoux\Cms\FileBundle\Entity\File $file
     * @return LoanDocument
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
     * @var boolean
     * @Expose()
     */
    private $required;


    /**
     * Set required
     *
     * @param boolean $required
     * @return LoanDocument
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
    
    public function serialize()
    {
    	$document = new \stdClass();
    	$document->name = $this->getFile()->getName();
    	$document->filePath = $this->getFile()->getAbsolutePath();
    	
    }
    /**
     * @var string
     * @Expose()
     */
    private $los_id;


    /**
     * Set los_id
     *
     * @param string $losId
     * @return LoanDocument
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $loan;


    /**
     * Add loan
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $loan
     * @return LoanDocument
     */
    public function addLoan(\Sudoux\MortgageBundle\Entity\LoanApplication $loan)
    {
        $this->loan[] = $loan;
    
        return $this;
    }

    /**
     * Remove loan
     *
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $loan
     */
    public function removeLoan(\Sudoux\MortgageBundle\Entity\LoanApplication $loan)
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
    /**
     * @var integer
     */
    private $los_status;


    /**
     * Set los_status
     *
     * @param integer $losStatus
     * @return LoanDocument
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
    
    public function getLosStatusValue()
    {
    	return $this->losStatusValues[$this->los_status];
    }

    /**
     * Set name
     *
     * @param string $name
     * @return LoanDocument
     */
    public function setName($name)
    {
        $this->name = $name;
        
        /// hack for late processing of the file. document should be subclass of file in next major release
        if(isset($this->file)) {
        	$this->file->setName($name);
        }
    
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
     * @var string
     * @Expose()
     */
    private $extension;


    /**
     * Set extension
     *
     * @param string $extension
     * @return LoanDocument
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
}