<?php

namespace Sudoux\MortgageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PricingConnectionType
 * @package Sudoux\MortgageBundle\Form
 * @author Dan Alvare
 */
class PricingConnectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password', 'password', array(
            	'required' => true,
            ))
            ->add('company')
            ->add('pricing_provider', 'entity', array(
				'class' => 'Sudoux\MortgageBundle\Entity\PricingProvider',
				'property' => 'name',
				'empty_value' => 'Select a Provider',
			))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\MortgageBundle\Entity\PricingConnection'
        ));
    }

    public function getName()
    {
        return 'sudoux_mortgagebundle_pricingconnectiontype';
    }
}
