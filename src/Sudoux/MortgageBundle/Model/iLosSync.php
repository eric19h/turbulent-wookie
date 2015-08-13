<?php

namespace Sudoux\MortgageBundle\Model;

use Sudoux\Cms\SiteBundle\Entity\Site;

use Sudoux\MortgageBundle\Entity\Borrower;

use Sudoux\MortgageBundle\Entity\LoanApplication;
use Sudoux\MortgageBundle\Entity\LoanDocument;

/**
 * Interface iLosSync
 * @package Sudoux\MortgageBundle\Model
 * @author Dan Alvare
 */
interface iLosSync
{
    /**
     * @return mixed
     */
	public function tryLogin();

    /**
     * @param \DateTime $startDate
     * @return mixed
     */
	public function getDeletedLoans(\DateTime $startDate);

    /**
     * @param LoanApplication $application
     * @return mixed
     */
	public function forceUnlockLoan(LoanApplication $application);

    /**
     * @param LoanApplication $application
     * @return mixed
     */
	public function loanLocked(LoanApplication $application);

    /**
     * @param LoanApplication $application
     * @return mixed
     */
	public function getLoan(LoanApplication $application);

    /**
     * @param LoanApplication $application
     * @param $format
     * @return mixed
     */
	public function exportLoan(LoanApplication $application, $format);

    /**
     * @param LoanApplication $application
     * @return mixed
     */
	public function deleteLoan(LoanApplication $application);

    /**
     * @param LoanApplication $application
     * @return mixed
     */
	public function upsertLoanToLos(LoanApplication $application);

    /**
     * @param LoanApplication $application
     * @return mixed
     */
	public function upsertLoanFromLos(LoanApplication $application);

    /**
     * @param Site $site
     * @param $losId
     * @return mixed
     */
	public function createLoanFromLos(Site $site, $losId);

    /**
     * @param LoanApplication $application
     * @return mixed
     */
	public function getLoanMilestones(LoanApplication $application);

    /**
     * @param LoanApplication $application
     * @return mixed
     */
	public function getLoanMilestone(LoanApplication $application);

    /**
     * @param LoanApplication $application
     * @param $milestoneId
     * @param $milestoneGroupId
     * @return mixed
     */
	public function setLoanMilestone(LoanApplication $application, $milestoneId, $milestoneGroupId);

    /**
     * @return mixed
     */
	public function getAllMilestones();

    /**
     * @param LoanApplication $application
     * @param LoanDocument $document
     * @return mixed
     */
	public function addDocument(LoanApplication $application, LoanDocument $document);

    /**
     * @param LoanApplication $application
     * @return mixed
     */
	public function upsertDocuments(LoanApplication $application);

    /**
     * @param LoanApplication $application
     * @return mixed
     */
	public function getDocuments(LoanApplication $application);

    /**
     * @param $serviceFileName
     * @param $outputFileName
     * @return mixed
     */
	public function getFile($serviceFileName, $outputFileName);

    /**
     * @param LoanApplication $application
     * @return mixed
     */
	public function addProspects(LoanApplication $application);
}