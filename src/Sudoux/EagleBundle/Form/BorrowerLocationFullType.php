<?php

namespace Sudoux\EagleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BorrowerLocationFullType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('created')
            ->add('years_at_location')
            ->add('months_at_location')
            ->add('own_residence')
            ->add('has_foreign_address')
            ->add('location')
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
