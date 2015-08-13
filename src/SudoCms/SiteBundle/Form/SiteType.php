<?php
/**
 * Summary
 *
 * Description
 *
 * @author Dan Alvare
 */


namespace SudoCms\SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sudoux\Cms\SiteBundle\Entity\Domain;
use Sudoux\Cms\SiteBundle\Form\DomainType;
use Sudoux\Cms\SiteBundle\Entity\Site;
use Doctrine\ORM\EntityRepository;
use SudoCms\SiteBundle\Form\SettingsType;

/**
 * Class SiteType extends AbstractType - Summary
 *
 * Description of this class
 *
 * @package SudoCms\SiteBundle\Form
 * @author Dan Alvare
 */
class SiteType extends AbstractType
{
    /**
     * @type \Sudoux\Cms\SiteBundle\Entity\Domain
     * @author Dan Alvare
     */
	private $primaryDomain;


    /**
     * @var \Sudoux\Cms\SiteBundle\Entity\Site
     * @author Dan Alvare
     */
	protected $site;


    /**
     * @param \Sudoux\Cms\SiteBundle\Entity\Site $site
     * @param \Sudoux\Cms\SiteBundle\Entity\Domain $primaryDomain
     */
    public function __construct(Site $site, Domain $primaryDomain = null)
	{

		$this->site = $site;
		$this->primaryDomain = $primaryDomain;
	}


    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @author Dan Alvare
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    	$site = $this->site;

    	$builder
	    	->add('name')
	    	->add('primary_domain', new DomainType())
	    	->add('timezone', 'timezone', array(
	    			'empty_value' => 'Select your timezone',
	    			'preferred_choices' => array(
	    					'America/New_York',
	    					'America/Chicago',
	    					'America/Denver',
	    					'America/Phoenix',
	    					'America/Los_Angeles',
	    					'America/Adak',
	    					'Pacific/Honolulu',
	    					'America/Anchorage'
	    			)
	    	))
	    	->add('configure', 'checkbox', array(
	    		'label' => 'Configure site now?',
	    		'required' => false,
	    	));
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\Cms\SiteBundle\Entity\Site'
        ));
    }

    public function getName()
    {
        return 'sudoux_mortgagebundle_sitetype';
    }
}
