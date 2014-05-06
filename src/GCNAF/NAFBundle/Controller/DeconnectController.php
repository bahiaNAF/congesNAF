<?php

namespace GCNAF\NAFBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use GCNAF\NAFBundle\Entity\Ressource;
use GCNAF\NAFBundle\Form\LoginForm;

use Symfony\Component\HttpFoundation\Session\Session;

class DeconnectController extends ContainerAware
{			 
	public function indexAction()
    {	  	  	  	  	  	  
	  $request = $this->container->get('request');	  
	  $session = $request->getSession();
	  $session->clear(); //supprimer tt les sessions
	  
	  return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_accueil'));		  			
	}
	 //end funct goout
}
?>
