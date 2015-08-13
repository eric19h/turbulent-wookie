<?php

namespace Sudoux\MortgageBundle\Model\Los;


use Sudoux\MortgageBundle\Entity\LoanApplication;
use Sudoux\MortgageBundle\Entity\LosConnection;
use GuzzleHttp\Post\PostFile;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sudoux\MortgageBundle\Model\LosSync;
use Sudoux\MortgageBundle\Model\iLosSync;

/**
 * Class Encompass
 * @package Sudoux\MortgageBundle\Model\Los
 * @author Dan Alvare
 */
class Encompass extends LosSync implements iLosSync
{
    /**
     * @param LosConnection $losConnection
     * @param \Twig_Environment $twig
     * @param ContainerInterface $container
     */
	public function __construct(LosConnection $losConnection, \Twig_Environment $twig, ContainerInterface $container) 
	{
		parent::__construct($losConnection, $twig, $container);
	}

    /**
     * @param LoanApplication $application
     * @return string
     */
	protected function getDocumentBasePath(LoanApplication $application)
	{
		return 'Encompass/' . $this->losConnection->getLicenseKey() . '/' . $application->getLosId();
	}

	/*protected function prepareLosSettings(LoanApplication $application)
	{
		$settings = parent::prepareLosSettings($application);

		// send the org id
		$loanOfficer = $application->getLoanOfficer();
		if(isset($loanOfficer)) {
			$branch = $loanOfficer->getBranch();
		} else {
			$branch = $application->getSite()->getSettings()->getBranch();
		}

		if(isset($branch)) {
			array_push($settings, array("Key" => "ORGID", "Value" => $branch->getLosId()));
		}

		//print_r($settings);
		return $settings;
	}*/
}