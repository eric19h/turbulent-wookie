<?php

namespace Sudoux\EagleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IncomeMonthlyFullType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('base')
            ->add('overtime')
            ->add('bonus')
            ->add('commission')
            ->add('interest')
            ->add('rent_net')
            ->add('other')
            ->add('borrower')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\EagleBundle\Entity\IncomeMonthlyFull'
        ));
    }

    public function getName()
    {
        return 'sudoux_eaglebundle_incomemonthlyfulltype';
    }
}
