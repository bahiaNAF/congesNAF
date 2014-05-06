<?php

namespace GCNAF\NAFBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use GCNAF\NAFBundle\Entity\Profil;
use GCNAF\NAFBundle\Entity\Ressource;
  
class RessourceFormController extends Controller
{  
	// 1 modifier salarie
	 public function editerAction($id)
	{	
		$message='';
		$civilites=array();
		$em = $this->container->get('doctrine')->getManager();
		$query=$this->getDoctrine()->getRepository('GCNAFNAFBundle:Profil')->findAll();
		foreach ($query as $ref) {
		$civilites[$ref->getIdProf()]=$ref->getNomProf();
		}		    
		$jour = $em->find('GCNAFNAFBundle:Ressource', $id);   
	    if (!$jour)	{ $message='Aucun Salarie trouve'; }		
		$formBuilder = $this->createFormBuilder($jour);
		$formBuilder
 		    ->add('cin','text', array('label' =>    'CIN'))
            ->add('nom','text', array('label' =>    'Nom'))
            ->add('prenom','text', array('label' => 'Prenom'))
            ->add('login','text', array('label' =>  'Login'))
            ->add('pwd','text', array('label' =>'Password',))
            ->add('dateEmb','date', array('label' =>'Date d embauche',
			'format' => 'yyyy-MM-dd','years' => range(1987 ,2037),
			))
            ->add('dateSor','date', array('label' =>'Date Sortie',
			'format' => 'yyyy-MM-dd','years' => range(1987 ,2037),
			))															
            ->add('mail','email', array('label' =>   'Email'))						
			->add('idProf', 'choice', array('choices' => $civilites, 'label' => 'Profil'));					
			
		$form = $formBuilder->getForm();		
		$request = $this->container->get('request');
  	   if ($request->getMethod() == 'POST') 
	  {
    	$form->bind($request);
		if ($form->isValid()) 
		{
		
		$form_user = $form->getData();
		$dateD = $form_user->getDateEmb();		  
		$dateF = $form_user->getDateSor();	
		if ($dateD < $dateF) 
			{	  
				$em->persist($jour);
				$em->flush();
				return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_salaries'));	
			}
		else
			{ $message='La date Embauche doit etre inferieure a la date de sortie';}	
 		}
	   }
		return $this->container->get('templating')->renderResponse(
	   'GCNAFNAFBundle:Ressource:editerRessource.html.twig',
		array(
		'form' => $form->createView(),
		'message' => $message,
		));	
	}// fin modification
	
	//2 fct ajouter jour
	public function ajouterAction()
    {	
		$message='';
		$civilites=array();
		$em = $this->container->get('doctrine')->getManager();
		$query=$this->getDoctrine()->getRepository('GCNAFNAFBundle:Profil')->findAll();
		foreach ($query as $ref) {
		$civilites[$ref->getIdProf()]=$ref->getNomProf();
		}		        
  	    $jour = new Ressource();	
		$formBuilder = $this->createFormBuilder($jour);
		$formBuilder			
		    ->add('cin','text', array('label' =>    'CIN'))
            ->add('nom','text', array('label' =>    'Nom'))
            ->add('prenom','text', array('label' => 'Prenom'))
            ->add('login','text', array('label' =>  'Login'))
            ->add('pwd','password', array('label' =>'Password'))
            ->add('dateEmb','date', array('label' =>'Date d embauche',
			'format' => 'yyyy-MM-dd','years' => range(1987 ,2037),
			))
            ->add('dateSor','date', array('label' =>'Date Sortie',
			'format' => 'yyyy-MM-dd','years' => range(1987 ,2037),
			))															
            ->add('mail','email', array('label' =>   'Email'))						
			->add('idProf', 'choice', array('choices' => $civilites, 'label' => 'Profil','empty_value' => 'Choisissez un profil'));			
		
		$form = $formBuilder->getForm();		
		$request = $this->container->get('request');
  	   if ($request->getMethod() == 'POST') 
	  {
    	$form->bind($request);
		if ($form->isValid()) 
		{
		$form_user = $form->getData();
		$dateD = $form_user->getDateEmb();		  
		$dateF = $form_user->getDateSor();	
		if ($dateD < $dateF) 
			{	  
				$em->persist($jour);
				$em->flush();
				return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_salaries'));	
			}
		else
			{ $message='La date Embauche doit etre inferieure a la date de sortie';}	
 		}
	   }
		return $this->container->get('templating')->renderResponse(
	   'GCNAFNAFBundle:Ressource:ajouterRessource.html.twig',
		array(
		'form' => $form->createView(),
		'message' => $message,
		));	
	}// fin l'jout   	  	
  	
}//end classe
