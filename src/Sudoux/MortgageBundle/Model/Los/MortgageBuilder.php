<?php

namespace Sudoux\MortgageBundle\Model\Los;

use MyProject\Proxies\__CG__\stdClass;
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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Guzzle\Stream\PhpStreamRequestFactory;

/**
 * Class MortgageBuilder
 * @package Sudoux\MortgageBundle\Model
 */
class MortgageBuilder
{
    const BASE_SERVICE_URL = 'https://mbwebservices.mortgagebuilder.com/ServicesNet/ArchConnect';

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
        if(!isset($serviceUrl)) {
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

    public function addProspects()
    {

    }

    public function getLoanOfficers()
    {
        $params = array(
            'vendor' => $this->losConnection->getUsername(),
            'cust' => $this->losConnection->getLicenseKey(),
        );

        $request = $this->client->post($this::BASE_SERVICE_URL . '/getLOInfo.ashx', array('Content-Type' => 'text/plain'), $params);
//		$response = $request->send();
//		$data = $response->getBody();


        $factory = new PhpStreamRequestFactory();
        $stream = $factory->fromRequest($request);
        $LO = new \stdClass();
        $listLOs = array();

        // Read until the stream is closed
        while (!$stream->feof()) {
            // Read a line from the stream
            $array = explode ( '|' , $stream->readLine(), -1) ;
            $LO = (object) $array;
            $listLOs[] = $LO;

            // JSON decode the line of data
            //    $data = json_decode($line, true);
        }

        return $listLOs;
    }

    /**
     * Creates or Updates a loan to the LOS
     * @param LoanApplication $application
     * @throws \Exception
     */
    public function upsertLoanToLos(LoanApplication $application)
    {
        try {
            $loId = null;
            $loanOfficer = $application->getLoanOfficer();
            if(isset($loanOfficer)) {
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

            if(!is_numeric($data)){

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


            if(is_numeric($data)){

                $application->setLosId($data);
                $modifiedDate = new \DateTime();
                $modifiedDate->getTimestamp();
                $application->setLosModified($modifiedDate);

                $application->setLosLoanNumber($data);

                $application->setStatus(1);

                // lookup lo
                if(isset($loId)) {
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



        } catch(\Exception $e) {
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

    public function getAllMilestones()
    {
        return array();
    }

    public function getLoans()
    {
        return array();
    }

    public function getDeletedLoans()
    {
        return array();
    }

    public function addDocument()
    {

    }
}