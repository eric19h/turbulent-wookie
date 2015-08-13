<?php

namespace Sudoux\MortgageBundle\Model\Credit;
use Doctrine\ORM\EntityManager;

use Sudoux\MortgageBundle\Entity\CreditConnection;


use Sudoux\MortgageBundle\Entity\Borrower;
use Sudoux\MortgageBundle\Entity\LoanApplication;

class CreditIntegration 
{
	protected $connection;
	protected $em;
	
	public function __construct(EntityManager $em, CreditConnection $conn)
	{
		$this->connection = $conn;	
		$this->em = $em;
	}
	
	public function getCreditByLoan(LoanApplication $loan)
	{
		$this->removeLoanCreditReports($loan);
	}
	
	public function getCreditByBorrower(Borrower $borrower)
	{
		$this->removeBorrowerCreditReport($borrower);
		
	}
	
	protected function removeLoanCreditReports(LoanApplication $loan)
	{
		$this->removeBorrowerCreditReport($loan->getBorrower());
		$coBorrowers = $loan->getCoBorrower();
		foreach($coBorrowers as $borrower) {
			$this->removeBorrowerCreditReport($borrower);			
		}
	}
	
	protected function removeBorrowerCreditReport(Borrower $borrower)
	{
		$creditReport = $borrower->getCreditReport();
		if(isset($creditReport)) {
			$this->em->remove($creditReport);
			$this->em->flush();
		}
	}
}