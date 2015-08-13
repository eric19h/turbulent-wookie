<?php

namespace Sudoux\MortgageCalculatorBundle\Controller;

use Sudoux\Cms\SiteBundle\Controller\FrontController;
use Sudoux\MortgageCalculatorBundle\Model\AmortizationOptions;

use Sudoux\MortgageCalculatorBundle\Model\CalculatorResult;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Validator\Constraints\NotNull;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sudoux\MortgageCalculatorBundle\Model\Calc;

/**
 * Class DefaultFrontController
 * @package Sudoux\MortgageCalculatorBundle\Controller
 * @author Dan Alvare
 */
class DefaultFrontController extends FrontController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:index.html.twig');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function paymentsAction(Request $request)
    {

    	$form = $this->createFormBuilder()
			    	->add('loan_amount','money', array(
			    			'currency' => 'USD',
			    			'required' => true,
			    			'precision' => 2,
			    			'grouping' => true,
			    			'constraints' => new NotBlank(),
			    			'attr' => array('value' => '250000')
			    	))
			    	->add('interest_rate','text', array(
			    			'required' => true,
			    			'constraints' => new NotBlank(),
			    			'attr' => array('class' => 'span1', 'value' => '5.875')
			    	))
			    	->add('mortgage_term','text', array(
			    			'label' => 'Length in years',
			    			'required' => true,
			    			'constraints' => new NotBlank(),
			    			'attr' => array('class' => 'span1', 'value' => '30'),
			    	))
			    	->add('home_value','money', array(
			    			'currency' => 'USD',
			    			'precision' => 2,
			    			'grouping' => true,
			    			'required' => true,
			    			'constraints' => new NotBlank(),
			    			'attr' => array('value' => '300000'),
			    	))
			    	->add('annual_taxes','money', array(
			    			'currency' => 'USD',
			    			'precision' => 2,
			    			'grouping' => true,
			    			'required' => true,
			    			'constraints' => new NotBlank(),
			    			'attr' => array('value' => '3000'),
			    	))
			    	->add('annual_insurance','money', array(
			    			'currency' => 'USD',
			    			'precision' => 2,
			    			'grouping' => true,
			    			'required' => true,
			    			'constraints' => new NotBlank(),
			    			'attr' => array('value' => '1500'),
			    	))
			    	->add('annual_pmi','money', array(
			    			'label' => 'Annual PMI',
			    			'required' => true,
			    			'constraints' => new NotBlank(),
			    			'attr' => array('value' => '1000'),
			    	))
                    ->add('send_to_email', 'email', array(
                            'label' => 'Email',
                            'required' => false
                    ))
                    ->add('send_to_name', 'text', array(
                        'label' => 'Name',
                        'required' => false
                    ))
                    ->add('send_to_phone', 'text', array(
                        'label' => 'Phone',
                        'required' => false
                    ))
			    	->getForm();
    	
    	$result = null;
    	
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    	
    		if ($form->isValid()) {
    			$calc = new Calc();
    			$loanAmount = $form['loan_amount']->getData();
    			$monthlyPayment = $calc->PeriodPayment($loanAmount, $form['interest_rate']->getData(), $form['mortgage_term']->getData());  			
    			$mortgageTermMonths = $form['mortgage_term']->getData() * 12;
    			
    			$result = new CalculatorResult();
    			$result->summary = 'Calculator summary results';
    			$result->analysis = new \stdClass();
    			$result->analysis->monthlyPayment = $monthlyPayment;
    			$result->analysis->monthlyTaxes =  $form['annual_taxes']->getData() / 12;
    			$result->analysis->monthlyInsurance = $form['annual_insurance']->getData() / 12;
    			$result->analysis->loanToValueRatio = ($loanAmount / $form['home_value']->getData()) * 100;
    			$result->analysis->monthlyPmi = $form['annual_pmi']->getData() / 12;
			
    			$options = new AmortizationOptions();
    			$options->PMI           = $form['annual_pmi']->getData();
    			$options->PropertyTaxes = $form['annual_taxes']->getData();
    			$options->Insurance     = $form['annual_insurance']->getData();
    			
    			$result->amortizationSchedule = $calc->BuildAmortizationTable($form['home_value']->getData(), $loanAmount, $form['interest_rate']->getData(), $mortgageTermMonths, $options);
    			$result->analysis->monthsWithPmi = $result->amortizationSchedule->TotalPeriodsWithPMI;

    			$monthlyTotal = $result->analysis->monthlyPayment + $result->analysis->monthlyTaxes + $result->analysis->monthlyInsurance + $result->analysis->monthlyPmi;
    			$result->analysis->monthlyTotal = $monthlyTotal;

    		}
    	}
    	
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:payments.html.twig', array(
			'form' => $form->createView(),
        	'result' => $result,  
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function affordabilityAction(Request $request)
    {   
    	$form = $this->createFormBuilder()
    	->add('down_payment','money', array(
    			'label' => 'Down Payment',
    			'required' => true,
                'precision' => 2,
                'currency' => 'USD',
    			'constraints' => new NotBlank(),
                'attr' => array('value' => '10000'),
    	))
        ->add('interest_rate','number', array(
                'label' => 'Interest Rate',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.875'),
        ))
        ->add('length', 'integer', array(
                'label' => 'Length',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30'),
        ))
        ->add('estimated_front_ratio', 'number', array(
                'label' => 'Estimated Front Ratio',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30.000'),
        ))
        ->add('estimated_back_ratio', 'number', array(
                'label' => 'Estimated Back Ratio',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '36.000'),
        ))
        ->add('income1', 'money', array(
                'label' => 'Income 1',
                'required' => true,
                'precision' => 2,
                'currency' => 'USD',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '5000.00'),
        ))
        ->add('income2', 'money', array(
            'label' => 'Income 2',
            'required' => true,
            'precision' => 2,
            'currency' => 'USD',
            'constraints' => new NotBlank(),
            'attr' => array('value' => '3000.00'),
        ))
        ->add('income3', 'money', array(
            'label' => 'Income 3',
            'required' => true,
            'precision' => 2,
            'currency' => 'USD',
            'constraints' => new NotBlank(),
            'attr' => array('value' => '0.00'),
        ))
        ->add('income4', 'money', array(
            'label' => 'Income 4',
            'precision' => 2,
            'currency' => 'USD',
            'required' => true,
            'constraints' => new NotBlank(),
            'attr' => array('value' => '0.00'),
        ))
        ->add('income5', 'money', array(
            'label' => 'Income 5',
            'required' => true,
            'precision' => 2,
            'currency' => 'USD',
            'constraints' => new NotBlank(),
            'attr' => array('value' => '0.00'),
        ))
        ->add('auto_loans', 'money', array(
            'label' => 'Auto Loans',
            'required' => true,
            'precision' => 2,
            'currency' => 'USD',
            'constraints' => new NotBlank(),
            'attr' => array('value' => '375.00'),
        ))
        ->add('student_loans', 'money', array(
            'label' => 'Student Loans',
            'required' => true,
            'precision' => 2,
            'currency' => 'USD',
            'constraints' => new NotBlank(),
            'attr' => array('value' => '425.00'),
        ))
        ->add('installment', 'money', array(
            'label' => 'Installment',
            'required' => true,
            'precision' => 2,
            'currency' => 'USD',
            'constraints' => new NotBlank(),
            'attr' => array('value' => '60.00'),
        ))
        ->add('revolving_accts', 'money', array(
            'label' => 'Revolving accts',
            'required' => true,
            'precision' => 2,
            'currency' => 'USD',
            'constraints' => new NotBlank(),
            'attr' => array('value' => '50.00'),
        ))
        ->add('other_debt', 'money', array(
            'label' => 'Other Debt',
            'required' => true,
            'precision' => 2,
            'currency' => 'USD',
            'constraints' => new NotBlank(),
            'attr' => array('value' => '0.00'),
        ))
        ->add('annual_taxes', 'money', array(
            'label' => 'Annual Taxes',
            'required' => true,
            'precision' => 2,
            'currency' => 'USD',
            'constraints' => new NotBlank(),
            'attr' => array('value' => '3000.00'),
        ))
        ->add('annual_insurance', 'money', array(
            'label' => 'Annual Insurance',
            'required' => true,
            'precision' => 2,
            'currency' => 'USD',
            'constraints' => new NotBlank(),
            'attr' => array('value' => '1500.00'),
        ))
        ->add('annual_pmi', 'money', array(
            'label' => 'Annual PMI',
            'required' => true,
            'precision' => 2,
            'currency' => 'USD',
            'constraints' => new NotBlank(),
            'attr' => array('value' => '1000'),
        ))
    	->getForm();
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			 
    		}
    	}
    	 	
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:affordability.html.twig', array(
			'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function mortgageLengthAction(Request $request)
    {  
    	$form = $this->createFormBuilder()
            ->add('loan_amount','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Loan Amount',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '250000')
            ))
            ->add('interest_rate','text', array(
                'required' => true,
                'label' => 'Interest Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.875')
            ))
            ->add('mortgage_term','text', array(
                'label' => 'Length in years',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30'),
            ))
            ->add('monthly_payment','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'label' => 'Monthly Payment',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '1,304.12')
            ))
    	->getForm();
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			 
    		}
    	}
    	  	
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:mortgageLength.html.twig', array(
			'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function mortgagePrincipalAction(Request $request)
    {    
    	$form = $this->createFormBuilder()
            ->add('loan_amount','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Loan Amount',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '250000')
            ))
            ->add('interest_rate','text', array(
                'required' => true,
                'label' => 'Interest Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.875')
            ))
            ->add('mortgage_term','text', array(
                'label' => 'Length in years',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30'),
            ))
            ->add('months_paid','integer', array(
                'required' => true,
                'label' => 'Months Paid',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '36')
            ))
    	->getForm();
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			 
    		}
    	}
    		
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:mortgagePrincipal.html.twig', array(
			'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function incomeQualifyAction(Request $request)
    {  
    	$form = $this->createFormBuilder()
            ->add('home_value','money', array(
                'label' => 'Home Value',
                'required' => true,
                'precision' => 2,
                'currency' => 'USD',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '300000.00'),
            ))
            ->add('down_payment','money', array(
                'label' => 'Down Payment',
                'required' => true,
                'precision' => 2,
                'currency' => 'USD',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '10000'),
            ))
            ->add('interest_rate','number', array(
                'label' => 'Interest Rate',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.875'),
            ))
            ->add('length', 'integer', array(
                'label' => 'Length',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30'),
            ))
            ->add('estimated_front_ratio', 'number', array(
                'label' => 'Estimated Front Ratio',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30.000'),
            ))
            ->add('estimated_back_ratio', 'number', array(
                'label' => 'Estimated Back Ratio',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '36.000'),
            ))
            ->add('annual_taxes', 'money', array(
                'label' => 'Annual Taxes',
                'required' => true,
                'precision' => 2,
                'currency' => 'USD',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '3000.00'),
            ))
            ->add('annual_insurance', 'money', array(
                'label' => 'Annual Insurance',
                'required' => true,
                'precision' => 2,
                'currency' => 'USD',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '1500.00'),
            ))
            ->add('annual_pmi', 'money', array(
                'label' => 'Annual PMI',
                'required' => true,
                'precision' => 2,
                'currency' => 'USD',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '1000'),
            ))
    	->getForm();
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			 
    		}
    	}
    		
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:incomeQualify.html.twig', array(
			'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function extraPaymentsAction(Request $request)
    {   
    	$form = $this->createFormBuilder()
            ->add('loan_amount','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Loan Amount',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '250000')
            ))
            ->add('interest_rate','text', array(
                'required' => true,
                'label' => 'Interest Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.875')
            ))
            ->add('mortgage_term','text', array(
                'label' => 'Length in years',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30'),
            ))
            ->add('additional_payment','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'label' => 'Additional Payment',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '50.00')
            ))
    	->getForm();
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			 
    		}
    	}
    	
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:extraPayments.html.twig', array(
			'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function bestLoanAction(Request $request)
    {    
    	$form = $this->createFormBuilder()
            ->add('loan_amount1','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Loan Amount',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '250000.00')
            ))
            ->add('interest_rate1','number', array(
                'required' => true,
                'label' => 'Interest Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.875')
            ))
            ->add('mortgage_term1','integer', array(
                'label' => 'Length in years',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30'),
            ))
            ->add('points1','number', array(
                'required' => true,
                'label' => 'Points',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '1.000')
            ))
            ->add('origination_fees1','number', array(
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Origination Fees',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '0.00')
            ))
            ->add('closing_costs1','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Closing Costs',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '1200.00')
            ))
            ->add('interest_rate2','number', array(
                'required' => true,
                'label' => 'Interest Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.875')
            ))
            ->add('mortgage_term2','integer', array(
                'label' => 'Length in years',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30'),
            ))
            ->add('points2','number', array(
                'required' => true,
                'label' => 'Points',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '1.000')
            ))
            ->add('origination_fees2','number', array(
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Origination Fees',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '0.00')
            ))
            ->add('closing_costs2','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Closing Costs',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '1200.00')
            ))
    	->getForm();
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			 
    		}
    	}
    		
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:bestLoan.html.twig', array(
			'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function loanIntervalAction(Request $request)
    {    
    	$form = $this->createFormBuilder()
            ->add('loan_amount','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Loan Amount',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '250000')
            ))
            ->add('interest_rate','number', array(
                'required' => true,
                'label' => 'Interest Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.875')
            ))
            ->add('mortgage_term','text', array(
                'label' => 'Length in years',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30'),
            ))
            ->add('tax_rate','number', array(
                'required' => true,
                'label' => 'Tax Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '26.000'),
            ))
            ->add('interest_compounded','choice', array(
                'required' => true,
                'label' => 'Interest Compounded',
                'choices' => array('0' => 'Monthly', '1' => 'Bi-Weekly'),
                'empty_value' => false,
                'multiple' => false,
                'expanded' => true,
                'data' => 0,

            ))
		    	->getForm();
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			 
    		}
    	}
    		
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:loanInterval.html.twig', array(
			'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function rentOrBuyAction(Request $request)
    { 
    	$form = $this->createFormBuilder()
            ->add('monthly_rent','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Monthly Rent',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '800.00')
            ))
            ->add('annual_rent_increase','number', array(
                'required' => true,
                'label' => 'Annual Rent Increase',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '4.000')
            ))
            ->add('home_value','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Home Value',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '300000.00')
            ))
            ->add('annual_maintenance','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Annual Maintenance',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '900.00')
            ))
            ->add('annual_appreciation','number', array(
                'required' => true,
                'label' => 'Annual Appreciation',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.000')
            ))
            ->add('years_before_sell','integer', array(
                'required' => true,
                'label' => 'Years Before Sell',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5')
            ))
            ->add('selling_cost','number', array(
                'required' => true,
                'label' => 'Selling Cost',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '7.000')
            ))
            ->add('loan_amount','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Loan Amount',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '250000')
            ))
            ->add('interest_rate','number', array(
                'required' => true,
                'label' => 'Interest Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.875')
            ))
            ->add('mortgage_term','text', array(
                'label' => 'Length in years',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30'),
            ))
            ->add('points','number', array(
                'required' => true,
                'label' => 'Points',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '1.000'),
            ))
            ->add('tax_rate','number', array(
                'required' => true,
                'label' => 'Tax Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '26.000'),
            ))
            ->add('annual_taxes','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Annual Taxes',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '3000.00')
            ))
            ->add('annual_insurance','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Annual Insurance',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '1500.00')
            ))
            ->add('annual_pmi','number', array(
                'required' => true,
                'label' => 'Annual PMI',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '0.500'),
            ))
		    	->getForm();
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			 
    		}
    	}
    	   	
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:rentOrBuy.html.twig', array(
			'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function refinanceAction(Request $request)
    {  
    	$form = $this->createFormBuilder()
            ->add('loan_amount','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Loan Amount',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '250000.00')
            ))
            ->add('interest_rate','number', array(
                'required' => true,
                'label' => 'Interest Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.875')
            ))
            ->add('mortgage_term','integer', array(
                'label' => 'Length in years',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30'),
            ))
            ->add('interest_rate_after_refi','number', array(
                'required' => true,
                'label' => 'Interest Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '4.875')
            ))
            ->add('mortgage_term_after_refi','integer', array(
                'label' => 'Length in years',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30'),
            ))
            ->add('months_paid','integer', array(
                'label' => 'Months Paid',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '36'),
            ))
            ->add('years_before_sell','integer', array(
                'label' => 'Years Before Sell',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5'),
            ))
            ->add('points','number', array(
                'required' => true,
                'label' => 'Points',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '1.000'),
            ))
            ->add('origination_fees','number', array(
                'required' => true,
                'label' => 'Origination Fees',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '0.00'),
            ))
            ->add('closing_costs','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Closing Costs',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '1200.00')
            ))
            ->add('tax_rate','number', array(
                'required' => true,
                'label' => 'Tax Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '26.000'),
            ))
            ->add('state_tax_rate','number', array(
                'required' => true,
                'label' => 'State Tax Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.000'),
            ))
    	->getForm();
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			 
    		}
    	}
    	  	
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:refinance.html.twig', array(
			'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function taxBenefitsAction(Request $request)
    {  
    	$form = $this->createFormBuilder()
            ->add('home_value','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Home Value',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '300000.00')
            ))
            ->add('years_before_sell','integer', array(
                'label' => 'Years Before Sell',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5'),
            ))
            ->add('loan_amount','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Loan Amount',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '250000.00')
            ))
            ->add('interest_rate','number', array(
                'required' => true,
                'label' => 'Interest Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.875')
            ))
            ->add('mortgage_term','integer', array(
                'label' => 'Length in years',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30'),
            ))
            ->add('points','number', array(
                'required' => true,
                'label' => 'Points',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '1.000')
            ))
            ->add('closing_costs','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Closing Costs',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '1200.00')
            ))
            ->add('annual_taxes','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Annual Taxes',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '3000.00')
            ))
            ->add('annual_insurance','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Annual Insurance',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '1500.00')
            ))
            ->add('annual_pmi','number', array(
                'required' => true,
                'label' => 'Annual PMI',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '0.500')
            ))
            ->add('tax_rate','number', array(
                'required' => true,
                'label' => 'Tax Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '26.000'),
            ))
            ->add('state_tax_rate','number', array(
                'required' => true,
                'label' => 'State Tax Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.000'),
            ))
            ->add('deductions','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Deductions',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '3000.00')
            ))
    	->getForm();
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			 
    		}
    	}
    	
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:taxBenefits.html.twig', array(
			'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function payPointsAction(Request $request)
    {  
    	$form = $this->createFormBuilder()
            ->add('loan_amount','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Loan Amount',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '250000.00')
            ))
            ->add('interest_rate','number', array(
                'required' => true,
                'label' => 'Interest Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.875')
            ))
            ->add('interest_rate_with_points','number', array(
                'required' => true,
                'label' => 'Interest Rate with Points',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '4.875')
            ))
            ->add('mortgage_term','integer', array(
                'label' => 'Length in years',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30'),
            ))
            ->add('points','number', array(
                'required' => true,
                'label' => 'Points',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '1.000')
            ))
            ->add('savings_rate','number', array(
                'required' => true,
                'label' => 'Your Savings Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.000')
            ))
    	->getForm();
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			 
    		}
    	}
    	  	
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:payPoints.html.twig', array(
			'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function creditCardPayoffAction(Request $request)
    {   
    	$form = $this->createFormBuilder()
            ->add('current_credit_card_balance','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Current Credit Card Balance',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '10000.00')
            ))
            ->add('current_monthly_payment','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Current Monthly Payment',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '250.00')
            ))
            ->add('interest_rate','number', array(
                'required' => true,
                'label' => 'Annual Interest Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '17.500')
            ))
            ->add('payoff_goal','integer', array(
                'label' => 'Payoff Goal',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '24'),
            ))
    	->getForm();
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			 
    		}
    	}
    	 	
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:creditCardPayoff.html.twig', array(
			'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function creditCardMinPaymentsAction(Request $request)
    {    
    	$form = $this->createFormBuilder()
            ->add('current_credit_card_balance','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Current Credit Card Balance',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '10000.00')
            ))
            ->add('current_min_payment','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Current Minimum Payment',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '250.00')
            ))
            ->add('interest_rate','number', array(
                'required' => true,
                'label' => 'Annual Interest Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '17.500')
            ))
            ->add('fixed_payment','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Fixed Payment Amount',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '250.00')
            ))
    	->getForm();
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			 
    		}
    	}
    		
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:creditCardMinPayments.html.twig', array(
			'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function paymentPerThousandAction(Request $request)
    {    
    	$form = $this->createFormBuilder()
            ->add('loan_amount','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Loan Amount',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '250000.00')
            ))
            ->add('interest_rate','number', array(
                'required' => true,
                'label' => 'Interest Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.875')
            ))
            ->add('mortgage_term','integer', array(
                'label' => 'Length in years',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30'),
            ))
            ->add('points','number', array(
                'required' => true,
                'label' => 'Points',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '1.000')
            ))
            ->add('origination_fees','number', array(
                'required' => true,
                'label' => 'Origination Fees',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '0.000')
            ))
            ->add('closing_costs','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Closing Costs',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '1200.00')
            ))
    	->getForm();
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			 
    		}
    	}
    		
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:paymentPerThousand.html.twig', array(
			'form' => $form->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Form\Exception\AlreadyBoundException
     * @throws \Symfony\Component\Form\Exception\FormException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function realAPRAction(Request $request)
    {    
    	$form = $this->createFormBuilder()
            ->add('home_value','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Home Value',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '300000.00')
            ))
            ->add('interest_rate','number', array(
                'required' => true,
                'label' => 'Interest Rate',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '5.875')
            ))
            ->add('mortgage_term','integer', array(
                'label' => 'Length in years',
                'required' => true,
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '30'),
            ))
            ->add('down_payment','number', array(
                'required' => true,
                'label' => 'Down Payment',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '10.000')
            ))
            ->add('points','number', array(
                'required' => true,
                'label' => 'Points',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '1.000')
            ))
            ->add('origination_fees','number', array(
                'required' => true,
                'label' => 'Origination Fees',
                'constraints' => new NotBlank(),
                'attr' => array('class' => 'span1', 'value' => '0.000')
            ))
            ->add('closing_costs','money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'label' => 'Closing Costs',
                'constraints' => new NotBlank(),
                'attr' => array('value' => '1200.00')
            ))
    	->getForm();
    	 
    	if($request->getMethod() == 'POST') {
    		$form->bind($request);
    		 
    		if ($form->isValid()) {
    			 
    		}
    	}
    		
        return $this->render('SudouxMortgageCalculatorBundle:DefaultFront:realAPR.html.twig', array(
			'form' => $form->createView(),
        ));
    }
}
