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
class Destiny implements iLosSync
{
    const BASE_HOST_URL = 'https://webtransfer-uat.uamc.com/webmax2destiny-listner/webmax-in.aspx';

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
        $hostUrl = $losConnection->getHost();
        if (!isset($hostUrl)) {
            $hostUrl = $this::BASE_HOST_URL;
        }

        $this->client = new Client($hostUrl);
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


            $loId = null;
            $loanOfficer = $application->getLoanOfficer();
            if(isset($loanOfficer)) {
                $loId = $loanOfficer->getLosId();
            }


            $loanUtil = $this->container->get('sudoux_mortgage.loanformat_util');
            $loanUtil->setType('destiny');


            $destinyApp = $loanUtil->convertFromLoanApp($application);

            $request = $this->client->post(null, array('content-type' => 'application/xml'),array() );
            $request->setBody($destinyApp);

            $response = $request->send();

            $statusCode = $response->getStatusCode();
            $reason = $response->getReasonPhrase();


            if ($statusCode == 200) {


                $this->resetApplication($application);

                $application->setLosId($application->getId());
                $modifiedDate = new \DateTime();
                $modifiedDate->getTimestamp();
                $application->setLosModified($modifiedDate);

                $application->setLosLoanNumber($application->getId());
                $application->setStatus(1);

                if (isset($loId)) {
                    $losLoanOfficer = $this->em->getRepository('SudouxMortgageBundle:LoanOfficer')->findOneBySiteAndLosId($application->getSite(), $loId);
                    $application->setLoanOfficer($losLoanOfficer);
                } else {
                    $application->setLoanOfficer(null);
                }

                $this->em->persist($application);
                $this->em->flush();

            } else {

                $e = new \Exception("Error adding loan - ID: " . $application->getId() . " Exception: " . $reason);
                $this->logger->crit($e->getMessage());
                throw $e;

            }

        } catch (\Exception $e) {
            $this->logger->crit($e->getMessage());
            throw $e;
        }

        return $response;
    }


    protected function resetApplication(LoanApplication $a){

        $this->em->refresh($a);
        $jobs = $a->getBorrower()->getEmployment();
        $asset = $a->getBorrower()->getAssetAccount();

        foreach($asset as $ass){

            $this->em->refresh($ass);

        }

        $coBorrowers = $a->getCoBorrower();
        $this->em->refresh($a->getBorrower());

        foreach($coBorrowers as $coBorrower){

            $coJobs = $coBorrower->getEmployment();
            $coAsset = $a->getBorrower()->getAssetAccount();

            foreach($coJobs as $coJob){

                $this->em->refresh($coJob);

            }
            foreach($coAsset as $coAss){

                $this->em->refresh($coAss);

            }



            $this->em->refresh($coBorrower);

        }

        foreach($jobs as $job){
            $this->em->refresh($job);
        }

    }


    protected function request($methodName, $params = array())
    {


        if($methodName == 'test'){

            $xml =  $this->container->get('templating')->render('SudouxMortgageBundle:LoanApplicationAdmin/formats:testResponse.xml.twig', array());
            $request = $this->client->post('http://httpbin.org/post',array('Content-Type' => 'text/xml'),$xml);
            $response = $request->send();
            $data = $response->json();


        }else{
            $data = null;

            if(isset($this->losConnection)) {
                try {
                    $request = $this->client->post($methodName, array('Content-Type' => 'text/json') , json_encode($params));
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


        }

        return $data;
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
     * @return mixed
     */
    public function getAllMilestones(){


        $milestone1 = array('ID' => 'Destiny_1',
                            'milestones' => array(
                                                array( 'ID'             => 1,
                                                        "name"          => "Loan Application Received",
                                                        "templateID"    => 1,
                                                        "templateName"  => "Short Flow"),

                                                array( 'ID'             => 12,
                                                        "name"          => "Loan Application Submitted to UW",
                                                        "templateID"    => 1,
                                                        "templateName"  => "Short Flow"),

                                                array( 'ID'             => 34,
                                                        "name"          => "Loan Conditionally Approved",
                                                        "templateID"    => 1,
                                                        "templateName"  => "Short Flow"),

                                                array( 'ID'             => 45,
                                                        "name"          => "Final Loan Approval",
                                                        "templateID"    => 1,
                                                        "templateName"  => "Short Flow"),


                                                 ),
                            'name' => "ShortFlow",

                            );


        $return["allMilestones"] = array($milestone1);

        return $return;

    }

    /**
     * @param \Sudoux\MortgageBundle\Entity\LoanApplication $application
     * @param \Sudoux\MortgageBundle\Entity\LoanDocument $document
     * @return null
     * @author Dan Alvare
     */
    public function addDocument(LoanApplication $application, LoanDocument $document){





        return null;
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