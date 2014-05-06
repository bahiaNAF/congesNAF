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
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\SwiftmailerBundle;
 
class ImprimerController extends Controller
{
	//imprimer la liste des soldes
    public function ImprimerSoldeAction()
    {         	     
		 $em = $this->container->get('doctrine')->getManager();	 		 
		 $request = $this->container->get('request');	  	  
		 $session = $request->getSession();
		 $userSimpleId = $session->get('cleuser');		
		 $ressource = $em->find('GCNAFNAFBundle:Ressource', $userSimpleId); 
		 $nom =$ressource->getNom();
		 $pren=$ressource->getPrenom();
		 
		 $qb = $em->createQueryBuilder();
		 $qb->select('c.refCpt,c.annee,c.cptInitial,c.cptSolde,c.idUser')
			->from('GCNAFNAFBundle:CompteurSolde', 'c')	
			->where('c.idUser = :userSimpleId')																		
			->orderBy('c.annee', 'ASC');			
		 $qb->setParameter('userSimpleId', $userSimpleId);				
		 $query = $qb->getQuery();               
		 $total = $query->getResult();	 						
		
		//on stock la vue à convertir en PDF en oubliant pas les paramètre twig si la vue comporte des données dynamiques
        $html = $this->renderView('GCNAFNAFBundle:User:PDFlisteSolde.html.twig', array(
            'entities' => $total,           		
			'nom' => $nom,
			'prenom' => $pren,
        ));	            
        //on instancie la class Html2Pdf_Html2Pdf en lui passer en parametre
        //le sens de la page "portrait" => p ou "paysage" => l
        //le format A4,A5...
        //la langue du document fr,en,it...
        $html2pdf = new \Html2Pdf_Html2Pdf('P','A4','fr');
 
        //SetDisplayMode définit la manière dont le document PDF va être affiché par l’utilisateur
        //fullpage : affiche la page entière sur l'écran
        //fullwidth : utilise la largeur maximum de la fenêtre
        //real : utilise la taille réelle
        $html2pdf->pdf->SetDisplayMode('real');
 
        //writeHTML va tout simplement prendre la vue stocker dans la variable $html pour la convertir en format PDF
        $html2pdf->writeHTML($html);
 
        //Output envoi le document PDF au navigateur internet avec un nom spécifique qui aura un rapport avec le contenue à convertir (exemple : Facture, Règlement…)
        $html2pdf->Output('ListeSoldes.pdf');
         
        //pour vous rappeller qu’il faut toujours retourner quelque chose dans vos methode de votre controller.
        return new Response();

    }// fin fct liste solde user
		
	public function ImprimerDemandeAction($id)
	{
	  $em = $this->container->get('doctrine')->getManager();
	  $demande = $em->find('GCNAFNAFBundle:Demande', $id);
	  				 	  
	  $msg  = $demande->getMsg();		
	  $dateD= $demande->getDateD();		
	  $dateF= $demande->getDateF();		
	  $conge= $demande->getIdCng();
	  $user = $demande->getIdUser();
	  $nbjours = $demande->getNbrJr();
	  $demidi = $demande->getDeMidi();
	  $amidi  = $demande->getJsqMidi();
	  
	  $Conges= $em->find('GCNAFNAFBundle:TypesConges', $conge);
	  $Users = $em->find('GCNAFNAFBundle:Ressource', $user);
	  
	  $nomCnge =$Conges->getNomCng();
	  $nomUser =$Users->getNom();
  	  $preUser =$Users->getPrenom();
  
	  $html = $this->renderView('GCNAFNAFBundle:User:PDFdemandeConge.html.twig', array(
		'entities'=> $demande,           		
		'nomCnge' => $nomCnge,
		'nom'     => $nomUser,
		'prenom'  => $preUser,
		'msg'     => $msg,
		'dateD'   => $dateD,
		'dateF'   => $dateF,
		'nbjours' => $nbjours,
		'demidi'  => $demidi,
		'amidi'   => $amidi,
		));	  		          
	  $html2pdf = new \Html2Pdf_Html2Pdf('P','A4','fr');
	  $html2pdf->pdf->SetDisplayMode('real');         
	  $html2pdf->writeHTML($html);         
	  $html2pdf->Output('FormulaireDemandeConge.pdf');        
	  return new Response();	
   }
}// fin classe