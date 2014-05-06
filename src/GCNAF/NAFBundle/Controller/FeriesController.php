<?php

namespace GCNAF\NAFBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use \DateTime;

use GCNAF\NAFBundle\Entity\Ressource;
use GCNAF\NAFBundle\Entity\Demande;
use GCNAF\NAFBundle\Entity\EtatConge;
use GCNAF\NAFBundle\Entity\TypesConges;
use GCNAF\NAFBundle\Entity\Profil;
use GCNAF\NAFBundle\Entity\CompteurSolde;
use GCNAF\NAFBundle\Entity\JoursFeries;
use GCNAF\NAFBundle\Entity\ListeJours;
use GCNAF\NAFBundle\Form\SearchDemandeForm;
  
class FeriesController extends Controller
{  
	 public function editerAction($id)
	{	
		$message='';
		$civilites=array();
		$em = $this->container->get('doctrine')->getManager();
		$query=$this->getDoctrine()->getRepository('GCNAFNAFBundle:ListeJours')->findAll();
		foreach ($query as $ref) {
		$civilites[$ref->getId()]=$ref->getLibelle();
		}		    
		$jour = $em->find('GCNAFNAFBundle:JoursFeries', $id);   
	    if (!$jour)	{ $message='Aucun jour ferie trouve'; }		
		$formBuilder = $this->createFormBuilder($jour);
		$formBuilder			
			->add('idProjet', 'choice', array('choices' => $civilites, 'label' => 'La Liste des Jours'))
			->add('date','date', array('label' =>'Choisissez une date','format' => 'yyyy-MM-dd','years' => range(2014 ,2030) )) ;			
		$form = $formBuilder->getForm();		
		$request = $this->container->get('request');
  	   if ($request->getMethod() == 'POST') 
	  {
    	$form->bind($request);
		if ($form->isValid()) 
		{
		$em->persist($jour);
		$em->flush();
		//***************************update nbjour des demandes congés saisis************************
		$demandes = $em->createQueryBuilder();
		$demandes->select('d')
		  		 ->from('GCNAFNAFBundle:Demande','d');			
		$res     = $demandes->getQuery();               
		$demandesall = $res->getResult();						
		$countdemandes=count($demandesall);
		$TABd=array();		
		for($k=0;$k<$countdemandes;$k++){ $TABd[$k]=$demandesall[$k]; }
		$calculnb=count($TABd);			
		//trouver le nom du jour d'une date donnee (saisie)
		$form_user = $form->getData();
		$myDate=$form_user->getDate();	
		$j  = $myDate->format('d');
		$m  = $myDate->format('m'); 
		$a  = $myDate->format('Y');
		$datestr1 = $a.'-'.$m.'-'.$j;
		$datet1=strtotime($datestr1);		
		$jours = Array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
		$numJour = date('w', $datet1);
		//$myjour = $jours[$numJour];		
		if( ($numJour!=0)&&($numJour!=6) ){
			if($demandesall){
				for($i=0;$i<$calculnb;$i++){			
					$dateD  =$TABd[$i]->getDateD();
					$dateF  =$TABd[$i]->getDateF();
					$nbrJr  =$TABd[$i]->getNbrJr();
					$idCng  =$TABd[$i]->getIdCng();
					$idUser =$TABd[$i]->getIdUser();
						
					if($idCng==1){
						 if( ($dateD<=$myDate) && ($myDate<$dateF)){	
							$qa = $em->createQueryBuilder();
							$qa->select('c')
							   ->from('GCNAFNAFBundle:CompteurSolde', 'c')
							   ->where('c.idUser = :cle');			   
							$qa->setParameter('cle', $idUser);										
							$quera = $qa->getQuery();               
							$soldeinfo = $quera->getResult();
														
							$taille2=count($soldeinfo);
							$tab3=array();
							for($k=0;$k<$taille2;$k++){ $tab3[$k]=$soldeinfo[$k]; }
							$com=count($tab3);
							$year1  = $dateD->format('Y');
							
							if($soldeinfo){			
							  for($l=0;$l<$com;$l++){							
										$cmpsolde = $tab3[$l]->getCptSolde();
										$annee    = $tab3[$l]->getAnnee();

									if(($annee == $year1) && ($idCng==1)){
											$som      = $nbrJr+$cmpsolde;
											$ressold  = ($som)-($nbrJr-1);
											$cptsolde = $tab3[$l]->setCptSolde($ressold);
											$dv=$TABd[$i]->setNbrJr($nbrJr-1);
											$em->persist($TABd[$i]);
											$em->flush();						
									}										
							  }// end for
							}							
						 }																		
						}// fin if cng=1												
						if($idCng!=1){
						 if( ($dateD<=$myDate) && ($myDate<$dateF)){										
							$dv=$TABd[$i]->setNbrJr($nbrJr-1);
							$em->persist($TABd[$i]);
							$em->flush();						
						 }
						}														
				}// fin for
			}// fin if demandesall        		
		}		
		//***************************update nbjour des demandes congés saisis************************			
		return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_jours'));		
 		}
	   }
		return $this->container->get('templating')->renderResponse(
	   'GCNAFNAFBundle:JoursFeries:editer.html.twig',
		array(
		'form' => $form->createView(),
		'message' => $message,
		));	
}// fin modification
	

//2 fct ajouter jour
public function ajouterAction()
{
		$civilites=array();
		$em = $this->container->get('doctrine')->getManager();
		$query=$this->getDoctrine()->getRepository('GCNAFNAFBundle:ListeJours')->findAll();
		foreach ($query as $ref) {
		$civilites[$ref->getId()]=$ref->getLibelle();
		}		    
  	    $jour = new JoursFeries();	
		$formBuilder = $this->createFormBuilder($jour);
		$formBuilder			
		->add('idProjet', 'choice', array('choices' => $civilites,'empty_value' => 'Choisissez un jour', 'label' => 'La Liste des Jours'))
		->add('date','date', array('label' =>'Choisissez une date','format' => 'yyyy-MM-dd','years' => range(2014 ,2030) )) ;					
		$form = $formBuilder->getForm();		
		$request = $this->container->get('request');
  	   if ($request->getMethod() == 'POST') 
	  {
    	$form->bind($request);
		if ($form->isValid()) 
		{			
		$em->persist($jour);
		$em->flush();	
		
		//***************************update nbjour des demandes congés saisis************************
		$demandes = $em->createQueryBuilder();
		$demandes->select('d')
		  		 ->from('GCNAFNAFBundle:Demande','d');			
		$res     = $demandes->getQuery();               
		$demandesall = $res->getResult();						
		$countdemandes=count($demandesall);
		$TABd=array();		
		for($k=0;$k<$countdemandes;$k++){ $TABd[$k]=$demandesall[$k]; }
		$calculnb=count($TABd);			
		//trouver le nom du jour d'une date donnee (saisie)
		$form_user = $form->getData();
		$myDate=$form_user->getDate();	
		$j  = $myDate->format('d');
		$m  = $myDate->format('m'); 
		$a  = $myDate->format('Y');
		$datestr1 = $a.'-'.$m.'-'.$j;
		$datet1=strtotime($datestr1);		
		$jours = Array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
		$numJour = date('w', $datet1);
		//$myjour = $jours[$numJour];		
		if( ($numJour!=0)&&($numJour!=6) ){
			if($demandesall){
				for($i=0;$i<$calculnb;$i++){			
					$dateD  =$TABd[$i]->getDateD();
					$dateF  =$TABd[$i]->getDateF();
					$nbrJr  =$TABd[$i]->getNbrJr();
					$idCng  =$TABd[$i]->getIdCng();
					$idUser =$TABd[$i]->getIdUser();
						
					if($idCng==1){
						 if( ($dateD<=$myDate) && ($myDate<$dateF)){	
							$qa = $em->createQueryBuilder();
							$qa->select('c')
							   ->from('GCNAFNAFBundle:CompteurSolde', 'c')
							   ->where('c.idUser = :cle');			   
							$qa->setParameter('cle', $idUser);										
							$quera = $qa->getQuery();               
							$soldeinfo = $quera->getResult();
														
							$taille2=count($soldeinfo);
							$tab3=array();
							for($k=0;$k<$taille2;$k++){ $tab3[$k]=$soldeinfo[$k]; }
							$com=count($tab3);
							$year1  = $dateD->format('Y');
							
							if($soldeinfo){			
							  for($l=0;$l<$com;$l++){							
										$cmpsolde = $tab3[$l]->getCptSolde();
										$annee    = $tab3[$l]->getAnnee();

									if(($annee == $year1) && ($idCng==1)){
											$som      = $nbrJr+$cmpsolde;
											$ressold  = ($som)-($nbrJr-1);
											$cptsolde = $tab3[$l]->setCptSolde($ressold);
											$dv=$TABd[$i]->setNbrJr($nbrJr-1);
											$em->persist($TABd[$i]);
											$em->flush();						
									}										
							  }// end for
							}	
						 
						 }																		
						}// fin if cng=1
												
						if($idCng!=1){
						 if( ($dateD<=$myDate) && ($myDate<$dateF)){										
							$dv=$TABd[$i]->setNbrJr($nbrJr-1);
							$em->persist($TABd[$i]);
							$em->flush();						
						 }
						}
														
				}// fin for
			}// fin if demandesall        		
		}		
		//***************************update nbjour des demandes congés saisis************************			
		return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_jours'));		 		
 		}
	   }
		return $this->container->get('templating')->renderResponse(
	   'GCNAFNAFBundle:JoursFeries:ajoutjourfer.html.twig',
		array(
		'form' => $form->createView(),
		));	
		
	}// fin l'jout   	  	  	
}//end classe
