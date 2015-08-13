<?php 
namespace Sudoux\MortgageCalculatorBundle\Model;

/**
 * Class AmortizationOptions
 * @package Sudoux\MortgageCalculatorBundle\Model
 * @author Dan Alvare
 */
class AmortizationOptions
{
	public $Periods       = 12;
	public $Compounds     = 0;
	public $PeriodPayment = 0;
	
	public $PaidPeriods   = 0;

	public $Taxes         = 0;
	
	public $PropertyTaxes = 0;
	public $Insurance     = 0;
	public $PMI           = 0;
	
	public $AdditionalPayment = 0;

	public $InterestOnly  = false;
	public $CalculateEveryPeriodPayment = false;

	public function AmortizationOptions() {
		global $CalcSettings;
		$this->Compounds = $CalcSettings->CompoundPeriods;
	}
}