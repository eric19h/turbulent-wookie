<?php

namespace Sudoux\MortgageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class RealtyWareConnectionType
 * @package Sudoux\MortgageBundle\Form
 * @author Dan Alvare
 */
class RealtyWareConnectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domain', 'text', array(
            	'attr' => array('class' => 'span5')		
            ))
            ->add('api_key', 'text', array(
            	'attr' => array('class' => 'span4')		
            ))
            ->add('client_id', 'text', array(
            	'attr' => array('class' => 'span5')		
            ))
            ->add('client_secret', 'text', array(
            	'attr' => array('class' => 'span5')		
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\MortgageBundle\Entity\RealtyWareConnection'
        ));
    }

    public function getName()
    {
        return 'sudoux_mortgagebundle_realtywareconnectiontype';
    }
}
