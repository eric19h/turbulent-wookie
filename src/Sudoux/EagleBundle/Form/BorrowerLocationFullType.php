<?php

namespace Sudoux\EagleBundle\Form;

use Sudoux\Cms\LocationBundle\Form\LocationRequiredType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BorrowerLocationFullType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', new LocationRequiredType())
            ->add('years_at_location', 'number', array(
                'label' => 'Years',
                'required' => true,
                'attr' => array(
                    'class' => 'field3 text-center years-at-residence form-group-element',
                    'maxlength' => 2,
                )
            ))
            ->add('months_at_location', 'number', array(
                'label' => 'Months',
                'required' => false,
                'attr' => array(
                    'class' => 'field3 text-center months-at-residence form-group-element',
                    'maxlength' => 2,
                    'value' => 0,
                )
            ))
            ->add('own_residence', 'choice', array(
                'label' => 'Do you own this residence?',
                'choices' => array(
                    true => 'Yes',
                    false => 'No',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\EagleBundle\Entity\BorrowerLocationFull'
        ));
    }

    public function getName()
    {
        return 'sudoux_eaglebundle_borrowerlocationfulltype';
    }
}
