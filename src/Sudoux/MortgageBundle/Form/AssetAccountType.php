<?php

namespace Sudoux\MortgageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Class AssetAccountType
 * @package Sudoux\MortgageBundle\Form
 * @author Dan Alvare
 */
class AssetAccountType extends AbstractType
{
	private $applicationId;
	
	public function __construct($applicationId = null)
	{
		if(isset($applicationId)) {
			$this->applicationId = $applicationId;
		}
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$applicationId = $this->applicationId;
    	
        $builder
            ->add('institution_name', 'text', array(
            	'required' => true,		
            ))
            ->add('type', 'choice', array(
            	'choices' => array(
	            	0 => 'Checking',		
	            	1 => 'Savings',		
	            	2 => 'Money Market',		
	            	3 => 'CD',		
	            	4 => 'Mutual Fund',		
	            	5 => 'Retirement',		
	            	6 => 'Other',		
            	),
            	'empty_value' => 'Select Type',
            	'required' => true
            )) 
            ->add('account_number')
            ->add('balance', 'money', array(
            	'currency' => 'USD',
            	'required' => true,
            	'precision' => 2,	
            	'grouping' => true,	
            ))
            ->add('borrower', 'entity', array(
            	'label' => 'Borrower/Co-Borrower',
            	'class' => 'SudouxMortgageBundle:Borrower',
            	'property' => 'full_name',
            	'multiple' => false,
            	'empty_value' => 'Choose a borrower',
            	'query_builder' => function(EntityRepository $er) use ($applicationId) {
            		return $er->findByLoanApplication($applicationId);
            	}
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\MortgageBundle\Entity\AssetAccount'
        ));
    }

    public function getName()
    {
        return 'sudoux_mortgagebundle_assetaccounttype';
    }
}
