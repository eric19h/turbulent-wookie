<?php

namespace Sudoux\MortgageApiBundle\Controller;

use Sudoux\Cms\LocationBundle\Entity\Location;
use Sudoux\MortgageBundle\Entity\Borrower;
use Sudoux\MortgageBundle\Entity\LoanApplication;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Sudoux\Cms\ApiBundle\Controller\ApiController as BaseController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiController
 * @package Sudoux\MortgageApiBundle\Controller
 * @author Eric Haynes
 */
class ApiController extends BaseController
{

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Eric Haynes
     */
    public function getAllLoansAction(Request $request){

           return $this->nodeAllGet($request, "Sudoux\\MortgageBundle\\Entity\\LoanApplication");

    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param $id
     * @author Eric Haynes
     */
    public function getLoansAction(Request $request, $id){

        return $this->nodeGet($request, $id, "Sudoux\\MortgageBundle\\Entity\\LoanApplication");

    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @author Eric Haynes
     */
    public function addLoansAction(Request $request){

        try{
            $fqn = "Sudoux\\MortgageBundle\\Entity\\LoanApplication";
            $class = "LoanApplication";

            $siteRequest = $this->get('sudoux.cms.site');
            $site = $siteRequest->getSite();
            $user = $this->get('security.context')->getToken()->getUser();
            $contentType = $request->headers->get('content-type');
            $serializer = $this->container->get('jms_serializer');
            $validSites = $user->getSiteIds();

            if ($contentType == "application/json") {

                $em = $this->getDoctrine()->getEntityManager();
                $requestData = $request->getContent();
                $jd = json_decode($requestData, true);

                if(!isset($jd)){
                    $logger = $this->get('logger');
                    $error = "Check Syntax of JSON";
                    $logger->crit($error);
                    $view = $this->view("Bad Request - $error", 400);
                    return $this->handleView($view);
                }

                if(isset($jd['site']['id'])){

                    $r_id = $jd['site']['id'];

                    if (!in_array($r_id, $validSites)) {

                        $view = $this->view("Access Denied - You Aren't Allowed To Add $class To Site ID:  $r_id", 403);
                        return $this->handleView($view);

                    } else {

                        try {

                            $site = $em->getRepository('SudouxCmsSiteBundle:Site')->find($r_id);

                        } catch (\Exception $e) {
                            $logger = $this->get('logger');
                            $error = $e->getMessage();
                            $logger->crit($error);
                            $view = $this->view("Bad Request - $error", 400);
                            return $this->handleView($view);
                        }
                    }

                }

                
                try {
                    $newData = $serializer->deserialize($requestData, $fqn, 'json');
                } catch (\Exception $e) {
                    $logger = $this->get('logger');
                    $error = $e->getMessage();
                    $logger->crit($error);
                    $view = $this->view("Bad Request - $error", 400);
                    return $this->handleView($view);

                }


                $errors = 0;
                try{

                    foreach($newData->getBorrower()->getEmployment() as $job){

                        $employmentLocation = $job->getLocation();
                        $jobName = $job->getEmployerName();

                        if(!isset($employmentLocation)){
                            $logger = $this->get('logger');
                            $error = "Location not set for job - {$jobName} ";
                            $logger->crit($error);
                            $view = $this->view("Employment Must Supply Location - $error", 400);
                            return $this->handleView($view);
                        }else{
                            $eState = $employmentLocation->getState();
                            if(!isset($eState)){
                                $logger = $this->get('logger');
                                $error = "Location for {$jobName} needs a valid state";
                                $logger->crit($error);
                                $view = $this->view("State Not Set - $error", 400);
                                return $this->handleView($view);
                            }

                        }

                    }


                    foreach($newData->getCoborrower() as $coB){

                        foreach($coB->getEmployment() as $job){

                            $employmentLocation = $job->getLocation();
                            $jobName = $job->getEmployerName();

                            if(!isset($employmentLocation)){
                                $logger = $this->get('logger');
                                $error = "Location not set for job - {$jobName} ";
                                $logger->crit($error);
                                $view = $this->view("Employment Must Supply Location - $error", 400);
                                return $this->handleView($view);
                            }else{
                                $eState = $employmentLocation->getState();
                                if(!isset($eState)){
                                    $logger = $this->get('logger');
                                    $error = "Location for {$jobName} needs a valid state";
                                    $logger->crit($error);
                                    $view = $this->view("State Not Set - $error", 400);
                                    return $this->handleView($view);
                                }

                            }

                        }

                    }

                    $newData->setApi();
                    $errors = $this->get('validator')->validate($newData);

                }catch(\Exception $e){
                    $logger = $this->get('logger');
                    $error = $e->getMessage();
                    $logger->crit($error);
                }


                if (count($errors) == 0) {

                    try {
                        $newData->setSite($site);
                        $newData->setUser($user);
                        $em->persist($newData);
                        $em->flush();
                        $view = $this->view($newData, 201);

                    } catch (\Exception $e) {
                        $logger = $this->get('logger');
                        $error = $e->getMessage();
                        $logger->crit($error);
                        $view = $this->view("Bad Request - $error", 400);
                    }

                } else {

                    $errorsString = (string)$errors;
                    $view = $this->view("Bad Request: $errorsString", 400);

                }

            } else {

                $view = $this->view("Not a Valid Content Type: $contentType.  Please Use application/json.", 400);

            }


        }catch(\Exception $e){

            $logger = $this->get('logger');
            $eMessage = $e->getMessage();
            $logger->alert($eMessage);
            $view = $this->view("Error - {$eMessage}", 500);
            return $this->handleView($view);

        }

        return $this->handleView($view);

    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param $id
     * @author Eric Haynes
     */
    public function editLoansAction(Request $request, $id){


        try{

            $fqn = "Sudoux\\MortgageBundle\\Entity\\LoanApplication";
            $class = "LoanApplication";

            $user = $this->get('security.context')->getToken()->getUser();
            $validSites = $user->getSiteIds();
            $serializer = $this->container->get('jms_serializer');
            $em = $this->getDoctrine()->getEntityManager();
            $loan = $em->getRepository($fqn)->find($id);
            $errors = null;

            if(!isset($loan)){

                $view = $this->view("$class Not Found - $class Id: {$id}", 404);

            }else{

                $siteId = $loan->getSite()->getId();

                if(in_array($siteId,$validSites)){

                    $now = new \DateTime();
                    $contentType = $request->headers->get('content-type');

                    if ($contentType == "application/json") {

                        $requestString = $request->getContent();
                        $jd = json_decode($requestString);
                        $jd->id = $id;
                        $fixed = json_encode($jd);
                        $mergedData = $serializer->deserialize($fixed, $fqn, 'json');
                        $validator = $this->get('validator');

                        try{
                            $errors = $validator->validate($mergedData);
                            $mergedData->setApi();
                        }catch(\Exception $e){
                            $logger = $this->get('logger');
                            $error = $e->getMessage();
                            $logger->crit($error);
                        }

                        foreach($mergedData->getBorrower()->getEmployment() as $job){

                            $employmentLocation = $job->getLocation();
                            $jobName = $job->getEmployerName();
                            if(!isset($employmentLocation)){
                                $logger = $this->get('logger');
                                $error = "Location not set for job - {$jobName} ";
                                $logger->crit($error);
                                $view = $this->view("Employment Must Supply Location - $error", 400);
                                return $this->handleView($view);
                            }else{
                                $eState = $employmentLocation->getState();
                                if(!isset($eState)){
                                    $logger = $this->get('logger');
                                    $error = "Location for {$jobName} needs a valid state";
                                    $logger->crit($error);
                                    $view = $this->view("State Not Set - $error", 400);
                                    return $this->handleView($view);
                                }

                            }

                        }


                        foreach($mergedData->getCoborrower() as $coB){

                            foreach($coB->getEmployment() as $job){

                                $employmentLocation = $job->getLocation();
                                $jobName = $job->getEmployerName();

                                if(!isset($employmentLocation)){
                                    $logger = $this->get('logger');
                                    $error = "Location not set for job - {$jobName} ";
                                    $logger->crit($error);
                                    $view = $this->view("Employment Must Supply Location - $error", 400);
                                    return $this->handleView($view);
                                }else{
                                    $eState = $employmentLocation->getState();
                                    if(!isset($eState)){
                                        $logger = $this->get('logger');
                                        $error = "Location for {$jobName} needs a valid state";
                                        $logger->crit($error);
                                        $view = $this->view("State Not Set - $error", 400);
                                        return $this->handleView($view);
                                    }

                                }
                            }

                        }


                        if (count($errors) == 0) {

                            try {
                                $mergedData->setModified($now);
                                $em->persist($mergedData);
                                $em->flush();
                                $view = $this->view($mergedData, 200);

                            } catch (\Exception $e) {
                                $logger = $this->get('logger');
                                $error = $e->getMessage();
                                $logger->crit($error);
                                $view = $this->view("Bad Request: $error", 400);
                            }

                        } else {

                            $errorsString = (string) $errors;
                            $view = $this->view("Bad Request: $errorsString", 400);

                        }


                    } else {

                        $view = $this->view("Not a Valid Content Type: $contentType.  Please Use application/json.", 400);

                    }

                }else{

                    $view = $this->view("Access Denied - Page ID: $id", 403);

                }

            }
            $view->setHeader("Accept-Patch","application/json");


        }catch(\Exception $e){

            $logger = $this->get('logger');
            $eMessage = $e->getMessage();
            $logger->alert($eMessage);
            $view = $this->view("Error - {$eMessage}", 500);
            return $this->handleView($view);

        }


        return $this->handleView($view);


    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param $id
     * @author Eric Haynes
     */
    public function deleteLoansAction(Request $request, $id){

        try{

            $fqn = "Sudoux\\MortgageBundle\\Entity\\LoanApplication";
            $class = "LoanApplication";

            $em = $this->getDoctrine()->getEntityManager();
            $loan = $em->getRepository($fqn)->find($id);
            $user = $this->get('security.context')->getToken()->getUser();
            $validSites = $user->getSiteIds();


            if(!isset($loan)){

                $view = $this->view("$class $id Not Found", 404);

            }else{

                $siteId = $loan->getSite()->getId();

                if(in_array($siteId,$validSites)) {

                    try {
                        // non destructive delete
                        $loan->setDeleted(true);
                        $em->persist($loan);
                        $em->flush();
                        $view = $this->view(NULL, 204);

                    } catch (\Exception $e) {

                        $logger = $this->get('logger');
                        $error = $e->getMessage();
                        $logger->crit($error);
                        $view = $this->view("Bad Request - $error", 400);

                    }

                }else{

                    $view = $this->view("Access Denied - $class ID: $id", 403);

                }

            }

            $view->setHeader("Accept-Patch","application/json");


        }catch(\Exception $e){

            $logger = $this->get('logger');
            $eMessage = $e->getMessage();
            $logger->alert($eMessage);
            $view = $this->view("Error - {$eMessage}", 500);
            return $this->handleView($view);

        }

        return $this->handleView($view);


    }

    
    
}
