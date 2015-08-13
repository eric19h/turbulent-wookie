<?php

namespace Sudoux\MortgageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CreditConnectionType
 * @package Sudoux\MortgageBundle\Form
 * @author Dan Alvare
 */
class CreditConnectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('credit_provider', 'entity', array(
	        		'class' => 'Sudoux\MortgageBundle\Entity\CreditProvider',
	        		'property' => 'name',
	        		'empty_value' => 'Select a Provider',
	        ))
            ->add('username')
            ->add('password', 'password', array(
            	'required' => true,
            ))
            ->add('company')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\MortgageBundle\Entity\CreditConnection'
        ));
    }

    public function getName()
    {
        return 'sudoux_mortgagebundle_creditconnectiontype';
    }
}
