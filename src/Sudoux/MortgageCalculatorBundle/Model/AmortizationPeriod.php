<?php 
namespace Sudoux\MortgageCalculatorBundle\Model;

/**
 * Class AmortizationPeriod
 * @package Sudoux\MortgageCalculatorBundle\Model
 * @author Dan Alvare
 */
class AmortizationPeriod
{
	public $PeriodPrincipalBalance;
	public $PeriodPaymentAmount;
	public $PeriodInterestPaid;
	public $PeriodPrincipalApplied;
	public $PeriodTaxSavings;
	public $PeriodPropertyTaxes;
	public $PeriodInsurance;
	public $PeriodPMI;
	public $PeriodRemainingBalance;

	
	// formatted values
	public $Period;
	public $PaymentAmount;
	public $PrincipalApplied;
	public $InterestPaid;
	public $TaxSavings;
	public $PropertyTaxes;
	public $Insurance;
	public $PMI;
	public $RemainingBalance;

	public $PeriodsWithPMI;
	public $Periods;	

	public $Type;
}