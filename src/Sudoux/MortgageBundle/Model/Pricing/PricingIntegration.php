<?php

namespace Sudoux\MortgageBundle\Model\Pricing;
use Sudoux\MortgageBundle\Entity\PricingConnection;
use Doctrine\ORM\EntityManager;
use Sudoux\MortgageBundle\Entity\Borrower;
use Sudoux\MortgageBundle\Entity\LoanApplication;

class PricingIntegration 
{
	protected $connection;
	protected $em;
	
	public function __construct(EntityManager $em, PricingConnection $conn)
	{
		$this->connection = $conn;	
		$this->em = $em;
	}
	
	public function getPricing(LoanApplication $loan)
	{
		foreach($loan->getPricingScenario() as $scenario) {
			$this->em->remove($scenario);
		}
		$loan->removeAllPricingScenarios();
		$this->em->flush();
	}
}