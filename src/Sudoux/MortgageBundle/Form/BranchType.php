<?php

namespace Sudoux\MortgageBundle\Form;

use Doctrine\ORM\EntityRepository;
use Sudoux\Cms\LocationBundle\Form\LocationRequiredType;

use Sudoux\Cms\SiteBundle\Entity\Site;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class BranchType
 * @package Sudoux\MortgageBundle\Form
 * @author Dan Alvare
 */
class BranchType extends AbstractType
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
            ->add('name')
            ->add('nmls_id', 'text', array(
            	'label' => 'NMLS #',
            	'required' => false,		
            )) 
            ->add('los_id')
            ->add('website', 'text', array(
            	'required' => false,	
            	'attr' => array('class' => 'span4')	
            ))
            ->add('directions', 'textarea', array(
            	'required' => false,
            	'attr' => array('class' => 'tinymce'),		
            ))
            ->add('description', 'textarea', array(
            	'required' => false,
            	'attr' => array('class' => 'tinymce'),		
            ))
            ->add('phone')
            ->add('fax')
            ->add('email')
            ->add('branch_photo_file', 'file', array(
            	'required' => false,
            	'label' => 'Branch Photo',
            ))
            ->add('active', 'checkbox', array(
            	'required' => false,		
            ))
            ->add('location', new LocationRequiredType())
            ->add('weight', 'number', array(
            	'required' => false,
            	'attr' => array('class' => 'span1')
            ))
            ->add('branch_manager', 'entity', array(
                'label' => 'Branch Manager',
                'class' => 'SudouxMortgageBundle:LoanOfficer',
                'property' => 'fullname',
                'multiple' => false,
                'empty_value' => 'Select a manager',
                'required' => false,
                'query_builder' => function(EntityRepository $er) use ($site) {
                    return $er->findAllBySiteQuery($site);
                }
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\MortgageBundle\Entity\Branch'
        ));
    }

    public function getName()
    {
        return 'sudoux_mortgagebundle_branchtype';
    }
}
