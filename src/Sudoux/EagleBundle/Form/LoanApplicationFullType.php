<?php

namespace Sudoux\EagleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LoanApplicationFullType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('guid')
            ->add('sale_price')
            ->add('loan_amount')
            ->add('los_id')
            ->add('los_loan_number')
            ->add('loan_term')
            ->add('loan_type')
            ->add('num_units')
            ->add('property_type')
            ->add('property_year_built')
            ->add('residency_type')
            ->add('title_company1')
            ->add('title_company2')
            ->add('title_company3')
            ->add('title_manner')
            ->add('has_realtor')
            ->add('realtor_name')
            ->add('realtor_company')
            ->add('realtor_phone')
            ->add('is_prequal')
            ->add('refinance_year_acquired')
            ->add('refinance_original_cost')
            ->add('refinance_existing_liens')
            ->add('refinance_current_rate')
            ->add('refinance_current_loan_type')
            ->add('refinance_current_lender')
            ->add('refinance_purpose')
            ->add('agreement_one')
            ->add('agreement_two')
            ->add('agreement_three')
            ->add('comments')
            ->add('completed')
            ->add('completed_date')
            ->add('lock_status')
            ->add('last_step_completed')
            ->add('status')
            ->add('status_date')
            ->add('deleted')
            ->add('created')
            ->add('modified')
            ->add('los_modified')
            ->add('sent_to_los')
            ->add('source')
            ->add('no_property_location')
            ->add('is_lennar_home')
            ->add('loan_purpose')
            ->add('need_to_sell')
            ->add('rent_own_status')
            ->add('are_joint_borrowers')
            ->add('down_payment_amount')
            ->add('down_payment_source')
            ->add('lennar_community_name')
            ->add('lennar_builder_name')
            ->add('has_communities')
            ->add('property_location')
            ->add('user')
            ->add('admin_user')
            ->add('site')
            ->add('borrower')
            ->add('expense_housing')
            ->add('loan_officer')
            ->add('message_thread')
            ->add('milestone_group')
            ->add('milestone')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\EagleBundle\Entity\LoanApplicationFull'
        ));
    }

    public function getName()
    {
        return 'sudoux_eaglebundle_loanapplicationfulltype';
    }
}
