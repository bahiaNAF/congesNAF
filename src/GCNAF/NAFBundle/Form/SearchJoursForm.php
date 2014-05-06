<?php

namespace GCNAF\NAFBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchJoursForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {               
		$builder
			 ->add('date','text', array('label' =>'Recherche par annee',
			))
	        ;
    }
  
    public function getName()
    {
	   return 'searchjoursform';
    }    
}
