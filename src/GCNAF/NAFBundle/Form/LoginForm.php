<?php

namespace GCNAF\NAFBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder
            ->add('login', 'text', array('label' =>   'Utilisateur  '))
            ->add('pwd', 'password', array('label' => 'Mot de passe '));
    }    
    public function getName()
    {
        return 'loginform';
    }    
}
