<?php

namespace Sudoux\EagleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AssetAccountFullType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('institution_name')
            ->add('type')
            ->add('account_number')
            ->add('balance')
            ->add('borrower')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\EagleBundle\Entity\AssetAccountFull'
        ));
    }

    public function getName()
    {
        return 'sudoux_eaglebundle_assetaccountfulltype';
    }
}
