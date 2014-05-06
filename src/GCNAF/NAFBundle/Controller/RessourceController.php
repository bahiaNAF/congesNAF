<?php

namespace GCNAF\NAFBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller; 

use GCNAF\NAFBundle\Entity\Ressource;
use GCNAF\NAFBundle\Entity\Profil;
use GCNAF\NAFBundle\Form\RessourceForm;
use GCNAF\NAFBundle\Form\SearchRessourceForm;

class RessourceController extends ContainerAware

{	// 1 affichage par pagination 
	public function listeRessourcePaginationAction($page)
    {
	 $form = $this->container->get('form.factory')->create(new SearchRessourceForm());
	 $em = $this->container->get('doctrine')->getManager();	 
	 // requete 1
	 $qb = $em->createQueryBuilder();
	 $qb->select('r.id,r.cin,r.nom,r.prenom,r.login,r.pwd,r.dateEmb,r.dateSor,r.mail,r.idProf,p.idProf,p.nomProf')
		->from('GCNAFNAFBundle:Ressource', 'r')
		->innerJoin('GCNAFNAFBundle:Profil ','p')
		->where('p.idProf = r.idProf')				
		->orderBy('r.nom', 'ASC');			
	 $query = $qb->getQuery();               
	 $total = $query->getResult();	 	
     $total_jours    = count($total);
     $jours_per_page = 10;
     $last_page      = ceil($total_jours / $jours_per_page);	 
     $previous_page     = $page > 1 ? $page - 1 : 1;
     $next_page         = $page < $last_page ? $page + 1 : $last_page;   	 	 
	 $qbnew = $em->createQueryBuilder();
	 $qbnew->select('r.id,r.cin,r.nom,r.prenom,r.login,r.pwd,r.dateEmb,r.dateSor,r.mail,r.idProf,p.idProf,p.nomProf')
		   ->from('GCNAFNAFBundle:Ressource', 'r')
           ->innerJoin('GCNAFNAFBundle:Profil ','p')
		   ->where('p.idProf = r.idProf')				
		   ->setFirstResult(($page * $jours_per_page) - $jours_per_page)
		   ->setMaxResults($jours_per_page)
		   ->orderBy('r.nom', 'ASC');
	 $querynew = $qbnew->getQuery();               
	 $total_fin = $querynew->getResult();		 
	 return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Ressource:listeRessourcePagination.html.twig', array(
            'entities' => $total_fin,
            'last_page' => $last_page,
            'previous_page' => $previous_page,
            'current_page' => $page,
            'next_page' => $next_page,
            'total_articles' => $total_jours,
			'form' => $form->createView(),
        ));	   
    }	
	
	//2 supprition d'un salarie
	public function supprimerAction($id)
	{
	  $em = $this->container->get('doctrine')->getManager();
	  $acteur = $em->find('GCNAFNAFBundle:Ressource', $id);			
	  if (!$acteur) 
	  {
		throw new NotFoundHttpException("Ressource non trouve");
	  }			
	  $em->remove($acteur);
	  $em->flush();        
	  return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_salaries'));
	}	
	
    // 3 recherche  par nom 		
	public function rechercherAction()
     {
	  $message='Resultats de votre recherche';
	  $user = new Ressource();
	  $form = $this->container->get('form.factory')->create(new SearchRessourceForm(), $user);
	  $request = $this->container->get('request');	
	  if ($request->getMethod() == 'POST') 
	  {
		$form->bind($request);	
		if ($form->isValid()) 
		{
		$em = $this->container->get('doctrine')->getManager();		  
		$form_user = $form->getData();
		$mynom=$form_user->getNom();
		//requete				
		$qb = $em->createQueryBuilder();
		$qb->select('r.id,r.cin,r.nom,r.prenom,r.login,r.pwd,r.dateEmb,r.dateSor,r.mail,r.idProf,p.idProf,p.nomProf')
		   ->from('GCNAFNAFBundle:Ressource', 'r')
           ->innerJoin('GCNAFNAFBundle:Profil ','p')
		   ->where('p.idProf = r.idProf')				
		   ->andWhere('r.nom = :mynom')
		   ->orderBy('r.nom', 'ASC');			
		$qb->setParameter('mynom', $mynom);					
		$query = $qb->getQuery();               
		$total = $query->getResult();					  		  				  		  		  
		if ($total)
		{
			return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Ressource:searchresource.html.twig',
            array('form' => $form->createView(),'message' =>$message,'total' =>$total
             ,));
		}
	  }	
	}	
   return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Ressource:searchresource.html.twig',
   array('form' => $form->createView(),'message' => $message,'total' =>$total
   ,));			
  }					
	
}//end classe
