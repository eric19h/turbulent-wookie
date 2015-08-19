<?php

namespace Sudoux\EagleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Class CreditReportFull
 * @package Sudoux\EagleBundle\Entity
 * @author Eric Haynes
 * @ExclusionPolicy("all")
 */
class CreditReportFull
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
    private $experian_score;

    /**
     * @var integer
     * @Expose()
     */
    private $transunion_score;

    /**
     * @var integer
     * @Expose()
     */
    private $equifax_score;

    /**
     * @var \DateTime
     * @Expose()
     */
    private $created;

    /**
     * @var \DateTime
     * @Expose()
     */
    private $modified;

    /**
     * @var \Sudoux\Cms\FileBundle\Entity\File
     * @Expose()
     */
    private $report_file;

    /**
     * @var \Sudoux\MortgageBundle\Entity\CreditProvider
     * @Expose()
     */
    private $credit_provider;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @author Eric Haynes
     */
    private $borrower;
    /**
     * Constructor
     */


    public function __construct()
    {
        $this->borrower = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return CreditReportFull
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
     * @return CreditReportFull
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
     * @return CreditReportFull
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
     * @return CreditReportFull
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
     * @return CreditReportFull
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
     * Add borrower
     *
     * @param \Sudoux\EagleBundle\Entity\BorrowerFull $borrower
     * @return CreditReportFull
     */
    public function addBorrower(\Sudoux\EagleBundle\Entity\BorrowerFull $borrower)
    {
        $this->borrower[] = $borrower;
    
        return $this;
    }

    /**
     * Remove borrower
     *
     * @param \Sudoux\EagleBundle\Entity\BorrowerFull $borrower
     */
    public function removeBorrower(\Sudoux\EagleBundle\Entity\BorrowerFull $borrower)
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
     * Set report_file
     *
     * @param \Sudoux\Cms\FileBundle\Entity\File $reportFile
     * @return CreditReportFull
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

    /**
     * Set credit_provider
     *
     * @param \Sudoux\MortgageBundle\Entity\CreditProvider $creditProvider
     * @return CreditReportFull
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
}