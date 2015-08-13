<?php

namespace Sudoux\MortgageBundle\Form;

use Doctrine\ORM\EntityRepository;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class LocationRequiredType
 * @package Sudoux\Cms\LocationBundle\Form
 * @author Dan Alvare
 */
class PropertyLocationType extends AbstractType
{
    /**
     * @var Site
     */
    protected $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $site = $this->site;

        $builder
            ->add('name', 'text', array(
            	'label' => 'Location Name',	
            	'required' => false,	
            	'attr' => array('class' => 'span3'),
            ))
            ->add('unit', 'text', array(
            	'label' => 'Unit',	
            	'required' => false,	
            	'attr' => array('class' => 'span1'),
            ))
            ->add('address1', 'text', array(
            	'label' => 'Address',	
            	'required' => true,		
            	'attr' => array('class' => 'span3'),
            ))
            ->add('address2', 'text', array(
            	'label' => 'Address 2',		
            	'required' => false,	
            	'attr' => array('class' => 'span3'),
            ))
            ->add('neighborhood', 'text', array(
            	'required' => false,	
            	'attr' => array('class' => 'span3'),
            ))
            ->add('zipcode', 'text', array(
            	'required' => true,	
            	'attr' => array('class' => 'span2', 'maxlength' => '5'),	
            ))
            ->add('city', 'text', array(
            	'required' => true,		
            	'attr' => array('class' => 'span3'),	
            ))
            ->add('county', 'text', array(
            	'required' => false,		
            	'attr' => array('class' => 'span2'),	
            ));

            $stateLicenses = $site->getSettings()->getInheritedStateLicense();
            if($stateLicenses->count() > 0) {
                $builder->add('state', 'entity', array(
                    'class' => 'Sudoux\Cms\LocationBundle\Entity\State',
                    'property' => 'name',
                    'empty_value' => 'Select a state',
                    'required' => true,
                    'attr' => array('class' => 'span3'),
                    'choices' => $stateLicenses,
                ));
            } else {
                $builder->add('state', 'entity', array(
                    'class' => 'Sudoux\Cms\LocationBundle\Entity\State',
                    'property' => 'name',
                    'empty_value' => 'Select a state',
                    'required' => true,
                    'attr' => array('class' => 'span3'),
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('u')->orderBy('u.name', 'ASC');
                    }
                ));
            }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\Cms\LocationBundle\Entity\Location',
        ));
    }

    public function getName()
    {
        return 'sudoux_mortgagebundle_propertylocationtype';
    }
}
