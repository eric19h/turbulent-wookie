<?php

namespace Sudoux\MortgageBundle\Form;

use Doctrine\ORM\EntityRepository;

use Sudoux\Cms\SiteBundle\Entity\Site;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class LoanOfficerType
 * @package Sudoux\MortgageBundle\Form
 * @author Dan Alvare
 */
class LoanOfficerType extends AbstractType
{
    /**
     * @var Site
     */
	protected $site;

    /**
     * @param Site $site
     */
	public function __construct(Site $site)
	{
		$this->site = $site;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$site = $this->site;
    	
        $builder
            ->add('first_name')
            ->add('last_name')
            ->add('email')
            ->add('los_id', 'text', array(
                'required' => true,
                'label' => 'LOS ID'
            ))
            ->add('nmls_id', 'text', array(
            	'required' => false,
            	'label' => 'NMLS ID'		
            ))
            ->add('title')
            ->add('phone_office', 'text', array(
                'label' => 'Office Phone',
                'required' => false,
            ))
            ->add('phone_mobile', 'text', array(
                'label' => 'Mobile Phone',
                'required' => false,
            ))
            ->add('phone_tollfree', 'text', array(
                'label' => 'Toll-free Phone',
                'required' => false,
            ))
            ->add('fax')
            ->add('officer_photo_file', 'file', array(
            		'required' => false,
            		'label' => 'Officer Photo',
            ))
            ->add('signature', 'textarea', array(
            	'required' => false,
            	'attr' => array('class' => 'tinymce'),		
            ))
            ->add('bio', 'textarea', array(
            	'required' => false,
            	'attr' => array('class' => 'tinymce'),		
            ))
            ->add('active', 'checkbox', array(
            	'required' => false,		
            ))
            ->add('branch', 'entity', array(
            	'label' => 'Branch',
            	'class' => 'SudouxMortgageBundle:Branch',
            	'property' => 'name',
            	'multiple' => false,
            	'empty_value' => 'Select Branch',
            	'required' => false,
            	'query_builder' => function(EntityRepository $er) use ($site) {
                    return $er->findAllBySiteQuery($site);
                }
            ))
            ->add('website', 'text', array(
            		'required' => false,
            		'attr' => array('class' => 'span4')
            ))
            ->add('weight', 'number', array(
            	'required' => false,
            	'attr' => array('class' => 'span1')		
            ))
            ->add('auto_create_user', 'checkbox', array(
                'required' => false,
                'label' => 'Create User',
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\MortgageBundle\Entity\LoanOfficer'
        ));
    }

    public function getName()
    {
        return 'sudoux_mortgagebundle_loanofficertype';
    }
}
