<?php

namespace Sudoux\EagleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BorrowerFullType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name')
            ->add('last_name')
            ->add('email')
            ->add('middle_name')
            ->add('suffix')
            ->add('phone_home')
            ->add('phone_mobile')
            ->add('ssn')
            ->add('birth_date')
            ->add('years_of_school')
            ->add('marital_status')
            ->add('ethnicity')
            ->add('race')
            ->add('is_male')
            ->add('employed')
            ->add('dependents')
            ->add('dependents_number')
            ->add('dependents_ages')
            ->add('govt_monitoring_opt_out')
            ->add('declaration_outstanding_judgement')
            ->add('declaration_outstanding_judgement_details')
            ->add('declaration_bankruptcy')
            ->add('declaration_bankruptcy_details')
            ->add('declaration_forclosure')
            ->add('declaration_forclosure_details')
            ->add('declaration_lawsuit')
            ->add('declaration_lawsuit_details')
            ->add('declaration_forclosure_obligation')
            ->add('declaration_forclosure_obligation_details')
            ->add('declaration_in_default')
            ->add('declaration_in_default_details')
            ->add('declaration_alimony_child_support')
            ->add('declaration_alimony_child_support_details')
            ->add('declaration_alimony_child_support_alimony')
            ->add('declaration_alimony_child_support_child_support')
            ->add('declaration_alimony_child_support_separate_maintenance')
            ->add('declaration_down_payment_borrowed')
            ->add('declaration_down_payment_borrowed_details')
            ->add('declaration_note_endorser')
            ->add('declaration_note_endorser_details')
            ->add('declaration_us_citizen')
            ->add('declaration_resident_alien')
            ->add('declaration_primary_residence')
            ->add('declaration_ownership_within_three_years')
            ->add('declaration_ownership_within_three_years_property_type')
            ->add('declaration_ownership_within_three_years_property_title')
            ->add('initials')
            ->add('signature')
            ->add('los_id')
            ->add('citizen_status')
            ->add('preferred_contact_time')
            ->add('preferred_contact_method')
            ->add('credit_report_authorized')
            ->add('consent_to_contact')
            ->add('electronic_delivery_consent')
            ->add('consent_to_share_info')
            ->add('living_status')
            ->add('credit_report')
            ->add('location')
            ->add('mailing_location')
            ->add('previous_location')
            ->add('employment')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\EagleBundle\Entity\BorrowerFull'
        ));
    }

    public function getName()
    {
        return 'sudoux_eaglebundle_borrowerfulltype';
    }
}
