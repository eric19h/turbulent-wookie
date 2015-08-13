<?php

namespace Sudoux\MortgageBundle\DependencyInjection;

use Sudoux\MortgageBundle\Entity\LosConnection;

use Sudoux\MortgageBundle\Entity\LoanDocument;

use Sudoux\MortgageBundle\Model\Los\Encompass;
use Sudoux\MortgageBundle\Model\Los\MortgageBuilder;
use Sudoux\MortgageBundle\Model\Los\Vantage;
use Sudoux\MortgageBundle\Model\Los\Destiny;
use Sudoux\MortgageBundle\Model\LosSync;
use Sudoux\MortgageBundle\Entity\LoanApplication;
use Sudoux\Cms\SiteBundle\DependencyInjection\SiteRequest;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Guzzle\Http\Client;

/**
 * Class LosUtil
 * @package Sudoux\MortgageBundle\DependencyInjection
 * @author Dan Alvare
 */
class LosUtil
{
    /**
     * @var ContainerInterface
     */
	protected $container;

    /**
     * @var \Twig_Environment
     */
	protected $twig;

    /**
     * @var LosSync
     */
	protected $losSync;

    /**
     * @param ContainerInterface $container
     * @param \Twig_Environment $twig
     */
	public function __construct(ContainerInterface $container, \Twig_Environment $twig)
	{
		$this->container = $container;
		$this->twig = $twig;
	}

    /**
     * @param Site $site
     */
	protected function setLosSync(Site $site)
	{
		$losConnection = $site->getSettings()->getInheritedLos();
		if(isset($losConnection)) {
			$losProvider = $losConnection->getLosProvider();
			$losClass = $losProvider->getClassName();

			if($losClass == 'Sudoux\MortgageBundle\Model\Los\Encompass') {
				$this->losSync = new Encompass($losConnection, $this->twig, $this->container);
			} elseif($losClass == 'Sudoux\MortgageBundle\Model\Los\MortgageBuilder') {
				$this->losSync = new MortgageBuilder($losConnection, $this->twig, $this->container);
			} elseif($losClass == 'Sudoux\MortgageBundle\Model\Los\Destiny') {
                $this->losSync = new Destiny($losConnection, $this->twig, $this->container);
            } elseif($losClass == 'Sudoux\MortgageBundle\Model\Los\Vantage') {
                $this->losSync = new Vantage($losConnection, $this->twig, $this->container);
            } elseif($losClass == 'Sudoux\MortgageBundle\Model\Los\LendingQb') {
                $this->losSync = new Vantage($losConnection, $this->twig, $this->container);
            } else{
				throw new \Exception('LOS not found');
			}


			//$this->losSync = new LosSync($this->losConnection, $twig, $em, $this->site);
		}		
	}

    /**
     * @param Site $site
     * @return array
     */
	public function tryLogin(Site $site)
	{
		$this->setLosSync($site);
		
		$response = new \stdClass();
		
		if(isset($this->losSync)) {
			return $this->losSync->tryLogin();
		}
	}

    /**
     * @param LoanApplication $application
     * @throws \Exception
     */
	public function upsertLoanToLos(LoanApplication $application)
	{
		$this->setLosSync($application->getSite());
		
		if(isset($this->losSync)) {
			$this->losSync->upsertLoanToLos($application);
		}
	}

    /**
     * @param Site $site
     * @param $losId
     */
	public function createLoanFromLos(Site $site, $losId)
	{
		$this->setLosSync($site);
		
		if(isset($this->losSync)) {
			$this->losSync->createLoanFromLos($site, $losId);
		}
	}

    /**
     * @param Site $site
     * @param $fanniemaeData
     * @param LoanApplication $application
     */
	public function upsertLoanFromFanniemaeFormat(Site $site, $fanniemaeData, LoanApplication $application = null)
	{
		$this->setLosSync($site);
		
		if(isset($this->losSync)) {
			$this->losSync->upsertLoanFromFanniemaeFormat($site, $fanniemaeData, $application, true);
		}		
	}


    public function createLoanApplicationFromDestiny(Site $site, $fanniemaeData, LoanApplication $application = null)
    {
        $this->setLosSync($site);

        if(isset($this->losSync)) {
            $this->losSync->upsertLoanFromFanniemaeFormat($site, $fanniemaeData, $application, true);
        }
    }
    /**
     * @param LoanApplication $application
     * @throws
     * @throws \Exception
     */
	public function upsertLoanFromLos(LoanApplication $application)
	{
		$this->setLosSync($application->getSite());
		
		if(isset($this->losSync)) {
			$this->losSync->upsertLoanFromLos($application);
		}
	}

    /**
     * @param LoanApplication $application
     * @return array|bool|float|int|null|string
     * @throws \Exception
     */
	public function getLoan(LoanApplication $application)
	{
		$this->setLosSync($application->getSite());
		
		if(isset($this->losSync)) {
			return $this->losSync->getLoan($application);
		}
	}

    /**
     * @param Site $site
     * @param \DateTime $modified
     * @return array|bool|float|int|null|string
     */
	public function getLoans(Site $site, \DateTime $modified)
	{
		$this->setLosSync($site);
		
		if(isset($this->losSync)) {
			return $this->losSync->getLoans($modified);
		}
	}

    /**
     * @param Site $site
     * @param \DateTime $modified
     * @return array
     */
	public function getDeletedLoans(Site $site, \DateTime $modified)
	{
		$this->setLosSync($site);
		
		if(isset($this->losSync)) {
			return $this->losSync->getDeletedLoans($modified);
		}
	}

    /**
     * @param LoanApplication $application
     * @return array|bool|float|int|null|string
     */
	public function deleteLoan(LoanApplication $application)
	{
		$this->setLosSync($application->getSite());
		
		if(isset($this->losSync)) {
			return $this->losSync->deleteLoan($application);
		}
	}

    /**
     * @param LoanApplication $application
     * @return array|bool|float|int|null|string
     */
	public function getLoanMilestone(LoanApplication $application)
	{
		$this->setLosSync($application->getSite());
		
		if(isset($this->losSync)) {
			return $this->losSync->getLoanMilestone($application);
		}
	}

    /**
     * @param LoanApplication $application
     * @return array|bool|float|int|null|string
     */
	public function getLoanMilestones(LoanApplication $application)
	{
		$this->setLosSync($application->getSite());
		
		if(isset($this->losSync)) {
			return $this->losSync->getLoanMilestones($application);
		}
	}

    /**
     * @param Site $site
     * @return array|bool|float|int|null|string
     */
	public function getAllMilestones(Site $site)
	{
		$this->setLosSync($site);
		
		if(isset($this->losSync)) {
			return $this->losSync->getAllMilestones();
		}
	}

    /**
     * @todo - this may be wrong
     * @param $fileName
     * @return string
     */
	/*public function getFile($fileName)
	{
		if(isset($this->losSync)) {
			return $this->losSync->getFile($fileName);
		}		
	}*/

    /**
     * @param LoanApplication $application
     */
	public function getAttachments(LoanApplication $application)
	{
		$this->setLosSync($application->getSite());
		
		if(isset($this->losSync)) {
			$this->losSync->getAttachments($application);
		}
	}

    /**
     * @param LoanApplication $application
     * @param string $format
     * @return array|bool|float|int|null|string
     */
	public function exportLoan(LoanApplication $application, $format = "AUS24")
	{
		
		$this->setLosSync($application->getSite());
		
		if(isset($this->losSync)) {
			return $this->losSync->exportLoan($application, $format);			
		}
	}

    /**
     * @param LoanApplication $application
     * @return array
     */
	public function getDocuments(LoanApplication $application)
	{
		$this->setLosSync($application->getSite());
		
		if(isset($this->losSync)) {
			return $this->losSync->getDocuments($application);
		}
	}

    /**
     * @param LoanApplication $application
     * @param LoanDocument $document
     * @return array|bool|float|int|null|string
     * @throws
     */
	public function addDocument(LoanApplication $application, LoanDocument $document)
	{
		$this->setLosSync($application->getSite());
		
		if(isset($this->losSync)) {
			return $this->losSync->addDocument($application, $document);
		}
	}

    /**
     * @param LoanApplication $application
     * @param LoanDocument $document
     * @throws \Exception
     */
	public function setDocumentFile(LoanApplication $application, LoanDocument $document)
	{
		$this->setLosSync($application->getSite());
		
		if(isset($this->losSync)) {
			return $this->losSync->setDocumentFile($application, $document);
		}
	}

    /**
     * @param LoanApplication $application
     * @return mixed
     */
	public function forceUnlockLoan(LoanApplication $application)
	{
		$this->setLosSync($application->getSite());
		
		if(isset($this->losSync)) {
			return $this->losSync->unlockLoan($application);
		}
	}

    /**
     * @param LoanApplication $application
     * @return array|bool|float|int|null|string
     */
	public function loanLocked(LoanApplication $application)
	{
		$this->setLosSync($application->getSite());
		
		if(isset($this->losSync)) {
			return $this->losSync->loanLocked($application);
		}
	}

    /**
     * @param LoanApplication $application
     * @return array|bool|float|int|null|string
     */
	public function addProspects(LoanApplication $application)
	{
		$this->setLosSync($application->getSite());
		
		if(isset($this->losSync)) {
			return $this->losSync->addProspects($application);
		}
	}

	public function getLoanOfficers(Site $site)
	{
		$this->setLosSync($site);

		if(isset($this->losSync)) {
			return $this->losSync->getLoanOfficers();
		}
	}

	public function setLoanMilestone(LoanApplication $application, $milestoneId, $milestoneGroupId)
	{

		$site = $application->getSite();
		$this->setLosSync($site);

		if(isset($this->losSync)) {
			return $this->losSync->setLoanMilestone( $application, $milestoneId, $milestoneGroupId);
		}
	}


}
