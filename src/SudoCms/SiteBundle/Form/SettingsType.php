<?php

namespace SudoCms\SiteBundle\Form;

use Sudoux\MortgageBundle\Form\PricingConnectionType;

use Sudoux\MortgageBundle\Form\CreditConnectionType;

use Sudoux\MortgageBundle\Entity\LosConnection;

use Sudoux\MortgageBundle\Form\LosConnectionType;

use Sudoux\Cms\SiteBundle\Entity\Site;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SettingsType
 * @package SudoCms\SiteBundle\Form
 * @author Dan Alvare
 */
class SettingsType extends \Sudoux\Cms\SiteBundle\Form\SettingsType
{
	
	public function __construct(Site $site)
	{
		parent::__construct($site);
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	parent::buildForm($builder, $options);
    	$site = $this->site;
        $builder
            ->add('loan_document_vocab', 'entity', array(
            	'label' => 'Document Checklist Category',
            	'class' => 'Sudoux\Cms\TaxonomyBundle\Entity\Vocabulary',
            	'property' => 'name',
            	'empty_value' => 'Select a Vocabulary',
            	'query_builder' => function(EntityRepository $er) use ($site) {
            		return $er->findAllBySiteQuery($site);
            	},
    		))
    		->add('show_los_milestones', 'checkbox', array(
    			'label' => 'Display Milestones',
    			'required' => false,
    		))
            ->add('send_milestones_notifications', 'checkbox', array(
                'label' => 'Send Milestone Notifications',
                'required' => false,
            ))
            ->add('loan_complete_url', 'text', array(
                'label' => 'Loan Completion URL',
                'attr' => array('class' => 'span5'),
                'required' => false,
            ))
            ->add('prequal_complete_url', 'text', array(
                'label' => 'Prequal Completion URL',
                'attr' => array('class' => 'span5'),
                'required' => false,
            ))
    		->add('loan_officer', 'entity', array(
            	'label' => 'Loan Officer',
            	'class' => 'Sudoux\MortgageBundle\Entity\LoanOfficer',
            	'property' => 'fullName',
            	'empty_value' => 'Select an Officer',
            	'query_builder' => function(EntityRepository $er) use ($site) {
            		return $er->findAllByParentSiteQuery($site);
            	},
    		))
    		->add('branch', 'entity', array(
            	'label' => 'Branch',
            	'class' => 'Sudoux\MortgageBundle\Entity\Branch',
            	'property' => 'name',
            	'empty_value' => 'Select a Branch',
            	'query_builder' => function(EntityRepository $er) use ($site) {
            		return $er->findAllBySiteQuery($site);
            	},
    		))
            ->add('member_portal_enabled', 'checkbox', array(
                'label' => 'Member Portal Enabled',
                'required' => false,
            ))
            ->add('member_portal_documents_enabled', 'checkbox', array(
                'label' => 'Member Portal Documents Enabled',
                'required' => false,
            ))
            ->add('state_license', 'entity', array(
                'label' => 'State Licenses',
                'class' => 'Sudoux\Cms\LocationBundle\Entity\State',
                'property' => 'name',
                'multiple' => true,
                'expanded' => true,
            ))
            ->add('auto_create_loan_officer_user', 'checkbox', array(
                'label' => 'Auto Create Loan Officer User',
                'required' => false,
            ))
        ;
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    	$resolver->setDefaults(array(
            'data_class' => 'Sudoux\Cms\SiteBundle\Entity\Settings',
            'validation_groups' => array('general', 'website', 'theme_settings', 'wizard_settings', 'wizard_billing', 'mortgage'),
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'sudocms_sitebundle_settingstype';
    }
}
