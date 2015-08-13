<?php

namespace Sudoux\MortgageBundle\Form;

use Doctrine\ORM\EntityRepository;
use Sudoux\Cms\LocationBundle\Form\LocationType;

use Sudoux\Cms\SiteBundle\Entity\Site;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class QuoteType
 * @package Sudoux\MortgageBundle\Form
 * @author Dan Alvare
 */
class QuoteType extends AbstractType
{

	protected $site;

	public function __construct(Site $site)
	{
		$this->site = $site;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$site = $this->site;

        $builder
            ->add('first_name', 'text', array(
            	'required' => false,	
            	'attr' => array('class' => 'span3'),
            ))
            ->add('last_name', 'text', array(
            	'required' => false,		
            	'attr' => array('class' => 'span3'),
            ))
            ->add('email', 'email', array(
            	'required' => true,		
            	'attr' => array('class' => 'span3'),
            ))
            ->add('home_phone', 'text', array(
            	'required' => false,
            	'label' => 'Phone',
            	'attr' => array('class' => 'span3 phonenumber'),
			))
            ->add('subject', 'choice', array(
            	'required' => false,
            	'choices' => array(
            		'Refinance Loan' => 'Refinance',
            		'Purchase Loan' => 'Purchase',
            	),
            	'attr' => array('class' => 'subject span3'),
            	'empty_value' => 'Select Loan Type',	
            	'label' => 'Loan Type'
            ))
            ->add('message', 'textarea', array(
            	'label' => 'Loan Details',
            	'required' => false,
            	'attr' => array(
            		'class' => 'message span3',
            		'placeholder' => 'Please enter loan amount and details'
            	),	
            ))
			//->add('location', new LocationType())
			->add('spam_field', 'text', array(
            	'required' => false,
            	'attr' => array('class' => 'hidden')	
            ))
			->add('referral_source', 'entity', array(
				'class' => 'Sudoux\Cms\FormBundle\Entity\ReferralSource',
				'property' => 'name',
				'required' => false,
				'multiple' => true,
				'expanded' => true,
				'attr' => array('class' => 'checkbox-group referral-sources'),
				'label' => 'How did you hear about us?',
				'query_builder' => function (EntityRepository $er) use ($site) {
					return $er->findAllActiveBySiteQuery($site);
				}
			))
			->add('referral_source_desc', 'textarea', array(
				'required' => false,
				'label' => 'Referral source description',
				'attr' => array('class' => 'referral-source-desc', 'placeholder' => 'Please explain'),
			))
		; 
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\Cms\FormBundle\Entity\Lead'
        ));
    }

    public function getName()
    {
        return 'contact_form';
    }
}
