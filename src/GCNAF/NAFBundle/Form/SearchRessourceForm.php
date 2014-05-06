<?php

namespace GCNAF\NAFBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchRessourceForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {               
		$builder
			->add('nom', 'text', array('label' => 'Recherche par Nom'));
    }
    
    public function getName()
    {
	   return 'searchressourceform';
    }    
}
