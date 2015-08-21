<?php

namespace Sudoux\EagleBundle\Form;

use Doctrine\ORM\EntityRepository;
use Sudoux\Cms\TaxonomyBundle\Entity\Vocabulary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EagleLoanDocumentType extends AbstractType
{
    /**
     * @var Vocabulary
     */
    protected $vocab;

    /**
     * @param Vocabulary $vocab
     */
    public function __construct(Vocabulary $vocab)
    {
        $this->vocab = $vocab;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $vocab = $this->vocab;
        $builder
            ->add('name', 'text', array(
                'label' => 'Name',
                'required' => true,
                'attr' => array('class' => 'span3'),
            ))
            ->add('file_field', 'file', array(
                'label' => 'File',
                'required' => true,
            ))
            ->add('type', 'entity', array(
                'label' => 'Document Category',
                'class' => 'Sudoux\Cms\TaxonomyBundle\Entity\Taxonomy',
                'property' => 'term',
                'empty_value' => 'Select a Category',
                'required' => false,
                'attr' => array('class' => 'span3'),
                'query_builder' => function(EntityRepository $er) use ($vocab) {
                    return $er->findTermsByVocabQueryBuilder($vocab);
                },
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sudoux\EagleBundle\Entity\EagleLoanDocument'
        ));
    }

    public function getName()
    {
        return 'sudoux_eaglebundle_EagleLoanDocumentType';
    }
}
