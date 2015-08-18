<?php

namespace Sudoux\EagleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AssetRealEstateFullType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('market_value')
            ->add('mortgage_amount')
            ->add('original_cost')
            ->add('mortgage_payment')
            ->add('rent_gross_income')
            ->add('ins_tax_exp')
            ->add('rent_net_income')
            ->add('date_aquired')
            ->add('status')
            ->add('location')
            ->add('borrower')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\EagleBundle\Entity\AssetRealEstateFull'
        ));
    }

    public function getName()
    {
        return 'sudoux_eaglebundle_assetrealestatefulltype';
    }
}
