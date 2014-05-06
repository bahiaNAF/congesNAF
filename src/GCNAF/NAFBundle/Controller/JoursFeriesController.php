<?php

namespace GCNAF\NAFBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller; 

use GCNAF\NAFBundle\Entity\JoursFeries;
use GCNAF\NAFBundle\Entity\ListeJours;

use GCNAF\NAFBundle\Form\SearchJoursForm;

class JoursFeriesController extends ContainerAware
{ 
	// 1 affichage avec pagination		
	public function listerPaginationAction($page)
    {
	 $form = $this->container->get('form.factory')->create(new SearchJoursForm());
	 $em = $this->container->get('doctrine')->getManager();	 	
	// requete 1			
 	 $qb = $em->createQueryBuilder();
	 $qb->select('j.refJf,j.idProjet,j.date,l.id,l.libelle')
		->from('GCNAFNAFBundle:JoursFeries', 'j')
		->innerJoin('GCNAFNAFBundle:ListeJours ','l')		
		->where('l.id = j.idProjet')
		->orderBy('l.libelle', 'ASC');			
	 $query = $qb->getQuery();               
	 $total = $query->getResult();
	 
     $total_jours    = count($total);
     $jours_per_page = 10;
     $last_page      = ceil($total_jours / $jours_per_page);	 
     $previous_page     = $page > 1 ? $page - 1 : 1;
     $next_page         = $page < $last_page ? $page + 1 : $last_page;   	 	 
	 // requete 2
	 $qbnew = $em->createQueryBuilder();
	 $qbnew->select('j.refJf,j.idProjet,j.date,l.id,l.libelle')
		   ->from('GCNAFNAFBundle:JoursFeries', 'j')
	   	   ->innerJoin('GCNAFNAFBundle:ListeJours ','l')
    	   ->where('l.id = j.idProjet')
		   ->setFirstResult(($page * $jours_per_page) - $jours_per_page)
		   ->setMaxResults($jours_per_page)
  		   ->orderBy('l.libelle', 'ASC');	
	 $querynew = $qbnew->getQuery();               
	 $total_fin = $querynew->getResult();		 
	 
	 return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:JoursFeries:pagination.html.twig', array(
            'entities' => $total_fin,
            'last_page' => $last_page,
            'previous_page' => $previous_page,
            'current_page' => $page,
            'next_page' => $next_page,
            'total_articles' => $total_jours,
			'form' => $form->createView(),
        ));	   
    }
	
	// 2 recherche par année
	public function searchByYearAction()
    {
      $erreur='Resultats de votre recherche';
	  $user = new JoursFeries();
	  $form = $this->container->get('form.factory')->create(new SearchJoursForm(), $user);
	  $request = $this->container->get('request');	
	  if ($request->getMethod() == 'POST') 
	  {
		$form->bind($request);	
		if ($form->isValid()) 
		{
		$em = $this->container->get('doctrine')->getManager();		  
		$form_user = $form->getData();
		$myear=$form_user->getDate();
		//requete				
		$qb = $em->createQueryBuilder();
		$qb->select('j.refJf,j.idProjet,j.date,l.id,l.libelle')
		   ->from('GCNAFNAFBundle:JoursFeries', 'j')
	   	   ->innerJoin('GCNAFNAFBundle:ListeJours ','l')
       	   ->where('l.id = j.idProjet')
		   ->andWhere('YEAR(j.date) = :myear')
		   ->orderBy('j.date', 'ASC');			
		$qb->setParameter('myear', $myear);					
		$query = $qb->getQuery();               
		$total = $query->getResult();					  		  				  		  		  
		if ($total)
		{
			return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:JoursFeries:searchlistejours.html.twig',
            array('form' => $form->createView(),'erreur' =>$erreur,'total' =>$total
             ,));
		}
	  }	
	}	
   return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:JoursFeries:searchlistejours.html.twig',
   array('form' => $form->createView(),'erreur' => $erreur,'total' =>$total
   ,));
}//end searche
		
	// 3 la fct supprimer
	public function supprimerAction($id)
	{
	  $em = $this->container->get('doctrine')->getManager();
	  $acteur = $em->find('GCNAFNAFBundle:JoursFeries', $id);			
	  if (!$acteur) 
	  {
		throw new NotFoundHttpException("Jour ferie non trouve");
	  }			
	  $em->remove($acteur);
	  $em->flush();        
	  return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_jours'));  
	}
		
}//end classe
