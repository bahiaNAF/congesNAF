<?php

namespace GCNAF\NAFBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CraType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('salarie')
            ->add('tache')
            ->add('date')
            ->add('duree')
            ->add('remarque')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GCNAF\NAFBundle\Entity\Cra'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gcnaf_nafbundle_cra';
    }
}
