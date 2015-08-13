<?php 
namespace Sudoux\MortgageBundle\Command;

use Doctrine\ORM\EntityManager;
use Sudoux\Cms\SiteBundle\Entity\BatchJob;
use Sudoux\Cms\SiteBundle\Entity\BatchJobLog;
use Sudoux\MortgageBundle\Entity\LoanApplication;
use Sudoux\MortgageBundle\Entity\LoanMilestone;

use Sudoux\MortgageBundle\Entity\LoanMilestoneGroup;

use Sudoux\MortgageBundle\DependencyInjection\LosUtil;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sudoux\Cms\SiteBundle\Entity\Site;

/**
 * Class LoanCommand
 * @package Sudoux\MortgageBundle\Command
 * @author Dan Alvare
 */
class LoanCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
	protected $em;

    /**
     * @var
     */
	protected $twig;

    /**
     * @var
     */
	protected $logger;

    /**
     * @var OutputInterface
     */
	protected $output;

    /**
     * @var LoanApplication
     */
	protected $loan;

    /**
     * @var LosUtil
     */
	protected $loanUtil;

	/**
	 * @var
	 */
	protected $verbose;

	/**
	 * @var
	 */
	protected $batchJob;

	protected $batchCommand = false;
	
    protected function configure()
    {
        $this
            ->setName('sudoux:mortgage:loan')
            ->setDescription('Loan Utility')
            ->addArgument('function', InputArgument::REQUIRED)
            ->addOption('loan_id',
            	null,
				InputOption::VALUE_OPTIONAL,
				'The loan ID to process'	
			)
			->addOption('document_id',
				null,
				InputOption::VALUE_OPTIONAL,
				'The document ID to process.'	
			)
			->addOption('start_date',
				null,
				InputOption::VALUE_OPTIONAL,
				'The start date for bulk processing.'	
			)
			->addOption('site_id',
				null,
				InputOption::VALUE_OPTIONAL,
				'The site id for processing.'	
			)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	ini_set('memory_limit', -1);
    	set_time_limit(0);

        //$output->writeln('Timezone: ' . date_default_timezone_get());
    	
    	$availableFunctions = array(
    		'upsert_loan_to_los', 
    		'upsert_loan_from_los', 
    		'delete_loan', 
    		'add_document', 
    		'update_loans', 
    		'delete_loans', 
    		'cron', 
    		'add_loan_prospects',
    		'upsert_milestones',
			'add_queued_docs'
    	);
    	
    	$function = $input->getArgument('function');
		
    	if(!in_array($function, $availableFunctions)) {
    		throw new \Exception("Function does not exist");
    	}
    	
    	$this->em = $this->getContainer()->get('doctrine')->getEntityManager();
    	$this->logger = $this->getContainer()->get('logger');
		$this->output = $output;
    	
    	$loanId = $input->getOption('loan_id');
    	if(isset($loanId)) {
			$this->loan = $this->em->getRepository('SudouxMortgageBundle:LoanApplication')->find($loanId);
			if(!isset($this->loan)) {
				throw new \Exception('Loan not found');
			}
    	}
		
		$this->loanUtil = $this->getContainer()->get('sudoux_mortgage.los_util');

		$batchFunctions = array('update_loans', 'delete_loans', 'cron');
		if(in_array($function, $batchFunctions)) {
			$this->batchCommand = true;
			$this->batchJob = new BatchJob();
			$this->batchJob->setName('Loan Command: ' . $function);
			$this->em->persist($this->batchJob);
			$this->em->flush($this->batchJob);
		}

    	switch($function) {
    		case 'upsert_loan_to_los':
		    	$this->loanUtil->upsertLoanToLos($this->loan);
    			break;
    		case 'upsert_loan_from_los':
		    	$this->loanUtil->upsertLoanFromLos($this->loan);
    			break;
    		case 'delete_loan':
    			$this->loanUtil->deleteLoan($this->loan);
    			break;
    		case 'add_document':
    			$documentId = $input->getOption('document_id');
    			if(!isset($documentId)) {
    				throw new \Exception("add_document method requires the --document_id option");
    			}
    			
				$document = $this->em->getRepository('SudouxMortgageBundle:LoanDocument')->find($documentId);
    			if(!isset($document)) {
    				throw new \Exception("Document not found.");
    			}

    			$response = $this->loanUtil->addDocument($this->loan, $document);
				if($response['success']) {
					$this->output->writeln($response['message']);
				} else {
					$this->output->writeln(sprintf('Adding document %s failed: %s', $document->getId(), $response['message']));
				}

    			break;
    		case 'update_loans':
    			$startDateOption = $input->getOption('start_date');
    			if(!isset($startDateOption)) {
    				throw new \Exception("Start Date must be set");
    			}
    			
    			$startDate = new \DateTime($startDateOption);
                //$startDate->modify('-1 day');
    			//$startDate->setTimezone(new \DateTimeZone("UTC"));
    			
    			$this->processLoans($startDate, 'update');
    			break;
    		case 'delete_loans':
    			$startDateOption = $input->getOption('start_date');
    			if(!isset($startDateOption)) {
    				throw new \Exception("Start Date must be set");
    			}
    			
    			$startDate = new \DateTime($startDateOption);
                //$startDate->modify('-1 day');
    			//$startDate->setTimezone(new \DateTimeZone("UTC"));
    			
    			$this->processLoans('delete', $startDate);
    			break;
    		case 'add_loan_prospects':
    			$this->loanUtil->addProspects($this->loan);
    			break;
			case 'add_queued_docs':

				$startDateOption = $input->getOption('start_date');
				if(!isset($startDateOption)) {
					throw new \Exception("Option --start_date must be set");
				}

				$startDate = new \DateTime($startDateOption);
				//$startDate->modify('-1 day');
				//$startDate->setTimezone(new \DateTimeZone("UTC"));

				$this->processLoans('add_docs', $startDate);
				break;
			case 'upsert_milestones':
				$siteId = $input->getOption('site_id');
				if(!isset($siteId)) {
					throw new \Exception("Option --site_id must be set");
				}

				$site = $this->em->getRepository('SudouxCmsSiteBundle:Site')->find($siteId);

				if(isset($site)) {
					$this->upsertMilestones($site);
				} else {
					throw new \Exception("Site not found with ID: " . $siteId);
				}

				break;
    		case 'cron':

    			$this->processLoans('cron');

    			break;
    	}

		if($this->batchCommand) {
			$completed = new \DateTime();
			$this->batchJob->setCompleted($completed);
			$this->batchJob->setStatus(1);
			$this->em->persist($this->batchJob);
			$this->em->flush($this->batchJob);
		}
    }
    
    /**
     * 
     * @param stringe $mode
     * @param \DateTime $startDate
     * @throws \Exception
     * @throws Exception
     */
    protected function processLoans($mode, \DateTime $startDate = null)
    {
    	$modes = array('update', 'delete', 'cron', 'add_docs');
    	
    	if(!in_array($mode, $modes)) {
    		throw new \Exception("Mode does not exist");
    	}

		
    	
    	$maxrows = true;
    	$limit = 100;
    	$lastId = 0;
    	
    	while($maxrows) {
	    	try {
	    		$dql = 'SELECT s FROM Sudoux\Cms\SiteBundle\Entity\Site s JOIN s.settings st JOIN st.los l WHERE s.deleted=0 AND s.active=1 AND st.los IS NOT NULL AND l.active=1 AND s.id > :last_id ORDER BY s.id ASC ';
	    		$query = $this->em->createQuery($dql);
	    		$query->setMaxResults($limit);
	    		$query->setParameter('last_id', $lastId);
	    		$sites = $query->getResult();
	    		
	    		if(count($sites) > 0) {
		    		foreach($sites as $site) {
		    			
		    			$this->output->writeln(sprintf('Processing loans for %s ID:%s', $site->getName(), $site->getId()));
		    			$lastId = $site->getId();

	    				switch($mode) {
	    					case 'update':
					    		if(!isset($startDate)) {
					    			throw new \Exception(sprintf('You must set a start date. %s ID: %s', $site->getName(), $site->getId()));
					    		}
	    						$this->processLoanUpdates($site, $startDate);
	    						break;
	    					case 'delete':
					    		if(!isset($startDate)) {
					    			throw new \Exception(sprintf('You must set a start date. %s ID: %s', $site->getName(), $site->getId()));
					    		}
								$canDeleteLoans = true;
								$losConn = $site->getSettings()->getLos();
								if(isset($losConn)) {
									$canDeleteLoans = $losConn->getAllowLoanDeletions();
								}
								if($canDeleteLoans) {
									$this->processLoanDeletions($site, $startDate);
								}

	    						break;
							case 'add_docs':
								$processDocuments = $site->getSettings()->getMemberPortalDocumentsEnabled();
								if($processDocuments) {
									$this->processLoanDocuments($site);
								}

								break;
	    					case 'cron':

								$lastUpdated = new \DateTime();
								//$lastUpdated->setTimezone(new \DateTimeZone("UTC"));
					    		$losConnection = $site->getSettings()->getLos();
					    		$startDate = $losConnection->getLastUpdated();
					    		
					    		if(!isset($startDate)) {
					    			throw new \Exception(sprintf('No start date for %s ID: %s', $site->getName(), $site->getId()));
					    		}
								
					    		$this->output->writeln('updating milestones');
					    		$this->upsertMilestones($site);
					    		$this->output->writeln('done.');
					    		
	    						$this->processLoanUpdates($site, $startDate);
	    						$this->processLoanDeletions($site, $startDate);

								$processDocuments = $site->getSettings()->getMemberPortalDocumentsEnabled();
								if($processDocuments) {
									$this->processLoanDocuments($site);
								}

	    						$losConnection->setLastUpdated($lastUpdated);
	    						$this->em->persist($losConnection);
	    						$this->em->flush($losConnection);
	    						
	    						break;
	    				}
		    			
		    		}
	    		} else {
	    			$maxrows = false;
	    		}
	    		 
	    	} catch (\Exception $e) {
	    		$this->logger->crit("Processing Loan Updates Failed: " . $e->getMessage());
	    		throw $e;
	    	}	
    	}
    	
    	$this->output->writeln('Loan updates complete!');
    }
    
    /**
     * 
     * @param Site $site
     * @param \DateTime $startDate
     */
    protected function processLoanUpdates(Site $site, \DateTime $startDate)
    {
    	
    	$updatedLoans = $this->loanUtil->getLoans($site, $startDate);
    	$updatedLoanIds = array();
    	if(isset($updatedLoans['loans'])) {
	    	$updatedLoanIds = $updatedLoans['loans'];
    	}
		
    	if(count($updatedLoanIds) > 0) {
    		$i = 1;
    		foreach($updatedLoanIds as $losId) {
    			
	    		$loan = $this->em->getRepository('SudouxMortgageBundle:LoanApplication')->findByLosIds($losId);
	    		if(isset($loan)) {

					try {
						$this->output->writeln(sprintf('processing loan #%s. LOS ID: %s', $loan->getId(), $losId));
						$this->loanUtil->upsertLoanFromLos($loan);
						$this->output->writeln(sprintf('updated loan: %s of %s loans processed - id: %s', $i, count($updatedLoanIds), $loan->getId()));
					} catch(\Exception $e) {

						$errorMessage = sprintf('updating loan #%s failed', $loan->getId()) . ' | ' . $e->getMessage();

						if($this->batchCommand) {
							$jobLog = new BatchJobLog();
							$jobLog->setStatus(2);
							$jobLog->setBatchJob($this->batchJob);
							$jobLog->setStackTrace($e->getTraceAsString());
							$jobLog->setMessage($errorMessage);
							$this->em->persist($jobLog);
							$this->em->flush($jobLog);
						}

						$this->output->writeln($errorMessage);
						$this->logger->crit($errorMessage);
					}
	    		} else {
	    			$losConn = $site->getSettings()->getLos();
	    			if(isset($losConn)) {
	    				$importLoans = $losConn->getImportLosLoans();
	    				if($importLoans) {
							try {
								$this->output->writeln(sprintf('processing loan with LOS #%s', $losId));
								$this->loanUtil->createLoanFromLos($site, $losId);
								$this->output->writeln(sprintf('created loan: %s of %s loans processed', $i, count($updatedLoanIds)));
							} catch(\Exception $e) {
								$errorMessage = sprintf('adding loan #%s failed', $losId) . $e->getMessage();
								$this->output->writeln($errorMessage);
								$this->logger->crit($errorMessage);

								if($this->batchCommand) {
									$jobLog = new BatchJobLog();
									$jobLog->setStatus(2);
									$jobLog->setBatchJob($this->batchJob);
									$jobLog->setStackTrace($e->getTraceAsString());
									$jobLog->setMessage($errorMessage);
									$this->em->persist($jobLog);
									$this->em->flush($jobLog);
								}
							}

	    				}
	    			}
	    		}
    			$i++;
    		}
			$this->output->writeln('Loan updates complete!');
    	} else {
    		$this->output->writeln("No loans to update.");
    	}
    }

    /**
     * 
     * @param Site $site
     * @param \DateTime $startDate
     */
    protected function processLoanDeletions(Site $site, \DateTime $startDate)
    {
    	$deletedLoans = $this->loanUtil->getDeletedLoans($site, $startDate);
    		
    	$deletedLoanIds = array();
    	if(isset($deletedLoans['loans'])) {
    		$deletedLoanIds = $deletedLoans['loans'];
    	}
    	
    	if(count($deletedLoanIds) > 0) {
    		$loans = $this->em->getRepository('SudouxMortgageBundle:LoanApplication')->findByLosIds($deletedLoanIds); // @todo - process in transactions
    		$i = 1;
    		foreach($deletedLoanIds as $losId) {
	    		$loan = $this->em->getRepository('SudouxMortgageBundle:LoanApplication')->findByLosIds($losId);
	    		if(isset($loan)) {
	    			$loan->setDeleted(true);
	    			$this->em->persist($loan);
		    		$this->em->flush();
	    			$this->output->writeln(sprintf('deleting %s of %s loans - id: %s', $i, count($deletedLoanIds), $loan->getId()));
	    			$i++;
	    		}
    		}
    	
    	} else {
    		$this->output->writeln("No loans to delete.");
    	}
    }
    
    /**
     * 
     * @param Site $site
     */
    protected function processLoanDocuments(Site $site)
    {
    	$limit = 100;
    	$offset = 0;
    	$maxrows = true;
  
    	while($maxrows) {
	    	$docs = $this->em->getRepository('SudouxMortgageBundle:LoanDocument')->findBySiteNotSentToLos($site, $limit, $offset);
    		if(count($docs) > 0) {
    			foreach($docs as $document) {
    				$loan = $document->getLoan()->get(0);
    				if(isset($loan)) { // fixes some bad data from testing
		    			$response = $this->loanUtil->addDocument($loan, $document);

						if(empty($response['success'])) {
							if(empty($response['message'])) {
								$response['message'] = 'No Message';
							}
							print_r($response);
							$this->output->writeln($response['message']);
							$this->logger->crit($response['message']);

							if($this->batchCommand) {
								$message =
								$jobLog = new BatchJobLog();
								$jobLog->setStatus(2);
								$jobLog->setBatchJob($this->batchJob);
								$jobLog->setMessage($response['message']);
								$this->em->persist($jobLog);
								$this->em->flush($jobLog);
							}
						} else {
							$this->output->writeln(sprintf('added document - id: %s', $document->getId()));
						}
    				}
    			}
    			$offset += $limit;
    		} else {
    			$maxrows = false;
    		}
    	}
    }
    
    /**
     *
     * @param Site $site
     */
    protected function upsertMilestones(Site $site)
    {
		try {
			$losMilestoneGroups = $this->loanUtil->getAllMilestones($site);
			// deactivate all milestone groups first
			$milestoneGroups = $this->em->getRepository('SudouxMortgageBundle:LoanMilestoneGroup')->findAllBySite($site);
			foreach($milestoneGroups as $milestoneGroup) {
				$milestoneGroup->setActive(false);
				$this->em->persist($milestoneGroup);
			}

			$this->em->flush();

			if(array_key_exists('allMilestones', $losMilestoneGroups)) {
				foreach($losMilestoneGroups['allMilestones'] as $losMilestoneGroup) {
					$milestoneGroup = $this->em->getRepository('SudouxMortgageBundle:LoanMilestoneGroup')->findOneByLosId($site, $losMilestoneGroup['ID']);

					if(!isset($milestoneGroup)) {
						$milestoneGroup = new LoanMilestoneGroup();
					}

					$milestoneGroup->setName($losMilestoneGroup['name']);
					$milestoneGroup->setLosId($losMilestoneGroup['ID']);
					$milestoneGroup->setSite($site);
					$milestoneGroup->setActive(true);

					$i=0;
					foreach($losMilestoneGroup['milestones'] as $losMilestone) {
						$milestone = $this->em->getRepository('SudouxMortgageBundle:LoanMilestone')->findOneMilestoneByLosId($site, $losMilestone['ID'], $losMilestoneGroup['ID']);

						if(!isset($milestone)) {
							$milestone = new LoanMilestone();
							$milestoneGroup->addMilestone($milestone);
						}

						$milestone->setLosId($losMilestone['ID']);
						$milestone->setName($losMilestone['name']);
						$milestone->setWeight($i);
						$milestone->setActive(true);
						$milestone->setMilestoneGroup($milestoneGroup);
						$this->em->persist($milestone);
						$i++;
					}

					$this->em->persist($milestoneGroup);
				}

				$this->em->flush();
			}
		} catch(\Exception $e) {
			$this->output->writeln($e->getMessage());
			$this->logger->crit($e->getMessage());

			if($this->batchCommand) {
				$jobLog = new BatchJobLog();
				$jobLog->setStatus(2);
				$jobLog->setBatchJob($this->batchJob);
				$jobLog->setMessage($e->getMessage());
				$jobLog->setStackTrace($e->getTraceAsString());
				$this->em->persist($jobLog);
				$this->em->flush($jobLog);
			}
		}

    }
}