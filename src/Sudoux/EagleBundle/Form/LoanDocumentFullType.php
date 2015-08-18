<?php

namespace Sudoux\EagleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LoanDocumentFullType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('extension')
            ->add('status')
            ->add('required')
            ->add('los_id')
            ->add('los_status')
            ->add('created')
            ->add('file')
            ->add('type')
            ->add('loan')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\EagleBundle\Entity\LoanDocumentFull'
        ));
    }

    public function getName()
    {
        return 'sudoux_eaglebundle_loandocumentfulltype';
    }
}
