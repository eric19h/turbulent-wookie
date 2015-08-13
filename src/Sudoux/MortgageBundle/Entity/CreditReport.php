<?php

namespace Sudoux\MortgageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
/**
 * Class CreditReport
 * @package Sudoux\MortgageBundle\Entity
 * @author Dan Alvare
 * @ExclusionPolicy("all")
 */
class CreditReport
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $experian_score;

    /**
     * @var integer
     */
    private $transunion_score;

    /**
     * @var integer
     */
    private $equifax_score;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $modified;

    public function __construct()
    {
    	$this->created = new \DateTime();
    	$this->modified = new \DateTime();
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
     * Set experian_score
     *
     * @param integer $experianScore
     * @return CreditReport
     */
    public function setExperianScore($experianScore)
    {
        $this->experian_score = $experianScore;
    
        return $this;
    }

    /**
     * Get experian_score
     *
     * @return integer 
     */
    public function getExperianScore()
    {
        return $this->experian_score;
    }

    /**
     * Set transunion_score
     *
     * @param integer $transunionScore
     * @return CreditReport
     */
    public function setTransunionScore($transunionScore)
    {
        $this->transunion_score = $transunionScore;
    
        return $this;
    }

    /**
     * Get transunion_score
     *
     * @return integer 
     */
    public function getTransunionScore()
    {
        return $this->transunion_score;
    }

    /**
     * Set equifax_score
     *
     * @param integer $equifaxScore
     * @return CreditReport
     */
    public function setEquifaxScore($equifaxScore)
    {
        $this->equifax_score = $equifaxScore;
    
        return $this;
    }

    /**
     * Get equifax_score
     *
     * @return integer 
     */
    public function getEquifaxScore()
    {
        return $this->equifax_score;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return CreditReport
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
     * Set modified
     *
     * @param \DateTime $modified
     * @return CreditReport
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    
        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime 
     */
    public function getModified()
    {
        return $this->modified;
    }
    /**
     * @var \Sudoux\MortgageBundle\Entity\CreditProvider
     */
    private $credit_provider;


    /**
     * Set credit_provider
     *
     * @param \Sudoux\MortgageBundle\Entity\CreditProvider $creditProvider
     * @return CreditReport
     */
    public function setCreditProvider(\Sudoux\MortgageBundle\Entity\CreditProvider $creditProvider)
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
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $borrower;


    /**
     * Add borrower
     *
     * @param \Sudoux\MortgageBundle\Entity\Borrower $borrower
     * @return CreditReport
     */
    public function addBorrower(\Sudoux\MortgageBundle\Entity\Borrower $borrower)
    {
        $this->borrower[] = $borrower;
    
        return $this;
    }

    /**
     * Remove borrower
     *
     * @param \Sudoux\MortgageBundle\Entity\Borrower $borrower
     */
    public function removeBorrower(\Sudoux\MortgageBundle\Entity\Borrower $borrower)
    {
        $this->borrower->removeElement($borrower);
    }

    /**
     * Get borrower
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBorrower()
    {
        return $this->borrower;
    }
    /**
     * @var \Sudoux\Cms\FileBundle\Entity\File
     */
    private $report_file;


    /**
     * Set report_file
     *
     * @param \Sudoux\Cms\FileBundle\Entity\File $reportFile
     * @return CreditReport
     */
    public function setReportFile(\Sudoux\Cms\FileBundle\Entity\File $reportFile = null)
    {
        $this->report_file = $reportFile;
    
        return $this;
    }

    /**
     * Get report_file
     *
     * @return \Sudoux\Cms\FileBundle\Entity\File 
     */
    public function getReportFile()
    {
        return $this->report_file;
    }
}