<?php 
namespace Sudoux\MortgageCalculatorBundle\Model;

/**
 * Class AmortizationTable
 * @package Sudoux\MortgageCalculatorBundle\Model
 * @author Dan Alvare
 */
class AmortizationTable
{
	public $Schedule = array();

	// Non-formatted values
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
	
	// Formatted values
	public $TotalPaymentAmount;
	public $TotalPrincipalApplied;
	public $TotalInterestPaid;
	public $TotalTaxSavings;
	public $TotalPropertyTaxes;
	public $TotalInsurance;
	public $TotalPMI;
	public $TotalRemainingBalance;
	public $TotalPeriodsWithPMI;
	public $TotalPeriods;
	
}