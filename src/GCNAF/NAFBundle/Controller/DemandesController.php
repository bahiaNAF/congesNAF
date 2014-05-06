<?php

namespace GCNAF\NAFBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use \DateTime;
use Symfony\Bundle\SwiftmailerBundle;

use GCNAF\NAFBundle\Entity\Ressource;
use GCNAF\NAFBundle\Entity\Demande;
use GCNAF\NAFBundle\Entity\EtatConge;
use GCNAF\NAFBundle\Entity\TypesConges;
use GCNAF\NAFBundle\Entity\Profil;
use GCNAF\NAFBundle\Entity\CompteurSolde;
use GCNAF\NAFBundle\Entity\JoursFeries;
use GCNAF\NAFBundle\Entity\ListeJours;
use GCNAF\NAFBundle\Form\SearchDemandeForm;

use Symfony\Component\HttpFoundation\Session\Session;
  
class DemandesController extends Controller
{  	
	public function indexAction($page)
    {	 
		 // l'affichage+ paginat
	     $form = $this->container->get('form.factory')->create(new SearchDemandeForm());		 
		 $em = $this->container->get('doctrine')->getManager();	 
		 $qb = $em->createQueryBuilder();
		 $qb->select('d.idDem,d.dateD,d.dateF,d.msg,d.validateur,d.dateEnrg,r.id,r.nom,r.prenom,e.idEtat,e.nomEtat,t.idTypes,t.nomCng')
			->from('GCNAFNAFBundle:Demande', 'd')
			->from('GCNAFNAFBundle:Ressource', 'r')
			->from('GCNAFNAFBundle:EtatConge', 'e')
			->from('GCNAFNAFBundle:TypesConges', 't')				
			->where('r.id = d.idUser ')								
			->andWhere('e.idEtat = d.idEtat ')				
			->andWhere('t.idTypes = d.idCng ')										
			->orderBy('d.dateEnrg', 'ASC');			
		 $query = $qb->getQuery();               
		 $total = $query->getResult();	 
		
		 $total_jours    = count($total);
     	 $jours_per_page = 10;
     	 $last_page      = ceil($total_jours / $jours_per_page);	 
	     $previous_page  = $page > 1 ? $page - 1 : 1;
	     $next_page      = $page < $last_page ? $page + 1 : $last_page; 
		  
		 $qbnew = $em->createQueryBuilder();
 		 $qbnew->select('d.idDem,d.dateD,d.dateF,d.msg,d.validateur,d.dateEnrg,r.id,r.nom,r.prenom,e.idEtat,e.nomEtat,t.idTypes,t.nomCng')
			   ->from('GCNAFNAFBundle:Demande', 'd')
 		 	   ->from('GCNAFNAFBundle:Ressource', 'r')
			   ->from('GCNAFNAFBundle:EtatConge', 'e')
			   ->from('GCNAFNAFBundle:TypesConges', 't')
			   ->where('r.id = d.idUser ')								
			   ->andWhere('e.idEtat = d.idEtat ')				
			   ->andWhere('t.idTypes = d.idCng ')														
		       ->setFirstResult(($page * $jours_per_page) - $jours_per_page)
		       ->setMaxResults($jours_per_page)
		       ->orderBy('d.dateEnrg', 'ASC');			
		 $querynew = $qbnew->getQuery();               
		 $total_fin = $querynew->getResult();				
		 return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Admin:ListeDemandes.html.twig', array(
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
	public function searchByYearAction()
    {
      $erreur='Resultats de votre recherche';
	  $user = new Demande();
	  $form = $this->container->get('form.factory')->create(new SearchDemandeForm(), $user);
	  $request = $this->container->get('request');	
	  if ($request->getMethod() == 'POST') 
	  {
		$form->bind($request);	
		if ($form->isValid()) 
		{
		$em = $this->container->get('doctrine')->getManager();		  
		$form_user = $form->getData();
		$myear=$form_user->getdateEnrg();
		//requete				
		 $qb = $em->createQueryBuilder();		  		  				  		  		  
		 $qb->select('d.idDem,d.dateD,d.dateF,d.msg,d.validateur,d.dateEnrg,r.id,r.nom,r.prenom,e.idEtat,e.nomEtat,t.idTypes,t.nomCng')
			->from('GCNAFNAFBundle:Demande', 'd')
			->from('GCNAFNAFBundle:Ressource', 'r')
			->from('GCNAFNAFBundle:EtatConge', 'e')
			->from('GCNAFNAFBundle:TypesConges', 't')				
			->where('r.id = d.idUser ')								
			->andWhere('e.idEtat = d.idEtat ')				
			->andWhere('t.idTypes = d.idCng ')	
 		    ->andWhere('YEAR(d.dateEnrg) = :myear')												
			->orderBy('d.dateEnrg', 'ASC');		
		 $qb->setParameter('myear', $myear);					
		 $query = $qb->getQuery();               
		 $total = $query->getResult();	 
		 
		if ($total)
		{
			return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Admin:searchdemandes.html.twig',
            array('form' => $form->createView(),'erreur' => $erreur,'total' =>$total
             ,));
		}
	  }	
	}	
   return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Admin:searchdemandes.html.twig',
   array('form' => $form->createView(),'erreur' => $erreur,'total' =>$total
   ,));
}//end searche

	 public function ajouterAction()
	{	
		$em = $this->container->get('doctrine')->getManager();
		$erreur='';			
		$request = $this->container->get('request');			
		$session = $request->getSession();
		$admincleID = $session->get('cleuser');	
		
		$admin=array();				
		$qbs = $em->createQueryBuilder();
		$qbs->select('r')
		   ->from('GCNAFNAFBundle:Ressource', 'r')
		   ->where('r.id = :admincleID');	
		$qbs->setParameter('admincleID', $admincleID);					
		$querys = $qbs->getQuery();               
		$totals = $querys->getResult();					  			
		foreach ($totals as $refs) {
		$admin[$refs->getId()]=$refs->getNom();
		}
		
		$conge=array();
		$query1=$this->getDoctrine()->getRepository('GCNAFNAFBundle:TypesConges')->findAll();
		foreach ($query1 as $ref1) {
		$conge[$ref1->getIdTypes()]=$ref1->getNomCng();
		}						    										  	    
		$jour = new Demande();				
		
		$formBuilder = $this->createFormBuilder($jour);
		$formBuilder								
			->add('idUser', 'choice', array('choices' => $admin,'disabled' =>'true'))
			->add('idCng', 'choice', array('choices' => $conge,'empty_value' =>'Choisissez type conge'))
            ->add('dateD','date', array('format' => 'yyyy-MM-dd','years' => range(2014 ,2030) ))
            ->add('dateF','date', array('format' => 'yyyy-MM-dd','years' => range(2014 ,2030) ))
			->add('deMidi', 'choice', array('choices' =>array('0' => 'Non', '1' => 'Oui' )))											
			->add('jsqMidi', 'choice', array('choices' =>array('0' => 'Non', '1' => 'Oui' )))
			->add('msg', 'textarea');		
		$form = $formBuilder->getForm();		
		
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
			$ui=$jour->setIdUser($admincleID);	// date validation 
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
			// id du congé choisi			
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
							// 3 test type conge <=> 1 doit etre fixe pour le type "congé payé"			
							if($idtypeconge == 1){
								$idadmin=$form_user->getIdUser();						
								$qa = $em->createQueryBuilder();
								$qa->select('c')
								   ->from('GCNAFNAFBundle:CompteurSolde', 'c')
								   ->where('c.idUser = :cle');			   
								$qa->setParameter('cle', $idadmin);										
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
										// 4 test sur annee
									if(($annee == $year1) && ($idtypeconge==1)){
											$ressold  = ($cmpsolde)-($lesjours);
											$cptsolde = $tab3[$l]->setCptSolde($ressold);
											$dv=$jour->setNbrJr($lesjours);
											$em->persist($jour);
											$em->flush();
											return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_demandes_conges'));
										}
										if($annee != $year1){$erreur='Il faut initialiser le compteur du solde pour cette periode';}				
									  }// end for
									}
									
								 if(!$soldeinfo){$erreur='Il faut initialiser votre compteur du solde!';}
							  }															
							
							if($idtypeconge!=1){								
								$dv=$jour->setNbrJr($lesjours);
								$em->persist($jour);
								$em->flush();
								return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_demandes_conges'));
							}								
						}// 2 test comparaison avec duree max 			
			  }// end for
			}// end if $dureec																	
		}//end test 1 sur les dates
		else {$erreur='La date debut du conge doit etre inferieure a la date de fin du conge!';}				
	   }
	 }
	return $this->container->get('templating')->renderResponse(
    'GCNAFNAFBundle:Admin:ajouterDemandeCng.html.twig',
	array(
	'form' => $form->createView(),	
	'erreur' => $erreur,					
	));				
	}					
	
	public function supprimerAction($id)
	{
	  $em = $this->container->get('doctrine')->getManager();
	  $acteur = $em->find('GCNAFNAFBundle:Demande', $id);			
	  if (!$acteur) {throw new NotFoundHttpException("aucune demande n a ete trouvee");}			

	  $typeconge = $acteur->getIdCng();
	  $iduser = $acteur->getIdUser();
	  $nbjours=	$acteur->getNbrJr();  
	  $dated  = $acteur->getDateD();
	  $year   = $dated->format('Y'); 	  
	 
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
				return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_demandes_conges'));	  
	     }// end for
  	  }		 
	if(!$soldeinfo){ return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_demandes_conges'));	 }
    
	}  	  	

  // # no congé payé 
  if($typeconge != 1)  
   {	
	 $em->remove($acteur);
	 $em->flush();        
	 return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_demandes_conges'));	 }   		    
 }
 
 public function AccorderDemandeAction($id)
    {			    
		$em = $this->container->get('doctrine')->getManager();											
		$demande = $em->find('GCNAFNAFBundle:Demande', $id);		
		$etatValidationDem = $demande->getIdEtat();		
		$myUser   = $demande->getIdUser();		 	  			 		 
		$salaries = $em->find('GCNAFNAFBundle:Ressource', $myUser); 
		$nomuser  = $salaries->getNom();
		$preuser  = $salaries->getPrenom();
		$mailuser = $salaries->getMail();		 						
		$request = $this->container->get('request');	  	  
		$session = $request->getSession();				
		$nomChef = $session->get('nomAdmin');		 
		$preChef = $session->get('preAdmin');		 							 			 	  			 		 		
if( $etatValidationDem == 'a' ){ 		
	  	//modification de demande						
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
		
	return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_demandes_conges'));	
}	
if( $etatValidationDem != 'a' ){  return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_demandes_conges'));}			
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
		$myUser    = $demande->getIdUser();
				 	  			 		 
		$salaries = $em->find('GCNAFNAFBundle:Ressource', $myUser); 
		$nomuser  = $salaries->getNom();
		$preuser  = $salaries->getPrenom();
		$mailuser = $salaries->getMail();		 						
		$request = $this->container->get('request');	  	  
		$session = $request->getSession();				
		$nomChef = $session->get('nomAdmin');		 
		$preChef = $session->get('preAdmin');

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
	   if(!$soldeinfo){ return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_demandes_conges')); }		 		
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
		
		return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_demandes_conges')); 
 }
if( $etatValidationDem != 'a' ){  return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_demandes_conges'));}			
}// fin refuser 

}//end classe