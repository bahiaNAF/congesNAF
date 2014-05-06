<?php

namespace GCNAF\NAFBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \DateTime;
use \DateTimeZone;
use \DateInterval;

use GCNAF\NAFBundle\Entity\Ressource;
use GCNAF\NAFBundle\Entity\EquipeChef;
use GCNAF\NAFBundle\Entity\ProjetChef;
  
class AffectationController extends Controller
{  	
	//l'affectation puis l'affichage...
	public function indexAction()
    {
		$em = $this->container->get('doctrine')->getManager();
		//requete 1 salaries
		$civilites=array();				
		$qb = $em->createQueryBuilder();
		$qb->select('r')
		   ->from('GCNAFNAFBundle:Ressource', 'r')
		   ->where('r.idProf = :profil')
		   ->orderBy('r.nom', 'ASC');			
		$qb->setParameter('profil', 'user');					
		$query = $qb->getQuery();               
		$total = $query->getResult();					  			
		foreach ($total as $ref) {
		$civilites[$ref->getId()]=$ref->getNom();
		}		    
  	    //requete 2 chefs
		$civilites2=array();				
		$qb2 = $em->createQueryBuilder();
		$qb2->select('r')
		   ->from('GCNAFNAFBundle:Ressource', 'r')
		   ->where('r.idProf = :profil')
		   ->orderBy('r.nom', 'ASC');			
		$qb2->setParameter('profil', 'chef');					
		$query2 = $qb2->getQuery();               
		$total2 = $query2->getResult();					  			
		foreach ($total2 as $ref2) {
		$civilites2[$ref2->getNom()]=$ref2->getNom();
		}
		 //requete 3 Projets
		$civilites3=array();				
		$qb3 = $em->createQueryBuilder();
		$qb3->select('r')
		   ->from('GCNAFNAFBundle:ProjetChef', 'r')
		   ->orderBy('r.projet', 'ASC');							
		$query3 = $qb3->getQuery();               
		$total3 = $query3->getResult();					  			
		foreach ($total3 as $ref3) {
		$civilites3[$ref3->getId()]=$ref3->getProjet();
		}
		//creation des form
		$affecter = new EquipeChef();	
		$formBuilder = $this->createFormBuilder($affecter);
		$formBuilder			
				->add('idSalarie', 'choice', array('choices' => $civilites,'empty_value' => 'Choisissez un salarie', 'label' => 'Liste des Salaries'))	
				->add('idChef', 'choice', array('choices' => $civilites2,'empty_value' => 'Choisissez un chef', 'label' => 'Liste des Chefs'))
				->add('projet', 'choice', array('choices' => $civilites3,'empty_value' => 'Choisissez un projet', 'label' => 'Liste des Projets'))		
  	            ->add('dateD','date', array('label' =>'Choisissez une Date','format' => 'yyyy-MM-dd','years' => range(2010 ,2030) ));		
		$form = $formBuilder->getForm();		
		$request = $this->container->get('request');
  	   if ($request->getMethod() == 'POST') 
	  {
    	$form->bind($request);
		if ($form->isValid()) 
		{		
		$form_user = $form->getData();
		$salarie=$form_user->getIdSalarie();// id salarie saisi
		$datedebut=$form_user->getDateD();	// date debut saisie				 											
			//rqt 1
			$q = $em->createQueryBuilder();
			$q->select('r')
			   ->from('GCNAFNAFBundle:EquipeChef', 'r')
			   ->where('r.idSalarie = :salarie');			
			$q->setParameter('salarie', $salarie);					
			$quer = $q->getQuery();               
			$res = $quer->getResult();
			
				$max=count($res);
				$tab=array();
				for($i=0;$i<$max;$i++){
					$tab[$i]=$res[$i];
				}
				$m=count($tab);
				
			if($res){			
						for($j=0;$j<$m;$j++){
							$datefin=$tab[$j]->getDateF();
						
						if($datefin==NULL)
									{									
									$interval = new DateInterval('P1D'); //-1 jour  
									$date3 = clone $datedebut;
									$date3->sub($interval);
									$date3->format('Y-m-d');									
									$newDF=$tab[$j]->setDateF($date3);																		
									$em->persist($affecter); //ajouter nv salarie
									$em->flush();
									return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_chef_equipe'));
									}
								}//end for
						}	//end if(res)

				if(!$res){	
				$em->persist($affecter);
				$em->flush();
				return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_chef_equipe'));							
				}//end if(!res)
	  } 
	 }	 
// requete de selection pr l'affichage---------------------------------
 $myq = $em->createQueryBuilder();
 $myq->select('e.ref,e.dateF,e.projet,e.idSalarie,e.idChef,e.dateD,r.id,r.nom,r.prenom,p.id,p.projet')
	->from('GCNAFNAFBundle:EquipeChef', 'e')
	->from('GCNAFNAFBundle:Ressource', 'r')
	->from('GCNAFNAFBundle:ProjetChef', 'p')	
	->where('e.projet = p.id')	
	->andWhere('e.idSalarie = r.id')
	->orderBy('r.nom', 'ASC');			
 $resu = $myq->getQuery();               
 $Tabchef = $resu->getResult();	 	 	 
//searche fct-----------------------------------------------------------
 //requete 1 salaries
		$search=array();				
		$qbs = $em->createQueryBuilder();
		$qbs->select('r')
		   ->from('GCNAFNAFBundle:Ressource', 'r')
		   ->where('r.idProf = :profil')
		   ->orderBy('r.nom', 'ASC');			
		$qbs->setParameter('profil', 'user');					
		$querys = $qbs->getQuery();               
		$totals = $querys->getResult();					  			
		foreach ($totals as $refs) {
		$search[$refs->getId()]=$refs->getNom();
		}
		//creation des form
		$selectline = new EquipeChef();	
		$formBuilderS = $this->createFormBuilder($selectline);
		$formBuilderS			
				->add('idSalarie', 'choice', array('choices' => $search,'empty_value' => 'Choisissez un salarie', 'label' => 'Recherche:'));
	    $formS = $formBuilderS->getForm();				
		
		return $this->container->get('templating')->renderResponse(
		'GCNAFNAFBundle:AffecterChef:index.html.twig',
		array(
		'form' => $form->createView(),
		'formSearch' => $formS->createView(),
        'entities' => $Tabchef,	
		));	
	}//end fct  	
	
//  la fct supprimer---------------------------------------------------
	public function supprimerAction($id)
	{
	  $em = $this->container->get('doctrine')->getManager();	  
	  $acteur = $em->find('GCNAFNAFBundle:EquipeChef', $id);	//id=ref	  
	  	$salarie=$acteur->getIdSalarie();
 	 	$dated=$acteur->getDateD();	
	  	$interval = new DateInterval('P1D'); //-1 jour  
	  	$date3 = clone $dated;
	  	$date3->sub($interval);
	  	$date3->format('Y-m-d');	//fin =dd-1
//requete
$qb = $em->createQueryBuilder();
$qb->select('r')
->from('GCNAFNAFBundle:EquipeChef', 'r')
->where('r.idSalarie = :ids');		
$qb->setParameter('ids', $salarie);					
$query = $qb->getQuery();               
$total = $query->getResult();	
	  $max=count($total);
	  $tab=array();
		for($i=0;$i<$max;$i++){
			$tab[$i]=$total[$i];
		}
	  $m=count($tab);	  		
		for($j=0;$j<$m;$j++){
		$datefin=$tab[$j]->getDateF();		
			if($datefin==$date3)
			{																											
			$newDF=$tab[$j]->setDateF(NULL);																											
			}
		}//end for
	  if (!$acteur) 
	  {
		throw new NotFoundHttpException("salarie non trouve");
	  }			
	  $em->remove($acteur);
	  $em->flush();        
	  return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_chef_equipe'));	  	  
	}	
// fin fct-----------------------------------------------------------
	public function searchAction()
     {
   		$em = $this->container->get('doctrine')->getManager();
	    //requete 1 salaries
		$civilites=array();				
		$qb = $em->createQueryBuilder();
		$qb->select('r')
		   ->from('GCNAFNAFBundle:Ressource', 'r')
		   ->where('r.idProf = :profil')
		   ->orderBy('r.nom', 'ASC');			
		$qb->setParameter('profil', 'user');					
		$query = $qb->getQuery();               
		$total = $query->getResult();					  			
		foreach ($total as $ref) {
		$civilites[$ref->getId()]=$ref->getNom();
		}
		//creation des form
		$affecter = new EquipeChef();	
		$formBuilder = $this->createFormBuilder($affecter);
		$formBuilder			
				->add('idSalarie', 'choice', array('choices' => $civilites,'empty_value' => 'Choisissez un salarie', 'label' => 'Recherche:'));
	    $form = $formBuilder->getForm();		
		$request = $this->container->get('request');		
	  if ($request->getMethod() == 'POST') 
	  {
		$form->bind($request);	
		if ($form->isValid()) 
		{		  
		$form_user = $form->getData();
		$ids=$form_user->getIdSalarie();
 
 $myq = $em->createQueryBuilder();
 $myq->select('e.ref,e.dateF,e.projet,e.idSalarie,e.idChef,e.dateD,r.id,r.nom,r.prenom,p.id,p.projet')
	->from('GCNAFNAFBundle:EquipeChef', 'e')
	->from('GCNAFNAFBundle:Ressource', 'r')
	->from('GCNAFNAFBundle:ProjetChef', 'p')	
	->where('e.projet = p.id')	
	->andWhere('e.idSalarie =:ids')
	->andWhere('r.id =:ids')	
	->orderBy('r.nom', 'ASC');		
 $myq->setParameter('ids', $ids);					
 $resu = $myq->getQuery();               
 $total = $resu->getResult();	 				  		  				  		  		  		
	  }	
	}	
   return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:AffecterChef:affectersearch.html.twig',
   array('formSearch' => $form->createView(),'total' =>$total
   ,));			
  }					
		
}//end classe