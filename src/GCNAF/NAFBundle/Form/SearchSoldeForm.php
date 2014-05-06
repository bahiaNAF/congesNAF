<?php

namespace GCNAF\NAFBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchSoldeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
		$builder
			 ->add('annee','text', array('label' =>'Recherche par annee',));
    }    
    public function getName()
    {
	   return 'searchsoldeform';
    }    
}
