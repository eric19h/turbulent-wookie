<?php

namespace Sudoux\MortgageBundle\Form;

use Sudoux\Cms\LocationBundle\Form\LocationRequiredType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class EmploymentType
 * @package Sudoux\MortgageBundle\Form
 * @author Dan Alvare
 */
class EmploymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('employer_name', 'text', array(
            	'attr' => array('class' => 'employer-name')		
            ))
            ->add('employer_phone', 'text', array(
            	'attr' => array(
            		'class' => 'phonenumber span3',
            	),
            	'required' => false
            ))
            ->add('title', 'text', array(
            	'label' => 'Position / Title / Type of Business'		
            ))
            ->add('start_date', 'date', array(
            	'widget' => 'single_text',
            	'format' => 'MM-dd-yyyy',
            	'attr' => array('class' => 'datepicker start-date'),
            	'label' => 'Time of Employment'
            ))
            ->add('end_date', 'date', array(
            	'widget' => 'single_text',
            	'format' => 'MM-dd-yyyy',
            	'attr' => array('class' => 'datepicker end-date'),
            ))
            ->add('self_employed', 'choice', array(
            	'choices' => array(
            		true => 'Yes',
            		false => 'No',
            	),
            	'multiple' => false,
            	'expanded' => true,
            ))
            ->add('years_employed', 'number', array(
            	'label' => 'Years employed in this line of work',
            	'attr' => array(
            		'class' => 'span1', 
            		'maxlength' => 2
            	)		
            ))
            ->add('location', new LocationRequiredType())
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\MortgageBundle\Entity\Employment',
        	'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'sudoux_mortgagebundle_employmenttype';
    }
}
