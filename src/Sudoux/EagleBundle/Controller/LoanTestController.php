<?php

namespace Sudoux\EagleBundle\Controller;

use Sudoux\MortgageBundle\Controller\LoanTestController as BaseController;
use Guzzle\Http\Exception\RequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Guzzle\Service\Client;
use Symfony\Component\HttpFoundation\Response;

use Sudoux\Cms\LocationBundle\Entity\Location;
use Sudoux\Cms\FileBundle\Entity\File;
use Sudoux\MortgageBundle\Entity\Branch;
use Sudoux\MortgageBundle\Form\BranchType;
use SudoCms\SiteBundle\Form\SettingsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CommerceGuys\Guzzle\Plugin\Oauth2\Oauth2Plugin;
use CommerceGuys\Guzzle\Plugin\Oauth2\GrantType\RefreshToken;

/**
 * Class ReportAdminController
 * @package Sudoux\MortgageBundle\Controller
 * @author Dan Alvare
 */
class LoanTestController extends BaseController
{
    public function indexAction(Request $request)
    {
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $siteRequest = $this->get('sudoux.cms.site');
        $site = $siteRequest->getSite();

        $em = $this->getDoctrine()->getEntityManager();

        $loanUtil = $this->get('sudoux_mortgage.los_util');
        /*$milestones = $loanUtil->getAllMilestones($site);
        echo '<pre>';
        print_r($milestones);
        echo '</pre>';

        exit;*/
        //$response = $loanUtil->getAllMilestones($site);
        //$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->find(183);
        //$response = $loanUtil->upsertLoanToLos($application);
        /*$modified = new \DateTime();
        $modified->modify('-45 days');
        $response = $loanUtil->getDeletedLoans($site, $modified);*/

        //$response = $loanUtil->getDocuments($application);
       /* $response = $loanUtil->tryLogin($site);
        echo '<pre>';
        print_r($response);
        echo '</pre>';*/

        try {
            $branch = $em->getRepository('SudouxMortgageBundle:Branch')->findOneBySiteAndLosId($site, '101');
            echo $branch->getName();
            $loanOfficers = $loanUtil->getLoanOfficers($site);

            echo '<pre>';
            print_r($loanOfficers);
            echo '</pre>';
        } catch(RequestException $e) {
            throw $e;
        }



        /*$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->find(143);
        $response = $loanUtil->addProspects($application);

        echo '<pre>';
        print_r($response);
        echo '</pre>';
        exit;*/

        /*$application = $em->getRepository('SudouxMortgageBundle:LoanApplication')->findOneBySite($site, $id);

        if(!isset($application)) {
            $request->getSession()->getFlashBag()->add('error', "Sorry, the loan you requested was not found or not accessible");
        }

        $session = $request->getSession();
        try {
            $loanUtil = $this->get('sudoux_mortgage.los_util');
            $loanUtil->upsertLoanToLos($application);

            $session->getFlashBag()->add('success', "Your loan was resent to your LOS successfully.");
        } catch (\Exception $e) {
            $session->getFlashBag()->add('error', "Sorry, the loan you tried to send did not complete successfully. Our support team has been notified and will respond promptly.");

            $logger = $this->get('logger');
            $logger->crit($e->getMessage() . " Error for loan " . $application->getId());
        }*/

        exit;
    }

    public function getLoanOfficersAction(Request $request)
    {
        $siteRequest = $this->get('sudoux.cms.site');
        $site = $siteRequest->getSite();

        $loanUtil = $this->get('sudoux_mortgage.los_util');

        $loanOfficers = $loanUtil->getLoanOfficers($site);

        echo '<pre>';
        print_r($loanOfficers);
        echo '</pre>';
        exit;
    }

    public function mbTestAction()
    {

    }

    public function testPostAction(){

        $site = $this->container->get('sudoux.cms.site')->getSite();
        $xml =  $this->container->get('templating')->render('SudouxMortgageBundle:LoanApplicationAdmin/formats:testResponse.xml.twig', array());

        $client = new Client();
        $request = $client->post('http://httpbin.org/post', array('Content-Type' => 'text/xml'), $xml);
        $response = $request->send();

        $data = $response->json();


        $loanUtil   = $this->get('sudoux_mortgage.loanformat_util');

        $loanUtil->setType('destiny');
        $newLoan = $loanUtil->createLoanFromFormat($data['data']);

        //$loanUtil->createLoanApplicationFromDestiny($data['data']);


        exit;

    }


    public function docUploadTestAction()
    {
        $client = new Client('https://lossync.sudoux.com/losService.svc', array(
            'ssl.certificate_authority' => false,
        ));
        $client->setDefaultOption('auth', array('dan', 'letmein!789', 'Basic'));

        /*$user = array(
            'AuthUser' => array(
                'uri' => 'https://lossync.sudoux.com/losService.svc',
                'username' => 'dan',
                'password' =>
            ),
            'lostype' => 0,
            'apiKey' => 'AAKLPCALBX',
        );*/



        $filePath = $this->container->get('kernel')->getRootDir() . '/../web/fff.jpg';

        if(!is_file($filePath)) {
            $e = new \Exception('Local file not found');
            $this->logger->crit($e->getMessage());
            throw $e;
        }

        // first upload the file
        $params = array(
            'file' => '@'.$filePath,
        );


      //  $request = $client->post('uploadFile');
// Set the body of the POST to stream the contents of /path/to/large_body.txt
    //    $request->setBody(fopen('/path/to/large_body.txt', 'r'));
     //   $response = $request->send();

        $request = $client->post('uploadFile');
        $request->setBody(fopen($filePath, 'r'));
        $response = $request->send();
        $data = $response->json();

    }
}