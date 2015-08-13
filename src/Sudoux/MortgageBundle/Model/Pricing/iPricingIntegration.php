<?php

namespace Sudoux\MortgageBundle\Model\Pricing;

use Sudoux\MortgageBundle\Entity\LoanApplication;

interface iPricingIntegration
{
	public function connect();
	public function getPricing(LoanApplication $loan);
}