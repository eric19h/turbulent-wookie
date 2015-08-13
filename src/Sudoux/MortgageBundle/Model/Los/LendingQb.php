<?php

namespace Sudoux\MortgageBundle\Model\Los;


use Guzzle\Service\Client;
use Sudoux\MortgageBundle\Model\Loan\LoanFormat;
use Sudoux\MortgageBundle\Entity\LoanApplication;
use Sudoux\MortgageBundle\Entity\LosConnection;
use Guzzle\Http;
use GuzzleHttp\Post\PostFile;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sudoux\MortgageBundle\Model\LosSync;
use Sudoux\MortgageBundle\Model\iLosSync;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Sudoux\MortgageBundle\Entity\LoanDocument;

/**
 * Class Destiny
 * @package Sudoux\MortgageBundle\Model\Los
 * @author Eric Haynes
 */
class LendingQb implements iLosSync
{
    const BASE_SERVICE_URL = '';

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
     * @param LosConnection $losConnection
     * @param \Twig_Environment $twig
     * @param ContainerInterface $container
     */
    public function __construct(LosConnection $losConnection, \Twig_Environment $twig, ContainerInterface $container)
    {
        $this->losConnection = $losConnection;
        $serviceUrl = $losConnection->getServiceUrl();
        if (!isset($serviceUrl)) {
            $serviceUrl = $this::BASE_SERVICE_URL;
        }

        $this->client = new Client($serviceUrl);
        $this->twig = $twig;
        $this->em = $container->get('doctrine')->getEntityManager();
        $this->container = $container;
        $this->logger = $this->container->get('logger');
    }


    public function tryLogin()
    {

        return array('success' => true);
    }

    public function addProspects(LoanApplication $application)
    {

    }


    /**
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $application
     * @return string
     * @throws \Exception
     * @author Eric Haynes
     */
    public function upsertLoanToLos(LoanApplication $application)
    {
        try {

            $this->em->detach($application);
            $mismo = new LoanFormat($application);
            $mismoApp = $mismo->getMismo231();

            $xml = $this->container->get('templating')->render('SudouxMortgageBundle:LoanApplicationAdmin/formats:destiny.xml.twig', array('application' => $mismoApp), 'text/xml');



            /*
            $loId = null;
            $loanOfficer = $application->getLoanOfficer();
            if (isset($loanOfficer)) {
                $loId = $loanOfficer->getLosId();
            }

            $params = array(
                'vendor' => $this->losConnection->getUsername(),
                'cust' => $this->losConnection->getLicenseKey(),
                'LO' => $loId,
                'FNM' => $this->loanToFannieMae($application),
            );

            $request = $this->client->post($this::BASE_SERVICE_URL . '/process1003.ashx', array('Content-Type' => 'text/plain'), $params);

            $factory = new PhpStreamRequestFactory();
            $stream = $factory->fromRequest($request);
            $data = '';


            // Read until the stream is closed
            while (!$stream->feof()) {
                // Read a line from the stream
                $data .= $stream->readLine();

            }

            if (!is_numeric($data)) {

                $params = array(
                    'vendor' => $this->losConnection->getUsername(),
                    'cust' => $this->losConnection->getLicenseKey(),
                    'LO' => '',
                    'FNM' => $this->loanToFannieMae($application),
                );

                $request = $this->client->post($this::BASE_SERVICE_URL . '/process1003.ashx', array('Content-Type' => 'text/plain'), $params);

                $stream = $factory->fromRequest($request);
                $data = '';


                // Read until the stream is closed
                while (!$stream->feof()) {
                    // Read a line from the stream
                    $data .= $stream->readLine();

                }


            }
            */

            $data = 3;
            if (is_numeric($data)) {

                $application->setLosId($data);
                $modifiedDate = new \DateTime();
                $modifiedDate->getTimestamp();
                $application->setLosModified($modifiedDate);

                $application->setLosLoanNumber($data);

                $application->setStatus(1);

                // lookup lo
                if (isset($loId)) {
                    $losLoanOfficer = $this->em->getRepository('SudouxMortgageBundle:LoanOfficer')->findOneBySiteAndLosId($application->getSite(), $loId);
                    $application->setLoanOfficer($losLoanOfficer);
                } else {
                    $application->setLoanOfficer(null);
                }

                $this->em->persist($application);
                $this->em->flush();

            } else {
                $e = new \Exception("Error adding loan - ID: " . $application->getId() . " Exception: " . $data);
                $this->logger->crit($e->getMessage());
                throw $e;

            }


        } catch (\Exception $e) {
            $this->logger->crit($e->getMessage());
            throw $e;
        }

        return $data;
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
     * @param \DateTime $startDate
     * @return mixed
     */
    public function getDeletedLoans(\DateTime $startDate){





    }

    /**
     * @param LoanApplication $application
     * @return mixed
     */
    public function forceUnlockLoan(LoanApplication $application){





    }

    /**
     * @param LoanApplication $application
     * @return mixed
     */
    public function loanLocked(LoanApplication $application){





    }

    /**
     * @param LoanApplication $application
     * @return mixed
     */
    public function getLoan(LoanApplication $application){





    }

    /**
     * @param LoanApplication $application
     * @param $format
     * @return mixed
     */
    public function exportLoan(LoanApplication $application, $format){





    }

    /**
     * @param LoanApplication $application
     * @return mixed
     */
    public function deleteLoan(LoanApplication $application){






    }

    /**
     * @param LoanApplication $application
     * @return mixed
     */
    public function upsertLoanFromLos(LoanApplication $application){




    }

    /**
     * @param Site $site
     * @param $losId
     * @return mixed
     */
    public function createLoanFromLos(Site $site, $losId){




    }

    /**
     * @param LoanApplication $application
     * @return mixed
     */
    public function getLoanMilestones(LoanApplication $application){





    }

    /**
     * @param LoanApplication $application
     * @return mixed
     */
    public function getLoanMilestone(LoanApplication $application){




    }

    /**
     * @param LoanApplication $application
     * @param $milestoneId
     * @param $milestoneGroupId
     * @return mixed
     */
    public function setLoanMilestone(LoanApplication $application, $milestoneId, $milestoneGroupId){





    }

    /**
     * @return mixed
     */
    public function getAllMilestones(){




    }

    /**
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $application
     * @param \Sudoux\MortgageBundle\Model\Los\LoanDocument $document
     * @author Eric Haynes
     */
    public function addDocument(LoanApplication $application, LoanDocument $document){






    }

    /**
     * @param LoanApplication $application
     * @return mixed
     */
    public function upsertDocuments(LoanApplication $application){





    }

    /**
     * @param LoanApplication $application
     * @return mixed
     */
    public function getDocuments(LoanApplication $application){




    }

    /**
     * @param $serviceFileName
     * @param $outputFileName
     * @return mixed
     */
    public function getFile($serviceFileName, $outputFileName){






    }



}