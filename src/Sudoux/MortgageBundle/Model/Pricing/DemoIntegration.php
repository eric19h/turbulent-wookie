<?php

namespace Sudoux\MortgageBundle\Model\Pricing;

use Sudoux\MortgageBundle\Entity\PricingScenario;

use Doctrine\ORM\EntityManager;
use Sudoux\MortgageBundle\Entity\PricingConnection;
use Sudoux\MortgageBundle\Entity\Borrower;
use Sudoux\MortgageBundle\Entity\LoanApplication;

class DemoIntegration extends PricingIntegration implements iPricingIntegration 
{
	public function __construct(EntityManager $em, PricingConnection $conn)
	{
		parent::__construct($em, $conn);
		if(!$this->connect()) {
			throw new \Exception("Pricing connection failed");			
		}
	}
	
	public function connect()
	{
		return true;
	}
	
	public function getPricing(LoanApplication $loan)
	{
		parent::getPricing($loan);
		
		for($i=0; $i<5; $i++) {
			$scenario = new PricingScenario();
			$scenario->setName("Company " . ($i + 1));
			$scenario->setApr(rand(40, 50) / 10);
			$scenario->setClosingCost(rand(500, 3000));
			$scenario->setDiscount(rand(800, 2000));
			$scenario->setMargin(0);
			$scenario->setRate(rand(40, 50) / 10);
			$scenario->setPi(rand(800, 900));
			
			// pricing options
			$options = array();
			$options['header'] = array(
				'MI Estimates Are Based On 25% Coverage',
				'BPMI Monthly',
				'Lender Paid Single',
				'BPMI Single Non-Refundable',
				'BPMI Single Refundable',
				'BPMI Split-Premium'	
			);
			
			$options['data'] = array(
				array('Loan Amount', '$180,000.00', '$180,000.00', '$180,000.00', '$180,000.00', '$180,000.00'),
				array('Initial Monthly Premium', '$55.50', null, null, null, '$33.00'),
				array('Monthly Premium Rate % through year 10', '0.370', null, null, null, '0.220'),
				array('Monthly Premium Rate % through years 11 & greater', '0.170', null, null, null, null),
				array('Upfront Premium', null, '$2,160.00', '$2,160.00', '$3,240.00', '$1,800.00'),
				array('Upfront Premium Rate %', null, 1.200, 1.200, 1.800, 1.000),
				array('MI Premium Tax Amount', '$0.72', '$28.08', '$28.08', '$42.12', null),
				array('MI Premium Tax Rate %', 1.300, 1.300, 1.300, 1.300, 1.300),
				array('MI Premium Tax Rate % + Tax Rate %', 0.375, 1.216, 1.216, 1.823, null),
			);
			
			$scenario->setOptions($options);

			$loan->addPricingScenario($scenario);
		}
		$this->em->persist($loan);
		$this->em->flush($loan);
	}
	
}