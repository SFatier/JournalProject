<?php

namespace Tribuca\Bundle\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsPaperType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Intitulé',
                'max_length' => 255,
                'required' => false,
            ))
            ->add('publicationDate', 'date', array(
                                    'widget'    => 'single_text',
                                    'label'     => 'Date',
                                    'format'    => 'dd-MM-yyyy',
                                    'invalid_message' => "Cette date n'est pas valide",
                                    'required' => false,
            ))
            ->add('number', 'text', array(
                                    'label' => 'Numéro',
                                    'max_length' => 16,
                                    'required' => false,
            ))
            ->add('file', 'file', array(
                                    'label' => 'Fichier',
                                    'required' => false,
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tribuca\Bundle\MainBundle\Entity\NewsPaper'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tribuca_bundle_mainbundle_newspaper';
    }
}

?>
