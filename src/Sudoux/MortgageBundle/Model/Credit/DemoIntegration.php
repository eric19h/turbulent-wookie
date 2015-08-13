<?php

namespace Sudoux\MortgageBundle\Model\Credit;

use Doctrine\ORM\EntityManager;

use Sudoux\MortgageBundle\Entity\CreditReport;

use Sudoux\MortgageBundle\Entity\CreditConnection;

use Sudoux\MortgageBundle\Entity\Borrower;
use Sudoux\MortgageBundle\Entity\LoanApplication;

class DemoIntegration extends CreditIntegration implements iCreditIntegration 
{
	public function __construct(EntityManager $em, CreditConnection $conn)
	{
		parent::__construct($em, $conn);
		if(!$this->connect()) {
			throw new \Exception("Credit connection failed");			
		}
	}
	
	public function connect()
	{
		return true;
	}
	
	public function getCreditByLoan(LoanApplication $loan)
	{
		parent::getCreditByLoan($loan);
		$borrower = $loan->getBorrower();
		$coBorrowers = $loan->getCoBorrower();
		
		$borrowers = array($borrower);
		
		foreach($coBorrowers as $coBorrower) {
			array_push($borrowers, $coBorrower);
		}
		
		foreach($borrowers as $b) {
			$creditReport = new CreditReport();
			$creditReport->setEquifaxScore(rand(690, 750));
			$creditReport->setExperianScore(rand(690, 750));
			$creditReport->setTransunionScore(rand(690, 750));
			$creditReport->setCreditProvider($this->connection->getCreditProvider());
			
			$b->setCreditReport($creditReport);
			$this->em->persist($borrower);
		}
		
		$this->em->flush();
	}
	
	public function getCreditByBorrower(Borrower $borrower)
	{
		parent::getCreditByBorrower($borrower);	
		
		$creditReport = new CreditReport();
		$creditReport->setEquifaxScore(rand(690, 750));
		$creditReport->setExperianScore(rand(690, 750));
		$creditReport->setTransunionScore(rand(690, 750));
		$creditReport->setCreditProvider($this->connection->getCreditProvider());
			
		$b->setCreditReport($creditReport);
		$this->em->persist($borrower);
		
		$this->em->flush();
	}
}