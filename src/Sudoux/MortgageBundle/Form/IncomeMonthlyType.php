<?php

namespace Sudoux\MortgageBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class IncomeMonthlyType
 * @package Sudoux\MortgageBundle\Form
 * @author Dan Alvare
 */
class IncomeMonthlyType extends AbstractType
{
	private $applicationId;
	
	public function __construct($applicationId)
	{
		$this->applicationId = $applicationId;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$applicationId = $this->applicationId;
    	
        $builder
            ->add('base', 'money', array(
            	'currency' => 'USD',
            	'required' => false,
            	'precision' => 2,	
            	'grouping' => true,	
            ))
            ->add('overtime', 'money', array(
            	'currency' => 'USD',
            	'required' => false,
            	'precision' => 2,	
            	'grouping' => true,	
            ))
            ->add('bonus', 'money', array(
            	'currency' => 'USD',
            	'required' => false,
            	'precision' => 2,	
            	'grouping' => true,	
            ))
            ->add('commission', 'money', array(
            	'currency' => 'USD',
            	'required' => false,
            	'precision' => 2,	
            	'grouping' => true,	
            ))
            ->add('interest', 'money', array(
            	'currency' => 'USD',
            	'required' => false,
            	'precision' => 2,	
            	'grouping' => true,	
            ))
            ->add('rent_net', 'money', array(
            	'currency' => 'USD',
            	'required' => false,
            	'precision' => 2,	
            	'grouping' => true,	
            ))
            ->add('other', 'money', array(
            	'currency' => 'USD',
            	'required' => false,
            	'precision' => 2,	
            	'grouping' => true,	
            ))
            ->add('borrower', 'entity', array(
            	'label' => 'Borrower/Co-Borrower',
            	'class' => 'SudouxMortgageBundle:Borrower',
            	'property' => 'full_name',
            	'multiple' => false,
            	'empty_value' => 'Select',
            	'query_builder' => function(EntityRepository $er) use ($applicationId) {
                    return $er->findByLoanApplication($applicationId);
                }
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\MortgageBundle\Entity\IncomeMonthly'
        ));
    }

    public function getName()
    {
        return 'sudoux_mortgagebundle_incomemonthlytype';
    }
}
