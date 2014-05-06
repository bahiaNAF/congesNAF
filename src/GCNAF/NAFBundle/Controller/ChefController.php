<?php

namespace GCNAF\NAFBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Bundle\SwiftmailerBundle;
use \DateTime;

use GCNAF\NAFBundle\Entity\ProjetChef;
use GCNAF\NAFBundle\Entity\EquipeChef;
use GCNAF\NAFBundle\Entity\Ressource;
use GCNAF\NAFBundle\Entity\Demande;
use GCNAF\NAFBundle\Entity\EtatConge;
use GCNAF\NAFBundle\Entity\TypesConges;
use GCNAF\NAFBundle\Entity\Profil;
use GCNAF\NAFBundle\Entity\CompteurSolde;
use GCNAF\NAFBundle\Entity\JoursFeries;
use GCNAF\NAFBundle\Entity\ListeJours;

use GCNAF\NAFBundle\Form\SearchEquipeForm;
use GCNAF\NAFBundle\Form\SearchSoldeForm;
use GCNAF\NAFBundle\Form\UserDemandeForm;
use Symfony\Component\HttpFoundation\Session\Session;

class ChefController extends Controller
{  		
	
	public function indexAction($id)
    {	
	$em = $this->container->get('doctrine')->getManager();				 		 				
	$ressource = $em->find('GCNAFNAFBundle:Ressource', $id); 
	$nomChef = $ressource->getNom();
	$preChef = $ressource->getPrenom();	
	
	$EquipeChef = new EquipeChef();
	$form = $this->container->get('form.factory')->create(new SearchEquipeForm(), $EquipeChef);	
			    
	$myq = $em->createQueryBuilder();
	$myq->select('e.ref,e.dateF,e.projet,e.idSalarie,e.idChef,e.dateD,r.id,r.mail,r.nom,r.prenom,p.id,p.projet')
	->from('GCNAFNAFBundle:EquipeChef', 'e')
	->from('GCNAFNAFBundle:Ressource', 'r')
	->from('GCNAFNAFBundle:ProjetChef', 'p')	
	->where('e.projet = p.id')	
	->andWhere('e.idSalarie = r.id')
	->andWhere('e.idChef = :nomChef')
	->orderBy('r.nom', 'ASC');	
	$myq->setParameter('nomChef', $nomChef);														
	$resu = $myq->getQuery();               
	$Tabchef = $resu->getResult();	 
	
	return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Chef:EquipeUsers.html.twig', array(           
            'total' => $Tabchef,
			'nom' => $nomChef,
			'prenom' => $preChef,
			'id' => $id,					
			'form' => $form->createView(),
        ));	   
	}	
	
 public function searchEquipeAction()
    {
	$em = $this->container->get('doctrine')->getManager();
	  
	$EquipeChef = new EquipeChef();
	$form = $this->container->get('form.factory')->create(new SearchEquipeForm(), $EquipeChef);		
	  
	$request = $this->container->get('request');	  	  
	$session = $request->getSession();
	$chefId = $session->get('cleuser');		 
	$ressource = $em->find('GCNAFNAFBundle:Ressource', $chefId); 
	$nom =$ressource->getNom();
	$pren=$ressource->getPrenom();
	 
  if ($request->getMethod() == 'POST') 
  {
	$form->bind($request);
	if ($form->isValid()) 
	{   	
	$form_user  = $form->getData();
	$dateappar  = $form_user->getDateD();	//date de recherche	
	 
	$myq = $em->createQueryBuilder();
	$myq->select('e.ref,e.dateF,e.projet,e.idSalarie,e.idChef,e.dateD,r.id,r.mail,r.nom,r.prenom,p.id,p.projet')
	->from('GCNAFNAFBundle:EquipeChef', 'e')
	->from('GCNAFNAFBundle:Ressource', 'r')
	->from('GCNAFNAFBundle:ProjetChef', 'p')	
	->where('e.projet = p.id')	
	->andWhere('e.idSalarie = r.id')
	->andWhere('e.idChef = :nomChef')
	->andWhere('e.dateD >= :dateappar')
	->orderBy('r.nom', 'ASC');	
	$myq->setParameter('nomChef', $nom);
	$myq->setParameter('dateappar', $dateappar);
	$resu = $myq->getQuery();               
	$total = $resu->getResult();	 								 
	
	  if($total){			
		return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Chef:RechercheEquipe.html.twig', array(           
		'total' => $total,
		'nom' => $nom,
		'prenom' => $pren,
		'id' => $chefId,		
		'form' => $form->createView(),
		 ));  
	   }// fin total		
	}
  }	
  return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Chef:RechercheEquipe.html.twig', array(           
		'total' => $total,
		'nom' => $nom,
		'prenom' => $pren,
		'id' => $chefId,		
		'form' => $form->createView(),
		 ));
  }	
  public function DemandesUsersAction($id)
    {			    
  		 // $id de l'utilisateur
		 $em = $this->container->get('doctrine')->getManager();		 				 		 				
		 $request = $this->container->get('request');	  	  
		 $session = $request->getSession();
		 $chefId = $session->get('cleuser');		 
		 $ressource = $em->find('GCNAFNAFBundle:Ressource', $chefId); 
		 $nomChef =$ressource->getNom();
		 $preChef =$ressource->getPrenom();		 			 		 
	  	 
		 $EtatsCng = new EtatConge();
	  	 $form = $this->container->get('form.factory')->create(new UserDemandeForm(), $EtatsCng);
		 
		 $salaries = $em->find('GCNAFNAFBundle:Ressource', $id); 
		 $nomuser =$salaries->getNom();
		 $preuser =$salaries->getPrenom();
		 $session->set('IDofUser', $id);
		 	
		 $qb = $em->createQueryBuilder();
		 $qb->select('d.idDem,d.dateD,d.dateF,d.dateEnrg,d.nbrJr,r.id,e.idEtat,e.nomEtat,t.idTypes,t.nomCng')
			->from('GCNAFNAFBundle:Demande', 'd')
			->from('GCNAFNAFBundle:Ressource', 'r')
			->from('GCNAFNAFBundle:EtatConge', 'e')
			->from('GCNAFNAFBundle:TypesConges', 't')				
			->where('r.id = :mycle')				
			->andWhere('r.id = d.idUser ')								
			->andWhere('e.idEtat = d.idEtat ')				
			->andWhere('t.idTypes = d.idCng ')										
			->orderBy('d.dateEnrg', 'ASC');	
		 $qb->setParameter('mycle', $id);												
		 $query = $qb->getQuery();               
		 $total = $query->getResult();			  						  					
				
		 return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Chef:ListeDemandesUsers.html.twig', array(           
            'total' => $total,
			'nom' => $nomChef,
			'prenom' => $preChef,
			'nomuser' => $nomuser,
			'preuser' => $preuser,
			'id' => $chefId,
			'form' => $form->createView(),		
        ));	   
	}							
	
	public function DemandeUserParEtatAction()
    {
	  $em = $this->container->get('doctrine')->getManager();
	  $EtatsCng = new EtatConge();
  	  $form = $this->container->get('form.factory')->create(new UserDemandeForm(), $EtatsCng);	
	
	  $request = $this->container->get('request');	  	  
	  $session = $request->getSession();
	  
 	  $chefId = $session->get('cleuser');		 
	  $ressource = $em->find('GCNAFNAFBundle:Ressource', $chefId); 
	  $nomChef =$ressource->getNom();
	  $preChef =$ressource->getPrenom();		 

 	  $myUser = $session->get('IDofUser');		 	  			 		 
	  $salaries = $em->find('GCNAFNAFBundle:Ressource', $myUser); 
	  $nomuser =$salaries->getNom();
	  $preuser =$salaries->getPrenom();
		 
	  if ($request->getMethod() == 'POST') 
	  {
		$form->bind($request);
	
		if ($form->isValid()) 
		{   	
		 $form_user  = $form->getData();
		 $idetatsName  = $form_user->getNomEtat();		 
				  		 		
		 $qb = $em->createQueryBuilder();
		 $qb->select('d.idDem,d.dateD,d.dateF,d.dateEnrg,d.nbrJr,r.id,e.idEtat,e.nomEtat,t.idTypes,t.nomCng')
			->from('GCNAFNAFBundle:Demande', 'd')
			->from('GCNAFNAFBundle:Ressource', 'r')
			->from('GCNAFNAFBundle:EtatConge', 'e')
			->from('GCNAFNAFBundle:TypesConges', 't')				
			->where('r.id = :mycle')				
			->andWhere('d.idEtat = :idEtatsName')								
			->andWhere('r.id = d.idUser ')								
			->andWhere('e.idEtat = d.idEtat ')				
			->andWhere('t.idTypes = d.idCng ')										
			->orderBy('d.dateEnrg', 'ASC');	
		 $qb->setParameter('mycle', $myUser);												
		 $qb->setParameter('idEtatsName', $idetatsName);												
		 $query = $qb->getQuery();               
		 $total = $query->getResult();			  					 
		
		  if($total){			
		 	return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Chef:DemandeUserParEtat.html.twig', array(           
            'total' => $total,
			'nom' => $nomChef,
			'prenom' => $preChef,
			'nomuser' => $nomuser,
			'preuser' => $preuser,
			'id' => $chefId,
			'form' => $form->createView(),		
       		 ));  
		   }// fin total		
		}
	  }	
	  return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Chef:DemandeUserParEtat.html.twig', array(           
            'total' => $total,
			'nom' => $nomChef,
			'prenom' => $preChef,
			'nomuser' => $nomuser,
			'preuser' => $preuser,
			'id' => $chefId,
			'form' => $form->createView(),		
        ));
	}
		
	public function AccorderDemandeAction($id)
    {			    
		//	$id = id de la demande
		$em = $this->container->get('doctrine')->getManager();											
		$demande = $em->find('GCNAFNAFBundle:Demande', $id);
		$etatValidationDem = $demande->getIdEtat();
		 						
		$request = $this->container->get('request');	  	  
		$session = $request->getSession();
		
		$chefId = $session->get('cleuser');		 
		$ressource = $em->find('GCNAFNAFBundle:Ressource', $chefId); 
		$nomChef =$ressource->getNom();
		$preChef =$ressource->getPrenom();		 
		
		$myUser = $session->get('IDofUser');		 	  			 		 
		$salaries = $em->find('GCNAFNAFBundle:Ressource', $myUser); 
		$nomuser =$salaries->getNom();
		$preuser =$salaries->getPrenom();
		$mailuser =$salaries->getMail();

if( $etatValidationDem == 'a' ){ 		
	  	// modification de damande						
		$etat =$demande->setIdEtat("v");
		$valid=$demande->setValidateur($nomChef);	
		$datejour= new DateTime('now');     
		$validD=$demande->setDateVal($datejour);
		$em->persist($demande);
		$em->flush();
		
		//newsletter 
		$message = \Swift_Message::newInstance()
			->setSubject('[noreply]-Validation de votre demande de conge NAF')
			->setFrom('b.hassiki.bts@gmail.com')
			->setTo($mailuser)
			->setBody('');
		$message->setBody($this->renderView('GCNAFNAFBundle:Admin:EmailPageReponse.html.twig', array(
		'nom' => $nomuser,'prenom' => $preuser,'ValidateurNom' => $nomChef,'ValidateurPren' => $preChef ,'etatfinalecng' =>'v',)),'text/html');					
		$this->get('mailer')->send($message);
		
		$EtatsCng = new EtatConge();
	  	$form = $this->container->get('form.factory')->create(new UserDemandeForm(), $EtatsCng);		
		
		$qb = $em->createQueryBuilder();
		$qb->select('d.idDem,d.dateD,d.dateF,d.dateEnrg,d.nbrJr,r.id,e.idEtat,e.nomEtat,t.idTypes,t.nomCng')
			->from('GCNAFNAFBundle:Demande', 'd')
			->from('GCNAFNAFBundle:Ressource', 'r')
			->from('GCNAFNAFBundle:EtatConge', 'e')
			->from('GCNAFNAFBundle:TypesConges', 't')				
			->where('r.id = :mycle')				
			->andWhere('r.id = d.idUser ')								
			->andWhere('e.idEtat = d.idEtat ')				
			->andWhere('t.idTypes = d.idCng ')										
			->orderBy('d.dateEnrg', 'ASC');	
		$qb->setParameter('mycle', $myUser);												
		$query = $qb->getQuery();               
		$total = $query->getResult();			
		
		return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Chef:ListeDemandesUsers.html.twig', array(           
            'total' => $total,
			'nom' => $nomChef,
			'prenom' => $preChef,
			'nomuser' => $nomuser,
			'preuser' => $preuser,
			'id' => $chefId,
			'form' => $form->createView(),		
        ));	
}	
if( $etatValidationDem != 'a' ){  return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_Userequipe_demandes',array('id'=> $myUser)));}			

}// fin accorder 	
 
public function RefuserDemandeAction($id)
    {			    
		$em = $this->container->get('doctrine')->getManager();											
		$demande = $em->find('GCNAFNAFBundle:Demande', $id); 
		
		$etatValidationDem = $demande->getIdEtat();
		$typeconge = $demande->getIdCng();	    
	    $nbjours   = $demande->getNbrJr();  
	    $dated     = $demande->getDateD();
	    $year      = $dated->format('Y'); 
	  						
		$request = $this->container->get('request');	  	  
		$session = $request->getSession();
		
		$chefId = $session->get('cleuser');		 
		$ressource = $em->find('GCNAFNAFBundle:Ressource', $chefId); 
		$nomChef =$ressource->getNom();
		$preChef =$ressource->getPrenom();		 
		
		$myUser = $session->get('IDofUser');		 	  			 		 
		$salaries = $em->find('GCNAFNAFBundle:Ressource', $myUser); 
		$nomuser =$salaries->getNom();
		$preuser =$salaries->getPrenom();
		$mailuser =$salaries->getMail();

if( $etatValidationDem == 'a' ){ 
	if($typeconge == 1){		
		$qa = $em->createQueryBuilder();
		$qa->select('c')
		   ->from('GCNAFNAFBundle:CompteurSolde', 'c')
		   ->where('c.idUser = :cle')
		   ->andWhere('c.annee = :year');		   
		$qa->setParameter('cle', $myUser);										
		$qa->setParameter('year', $year);										
		$quera = $qa->getQuery();               
		$soldeinfo = $quera->getResult();			
		$taille2=count($soldeinfo);
		$tab3=array();
	 	 for($k=0;$k<$taille2;$k++){
			$tab3[$k]=$soldeinfo[$k];
		 }
		$com=count($tab3);	
		if($soldeinfo)
	    {		
	   	 for($l=0;$l<$com;$l++){							
				$cmpsolde = $tab3[$l]->getCptSolde();
			 	$annee    = $tab3[$l]->getAnnee();						
				$ressold  = ($cmpsolde)+($nbjours);
				$cptsolde = $tab3[$l]->setCptSolde($ressold);
				// modification de damande						
				$etat =$demande->setIdEtat("r");
				$valid=$demande->setValidateur($nomChef);	
				$datejour= new DateTime('now');     
				$validD=$demande->setDateVal($datejour);
				$em->persist($demande);
				$em->flush();				
	     }// end for
  	   }
	   if(!$soldeinfo){ return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_Userequipe_demandes',array('id'=> $myUser))); }		 		
}// fin congé payé	  	
if($typeconge != 1)  
   {	
	$etat =$demande->setIdEtat("r");
	$valid=$demande->setValidateur($nomChef);	
	$datejour= new DateTime('now');     
	$validD=$demande->setDateVal($datejour);
	$em->persist($demande);
	$em->flush();				
   }	 
		//newsletter 
		$message = \Swift_Message::newInstance()
			->setSubject('[noreply]-Validation de votre demande de conge NAF')
			->setFrom('b.hassiki.bts@gmail.com')
			->setTo($mailuser)
			->setBody('');
		$message->setBody($this->renderView('GCNAFNAFBundle:Admin:EmailPageReponse.html.twig', array(
		'nom' => $nomuser,'prenom' => $preuser,'ValidateurNom' => $nomChef,'ValidateurPren' => $preChef ,'etatfinalecng' =>'r',)),'text/html');					
		$this->get('mailer')->send($message);
		
		$EtatsCng = new EtatConge();
	  	$form = $this->container->get('form.factory')->create(new UserDemandeForm(), $EtatsCng);		
		
		$qb = $em->createQueryBuilder();
		$qb->select('d.idDem,d.dateD,d.dateF,d.dateEnrg,d.nbrJr,r.id,e.idEtat,e.nomEtat,t.idTypes,t.nomCng')
			->from('GCNAFNAFBundle:Demande', 'd')
			->from('GCNAFNAFBundle:Ressource', 'r')
			->from('GCNAFNAFBundle:EtatConge', 'e')
			->from('GCNAFNAFBundle:TypesConges', 't')				
			->where('r.id = :mycle')				
			->andWhere('r.id = d.idUser ')								
			->andWhere('e.idEtat = d.idEtat ')				
			->andWhere('t.idTypes = d.idCng ')										
			->orderBy('d.dateEnrg', 'ASC');	
		$qb->setParameter('mycle', $myUser);												
		$query = $qb->getQuery();               
		$total = $query->getResult();							
		return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Chef:ListeDemandesUsers.html.twig', array(           
            'total' => $total,
			'nom' => $nomChef,
			'prenom' => $preChef,
			'nomuser' => $nomuser,
			'preuser' => $preuser,
			'id' => $chefId,
			'form' => $form->createView(),		
        ));	
 }
if( $etatValidationDem != 'a' ){  return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_Userequipe_demandes',array('id'=> $myUser)));}			

}// fin refuser 
	
	public function soldeAction($page)
    {	 
		// l'affichage+ paginat solde chef
	    $form = $this->container->get('form.factory')->create(new SearchSoldeForm());		 
	    $em = $this->container->get('doctrine')->getManager();	 		 
		$request = $this->container->get('request');	  	  
		$session = $request->getSession();
		
		$chefId = $session->get('cleuser');		 
		$ressource = $em->find('GCNAFNAFBundle:Ressource', $chefId); 
		$nomChef =$ressource->getNom();
		$preChef =$ressource->getPrenom();		 		
		 
		 $qb = $em->createQueryBuilder();
		 $qb->select('c.refCpt,c.annee,c.cptInitial,c.cptSolde,c.idUser')
			->from('GCNAFNAFBundle:CompteurSolde', 'c')	
			->where('c.idUser = :chefId')																		
			->orderBy('c.annee', 'ASC');			
		 $qb->setParameter('chefId', $chefId);				
		 $query = $qb->getQuery();               
		 $total = $query->getResult();	 
		
		 $total_jours    = count($total);
     	 $jours_per_page = 20;
     	 $last_page      = ceil($total_jours / $jours_per_page);	 
	     $previous_page  = $page > 1 ? $page - 1 : 1;
	     $next_page      = $page < $last_page ? $page + 1 : $last_page; 
		  
		 $qbnew = $em->createQueryBuilder();
 		 $qbnew->select('c.refCpt,c.annee,c.cptInitial,c.cptSolde,c.idUser')
			   ->from('GCNAFNAFBundle:CompteurSolde', 'c') 		 	   
			   ->where('c.idUser = :chefId')
		       ->setFirstResult(($page * $jours_per_page) - $jours_per_page)
		       ->setMaxResults($jours_per_page)
			   ->orderBy('c.annee', 'ASC');	
		 $qbnew->setParameter('chefId', $chefId);							   		
		 $querynew = $qbnew->getQuery();               
		 $total_fin = $querynew->getResult();				
		
		 return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Chef:ListeSolde.html.twig', array(
            'entities' => $total_fin,
            'last_page' => $last_page,
            'previous_page' => $previous_page,
            'current_page' => $page,
            'next_page' => $next_page,
            'total_articles' => $total_jours,
			'form' => $form->createView(),			
			'nom' => $nomChef,
			'prenom' => $preChef,
			'id' => $chefId,
        ));	   
	}//end fct  	
 	public function searchSoldeAction()
    {
	  $user = new CompteurSolde();
	  $form = $this->container->get('form.factory')->create(new SearchSoldeForm(), $user); 	  
	  $em = $this->container->get('doctrine')->getManager();	
	  $request = $this->container->get('request');		  
	  $session = $request->getSession();
	  $userSimpleId = $session->get('cleuser');		 	  
	  $ressource = $em->find('GCNAFNAFBundle:Ressource', $userSimpleId); 
	  $nom =$ressource->getNom();
	  $pren=$ressource->getPrenom();
		 
	  if ($request->getMethod() == 'POST') 
	  {
		$form->bind($request);	
		if ($form->isValid()) 
		{	  
		$form_user = $form->getData();
		$myear=$form_user->getAnnee();
		//requete				
		 $qb = $em->createQueryBuilder();		  		  				  		  		  
		 $qb->select('c.refCpt,c.annee,c.cptInitial,c.cptSolde,c.idUser')
			->from('GCNAFNAFBundle:CompteurSolde', 'c')			
			->where('c.idUser = :userSimpleId')
 		    ->andWhere('c.annee = :myear')												
			->orderBy('c.annee', 'ASC');
		 $qb->setParameter('userSimpleId', $userSimpleId);							   							
		 $qb->setParameter('myear', $myear);					
		 $query = $qb->getQuery();               
		 $total = $query->getResult();	 
		
		if ($total)
		{
			return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Chef:searchsolde.html.twig',
            array(
			'form' => $form->createView(),
			'entities' =>$total,
            'nom' => $nom,
			'prenom' => $pren,
			'id' => $userSimpleId,
			));
		}
	  }	
	}	
   return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Chef:searchsolde.html.twig',
    array(
	'form' => $form->createView(),
	'entities' =>$total,
	'nom' => $nom,
	'prenom' => $pren,
	'id' => $userSimpleId,
	));
  }
  
  public function demandesAction($id)
    {			    
		 $em = $this->container->get('doctrine')->getManager();				 		 				
		 $ressource = $em->find('GCNAFNAFBundle:Ressource', $id); //id pr chef 
		 $nom =$ressource->getNom();
		 $pren=$ressource->getPrenom();		 	
		 
		 $EtatsCng = new EtatConge();
	  	 $form = $this->container->get('form.factory')->create(new UserDemandeForm(), $EtatsCng);
	  
		 $qb = $em->createQueryBuilder();
		 $qb->select('d.idDem,d.dateD,d.dateF,d.msg,d.validateur,d.dateEnrg,d.nbrJr,d.dateVal,r.id,e.idEtat,e.nomEtat,t.idTypes,t.nomCng')
			->from('GCNAFNAFBundle:Demande', 'd')
			->from('GCNAFNAFBundle:Ressource', 'r')
			->from('GCNAFNAFBundle:EtatConge', 'e')
			->from('GCNAFNAFBundle:TypesConges', 't')				
			->where('r.id = :mycle')				
			->andWhere('r.id = d.idUser ')								
			->andWhere('e.idEtat = d.idEtat ')				
			->andWhere('t.idTypes = d.idCng ')										
			->orderBy('d.dateEnrg', 'ASC');	
		 $qb->setParameter('mycle', $id);												
		 $query = $qb->getQuery();               
		 $total = $query->getResult();			  						  					
				
		 return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Chef:ListeDemandes.html.twig', array(           
            'total' => $total,
			'nom' => $nom,
			'prenom' => $pren,
			'id' => $id,		
			'form' => $form->createView(),
        ));	   
	}	
							
	public function SearchDemandeAction()
    {
	  $em = $this->container->get('doctrine')->getManager();
	  $EtatsCng = new EtatConge();
  	  $form = $this->container->get('form.factory')->create(new UserDemandeForm(), $EtatsCng);	
	  $request = $this->container->get('request');	  	  
	  $session = $request->getSession();
	  $userSimpleId = $session->get('cleuser');		 
	  $ressource = $em->find('GCNAFNAFBundle:Ressource', $userSimpleId); 
	  $nom =$ressource->getNom();
	  $pren=$ressource->getPrenom();
	 
	  if ($request->getMethod() == 'POST') 
	  {
		$form->bind($request);
	
		if ($form->isValid()) 
		{   	
		 $form_user  = $form->getData();
		 $idetatsName  = $form_user->getNomEtat();		 
				  		 		
		 $qb = $em->createQueryBuilder();
		 $qb->select('d.idDem,d.dateD,d.dateF,d.msg,d.validateur,d.dateEnrg,d.nbrJr,d.dateVal,r.id,e.idEtat,e.nomEtat,t.idTypes,t.nomCng')
			->from('GCNAFNAFBundle:Demande', 'd')
			->from('GCNAFNAFBundle:Ressource', 'r')
			->from('GCNAFNAFBundle:EtatConge', 'e')
			->from('GCNAFNAFBundle:TypesConges', 't')				
			->where('r.id = :mycle')				
			->andWhere('d.idEtat = :idEtatsName')								
			->andWhere('r.id = d.idUser ')								
			->andWhere('e.idEtat = d.idEtat ')				
			->andWhere('t.idTypes = d.idCng ')										
			->orderBy('d.dateEnrg', 'ASC');	
		 $qb->setParameter('mycle', $userSimpleId);												
		 $qb->setParameter('idEtatsName', $idetatsName);												
		 $query = $qb->getQuery();               
		 $total = $query->getResult();			  					 
		
		  if($total){			
		 	return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Chef:RechercheDemandes.html.twig', array(           
            'total' => $total,
			'nom' => $nom,
			'prenom' => $pren,
			'id' => $userSimpleId,		
			'form' => $form->createView(),
       		 ));  
		   }// fin total		
		}
	  }	
	  return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Chef:RechercheDemandes.html.twig', array(           
            'total' => $total,
			'nom' => $nom,
			'prenom' => $pren,
			'id' => $userSimpleId,		
			'form' => $form->createView(),
       		 ));
	}	
	
// modification d'une demande		
public function editerAction($id)
	{	
		// $id = id demande
		$em = $this->container->get('doctrine')->getManager();
		$erreur='';										
		$conge=array();
		$query1=$this->getDoctrine()->getRepository('GCNAFNAFBundle:TypesConges')->findAll();
		foreach ($query1 as $ref1) {
		$conge[$ref1->getIdTypes()]=$ref1->getNomCng();
		}						    										  	    		
		$jour = $em->find('GCNAFNAFBundle:Demande', $id); 
				
		$etatValidationDem = $jour->getIdEtat();		
		$oldnbjours        = $jour->getNbrJr();		
		
		$formBuilder = $this->createFormBuilder($jour);
		$formBuilder											
			->add('idCng', 'choice', array('choices' => $conge,'empty_value' =>'Choisissez type conge','disabled' =>'true'))
            ->add('dateD','date', array('format' => 'yyyy-MM-dd','years' => range(2014 ,2030) ))
            ->add('dateF','date', array('format' => 'yyyy-MM-dd','years' => range(2014 ,2030) ))
			->add('deMidi', 'choice', array('choices' =>array('0' => 'Non', '1' => 'Oui' )))											
			->add('jsqMidi', 'choice', array('choices' =>array('0' => 'Non', '1' => 'Oui' )))
			->add('msg', 'textarea');		
		$form = $formBuilder->getForm();		
		$request = $this->container->get('request');
		// id chef
		$session = $request->getSession();
	    $userSimpleId = $session->get('cleuser');		 
	    $ressource = $em->find('GCNAFNAFBundle:Ressource', $userSimpleId); 
	    $nom =$ressource->getNom();
	    $pren=$ressource->getPrenom();						  		

if( $etatValidationDem == 'a' ){ 

		   if ($request->getMethod() == 'POST') 
		  {
			$form->bind($request);
			if ($form->isValid()) 
			{			
			$form_user = $form->getData();
			$dateD = $form_user->getDateD();	
			$dateF = $form_user->getDateF();

		
			if ($dateD < $dateF) 
			{				
			$datejour= new DateTime('now');    
			$dc=$jour->setDateEnrg($datejour);	
			$dv=$jour->setDateVal($datejour);	
			//------------------------------------------------------------
			$day1   = $dateD->format('d');
			$month1 = $dateD->format('m'); 
			$year1  = $dateD->format('Y'); 
			$datestr1 = $year1.'-'.$month1.'-'.$day1; 
			
			$day2   = $dateF->format('d');
			$month2 = $dateF->format('m'); 
			$year2  = $dateF->format('Y'); 
			$datestr2 = $year2.'-'.$month2.'-'.$day2;

			$datet1=strtotime($datestr1);
			$datet2=strtotime($datestr2);						
			$arr_bank_holidays = array();			

			$q = $em->createQueryBuilder();
			$q->select('r')
			   ->from('GCNAFNAFBundle:JoursFeries', 'r')
			   ->orderBy('r.date', 'ASC');								
			$quer = $q->getQuery();               
			$resjour = $quer->getResult();			
			$max=count($resjour);
			$tab=array();
			for($i=0;$i<$max;$i++){
					$tab[$i]=$resjour[$i];
			}
			$m=count($tab);
			if($resjour){			
					for($j=0;$j<$m;$j++){						
						$jourferie=$tab[$j]->getDate();																								
						$dayj   = $jourferie->format('d');
						$monthj = $jourferie->format('m'); 
						$yearj  = $jourferie->format('Y'); 						
						$datestrj = $yearj.'-'.$monthj.'-'.$dayj; 
						$days= date('j',  strtotime($datestrj));	
						$mois= date('n',  strtotime($datestrj));			
						$annee=date('Y',  strtotime($datestrj));								
						$arr_bank_holidays[] = $days.'_'.$mois.'_'.$annee;									
					}//end for
			}	//end if(res)																
			$lesjours = 0;
			while ($datet1 < $datet2) {
				if (!in_array(date('w', $datet1), array(0, 6))  && !in_array(date('j_n_'.$annee, $datet1), $arr_bank_holidays)) {
				$lesjours++;
				}
				$datet1 += 86400;
				}			
			if($form_user->getDeMidi()== 1){
				$lesjours += 0.5 ;
			}
			if($form_user->getJsqMidi()== 1){
				$lesjours +=0.5 ;
			} 						
			
			$idtypeconge=$form_user->getIdCng();
			
			$qc = $em->createQueryBuilder();
			$qc->select('t')
			   ->from('GCNAFNAFBundle:TypesConges', 't')
			   ->where('t.idTypes = :code')			   
			   ->orderBy('t.dureeMax', 'ASC');	
			$qc->setParameter('code', $idtypeconge);										
			$querc = $qc->getQuery();               
			$dureec = $querc->getResult();						
			$taille=count($dureec);
			$tab2=array();

			for($ii=0;$ii<$taille;$ii++){
					$tab2[$ii]=$dureec[$ii];
			}
			$c=count($tab2);
			if($dureec){			
					for($jj=0;$jj<$c;$jj++){
						$dureemax  = $tab2[$jj]->getDureeMax();
						if($lesjours > $dureemax ) { $erreur='vous avez depasse la duree maximale  de ce type conge!'; }							
						if($lesjours <= $dureemax ){

							if($idtypeconge == 1){								
								$qa = $em->createQueryBuilder();
								$qa->select('c')
								   ->from('GCNAFNAFBundle:CompteurSolde', 'c')
								   ->where('c.idUser = :cle');			   
								$qa->setParameter('cle', $userSimpleId);										
								$quera = $qa->getQuery();               
								$soldeinfo = $quera->getResult();								
								
								$taille2=count($soldeinfo);
								$tab3=array();
								for($k=0;$k<$taille2;$k++){
										$tab3[$k]=$soldeinfo[$k];
								}
								$com=count($tab3);
								if($soldeinfo){			
								 for($l=0;$l<$com;$l++){							
										$cmpsolde = $tab3[$l]->getCptSolde();
										$annee    = $tab3[$l]->getAnnee();
									
									if(($annee == $year1) && ($idtypeconge==1)){
											$som      = $oldnbjours+$cmpsolde;
											$ressold  = ($som)-($lesjours);
											$cptsolde = $tab3[$l]->setCptSolde($ressold);
											$dv=$jour->setNbrJr($lesjours);
											$simpleuserID=$jour->setIdUser($userSimpleId);
											$em->persist($jour);
											$em->flush();
										return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_chef_equipe_demandes',array('id'=> $userSimpleId)));		
										}
										if($annee != $year1){$erreur='Il faut initialiser votre compteur du solde pour cette periode!';}				
									  }// end for
									}
									if(!$soldeinfo){$erreur='Il faut initialiser votre compteur du solde!';}		
							  }	// 3 test type conge=1		
							  													
							if($idtypeconge!=1){								
								$dv=$jour->setNbrJr($lesjours);
								$simpleuserID=$jour->setIdUser($userSimpleId);
								$em->persist($jour);
								$em->flush();
								return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_chef_equipe_demandes',array('id'=> $userSimpleId)));		
							}								
						}// 2 test comparaison avec duree max 			
			  }// end for
			}// end if $dureec																	
		}//end test 1 sur les dates

		else {$erreur='La date debut du conge doit etre inferieure a la date de fin!';}				
	   }
	 }
	return $this->container->get('templating')->renderResponse(
    'GCNAFNAFBundle:Chef:modifierDemandeCng.html.twig',
	array(
	'form' => $form->createView(),	
	'erreur' => $erreur,		
	'nom' => $nom,
	'prenom' => $pren,
	'id' => $userSimpleId,	
	));			
		
}	// fin si etat=a
if( $etatValidationDem != 'a' ){  return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_chef_equipe_demandes',array('id'=> $userSimpleId)));}		
}
// fin fct modifier---------------------------------------------------------------------------		
												
public function supprimerAction($id)
	{
	  $em = $this->container->get('doctrine')->getManager();
	  $acteur = $em->find('GCNAFNAFBundle:Demande', $id);			
	  if (!$acteur) {throw new NotFoundHttpException("aucune demande n a ete trouvee");}			
	  
	  $etatValidationDem = $acteur->getIdEtat();		
	  $typeconge = $acteur->getIdCng();
	  $iduser = $acteur->getIdUser();
	  $nbjours=	$acteur->getNbrJr();  
	  $dated  = $acteur->getDateD();
	  $year   = $dated->format('Y'); 	  

if( $etatValidationDem == 'a' ){
	 
	  if($typeconge == 1){		   	
		
		$qa = $em->createQueryBuilder();
		$qa->select('c')
		   ->from('GCNAFNAFBundle:CompteurSolde', 'c')
		   ->where('c.idUser = :cle')
		   ->andWhere('c.annee = :year');		   
		$qa->setParameter('cle', $iduser);										
		$qa->setParameter('year', $year);										
		$quera = $qa->getQuery();               
		$soldeinfo = $quera->getResult();		
		
		$taille2=count($soldeinfo);
		$tab3=array();
	 	 for($k=0;$k<$taille2;$k++){
			$tab3[$k]=$soldeinfo[$k];
		 }
		$com=count($tab3);	
	  if($soldeinfo)
	  {		
	   	 for($l=0;$l<$com;$l++){							
				$cmpsolde = $tab3[$l]->getCptSolde();
			 	$annee    = $tab3[$l]->getAnnee();						
				$ressold  = ($cmpsolde)+($nbjours);
				$cptsolde = $tab3[$l]->setCptSolde($ressold);
				$em->remove($acteur);
				$em->flush();        
				return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_chef_equipe_demandes',array('id'=> $iduser)));	  
	     }// end for
  	  }		 
	if(!$soldeinfo){ return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_chef_equipe_demandes',array('id'=> $iduser))); }
    
	}  	  	
  // # no congé payé 
  if($typeconge != 1)  
   {	
	 $em->remove($acteur);
	 $em->flush();        
	 return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_chef_equipe_demandes',array('id'=> $iduser)));}
	
} // fin etat en attente

if( $etatValidationDem != 'a' ){ return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_chef_equipe_demandes',array('id'=> $iduser)));}		    

}
 //l'ajout dmd
 public function AjouterAction()
	{	
		$em = $this->container->get('doctrine')->getManager();
		$erreur='';	
									
		$conge=array();
		$query1=$this->getDoctrine()->getRepository('GCNAFNAFBundle:TypesConges')->findAll();
		foreach ($query1 as $ref1) {
		$conge[$ref1->getIdTypes()]=$ref1->getNomCng();
		}						    										  	    		
		$jour = new Demande();				
		$request = $this->container->get('request');
		
		$formBuilder = $this->createFormBuilder($jour);
		$formBuilder											
			->add('idCng', 'choice', array('choices' => $conge,'empty_value' =>'Choisissez type conge'))
            ->add('dateD','date', array('format' => 'yyyy-MM-dd','years' => range(2014 ,2030) ))
            ->add('dateF','date', array('format' => 'yyyy-MM-dd','years' => range(2014 ,2030) ))
			->add('deMidi', 'choice', array('choices' =>array('0' => 'Non', '1' => 'Oui' )))											
			->add('jsqMidi', 'choice', array('choices' =>array('0' => 'Non', '1' => 'Oui' )))
			->add('msg', 'textarea');					
		$form = $formBuilder->getForm();					
				
		$session = $request->getSession();
	    $userSimpleId = $session->get('cleuser');		 
	    $ressource = $em->find('GCNAFNAFBundle:Ressource', $userSimpleId); 
	    $nom =$ressource->getNom();
	    $pren=$ressource->getPrenom();	  	
			
		   if ($request->getMethod() == 'POST') 
		  {
			$form->bind($request);			
			if ($form->isValid()) 
			{			
			$form_user = $form->getData();
			$dateD = $form_user->getDateD();	
			$dateF = $form_user->getDateF();
			//test 1 sur les dates				
			if ($dateD < $dateF) 
			{				
			$datejour= new DateTime('now');     // date du jour
			$dc=$jour->setDateEnrg($datejour);	// date creation 
			$dv=$jour->setDateVal($datejour);	// date validation 
//-------------------------------------------------------------------
			$day1   = $dateD->format('d');
			$month1 = $dateD->format('m'); 
			$year1  = $dateD->format('Y'); 
			$datestr1 = $year1.'-'.$month1.'-'.$day1; //string date debut
			
			$day2   = $dateF->format('d');
			$month2 = $dateF->format('m'); 
			$year2  = $dateF->format('Y'); 
			$datestr2 = $year2.'-'.$month2.'-'.$day2;//string date fin Y-m-d

			$datet1=strtotime($datestr1);
			$datet2=strtotime($datestr2);						
			$arr_bank_holidays = array();			
			//les jours feries de la BDD
			$q = $em->createQueryBuilder();
			$q->select('r')
			   ->from('GCNAFNAFBundle:JoursFeries', 'r')
			   ->orderBy('r.date', 'ASC');								
			$quer = $q->getQuery();               
			$resjour = $quer->getResult();			
			$max=count($resjour);
			$tab=array();
			for($i=0;$i<$max;$i++){
					$tab[$i]=$resjour[$i];
			}
			$m=count($tab);
			if($resjour){			
					for($j=0;$j<$m;$j++){						
						$jourferie=$tab[$j]->getDate();																								
						$dayj   = $jourferie->format('d');
						$monthj = $jourferie->format('m'); 
						$yearj  = $jourferie->format('Y'); 						
						$datestrj = $yearj.'-'.$monthj.'-'.$dayj; //string date jour ferié																																	
						$days= date('j',  strtotime($datestrj));	
						$mois= date('n',  strtotime($datestrj));			
						$annee=date('Y',  strtotime($datestrj));								
						$arr_bank_holidays[] = $days.'_'.$mois.'_'.$annee;									
					}//end for
			}	//end if(res)																
			$lesjours = 0;
			while ($datet1 < $datet2) {
				if (!in_array(date('w', $datet1), array(0, 6))  && !in_array(date('j_n_'.$annee, $datet1), $arr_bank_holidays)) {
				$lesjours++;
				}
				$datet1 += 86400;
				}			
			if($form_user->getDeMidi()== 1){
				$lesjours += 0.5 ;
			}
			if($form_user->getJsqMidi()== 1){
				$lesjours +=0.5 ;
			} 						
			
			$idtypeconge=$form_user->getIdCng();			
			// 2 test comparaison avec duree max 
			$qc = $em->createQueryBuilder();
			$qc->select('t')
			   ->from('GCNAFNAFBundle:TypesConges', 't')
			   ->where('t.idTypes = :code')			   
			   ->orderBy('t.dureeMax', 'ASC');	
			$qc->setParameter('code', $idtypeconge);										
			$querc = $qc->getQuery();               
			$dureec = $querc->getResult();						
			$taille=count($dureec);
			$tab2=array();
			for($ii=0;$ii<$taille;$ii++){
					$tab2[$ii]=$dureec[$ii];
			}
			$c=count($tab2);
			if($dureec){			
					for($jj=0;$jj<$c;$jj++){
						$dureemax  = $tab2[$jj]->getDureeMax();
						if($lesjours > $dureemax ) { $erreur='vous avez depasse la duree maximale  de ce type conge!'; }							
						if($lesjours <= $dureemax ){
							if($idtypeconge == 1){								
								$qa = $em->createQueryBuilder();
								$qa->select('c')
								   ->from('GCNAFNAFBundle:CompteurSolde', 'c')
								   ->where('c.idUser = :cle');			   
								$qa->setParameter('cle', $userSimpleId);										
								$quera = $qa->getQuery();               
								$soldeinfo = $quera->getResult();								
								
								$taille2=count($soldeinfo);
								$tab3=array();
								for($k=0;$k<$taille2;$k++){
										$tab3[$k]=$soldeinfo[$k];
								}
								$com=count($tab3);
								if($soldeinfo){			
								 for($l=0;$l<$com;$l++){							
										$cmpsolde = $tab3[$l]->getCptSolde();
										$annee    = $tab3[$l]->getAnnee();
									if(($annee == $year1) && ($idtypeconge==1)){
											$ressold  = ($cmpsolde)-($lesjours);
											$cptsolde = $tab3[$l]->setCptSolde($ressold);
											$dv=$jour->setNbrJr($lesjours);
											$simpleuserID=$jour->setIdUser($userSimpleId);
											$em->persist($jour);
											$em->flush();
										//  Gestion Newsletter----------------------------------	--------------------------------	--------------------------------																		
											$adminreq = $em->createQueryBuilder();
											$adminreq->select('m')
											->from('GCNAFNAFBundle:Ressource', 'm')
											->where('m.idProf = :prof');			   
											$adminreq->setParameter('prof', 'admin');										
											$queradmin = $adminreq->getQuery();               
											$AdminMail = $queradmin->getResult();												
										if($AdminMail){
											$nbMails=count($AdminMail);
											for($i=0;$i<$nbMails;$i++){
												$adressMail = $AdminMail[$i]->getMail();
												$message = \Swift_Message::newInstance()
													->setSubject('[noreply]-Demande de validation de conge NAF')
													->setFrom('b.hassiki.bts@gmail.com')
													->setTo($adressMail)
													->setBody('');
												$message->setBody($this->renderView('GCNAFNAFBundle:User:EmailPage.html.twig', array(
												'nom' => $nom,'prenom' => $pren)),'text/html');
												$this->get('mailer')->send($message);	
											}
										}												
									//  Gestion Newsletter----------------------------------	--------------------------------	--------------------------------																													
										return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_chef_equipe_demandes',array('id'=> $userSimpleId)));		
										}
										if($annee != $year1){$erreur='Il faut initialiser votre compteur du solde pour cette periode!';}				
									  }// end for
									}
									if(!$soldeinfo){$erreur='Il faut initialiser votre compteur du solde!';}			
							  }	// 3 test type conge=1		
							  													
							if($idtypeconge!=1){								
								$dv=$jour->setNbrJr($lesjours);
								$simpleuserID=$jour->setIdUser($userSimpleId);
								$em->persist($jour);
								$em->flush();
							//  Gestion Newsletter----------------------------------	--------------------------------	--------------------------------																		
											$adminreq = $em->createQueryBuilder();
											$adminreq->select('m')
											->from('GCNAFNAFBundle:Ressource', 'm')
											->where('m.idProf = :prof');			   
											$adminreq->setParameter('prof', 'admin');										
											$queradmin = $adminreq->getQuery();               
											$AdminMail = $queradmin->getResult();												
										if($AdminMail){
											$nbMails=count($AdminMail);
											for($i=0;$i<$nbMails;$i++){
												$adressMail = $AdminMail[$i]->getMail();
												$message = \Swift_Message::newInstance()
													->setSubject('[noreply]-Demande de validation de conge NAF')
													->setFrom('b.hassiki.bts@gmail.com')
													->setTo($adressMail)
													->setBody('');
												$message->setBody($this->renderView('GCNAFNAFBundle:User:EmailPage.html.twig', array(
												'nom' => $nom,'prenom' => $pren)),'text/html');
												$this->get('mailer')->send($message);	
											}
										}										
							//  Gestion Newsletter----------------------------------	--------------------------------	--------------------------------																																						
								return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_chef_equipe_demandes',array('id'=> $userSimpleId)));		
							}								
						}		
			  }// end for
			}																
		}//end test 1 sur les dates
		else {$erreur='La date debut du conge doit etre inferieure a la date de fin!';}				
	   }
	 }
	return $this->container->get('templating')->renderResponse(
    'GCNAFNAFBundle:Chef:ajouterDemandeCng.html.twig',
	array(
	'form' => $form->createView(),
	'erreur' => $erreur,		
	'nom' => $nom,
	'prenom' => $pren,
	'id' => $userSimpleId,				
	));				
}
												
}//end classe