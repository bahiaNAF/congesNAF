<?php

namespace GCNAF\NAFBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use GCNAF\NAFBundle\Entity\Ressource;
use GCNAF\NAFBundle\Form\LoginForm;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends ContainerAware
{			 
	//l'authentification
	public function loginAction()
    {	  	  	  	  	  
	  $message='';
	  $user = new Ressource();
	  $form = $this->container->get('form.factory')->create(new LoginForm(), $user);	
	  $request = $this->container->get('request');
	  
	  $session = $request->getSession();
	  
	  if ($request->getMethod() == 'POST') 
	  {
		$form->bind($request);
	
		if ($form->isValid()) 
		{
		  $em = $this->container->get('doctrine')->getManager();		  
		  $repository = $em->getRepository('GCNAFNAFBundle:Ressource');
		  $form_user = $form->getData();
		  $mon_user = $repository->findOneByLogin($form_user->getLogin());		  
		  
		  if(! $repository->findOneByLogin($form_user->getLogin()))
return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Default:index.html.twig',array('form' => $form->createView(),'message' => 'Votre Login est incorrect! '
,));			
		else
		{
			if($mon_user->getPwd()==$form_user->getPwd())
			{
				$profil=$mon_user->getIdProf();
				$nom   =$mon_user->getNom();
				$pre   =$mon_user->getPrenom();
				$iduser=$mon_user->getId();		    			
			
				if($profil== 'admin'){				
					$session->set('nomAdmin', $nom);
					$session->set('preAdmin', $pre);
					$NameOfAd    = $session->get('nomAdmin');
					$LasteNameAd = $session->get('preAdmin');
											
					$session->set('cleuser', $iduser);
					$cle = $session->get('cleuser');							
					return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_demandes_conges'));												
				}
				
				if($profil== 'chef'){
					$session->set('cleuser', $iduser);
					$cle = $session->get('cleuser');
					return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_chef_equipe_index',array('id'=> $cle)));				
				}
				
				if($profil== 'user'){
					$session->set('cleuser', $iduser);
					$cle = $session->get('cleuser');								
					return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_user_simple_demandes',array('id'=> $cle)));	
				}

			}// end if 
		else
			return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Default:index.html.twig',array('form' => $form->createView()
			,'message' => 'Le Mot de passe est incorrect!',));		
			$session->clear();			
		}//end else
	  }	//end if 	
    }	//end if
	
	return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Default:index.html.twig',array('form' => $form->createView(),'message' => $message
	,));
	
	}//end funct login
}
?>
