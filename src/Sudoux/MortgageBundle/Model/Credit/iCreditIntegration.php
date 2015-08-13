<?php

namespace Sudoux\MortgageBundle\Model\Credit;

use Sudoux\MortgageBundle\Entity\Borrower;
use Sudoux\MortgageBundle\Entity\LoanApplication;

interface iCreditIntegration
{
	public function connect();
	public function getCreditByLoan(LoanApplication $loan);
	public function getCreditByBorrower(Borrower $borrower);
}