<?php

namespace Sudoux\MortgageBundle\Form;

use Sudoux\MortgageBundle\Entity\LosConnection;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class LosConnectionType
 * @package Sudoux\MortgageBundle\Form
 * @author Dan Alvare
 */
class LosConnectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$losConnection = new LosConnection();
        $builder
            ->add('service_url', 'text', array(
            	'required' => false,
            	'attr' => array('class' => 'span5')
            ))
            ->add('host', 'text', array(
            	'required' => false,
            	'attr' => array('class' => 'span5'),
            	'label' => 'LOS Host',
            ))
            ->add('los_provider', 'entity', array(
				'class' => 'Sudoux\MortgageBundle\Entity\LosProvider',
				'property' => 'name',
				'empty_value' => 'Select a Provider',
			))
            ->add('username')
            ->add('password', 'password', array(
            	'required' => true,
            ))
            ->add('license_key', 'text', array(
            	'required' => false,
            	'attr' => array('class' => 'span6')
            ))
            ->add('settings', 'textarea', array(
            	'required' => false,		
            ))
            ->add('automatic', 'checkbox', array(
            	'required' => false,
            	'label' => 'Automatic Sync'		
            ))
            ->add('import_los_loans', 'checkbox', array(
            	'label' => 'Import Loans Originated in the LOS',
            	'required' => false,
            ))
            ->add('allow_loan_deletions', 'checkbox', array(
                'label' => 'Allow Loan Deletions',
                'required' => false,
            ))
            ->add('auto_send_docs', 'checkbox', array(
                'label' => 'Auto Send Documents',
                'required' => false,
            ))
            ->add('active', 'checkbox', array(
                'label' => 'Active',
                'required' => false,
            ))
            ->add('last_updated', 'date', array(
                'widget' => 'single_text',
                'format' => 'MM-dd-yyyy hh:mm a',
                'attr' => array('class' => 'datetimepicker'),
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\MortgageBundle\Entity\LosConnection',
        ));
    }

    public function getName()
    {
        return 'sudoux_mortgagebundle_losconnectiontype';
    }
}
