<?php

namespace Sudoux\EagleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EagleCreditReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('experian_score')
            ->add('transunion_score')
            ->add('equifax_score')
            ->add('created')
            ->add('modified')
            ->add('report_file')
            ->add('credit_provider')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\EagleBundle\Entity\CreditReportFull'
        ));
    }

    public function getName()
    {
        return 'sudoux_eaglebundle_EagleCreditReportType';
    }
}
