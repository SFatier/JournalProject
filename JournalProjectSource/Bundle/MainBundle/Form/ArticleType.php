<?php

namespace Tribuca\Bundle\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                    'label' => 'Titre',
                    'max_length' => 255,
                    'required' => false, 
            ))
            ->add('page', 'integer',  array(
                    'label' => 'Page',
                    'max_length' => 3, 
                    'required' => false, 
            ))
            ->add('author', 'text', array(
                    'label' => 'Auteur',
                    'max_length' => 255,
                    'required' => false, 
            ))
            ->add('content', 'textarea', array(
                    'label' => 'Article',
                    'max_length' => 4294967295,
                    'required' => false, 
            ))
            // ->add('beginning', 'text', array(
            //         'label' => 'Début d\'article',
            //         'max_length' => 1024,                                      
            //         'required' => false,   
            // ))
            // ->add('end', 'text', array(
            //         'label' => 'Fin d\'article',
            //         'max_length' => 1024,                                      
            //         'required' => false, 
            // ))
            ->add('newspaper')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tribuca\Bundle\MainBundle\Entity\Article'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tribuca_bundle_mainbundle_article';
    }
}
