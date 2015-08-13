<?php

namespace Sudoux\MortgageBundle\Model;

use Monolog\Logger;

use Sudoux\MortgageBundle\DependencyInjection\FannieMaeUtil;

use Sudoux\MortgageBundle\Entity\Borrower;

use Sudoux\Cms\MessageBundle\Entity\Email;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Guzzle\Stream\PhpStreamRequestFactory;
use Sudoux\MortgageBundle\Entity\LosRequest;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sudoux\Cms\FileBundle\Entity\File;
use Sudoux\Cms\SiteBundle\DependencyInjection\StringUtil;
use Sudoux\MortgageBundle\Entity\LoanDocument;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Doctrine\ORM\EntityManager;
use Sudoux\MortgageBundle\Entity\LoanApplication;
use Sudoux\MortgageBundle\Entity\LosConnection;
use Guzzle\Http\Client;
use Symfony\Component\Finder\Finder;
use GuzzleHttp\Post\PostFile;
use Symfony\Component\Yaml\Yaml;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Stream;
use Guzzle\Http\EntityBody;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Class LosSync
 * @package Sudoux\MortgageBundle\Model
 * @author Dan Alvare
 */
class LosSync {
	
	const MISMO = 0;
	const FANNIE_MAE = 1;
	const DEFAULT_SERVICE_URL = 'https://lossync.wmmortgageware.com/losService.svc';
	const USERNAME = 'dan';
	const PASSWORD = 'letmein!789';
	const DATE_FORMAT = "n/j/Y g:i:s A";
	const SERVICE_DATE_FORMAT = "m/d/Y H:i:s";
	
	/**
	 * @var LosConnection
	 */
	protected $losConnection;
	
	/**
	 * 
	 * @var Guzzle\Http\Client
	 */
	protected $client;
	
	/**
	 * 
	 * @var \Twig_Environment
	 */
	protected $twig;
	
	/**
	 * 
	 * @var array
	 */
	protected $user;
	
	/**
	 * 
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	/**
	 * 
	 * @var Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected $container;
	
	/**
	 * 
	 * @var Monolog\Logger
	 */
	protected $logger;
	
	/**
	 * 
	 * @param LosConnection $losConnection
	 * @param \Twig_Environment $twig
	 * @param ContainerInterface $container
	 */
	public function __construct(LosConnection $losConnection, \Twig_Environment $twig, ContainerInterface $container) {
		$this->losConnection = $losConnection;
		$serviceUrl = $losConnection->getServiceUrl();
		if(!isset($serviceUrl)) {
			$serviceUrl = $this::DEFAULT_SERVICE_URL;
		}
		
		$this->client = new Client($serviceUrl, array(
			'ssl.certificate_authority' => false,
		));
		
		$this->twig = $twig;
		$this->em = $container->get('doctrine')->getEntityManager();
		$this->container = $container;
		
		$this->client->setDefaultOption('auth', array($this::USERNAME, $this::PASSWORD, 'Basic'));
		$this->user = array(
			'AuthUser' => array(
				'uri' => $losConnection->getHost(),
				'username' => $losConnection->getUsername(), 
				'password' => $losConnection->getPassword(),
			),
			'lostype' => 0,
			'apiKey' => $losConnection->getLicenseKey(),
		);
		
		$this->logger = $this->container->get('logger');
	}
	
	/**
	 * 
	 * @return array
	 */
	public function tryLogin() {
		return $this->request('tryLogin', $this->user);
	}
	
	/**
	 * 
	 * @param LoanApplication $application
	 * @throws \Exception
	 */
	public function getLoan(LoanApplication $application) {
		$losId = $application->getLosId();
		
		$params = array(
			'user' => $this->user,
			'loanID' => $losId,
		);
		
		if(!isset($losId)) {
			throw new \Exception('LOS ID is required for this request.');
		}
		
		return $this->request('getLoan', $params);	
	}
	
	/**
	 * Fetches loans by date modified
	 * @param \DateTime $modified
	 */
	public function getLoans(\DateTime $modified) {
		$params = array(
			'user' => $this->user,
			'modified' => $modified->format($this::SERVICE_DATE_FORMAT),
		);
		
		return $this->request('getLoans', $params);	
	}

	/**
	 * Creates or Updates a loan to the LOS
	 * @param LoanApplication $application
	 * @throws \Exception
	 */
	public function upsertLoanToLos(LoanApplication $application) 
	{
		try {
			
			$losId = $application->getLosId();
			
			$applicationLoanOfficer = $application->getLoanOfficer();
			$paramLoanOfficer = null;
			if(isset($applicationLoanOfficer)) {
				$loanOfficerLosId = $applicationLoanOfficer->getLosId(); 
				if(isset($loanOfficerLosId)) {
					$paramLoanOfficer['username'] = $loanOfficerLosId;
				}
			}
			
			$params = array(
				'user' => $this->user,
				'loanString' => $this->loanToFannieMae($application),
				'options' => $this->prepareLosSettings($application),
				'loanOfficer' => $paramLoanOfficer,
			);
			
			//echo json_encode($params); exit;
			
			if($losId) {
				$params['loanID'] = $losId;
				$data = $this->request('updateLoanByString', $params);
			} else {				
				$data = $this->request('importNewLoanByString', $params);
				$application->setSentToLos(true);
			}
			
			if($data['loan']['success']) {
				// find the milestones
				// @todo - fix the relationship to the los connection for milestones. child site lookups will not work correctly if there is an integration override
                $milestoneGroups = $this->em->getRepository('SudouxMortgageBundle:LoanMilestoneGroup')->findAllByParentSites($application->getSite());
                //$milestoneGroups = $application->getSite()->getSettings()->getInheritedMilestoneGroup();
                $currentMilestoneGroup = null;
                $currentMilestone = null;
                //print_r('Milestone GRoup Count: ' . count($milestoneGroups));
                if(count($milestoneGroups) > 0) {
                    foreach($milestoneGroups as $milestoneGroup) {
                        if($milestoneGroup->getLosId() == $data['loan']['currentMilestone']['templateID']) {
                            $currentMilestoneGroup = $milestoneGroup;
                            break;
                        }
                    }

                    $milestones = $currentMilestoneGroup->getMilestone();
                    if(count($milestones) > 0) {
                        foreach($milestones as $milestone) {
                            if($milestone->getLosId() == $data['loan']['currentMilestone']['ID']) {
                                $currentMilestone = $milestone;
                                break;
                            }
                        }
                    }
                }


                //$currentMilestone = $this->em->getRepository('SudouxMortgageBundle:LoanMilestone')->findOneMilestoneByLosId($application->getSite(), $data['loan']['currentMilestone']['ID'], $data['loan']['currentMilestone']['templateID']);


                if(isset($currentMilestone)) {
					$application->setMilestone($currentMilestone);
					$application->setMilestoneGroup($currentMilestoneGroup);
				}
				
				$application->setLosId($data['loan']['losID']);
				$modifiedDate = new \DateTime();
				$modifiedDate->createFromFormat($this::DATE_FORMAT, $data['loan']['lastModified']);
				$application->setLosModified($modifiedDate);
				
				$application->setLosLoanNumber($data['loan']['losLoanNumber']);

				// lookup lo
				if(isset($data['loan']['loanOfficerID'])) {
					$losLoanOfficer = $this->em->getRepository('SudouxMortgageBundle:LoanOfficer')->findOneBySiteAndLosId($application->getSite(), $data['loan']['loanOfficerID']);
					$application->setLoanOfficer($losLoanOfficer);
				} else {
					$application->setLoanOfficer(null);
				}
				
				$this->em->persist($application);
				$this->em->flush();
	
			} else {
				$e = new \Exception("Error adding loan - ID: " . $application->getId() . " Exception: " . $data['loan']['exception'] . " Message: " . $data['loan']['message']);
				$this->logger->crit($e->getMessage());
				throw $e;
			}
		} catch (\Exception $e) {
			$this->logger->crit($e->getMessage());
			throw $e;
		}
	}
	
	/**
	 * Creates or updates a loan from the LOS
	 * @param LoanApplication $application
	 * @throws \Exception
	 */
	public function upsertLoanFromLos(LoanApplication $application) 
	{
		$loanData = $this->getLoan($application);

		try {
			if(!empty($loanData['loan']['success'])) {
				/*echo '<pre>';
				print_r($loanData['loan']);
				echo '</pre>';
				exit;*/
				// upsert loan
				$modifiedDate = new \DateTime();
				$modifiedDate->createFromFormat($this::DATE_FORMAT, $loanData['loan']['lastModified']);
				$application->setLosModified($modifiedDate);
				
				$fannieMaeUtil = $this->container->get('sudoux_mortgage.fanniemae_util');
				$fannieMaeUtil->convertFannieMaeToLoanApplication($loanData['loan']['loanData'], $application);

				//print_r($loanData);
				$this->setLoanMilestone($application, $loanData['loan']['milestone']['ID'], $loanData['loan']['milestone']['templateID']);
				
				$application->setLosLoanNumber($loanData['loan']['losLoanNumber']);
				// lookup lo

				if(isset($loanData['loan']['loanOfficerID'])) {
					$losLoanOfficer = $this->em->getRepository('SudouxMortgageBundle:LoanOfficer')->findOneBySiteAndLosId($application->getSite(), $loanData['loan']['loanOfficerID']);
					$application->setLoanOfficer($losLoanOfficer);
				} else {
					$application->setLoanOfficer(null);
				}
				
				// data only doc upsert
				//$this->upsertDocumentsWithAttachments($application, $loanData['loan']['attachments']);
				$application->setDeleted(false);
				$this->em->persist($application);
				$this->em->flush();
	
			} else {
				$e = \Exception("Error adding loan - ID: " . $application->getId() . " Exception: " . $loanData['loan']['exception']);
	    		$this->logger->crit($e->getMessage());
	    		throw $e;
			}
		} catch (\Exception $e) {
    		$this->logger->crit($e->getMessage());
    		throw $e;
		}

		return $loanData;
	}
	
	/**
	 * 
	 * @param Site $site
	 * @param string $losId
	 */
	public function createLoanFromLos(Site $site, $losId)
	{
		$loan = new LoanApplication();
		$loan->setLosId($losId);
		$loan->setSite($site);
		$loan->setSource(1);
		$loan->setStatus(6);
		$loan->setCompleted(true);
		$this->upsertLoanFromLos($loan);
		
		return $loan;
	}
	
	/**
	 * 
	 * @param LoanApplication $application
	 */
	public function getLoanMilestones(LoanApplication $application)
	{
		$params = array(
			'user' => $this->user,
			'loanID' => $application->getLosId(),
		);
					
		return $this->request('getLoanMilestones', $params);
	}
	
	/**
	 * 
	 * @param LoanApplication $application
	 */
	public function getLoanMilestone(LoanApplication $application)
	{
		$params = array(
			'user' => $this->user,
			'loanID' => $application->getLosId(),
		);
		
		return $this->request('getLoanMilestone', $params);
	}
	
	/**
	 * 
	 * @param LoanApplication $application
	 * @param int $milestoneId
	 * @param int $milestoneGroupId
	 * @throws \Exception
	 */
	public function setLoanMilestone(LoanApplication $application, $milestoneId, $milestoneGroupId)
	{
		try {

			$integrationSite = $this->losConnection->getSite();
			//print_r('Site ID: '. $integrationSite->getId() . ' | Milestone ID: '. $milestoneId . ' | Milestone Group ID: ' . $milestoneGroupId);
			$newMilestone = $this->em->getRepository('SudouxMortgageBundle:LoanMilestone')->findOneMilestoneByLosId($integrationSite, $milestoneId, $milestoneGroupId);
			//print_r('Milestone Name: ' . $newMilestone->getName());
			if(isset($newMilestone)) {
				// check for a change and send to the
				$currentMilestone = $application->getMilestone();
				if(isset($currentMilestone)) {
					$sendNotifications = $application->getSite()->getSettings()->getInheritedSendMilestonesNotifications();
					$user = $application->getUser();

					if($currentMilestone->getId() != $newMilestone->getId() && $sendNotifications && isset($user)) {
						if($user->hasRole('ROLE_MEMBER')) {
							$email = new Email();
							$email->setSubject("Your loan status has been updated");
							$email->setMessage(sprintf("Your loan status for %s has been updated to %s.", $application->getPropertyLocation()->getAddress1(), $newMilestone->getName()));
							$email->setRecipient($user->getEmail());
							$email->setSite($application->getSite());

							$application->addEmail($email);

							$emailUtil = $this->container->get('sudoux.cms.message.email_util');
							$emailUtil->logAndSend($email);
						}
					}
				}

				$application->setMilestone($newMilestone);
				$application->setMilestoneGroup($newMilestone->getMilestoneGroup());
			} else {
				$e = new \Exception("Milestone not found for loan " . $application->getId());
				$this->logger->crit($e->getMessage());
			}

			
		} catch (\Exception $e) {
			$this->logger->crit($e->getMessage());
		}
	}
	
	/**
	 * 
	 */
	public function getAllMilestones()
	{
		$params = array(
			'user' => $this->user,
		);
		
		return $this->request('getAllMilestones', $params);
	}
	
	/**
	 * 
	 * @param LoanApplication $application
	 * @param LoanDocument $document
	 * @throws \Exception
	 */
	public function addDocument(LoanApplication $application, LoanDocument $document)
	{
		$data = null;
		
		try {
			$docLosId = $document->getLosId();
			if(empty($docLosId)) {
				$document->setLosStatus(6); // 6 is queued
				$this->em->persist($document);
				$this->em->flush();

				$category = $document->getType();
				$categoryName = null;
				if(isset($category)) {
					$categoryName = $category->getTermKey();
				}

				$documentFile = $document->getFile();
				if(!isset($documentFile)) {
					$e = new \Exception('File not assigned to document.');
					$this->logger->crit($e->getMessage());
					throw $e;
				}

				$filePath = $this->container->get('kernel')->getRootDir() . '/../web/' . $documentFile->getPath();

				if(!is_file($filePath)) {
					$e = new \Exception('Local file not found');
					$this->logger->crit($e->getMessage());
					throw $e;
				}

				// first upload the file
				/*$params = array(
					'file' => '@'.$filePath,
				);

				$request = $this->client->post('uploadFile', array('Content-Type' => 'text/plain'), $params);*/
				$request = $this->client->post('uploadFile');
				$request->setBody(fopen($filePath, 'r'));

				$response = $request->send();
				$data = $response->json();
				if($data['success']) {
					// assign the uploaded file to the loan
					$fileName = $data['filePath'];
					$params = array(
						'user' => $this->user,
						'loanID' => $application->getLosId(),
						'document' => array(
							'title' => $document->getFile()->getName(),
							'category' => $categoryName,
							'filename' => $fileName,
							'fileExtension' => $document->getFile()->getExtension(),
						),
					);

					$data = $this->request('addAttachment', $params);

					if(array_key_exists('attachment', $data)) {
						if($data['attachment']['success']) {
							$data['success'] = true;
							$data['message'] = 'Successfully added the document';
							$document->setLosStatus(1); // success
							$document->setLosId($data['attachment']['attachmentID']);
						} else {
							$document->setLosStatus(3); // locked or failed
							$data = array();
							$data['success'] = false;
							$data['message'] = "Failed to add the document " . $document->getId() . ' | ' . implode(' ', $data);
							$this->logger->crit($data['message']);
						}
					} else {
						$document->setLosStatus(5); // failed
						$data = array();
						$data['success'] = false;
						$data['message'] = "Service failed to return a response #" . $document->getId();
						$this->logger->crit($data['message']);
					}

					$this->em->persist($document);
					$this->em->flush();
				} else {
					$data = array();
					$data['success'] = false;
					$data['message'] = "Failed to assign the uploaded file to the loan. Document ID: " . $document->getId();

					$document->setLosStatus(5); // failed
					$this->em->persist($document);
					$this->em->flush();
					$this->logger->crit($data['message']);
				}
			} else {
				$data = array();
				$data['success'] = true;
				$data['message'] = "The document is already added.";
			}
		} catch (\Exception $e) {
			$data = array();
			$data['success'] = false;
			$data['message'] = $e->getMessage();
			$this->logger->crit($e->getMessage());
		}
		
		return $data;
		
	}
	
	/**
	 * 
	 * @param LoanApplication $application
	 * @return array
	 */
	public function getDocuments(LoanApplication $application)
	{
		$params = array(
			'user' => $this->user,
			'loanID' => $application->getLosId(),
		);

		return $this->request('getAttachments', $params);	
	}
	
	/**
	 * 
	 * @param LoanApplication $application
	 */
	public function upsertDocuments(LoanApplication $application)
	{
		$loanDocuments = $this->getDocuments($application);
		$this->upsertDocumentsWithAttachments($application, $loanDocuments['attachments']);
	}
	
	/**
	 * Data only local upsert
	 * @param LoanApplication $application
	 * @param array $attachments
	 */
	protected function upsertDocumentsWithAttachments(LoanApplication $application, $attachments)
	{
		try {
			$loanDocuments = $this->getDocuments($application);
	
			// upsert documents
			$existingDocuments = $application->getDocument();
			$tmpFiles = array();
			if(count($attachments) > 0) {
				foreach($attachments as $document) {
					$loanDocument = null;
					// check if it exists
					foreach($existingDocuments as $ed) {
						if($document['attachmentID'] == $ed->getLosId()) {
							// matched
							$loanDocument = $ed;
							break;
						}
					}
			
					if(isset($loanDocument)) {
						// update
						$loanDocument->setName($document['title']);
					} else {
						// insert
						$loanDocument = new LoanDocument();
						$loanDocument->setName($document['title']);
						$loanDocument->setLosId($document['attachmentID']);
						$loanDocument->setLosStatus(2); // pending download
                        $loanDocument->setStatus(3); // accepted
						$loanDocument->setExtension($document['extension']);
						$application->addDocument($loanDocument);
					}
						
					$this->em->persist($loanDocument);
				}
			}
		} catch (\Exception $e) {
			$this->logger->crit($e->getMessage());
		}
	}
	
	/**
	 * 
	 * @param LoanApplication $application
	 * @param LoanDocument $document
	 */
	public function setDocumentFile(LoanApplication $application, LoanDocument $document)
	{
		try {
			
			$fileUser = $this->em->getRepository('SudouxCmsUserBundle:User')->findByUsernameOrEmail('losuser');
			
			$serviceFileName = $this->getDocumentBasePath($application) . '/' . $document->getLosId();
			
			$tmpFilePath = $this->getFile($serviceFileName, $document->getLosId());
			
			$managedFile = new File();
			$managedFile->setName($document->getName());
			$managedFile->setSite($application->getSite());
			$managedFile->setUser($fileUser);
			$managedFile->setPublic(false);
			$managedFile->setExtension($document->getExtension());
			
			$file = new \Symfony\Component\HttpFoundation\File\File($tmpFilePath);
			$managedFile->setFile($file);
			
			$document->setFile($managedFile);
			$document->setLosStatus(4);
			
			$this->em->persist($document);
			$this->em->flush();
		} catch (\Exception $e) {
			$this->logger->crit($e->getMessage());
			throw $e;
		}
	}
	
	/**
	 * 
	 * @param string $serviceFileName
	 * @param string $outputFileName
	 */
	public function getFile($serviceFileName, $outputFileName)
	{
		set_time_limit(0);
		
		$params = array(
			'user' => $this->user,
			'fileName' => $serviceFileName,
		);
		
		$filepath = $_SERVER['DOCUMENT_ROOT'] . "/../tmp/" . $outputFileName;

		$filehandle = fopen($filepath, 'w');
		
		$request = $this->client->post('getFile', array('Content-Type' => 'application/json'), json_encode($params))
						->setResponseBody($filepath);
		
		$factory = new PhpStreamRequestFactory();
		$stream = $factory->fromRequest($request);
		
		while (!$stream->feof()) {
			$line = $stream->readLine();
			fwrite($filehandle, $line);
		}
		
		fclose($filehandle);
		
		return $filepath;			
	}
	
	/**
	 * 
	 * @param LoanApplication $application
	 * @param string $format
	 */
	public function exportLoan(LoanApplication $application, $format)
	{
		$params = array(
			'user' => $this->user,
			'loanID' => $application->getLosId(),
			'format' => $format,
		);
		
		return $this->request('exportLoan', $params);
	}
	
	/**
	 * 
	 * @param LoanApplication $application
	 */
	public function deleteLoan(LoanApplication $application)
	{
		$params = array(
			'user' => $this->user,
			'loanID' => $application->getLosId(),
		);
				
		return $this->request('deleteLoan', $params);
	}

	/**
	 *
	 * @param \DateTime $startDate
	 * @return array
	 */
	public function getDeletedLoans(\DateTime $startDate) 
	{
		$params = array(
			'user' => $this->user,
			'dateDeleted' => $startDate->format($this::SERVICE_DATE_FORMAT),
		);
				
		return $this->request('getDeletedLoans', $params);		
	}

	/**
	 * 
	 * @param LoanApplication $application
	 */
	public function loanLocked(LoanApplication $application) 
	{
		$params = array(
			'user' => $this->user,
			'loanID' => $application->getLosId(),
		);
		
		return $this->request('loanLocked', $params);		
	}

	/**
	 * 
	 * @param LoanApplication $application
	 */
	public function forceUnlockLoan(LoanApplication $application) 
	{
		$params = array(
			'user' => $this->user,
			'loanID' => $application->getLosId(),
		);
		
		return $this->request('forceUnlockLoan', $params);		
	}

	/**
	 * 
	 * @param LoanApplication $application
	 */
	public function addProspects(LoanApplication $application) 
	{
		$response = null;
		try {
			
			$borrowers = array($application->getBorrower());
			foreach($application->getCoBorrower() as $coBorrower) {
				array_push($borrowers, $coBorrower);
			}
			
			// prepare borrowers
			$prospects = array();
			foreach($borrowers as $borrower) {

				$married = false;
				if($borrower->getMaritalStatus() == 0) {
					$married = true;
				}

				$prospect = array(
					'id' => $borrower->getId(),
					'firstName' => $borrower->getFirstName(),
					'lastName' => $borrower->getLastName(),						
					'email' => $borrower->getEmail(),						
					'primaryPhone' => $borrower->getPhoneHome(),
					'suffix' => $borrower->getSuffix(),
					'middleInitial' => $borrower->getMiddleInitial(),
					'ssn' => $borrower->getSsn(),
					'married' => $married,
					'birthDate' => $borrower->getBirthDate()->format($this::SERVICE_DATE_FORMAT),
					'homeAddress' => array(
						'street1' => $borrower->getLocation()->getLocation()->getAddress1(),
						'street2' => $borrower->getLocation()->getLocation()->getAddress2(),
						'city' => $borrower->getLocation()->getLocation()->getCity(),
						'state' => $borrower->getLocation()->getLocation()->getState()->getName(),
						'stateAbbreviation' => $borrower->getLocation()->getLocation()->getState()->getAbbreviation(),
						'zipcode' => $borrower->getLocation()->getLocation()->getZipcode(),
					)
				);
				array_push($prospects, $prospect);
			}

			$paramLoanOfficer = null;
			$loanOfficer = $application->getLoanOfficer();
			if(isset($loanOfficer)) {
				$loanOfficerLosId = $loanOfficer->getLosId();
				if(isset($loanOfficerLosId)) {
					$paramLoanOfficer['username'] = $loanOfficerLosId;
				}
			}

			$params = array(
				'user' => $this->user,
				'prospects' => $prospects,
				'loanOfficer' => $paramLoanOfficer,
			);
			
			$response = $this->request('createProspect', $params);
			// update the los ids
			if($response['result']['success']) {
				foreach($response['result']['prospects'] as $p) {
					$borrower = $this->em->getRepository('SudouxMortgageBundle:Borrower')->find($p['id']);
					$borrower->setLosId($p['losID']);
					$this->em->persist($borrower);
				}
				$this->em->flush();
			}

		} catch (\Exception $e) {
			$this->logger->crit($e->getMessage());
		}
		
		return $response;		
	}
	
	/**
	 * Converts a LoanApplication to Fannie Mae 3.2 Format
	 * @param LoanApplication $application
	 * @param unknown_type $version
	 */
	protected function loanToFannieMae(LoanApplication $application, $version = "3.2")
	{
		$fnmTemplateView = 'SudouxMortgageBundle:LoanApplicationAdmin:fanniemae.fnm.twig';
		$fnmTemplate = $this->twig->loadTemplate($fnmTemplateView);
		
		$fnmDoc = $fnmTemplate->render(array('application' => $application, 'emptyString' => ''));

		//file_put_contents(__DIR__ . "/../Resources/tests/loan-test-" . $application->getId() . "-new.fnm", $fnmDoc);
		//$fnmDoc = file_get_contents(__DIR__ . "/../Resources/tests/loan-14000159.fnm");
		
		return $fnmDoc;
	}

	/**
	 * @return array|bool|float|int|null|string
	 * @throws RequestException
	 * @throws \Exception
	 */
	public function getLoanOfficers()
	{
		return $this->request('getLoanOfficers', $this->user);
	}

	/**
	 * 
	 * @param array $data
	 */
	protected function debug($data)
	{
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}
	
	/**
	 * 
	 * @param string $methodName
	 * @param array $params
	 * @throws RequestException
	 */
	protected function request($methodName, $params = array())
	{
		$data = null;
		
		if(isset($this->losConnection)) {
			try {
				$request = $this->client->post($methodName, array('Content-Type' => 'application/json'), json_encode($params));
              //  print_r($request);
				$response = $request->send();
				$data = $response->json();
			} catch (RequestException $e) {
				throw $e;
			    /*echo $e->getRequest() . "\n";
			    if ($e->hasResponse()) {
			        echo $e->getResponse() . "\n";
			    }*/
			}
		}
		
		return $data;
	}
	
	/**
	 * Stub for overide
	 * @param LoanApplication $application
	 * @return string
	 */
	protected function getDocumentBasePath(LoanApplication $application)
	{
		return $this->losConnection->getLicenseKey() . '/' . $application->getLosId();
	}
	
	/**
	 * 
	 */
	protected function prepareLosSettings(LoanApplication $application)
	{
		$settings = unserialize($this->losConnection->getSettings());
		$losSettings = array();
		if(isset($settings)) {
			foreach($settings as $key => $value) {
				array_push($losSettings, array("Key" => $key, "Value" => $value));
			}
		}

		return $losSettings;
	}
}
