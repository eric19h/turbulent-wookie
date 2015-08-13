<?php

namespace Sudoux\MortgageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ExpenseHousingType
 * @package Sudoux\MortgageBundle\Form
 * @author Dan Alvare
 */
class ExpenseHousingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rent', 'money', array(
            	'currency' => 'USD',
            	'required' => false,
            	'precision' => 2,	
            	'grouping' => true,	
            ))
            ->add('mortgage', 'money', array(
            	'currency' => 'USD',
            	'required' => false,
            	'precision' => 2,	
            	'grouping' => true,	
            ))
            ->add('other_financial', 'money', array(
            	'label' => 'Other Financial',
            	'currency' => 'USD',
            	'required' => false,
            	'precision' => 2,	
            	'grouping' => true,	
            ))
            ->add('insurance_hazard', 'money', array(
            	'label' => 'Hazard Insurance',
            	'currency' => 'USD',
            	'required' => false,
            	'precision' => 2,	
            	'grouping' => true,	
            ))
            ->add('insurance_mortgage', 'money', array(
            	'label' => 'Mortgage Insurance',
            	'currency' => 'USD',
            	'required' => false,
            	'precision' => 2,	
            	'grouping' => true,	
            ))
            ->add('tax_real_estate', 'money', array(
            	'label' => 'Real Estate Tax',
            	'currency' => 'USD',
            	'required' => false,
            	'precision' => 2,	
            	'grouping' => true,	
            ))
            ->add('hoa_dues', 'money', array(
            	'label' => 'HOA Dues',
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
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\MortgageBundle\Entity\ExpenseHousing'
        ));
    }

    public function getName()
    {
        return 'sudoux_mortgagebundle_expensehousingtype';
    }
}
