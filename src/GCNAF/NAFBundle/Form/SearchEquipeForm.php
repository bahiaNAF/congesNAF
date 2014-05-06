<?php

namespace GCNAF\NAFBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchEquipeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
		$builder
			 ->add('dateD','date', array('format' => 'yyyy-MM-dd','years' => range(2014 ,2030) ));		
    }    
    public function getName()
    {
	   return 'searchequipeform';
    }    
}
