<?php

namespace Sudoux\EagleBundle\Form;

use Sudoux\EagleBundle\Entity\BorrowerFull;
use Sudoux\MortgageBundle\Form\BorrowerLocationType;
use Sudoux\MortgageBundle\Form\EmploymentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BorrowerFullType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $borrower = new BorrowerFull();

        $builder
            ->add('first_name', 'text', array(
                'attr' => array('class' => 'span3'),
            ))
            ->add('last_name', 'text', array(
                'attr' => array('class' => 'span3'),
            ))
            ->add('middle_name', 'text', array(
                'required' => false,
                'attr' => array('class' => 'span1', 'maxlength' => '1'),
                'label' => 'Middle Initial',
            ))
            ->add('suffix', 'text', array(
                'required' => false,
                'attr' => array('class' => 'span1')
            ))
            ->add('phone_home', 'text', array(
                'label' => 'Primary Phone',
                'attr' => array('class' => 'phonenumber span3'),
            ))
            ->add('phone_mobile', 'text', array(
                'label' => 'Mobile Phone',
                'attr' => array('class' => 'phonenumber span3'),
                'required' => false,
            ))
            ->add('email', 'text', array(
                'attr' => array('class' => 'span3'),
            ))
            ->add('ssn', 'text', array(
                'label' => 'SSN',
                'attr' => array(
                    'maxlength' => 11,
                    'class' => 'ssn-field span3',
                ),
                'required' => true,
            ))
            ->add('birth_date', 'date', array(
                'widget' => 'single_text',
                'format' => 'MM-dd-yyyy',
                'attr' => array('class' => 'datepicker birthdate'),
            ))
            ->add('years_of_school', 'number', array(
                'attr' => array(
                    'class' => 'field3 text-center',
                    'maxlength' => 2
                )
            ))
            ->add('marital_status', 'choice', array(
                'choices' => $borrower->maritial_statuses,
                'empty_value' => 'Select a value',
                'attr' => array('class' => 'span3'),
            ))
            ->add('location', new BorrowerLocationFullType())
            ->add('previous_location', 'collection', array (
                'type' => new BorrowerLocationFullType(),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'cascade_validation' => true,
            ))
            ->add('ethnicity', 'choice', array(
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'choices' => $borrower->ethnicities,
                'empty_value' => 'Select your ethnicity',
            ))
            ->add('race', 'choice', array(
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'choices' => $borrower->races,
                'empty_value' => 'Select your race'
            ))
            ->add('is_male', 'choice', array(
                'label' => 'Sex',
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    0 => 'Female',
                    1 => 'Male'
                )
            ))
            ->add('govt_monitoring_opt_out', 'checkbox', array(
                'required' => false,
                'label' => 'I do not wish to furnish this information',
            ))
            ->add('declaration_outstanding_judgement', 'choice', array(
                'label' => 'Are there any outstanding judgments against you?',
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('declaration_outstanding_judgement_details', 'textarea', array(
                'label' => 'Please provide additional details.',
                'required' => false,
            ))
            ->add('declaration_bankruptcy', 'choice', array(
                'label' => 'Have you been declared bankrupt within the past 7 years?',
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('declaration_bankruptcy_details', 'textarea', array(
                'label' => 'Please provide additional details.',
                'required' => false,
            ))
            ->add('declaration_forclosure', 'choice', array(
                'label' => 'Have you had property foreclosed upon or given title or deed in lieu thereof in the last 7 years?',
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('declaration_forclosure_details', 'textarea', array(
                'label' => 'Please provide additional details.',
                'required' => false,
            ))
            ->add('declaration_lawsuit', 'choice', array(
                'label' => 'Are you a party to a lawsuit?',
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('declaration_lawsuit_details', 'textarea', array(
                'label' => 'Please provide additional details.',
                'required' => false,
            ))
            ->add('declaration_forclosure_obligation', 'choice', array(
                'label' => 'Have you directly or indirectly been obligated on any loan which resulted in foreclosure, transfer of title in lieu of foreclosure, or judgment? ',
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('declaration_forclosure_obligation_details', 'textarea', array(
                'label' => 'Please provide additional details.',
                'required' => false,
            ))
            ->add('declaration_in_default', 'choice', array(
                'label' => 'Are you presently delinquent or in default on any Federal debt or any other loan, mortgage, financial obligation, bond or loan guarantee?',
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('declaration_in_default_details', 'textarea', array(
                'label' => 'Please provide additional details.',
                'required' => false,
            ))
            ->add('declaration_alimony_child_support', 'choice', array(
                'label' => 'Are you obligated to pay alimony, child support, or separate maintenance?',
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('declaration_alimony_child_support_alimony', 'money', array(
                'label' => 'Alimony',
                'required' => false,
                'currency' => 'USD',
                'precision' => 2,
                'grouping' => true,
                'attr' => array('class' => 'span2'),
            ))
            ->add('declaration_alimony_child_support_child_support', 'money', array(
                'label' => 'Child Support',
                'required' => false,
                'currency' => 'USD',
                'precision' => 2,
                'grouping' => true,
                'attr' => array('class' => 'span2'),
            ))
            ->add('declaration_alimony_child_support_separate_maintenance', 'money', array(
                'label' => 'Separate Maintenance',
                'required' => false,
                'currency' => 'USD',
                'precision' => 2,
                'grouping' => true,
                'attr' => array('class' => 'span2'),
            ))
            ->add('declaration_down_payment_borrowed', 'choice', array(
                'label' => 'Is any part of the down payment borrowed?',
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('declaration_down_payment_borrowed_details', 'textarea', array(
                'label' => 'Please provide additional details.',
                'required' => false,
            ))
            ->add('declaration_note_endorser', 'choice', array(
                'label' => 'Are you a co-maker or endorser on a note?',
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('declaration_note_endorser_details', 'textarea', array(
                'label' => 'Please provide additional details.',
                'required' => false,
            ))
            ->add('declaration_us_citizen', 'choice', array(
                'label' => 'Are you a U.S. citizen?',
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'multiple' => false,
                'expanded' => true,
            ))

            ->add('declaration_resident_alien', 'choice', array(
                'label' => 'Are you a permanent resident alien?',
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('declaration_primary_residence', 'choice', array(
                'label' => 'Do you intend to occupy the property as your primary residence?',
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('declaration_ownership_within_three_years', 'choice', array(
                'label' => 'Have you had an ownership interest in a property in the last three years?',
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('declaration_ownership_within_three_years_property_type', 'choice', array(
                'label' => 'What type of property did you own?',
                'choices' => $borrower->property_ownership_types,
                'multiple' => false,
                'expanded' => false,
                'required' => true,
                'empty_value' => 'Select one',
            ))
            ->add('declaration_ownership_within_three_years_property_title', 'choice', array(
                'label' => 'How did you hold the title to the home?',
                'choices' => $borrower->property_ownership_title_types,
                'multiple' => false,
                'required' => true,
                'expanded' => false,
                'empty_value' => 'Select one',
            ))
            ->add('initials', 'text', array(
                'label' => "Borrower's Initials",
                'required' => true,
                'attr' => array('class' => 'span1 borrower-initials'),
            ))
            ->add('signature', 'hidden', array(
                'attr' => array('class' => 'output'),
                'required' => false,
            ))
            ->add('employed', 'choice', array(
                'label' => 'Are you currently employed?',
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('employment', 'collection', array (
                'type' => new EmploymentType(),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'cascade_validation' => true,
            ))
            ->add('dependents', 'choice', array(
                'label' => 'Dependents',
                'choices' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('dependents_number', 'number', array(
                'required' => true,
                'label' => 'No.',
                'attr' => array('class' => 'span1')
            ))
            ->add('dependents_ages', 'text', array(
                'required' => true,
                'label' => 'Ages',
                'attr' => array('class' => 'span2')
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\EagleBundle\Entity\BorrowerFull'
        ));
    }

    public function getName()
    {
        return 'sudoux_eaglebundle_borrowerfulltype';
    }
}
