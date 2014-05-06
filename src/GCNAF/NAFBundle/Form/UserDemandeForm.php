<?php

namespace GCNAF\NAFBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use GCNAF\NAFBundle\Entity\EtatConge;

class UserDemandeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {  	
		$builder
		      ->add('nomEtat', 'choice', array('choices' => array('a' => 'en attente', 'r' => 'annulee', 'v' => 'validee' ),));				
    }   
	 
    public function getName()
    {
	   return 'userdemandeform';
    }    
}
