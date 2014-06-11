<?php

namespace GCNAF\NAFBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use \DateTime;
use GCNAF\NAFBundle\Entity\Ressource;
use GCNAF\NAFBundle\Entity\CompteurSolde;
use GCNAF\NAFBundle\Entity\Demande;

use GCNAF\NAFBundle\Form\SearchSoldeForm;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\SwiftmailerBundle;
  
class SoldeController extends Controller
{  	
	public function indexAction($page)
    {	 
		 // l'affichage+ paginat
	     $form = $this->container->get('form.factory')->create(new SearchSoldeForm());		 
		 $em = $this->container->get('doctrine')->getManager();	 
		 $qb = $em->createQueryBuilder();
		 $qb->select('c.refCpt,c.annee,c.cptInitial,c.cptSolde,c.idUser,r.id,r.nom,r.prenom')
			->from('GCNAFNAFBundle:CompteurSolde', 'c')
			->from('GCNAFNAFBundle:Ressource', 'r')			
			->where('r.id = c.idUser ')																		
			->orderBy('r.nom', 'ASC');			
		 $query = $qb->getQuery();               
		 $total = $query->getResult();	 
		
		 $total_jours    = count($total);
     	 $jours_per_page = 20;
     	 $last_page      = ceil($total_jours / $jours_per_page);	 
	     $previous_page  = $page > 1 ? $page - 1 : 1;
	     $next_page      = $page < $last_page ? $page + 1 : $last_page; 
		  
		 $qbnew = $em->createQueryBuilder();
 		 $qbnew->select('c.refCpt,c.annee,c.cptInitial,c.cptSolde,c.idUser,r.id,r.nom,r.prenom')
			   ->from('GCNAFNAFBundle:CompteurSolde', 'c')
 		 	   ->from('GCNAFNAFBundle:Ressource', 'r')
			   ->where('r.id = c.idUser ')																																		
		       ->setFirstResult(($page * $jours_per_page) - $jours_per_page)
		       ->setMaxResults($jours_per_page)
			   ->orderBy('r.nom', 'ASC');			
		 $querynew = $qbnew->getQuery();               
		 $total_fin = $querynew->getResult();				
		 return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Admin:ListeSolde.html.twig', array(
            'entities' => $total_fin,
            'last_page' => $last_page,
            'previous_page' => $previous_page,
            'current_page' => $page,
            'next_page' => $next_page,
            'total_articles' => $total_jours,
			'form' => $form->createView(),			
        ));	   
	}//end fct  	
	
	// 2 recherche par année
	public function searchSoldeAction()
    {
      $erreur='Resultats de votre recherche';
	  $user = new CompteurSolde();
	  $form = $this->container->get('form.factory')->create(new SearchSoldeForm(), $user);
	  $request = $this->container->get('request');	
	  $session = $request->getSession();	  
	  
	  if ($request->getMethod() == 'POST') 
	  {
		$form->bind($request);	
		if ($form->isValid()) 
		{
		$em = $this->container->get('doctrine')->getManager();		  
		$form_user = $form->getData();
		$myear=$form_user->getAnnee();
		
		$session->set('anneesolde', $myear);
		$YearOfSolde = $session->get('anneesolde');
		
		//requete				
		 $qb = $em->createQueryBuilder();		  		  				  		  		  
		 $qb->select('c.refCpt,c.annee,c.cptInitial,c.cptSolde,c.idUser,r.id,r.nom,r.prenom')
			->from('GCNAFNAFBundle:CompteurSolde', 'c')
			->from('GCNAFNAFBundle:Ressource', 'r')			
			->where('r.id = c.idUser ')											
 		    ->andWhere('c.annee = :myear')												
			->orderBy('c.annee', 'ASC');		
		 $qb->setParameter('myear', $myear);					
		 $query = $qb->getQuery();               
		 $total = $query->getResult();	 
		 
		if ($total)
		{
			return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Admin:searchsolde.html.twig',
            array('form' => $form->createView(),'erreur' => $erreur,'total2' =>$total
             ,));
		}
	  }	
	}	
   return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Admin:searchsolde.html.twig',
   array('form' => $form->createView(),'erreur' => $erreur,'total2' =>$total
   ,));
  }
  
	 public function editerAction($id)
	{	
		$em = $this->container->get('doctrine')->getManager();	
	
		$salarie=array();
		$query1=$this->getDoctrine()->getRepository('GCNAFNAFBundle:Ressource')->findAll();
		foreach ($query1 as $ref1) {
		$salarie[$ref1->getId()]=$ref1->getNom();
		}				    											
		$jour = $em->find('GCNAFNAFBundle:CompteurSolde', $id); 						    

	   	if (!$jour)	{ $message='Solde pas encore inisialise!'; }		
		
		$formBuilder = $this->createFormBuilder($jour);
		$formBuilder						
			->add('idUser', 'choice', array('choices' => $salarie))
            ->add('annee', 'text')			
			->add('cptInitial', 'text')
			->add('cptSolde', 'text', array('disabled' => 'true'));	
		$form = $formBuilder->getForm();		
		$request = $this->container->get('request');
		   if ($request->getMethod() == 'POST') 
		  {
			$form->bind($request);
			if ($form->isValid()) 
			{	
			$form_solde = $form->getData();			
			$cleuser    = $form_solde->getIdUser();	
			$annee      = $form_solde->getAnnee();  
			$anneeprc   = $annee-1;

			$qa = $em->createQueryBuilder();
			$qa->select('c')
			   ->from('GCNAFNAFBundle:CompteurSolde', 'c')
			   ->where('c.idUser = :cle')
			   ->andWhere('c.annee = :a');			   			   
			$qa->setParameter('cle', $cleuser);	
			$qa->setParameter('a', $anneeprc);													
			$quera = $qa->getQuery();               
			$res = $quera->getResult();
			
			$taille=count($res);
			$tab2=array();
			for($ii=0;$ii<$taille;$ii++){
					$tab2[$ii]=$res[$ii];
			}
			$c=count($tab2);
			// si oui 
			if($res){ 
				for($jj=0;$jj<$c;$jj++){				
					$soldepre = $tab2[$jj]->getCptSolde();      
					$soldesaisie= $form_solde->getCptInitial(); 
					$newsolde=$soldepre+$soldesaisie;			
					$newcpt=$jour->setCptSolde($newsolde);
					$em->persist($jour);
					$em->flush();
					return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_compteur_solde'));
				}
			}
			// si non 
			if(!$res){
				// mettre a jour a l'aide nbjours du congé payé
				$q2 = $em->createQueryBuilder();
				$q2->select('d')
				   ->from('GCNAFNAFBundle:Demande', 'd')
				   ->where('d.idUser = :utilisateur')
		 		   ->andWhere('YEAR(d.dateD) = :an')
				   ->andWhere('d.idCng = :cong');
				$q2->setParameter('utilisateur', $cleuser);	
				$q2->setParameter('an', $annee);													
				$q2->setParameter('cong', 1);													
				$quer2 = $q2->getQuery();               
				$NBjoursRes = $quer2->getResult();
						
				$t=count($NBjoursRes);
				$tb=array();
				for($i=0;$i<$t;$i++){
					$tb[$i]=$NBjoursRes[$i]->getNbrJr();
				}
				$cnt=count($tb);
				$calculnbj=0;				
				// si on a demandes			
			   if($NBjoursRes){ 
				
				 for($j=0;$j<$cnt;$j++){											
					$calculnbj = $calculnbj + $tb[$j];
				  }									
					$formsolde  = $form_solde->getCptInitial();
					$upcompteur = $formsolde - $calculnbj;
					$nvcptr=$jour->setCptSolde($upcompteur);					
					$em->persist($jour);
					$em->flush();
					return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_compteur_solde'));
				}
				if(!$NBjoursRes){ 
				
					$mysolde= $form_solde->getCptInitial();
					$newcpt2=$jour->setCptSolde($mysolde);
					$em->persist($jour);
					$em->flush();
					return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_compteur_solde'));
				}// end if 
				
			 }	// end if !res			
			}
		   }
			return $this->container->get('templating')->renderResponse(
		   'GCNAFNAFBundle:Admin:editerSolde.html.twig',
			array(
			'form' => $form->createView(),		
			));	
	}

	 public function initialiserAction()
	{	
		$em = $this->container->get('doctrine')->getManager();		
		
		$salarie=array();
		$query1=$this->getDoctrine()->getRepository('GCNAFNAFBundle:Ressource')->findAll();
		foreach ($query1 as $ref1) {
		$salarie[$ref1->getId()]=$ref1->getNom();
		}				    										
  	    $jour = new CompteurSolde();				
		
		$formBuilder = $this->createFormBuilder($jour);
		$formBuilder						
			->add('idUser', 'choice', array('choices' => $salarie,'empty_value' =>'Choisissez le salarie'))
            ->add('annee', 'text')			
			->add('cptInitial', 'text');
		$form = $formBuilder->getForm();		
		$request = $this->container->get('request');
		   if ($request->getMethod() == 'POST') 
		  {
			$form->bind($request);
			if ($form->isValid()) 
			{	
			$form_solde = $form->getData();			
			$cleuser    = $form_solde->getIdUser();
			$annee      = $form_solde->getAnnee(); 
			$anneeprc   = $annee-1;

			$qa = $em->createQueryBuilder();
			$qa->select('c')
			   ->from('GCNAFNAFBundle:CompteurSolde', 'c')
			   ->where('c.idUser = :cle')
			   ->andWhere('c.annee = :a');			   			   
			$qa->setParameter('cle', $cleuser);	
			$qa->setParameter('a', $anneeprc);													
			$quera = $qa->getQuery();               
			$res = $quera->getResult();
			
			$taille=count($res);
			$tab2=array();
			for($ii=0;$ii<$taille;$ii++){
					$tab2[$ii]=$res[$ii];
			}
			$c=count($tab2);
			// si oui 
			if($res){ 
				for($jj=0;$jj<$c;$jj++){				
					$soldepre = $tab2[$jj]->getCptSolde();      // reste solde du compteur 9dim
					$soldesaisie= $form_solde->getCptInitial(); // solde initialis jdid
					$newsolde=$soldepre+$soldesaisie;			// new compteur solde 
					$newcpt=$jour->setCptSolde($newsolde);
					$em->persist($jour);
					$em->flush();
					return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_compteur_solde'));
				}
			}
			// si non 
			if(!$res){
				$mysolde= $form_solde->getCptInitial();	// solde initialise jdid
				$newcpt2=$jour->setCptSolde($mysolde); 
				$em->persist($jour);
				$em->flush();
				return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_compteur_solde'));	
			 }	// end if 				
			}
		   }
			return $this->container->get('templating')->renderResponse(
		   'GCNAFNAFBundle:Admin:ajouterSolde.html.twig',
			array(
			'form' => $form->createView(),		
			));	
	}
	
  public function ImprimerSoldeTotalAction()
{
	$em          = $this->container->get('doctrine')->getManager();	
	$request     = $this->container->get('request');	
    $session     = $request->getSession();  		
	$YearOfSolde = $session->get('anneesolde');
	$anneepre    = $YearOfSolde-1;
	
	$qb = $em->createQueryBuilder();		  		  				  		  		  
	$qb->select('c.refCpt,c.annee,c.cptInitial,c.cptSolde,c.idUser,r.id,r.nom,r.prenom,r.dateEmb')
		->from('GCNAFNAFBundle:CompteurSolde', 'c')
		->from('GCNAFNAFBundle:Ressource', 'r')			
		->where('r.id = c.idUser ')											
		->andWhere('c.annee = :myear')												
		->orderBy('r.dateEmb', 'ASC');		
	$qb->setParameter('myear', $YearOfSolde);					
	$query = $qb->getQuery();               
	$total = $query->getResult();	 
	
	$html = $this->renderView('GCNAFNAFBundle:Admin:PDFlisteSoldeTotal.html.twig', array(
		'entities' => $total,           		
		'anneeplus' => $YearOfSolde,
		'anneemoins' => $anneepre,
	));	    
	$html2pdf = new \Html2Pdf_Html2Pdf('P','A4','fr');
	$html2pdf->pdf->SetDisplayMode('real');
	$html2pdf->writeHTML($html);
	$html2pdf->Output('GestionCongesNAFListeSoldes.pdf');
	return new Response();
	
}// fin fct
	
}//end classe