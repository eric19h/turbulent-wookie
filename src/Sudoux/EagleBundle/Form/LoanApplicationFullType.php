<?php

namespace Sudoux\EagleBundle\Form;

use Sudoux\Cms\SiteBundle\Entity\Site;
use Sudoux\EagleBundle\Entity\AssetAccountFull;
use Sudoux\EagleBundle\Entity\LoanApplicationFull;
use Sudoux\MortgageBundle\Form\ExpenseHousingType;
use Sudoux\MortgageBundle\Form\PropertyLocationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
/**
 * Class LoanApplicationFullType
 * @package Sudoux\EagleBundle\Form
 * @author Eric Haynes
 */
class LoanApplicationFullType extends AbstractType
{

    /**
     * @var LoanApplicationFull
     */
    private $application;

    /**
     * @var Site
     */
    protected $site;

    /**
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $site
     * @param \Sudoux\EagleBundle\Entity\LoanApplicationFull|null $application
     */
    public function __construct(Site $site, LoanApplicationFull $application = null)
    {
        if(isset($application)) {
            $this->application = $application;
        } else {
            $this->application = new LoanApplicationFull();
        }

        $this->site = $site;

    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @author Eric Haynes
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $site = $this->site;

        $application = $this->application;

        $builder
            ->add('sale_price', 'money', array(
                'label' => 'Sale Price',
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'attr' => array('class' => 'span2')
            ))
            ->add('loan_amount', 'money', array(
                'label' => 'Loan Amount',
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'attr' => array('class' => 'span2')
            ))
            ->add('loan_term', 'number', array(
                'label' => 'Term (Years)',
                'required' => true,
                'attr' => array('class' => 'span1', 'maxlength' => 2)
            ))
            ->add('loan_type', 'choice', array(
                'label' => 'Which type of loan are you applying for?',
                'multiple' => false,
                'choices' => $application->loanTypes,
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('status', 'choice', array(
                'label' => 'Loan Status',
                'choices' => $application->loanStatuses,
            ))
            ->add('num_units', 'number', array(
                'label' => 'Number of Units',
                'required' => true,
                'attr' => array('class' => 'span1')
            ))
            ->add('property_type', 'choice', array(
                'choices' => $application->propertyTypes,
                'empty_value' => 'Please select one'
            ))
            ->add('property_year_built', 'text', array(
                'label' => 'Year Built',
                'required' => false,
                'attr' => array('class' => 'span2', 'maxlength' => 4)
            ))
            ->add('residency_type', 'choice', array(
                'choices' => $application->residencyTypes,
                'empty_value' => 'Please select one'
            ))
            ->add('title_company1', 'text', array(
                'label' => 'Name #1',
                'required' => false,
            ))
            ->add('title_company2', 'text', array(
                'label' => 'Name #2',
                'required' => false,
            ))
            ->add('has_realtor', 'choice', array(
                'label' => 'Are you currently working with a Realtor?',
                'choices' => array(
                    0 => 'No',
                    1 => 'Yes',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('property_location', new PropertyLocationType($site), array(
                'validation_groups' => array('step2'),
            ))
            ->add('realtor_name', 'text', array(
                'attr' => array('class' => 'realtor-info-field'),
            ))
            ->add('realtor_company', 'text', array(
                'attr' => array('class' => 'realtor-info-field'),
            ))
            ->add('realtor_phone', 'text', array(
                'attr' => array('class' => 'realtor-info-field phonenumber'),
            ))
            ->add('borrower', new BorrowerFullType())
            ->add('co_borrower', 'collection', array (
                'type' => new BorrowerFullType(),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
            ))
            ->add('asset_account', 'collection', array (
                'type' => new AssetAccountFullType($this->application->getId()),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
            ))
            ->add('asset_real_estate', 'collection', array (
                'type' => new AssetRealEstateFullType($this->application->getId()),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
            ))
            ->add('income_other', 'collection', array (
                'type' => new IncomeOtherFullType($this->application->getId()),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
            ))
            ->add('income_monthly', 'collection', array (
                'type' => new IncomeMonthlyFullType($this->application->getId()),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
            ))
            ->add('agreement_one', 'checkbox', array(
                'required' => true,
            ))
            ->add('agreement_two', 'checkbox', array(
                'required' => true,
            ))
            ->add('agreement_three', 'checkbox', array(
                'required' => true,
            ))
            ->add('expense_housing', new ExpenseHousingType())
            ->add('comments', 'textarea', array(
                'attr' => array('class' => 'span3'),
                'required' => false,
            ))
            ->add('loan_officer', 'entity', array(
                'label' => 'Are you working with a loan officer?',
                'class' => 'SudouxMortgageBundle:LoanOfficer',
                'property' => 'fullname',
                'multiple' => false,
                'empty_value' => 'Select a loan officer',
                'required' => false,
                'query_builder' => function(EntityRepository $er) use ($site) {
                    return $er->findAllBySiteType($site);
                }
            ))
            ->add('refinance_year_acquired', 'number', array(
                'label' => 'Year Acquired',
                'required' => true,
                'attr' => array('class' => 'span1', 'maxlength' => 4),
            ))
            ->add('refinance_original_cost', 'money', array(
                'label' => 'Original Cost',
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'attr' => array('class' => 'span2'),
            ))
            ->add('refinance_existing_liens', 'money', array(
                'label' => 'Existing Liens',
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
                'attr' => array('class' => 'span2'),
            ))
            ->add('refinance_current_rate', 'text', array(
                'label' => 'Current Rate',
                'required' => true,
                'attr' => array('class' => 'span1'),
            ))
            ->add('refinance_current_loan_type', 'text', array(
                'label' => 'Current Loan Type',
                'required' => true,
                'attr' => array('class' => 'span3'),
            ))
            ->add('refinance_current_lender', 'text', array(
                'label' => 'Current Lender',
                'required' => true,
                'attr' => array('class' => 'span3'),
            ))
            ->add('refinance_purpose', 'choice', array(
                'label' => 'Purpose of Refinance',
                'choices' => $application->refinancePurposes,
                'required' => true,
                'empty_value' => 'Please select one',
            ))
            ->add('referral_source', 'entity', array(
                'class' => 'Sudoux\Cms\FormBundle\Entity\ReferralSource',
                'property' => 'name',
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'label' => 'Where did you hear about us?',
                'attr' => array('class' => 'checkbox-group'),
                'query_builder' => function (EntityRepository $er) use ($site) {
                    return $er->findAllActiveBySiteQuery($site);
                }
            ))
            ->add('no_property_location', 'checkbox', array(
                'label' => 'I do not have a property yet',
                'attr' => array('class' => ''),
                'required' => false,
            ))/*
            /*->add('client_user', 'entity', array(
            		'label' => 'Add Additional Users',
            		'class' => 'SudouxCmsUserBundle:User',
            		'property' => 'fullname',
            		'multiple' => false,
            		'empty_value' => 'Select a user',
            		'required' => false,
            		'query_builder' => function(EntityRepository $er) use ($site) {
		            	return $er->findAllBySiteQueryBuilder($site);
		            }
            ))*/
        ;

        $titleManner = $this->application->getTitleManner();
        if(isset($titleManner)) {
            $builder->add('title_manner', 'text', array(
                'label' => 'Manner',
                'required' => false,
            ));
        } else {
            $builder->add('title_manner', 'choice', array(
                'label' => 'Manner',
                'required' => false,
                'choices' => $application->titleManners,
                'empty_value' => 'Please select one',
                'required' => false,
            ));
        }

        /*$group = $application->getMilestoneGroup();
        if(isset($group)) {
			$builder->add('milestone', 'entity', array(
				'label' => 'Loan Status',
				'class' => 'SudouxMortgageBundle:LoanMilestone',
				'property' => 'name',
				'multiple' => false,
				'empty_value' => 'Select a status',
				'required' => false,
				'query_builder' => function(EntityRepository $er) use ($group) {
					return $er->findByMilestoneGroupQuery($group->getId());
				}
			));
		}*/
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     * @author Eric Haynes
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\EagleBundle\Entity\LoanApplicationFull',
            'validation_groups' => array(
                'step1', 'step2', 'step3', 'step4', 'step5', 'step6', 'step7', 'prequalify',
            ),
        ));
    }


    /**
     * @return string
     * @author Eric Haynes
     */
    public function getName()
    {
        return 'sudoux_eaglebundle_loanapplicationfulltype';
    }

}
