<?php

namespace Sudoux\MortgageBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class IncomeOtherType
 * @package Sudoux\MortgageBundle\Form
 * @author Dan Alvare
 */
class IncomeOtherType extends AbstractType
{
    /**
     * @var int|null
     */
	private $applicationId;

    /**
     * @param null $applicationId
     */
	public function __construct($applicationId = null)
	{
		if(isset($applicationId)) {
			$this->applicationId = $applicationId;
		}
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$applicationId = $this->applicationId;
    	
        $builder
            ->add('description')
            ->add('income', 'money', array(
            	'currency' => 'USD',
            	'required' => true,
            	'precision' => 2,	
            	'grouping' => true,	
            ))
            ->add('borrower', 'entity', array(
            	'label' => 'Borrower/Co-Borrower',
            	'class' => 'SudouxMortgageBundle:Borrower',
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
            'data_class' => 'Sudoux\MortgageBundle\Entity\IncomeOther'
        ));
    }

    public function getName()
    {
        return 'sudoux_mortgagebundle_incomeothertype';
    }
}
