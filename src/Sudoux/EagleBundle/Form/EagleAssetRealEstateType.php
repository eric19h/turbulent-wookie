<?php

namespace Sudoux\EagleBundle\Form;

use Doctrine\ORM\EntityRepository;
use Sudoux\Cms\LocationBundle\Form\LocationRequiredType;
use Sudoux\EagleBundle\Entity\EagleAssetRealEstate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EagleAssetRealEstateType extends AbstractType
{
    private $applicationId;

    public function __construct($applicationId = null)
    {
        if(isset($applicationId)) {
            $this->applicationId = $applicationId;
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $assetRealEstate = new EagleAssetRealEstate();
        $applicationId = $this->applicationId;
        $builder
            ->add('market_value', 'money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
            ))
            ->add('mortgage_amount', 'money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
            ))
            ->add('mortgage_payment', 'money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
            ))
            ->add('rent_gross_income', 'money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
            ))
            ->add('ins_tax_exp', 'money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
            ))
            ->add('rent_net_income', 'money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
            ))
            ->add('original_cost', 'money', array(
                'currency' => 'USD',
                'required' => true,
                'precision' => 2,
                'grouping' => true,
            ))
            ->add('date_aquired', 'date', array(
                'widget' => 'single_text',
                'format' => 'MM-dd-yyyy',
                'attr' => array('class' => 'datepicker'),
            ))
            ->add('status', 'choice', array(
                'choices' => $assetRealEstate->statuses,
                'required' => false,
                'empty_value' => 'Select a Status',
            ))
            ->add('location', new LocationRequiredType())
            ->add('borrower', 'entity', array(
                'label' => 'Borrower/Co-Borrower',
                'class' => 'SudouxEagleBundle:EagleBorrower',
                'property' => 'full_name',
                'multiple' => false,
                'empty_value' => 'Choose a borrower',
                'query_builder' => function(EntityRepository $er) use ($applicationId) {
                    return $er->findByLoanApplication($applicationId);
                }
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\EagleBundle\Entity\EagleAssetRealEstate'
        ));
    }

    public function getName()
    {
        return 'sudoux_eaglebundle_EagleAssetRealEstateType';
    }
}
