<?php

namespace GCNAF\NAFBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use JpGraph\JpGraph;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use \DateTime;

use GCNAF\NAFBundle\Entity\Cra;
use GCNAF\NAFBundle\Entity\Taches;
use GCNAF\NAFBundle\Entity\ProjetChef;
use GCNAF\NAFBundle\Entity\Ressource;

use GCNAF\NAFBundle\Form\CraType;

/**
 * Cra controller.
 *
 * @Route("/naf")
 */
class CraController extends Controller
{
    /**
     * Lists all Cra entities.
     *
     * 
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();     		
		$myq = $em->createQueryBuilder();
		$myq->select('c.id as IdCra,c.salarie,c.tache,c.date,c.duree,c.remarque,t.id as IDtache,t.libelle,t.dated,t.datef,t.projet,p.id as IDProj,p.projet as NomProj,r.id as IDuser,r.nom,r.prenom')
			->from('GCNAFNAFBundle:Cra', 'c')
			->from('GCNAFNAFBundle:ProjetChef', 'p')	
			->from('GCNAFNAFBundle:Ressource', 'r')
			->from('GCNAFNAFBundle:Taches', 't')
			->where('t.projet = p.id')
			->andWhere('t.id = c.tache')
			->andWhere('c.salarie = r.id')	
			->orderBy('c.date', 'DESC');			
		$resu = $myq->getQuery();               
		$entities = $resu->getResult();	
		
		$request = $this->container->get('request');			
		$session = $request->getSession();  		
		$name   = $session->get('nomAdmin');
		$prenom = $session->get('preAdmin');											
		
		return array(
            'entities' => $entities,'name'=> $name ,'prenom'=> $prenom);
    }
    /**
     * Creates a new Cra entity.
     *
     * @Route("/", name="naf_create")
     * @Method("POST")    
     */
    public function createAction(Request $request)
    {
        $entity = new Cra();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('naf_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Cra entity.
    *
    * @param Cra $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Cra $entity)
    {
        $form = $this->createForm(new CraType(), $entity, array(
            'action' => $this->generateUrl('naf_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Cra entity.
     *
     * @Route("/new", name="naf_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Cra();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Cra entity.
     *
     * @Route("/{id}", name="naf_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GCNAFNAFBundle:Cra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cra entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Cra entity.
     *
     * @Route("/{id}/edit", name="naf_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GCNAFNAFBundle:Cra')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cra entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Cra entity.
    *
    * @param Cra $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Cra $entity)
    {
        $form = $this->createForm(new CraType(), $entity, array(
            'action' => $this->generateUrl('naf_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
   
    public function updateAction($id)
    {
      	$msg="";
		$em = $this->container->get('doctrine')->getManager();
		//les projets
		$projets=array();			
		$qb = $em->createQueryBuilder();
		$qb->select('t.projet,p.id,p.projet as nomprojet')
		   ->from('GCNAFNAFBundle:Taches', 't')
		   ->from('GCNAFNAFBundle:ProjetChef', 'p')
		   ->where('t.projet = p.id')
		   ->distinct(true)
		   ->orderBy('p.projet', 'ASC');						
		$query = $qb->getQuery();               
		$total = $query->getResult();		
		$taille2=count($total);
		for($k=0;$k<$taille2;$k++){	$projets[$total[$k]['id']]=$total[$k]['nomprojet']; }							
		$listeprojets = new ProjetChef();	
		$formBuilder  = $this->createFormBuilder($listeprojets);
		$formBuilder->add('id', 'choice', array('choices' => $projets,'empty_value' => 'Choisissez un projet', 'label' => 'Liste des Projets'));           
		$formProjet = $formBuilder->getForm();	
		
		//liste taches
		$taches=array();				
		$qb2 = $em->createQueryBuilder();
		$qb2->select('t')
		   ->from('GCNAFNAFBundle:Taches', 't')
		   ->orderBy('t.libelle', 'ASC');								
		$query2 = $qb2->getQuery();               
		$total2 = $query2->getResult();					  			
		foreach ($total2 as $ref2) {
		$taches[$ref2->getId()]=$ref2->getLibelle();
		}		
		//formulaire du cra
		$cra = $em->find('GCNAFNAFBundle:Cra', $id);   
		$formBuilder2  = $this->createFormBuilder($cra);
		$formBuilder2			
					->add('tache', 'choice', array('choices'=>$taches,'empty_value' => 'Choisissez la tache', 'label' => 'Liste des Taches'))
					->add('date','date', array('label' =>'Choisissez une Date','format' => 'yyyy-MM-dd','years' => range(2014 ,2030)))
					->add('duree','text')
					->add('remarque','textarea');
		$formCra = $formBuilder2->getForm();	

		$request = $this->container->get('request');
		$session = $request->getSession();  													
		$cle     = $session->get('cleuser');
		$salaries   = $em->find('GCNAFNAFBundle:Ressource', $cle);
		$nameuser   = $salaries->getNom();
	    $prenomuser = $salaries->getPrenom();
		$profil     = $salaries->getIdProf();
		
		if ($request->getMethod() == 'POST') 
		{
			$formCra->bind($request);
			if ($formCra->isValid()) 
			{	
			$form_user = $formCra->getData();
			$myduree=$form_user->getDuree();
				if($myduree>1){
				$msg="la duree doit etre inferieur ou egale a un jour !";
				}
				else{
				$s=$cra->setSalarie($cle);
				$em->persist($cra);
				$em->flush();
				return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_user_listeCRA'));	}
			}
		}	
		return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Cra:edit.html.twig',
		array('formProjet' => $formProjet->createView(),'formCra' => $formCra->createView(),
		'name'=> $nameuser ,'prenom'=> $prenomuser,'profil'=>$profil,'id'=>$cle	,'msg'=>$msg,
		));	  
  }
   
    public function deleteAction($id)
    {
   		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('GCNAFNAFBundle:Cra')->find($id);
		if (!$entity) {
			throw $this->createNotFoundException('Tache introuvable');
		}
		$em->remove($entity);
		$em->flush();	  
		return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_user_listeCRA'));   
    }

    /**
     * Creates a form to delete a Cra entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('naf_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
	
	public function homeAction()
    {
		$request = $this->container->get('request');			
		$session = $request->getSession();  		
		$name   = $session->get('nomAdmin');
		$prenom = $session->get('preAdmin');											
		$cle    = $session->get('cleuser');
		
		return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Default:homecra.html.twig',array('name'=> $name ,'prenom'=> $prenom ));		
    }
	
	public function homeFirstAction()
    {
		$em = $this->getDoctrine()->getManager();
		$request = $this->container->get('request');			
		$session = $request->getSession();  													
		$cle     = $session->get('cleuser');
		$salaries   = $em->find('GCNAFNAFBundle:Ressource', $cle);
		$nameuser   = $salaries->getNom();
	    $prenomuser = $salaries->getPrenom();
		$profil     = $salaries->getIdProf();
		
		return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Default:homefirst.html.twig',
		array('name'=> $nameuser ,'prenom'=> $prenomuser,'profil'=>$profil,'id'=>$cle));		
    }
	
	public function statistiquesAction($ids,$idp)
    {
		$request = $this->container->get('request');			
		$session = $request->getSession();  		
		$name   = $session->get('nomAdmin');
		$prenom = $session->get('preAdmin');											
		//***************select taches & durees***
		$em = $this->getDoctrine()->getManager();     		
		$myq = $em->createQueryBuilder();
		$myq->select('c.salarie,c.tache,c.duree,t.id as IDtache,t.libelle,t.projet,p.id as IDProj,p.projet as NomProj,r.id as IDuser,r.nom,r.prenom')
			->from('GCNAFNAFBundle:Cra', 'c')
			->from('GCNAFNAFBundle:ProjetChef', 'p')	
			->from('GCNAFNAFBundle:Ressource', 'r')
			->from('GCNAFNAFBundle:Taches', 't')
			->where('c.salarie = :ids')
			->andWhere('c.salarie = r.id')
			->andWhere('c.tache = t.id')
			->andWhere('t.projet = :idp')	
			->andWhere('t.projet = p.id');	
		$myq->setParameter('ids', $ids);				
		$myq->setParameter('idp', $idp);				
		$resu = $myq->getQuery();               
		$entities = $resu->getResult();					
		//*************************************
		$salaries   = $em->find('GCNAFNAFBundle:Ressource', $ids);
		$projetname = $em->find('GCNAFNAFBundle:ProjetChef',$idp);				
		$nameuser   = $salaries->getNom();
	    $prenomuser = $salaries->getPrenom();
 	    $myprojet   = $projetname->getProjet(); 
		//***************Graph******************
		JpGraph::load();
		JpGraph::module('line');
		$graph = new \Graph(500,300);
		$ydata = array();
		$taille2=count($entities);
	 	for($k=0;$k<$taille2;$k++){
			$ydata[$k]=$entities[$k]['duree'];
		} 
		$xdata = array();
		for($k=0;$k<$taille2;$k++){
			$xdata[$k]=$entities[$k]['tache'];
		}
		$graph->SetScale('intint');
		$lineplot = new \LinePlot($ydata, $xdata);
		$lineplot->SetColor('forestgreen');
		$graph->Add($lineplot);
		$graph->title->Set('Projet: '.$myprojet);
		$graph->xaxis->title->Set('Code (tâches)');
		$graph->yaxis->title->Set('Durée (jour)');
		$lineplot->SetWeight(3);		
		$gdImgHandler = $graph->Stroke(_IMG_HANDLER);
		ob_start();     
		$graph->img->Stream();
		$image_data = ob_get_contents();
		ob_end_clean();
		$image = base64_encode($image_data);		
			
		return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Cra:statistiques.html.twig',
		array('nameuser' => $nameuser,'prenomuser' => $prenomuser,'name'=> $name ,'prenom'=> $prenom,'image'=> $image,'data' => $data,'texte' => $texte,'entities'=>$entities));
    }
//l'ajout et affichage	
	public function suiviCraAction()
    {		
		$msg="";
		$em = $this->container->get('doctrine')->getManager();
		//les projets
		$projets=array();			
		$qb = $em->createQueryBuilder();
		$qb->select('t.projet,p.id,p.projet as nomprojet')
		   ->from('GCNAFNAFBundle:Taches', 't')
		   ->from('GCNAFNAFBundle:ProjetChef', 'p')
		   ->where('t.projet = p.id')
		   ->distinct(true)
		   ->orderBy('p.projet', 'ASC');						
		$query = $qb->getQuery();               
		$total = $query->getResult();		
		$taille2=count($total);
		for($k=0;$k<$taille2;$k++){	$projets[$total[$k]['id']]=$total[$k]['nomprojet']; }							
		$listeprojets = new ProjetChef();	
		$formBuilder  = $this->createFormBuilder($listeprojets);
		$formBuilder->add('id', 'choice', array('choices' => $projets,'empty_value' => 'Choisissez un projet', 'label' => 'Liste des Projets'));           
		$formProjet = $formBuilder->getForm();	
		
		//liste taches
		$taches=array();				
		$qb2 = $em->createQueryBuilder();
		$qb2->select('t')
		   ->from('GCNAFNAFBundle:Taches', 't')
		   ->orderBy('t.libelle', 'ASC');								
		$query2 = $qb2->getQuery();               
		$total2 = $query2->getResult();					  			
		foreach ($total2 as $ref2) {
		$taches[$ref2->getId()]=$ref2->getLibelle();
		}		
		//formulaire du cra
		$cra = new Cra();	
		$formBuilder2  = $this->createFormBuilder($cra);
		$formBuilder2			
					->add('tache', 'choice', array('choices'=>$taches,'empty_value' => 'Choisissez la tache', 'label' => 'Liste des Taches'))
					->add('date','date', array('label' =>'Choisissez une Date','format' => 'yyyy-MM-dd','years' => range(2014 ,2030)))
					->add('duree','text')
					->add('remarque','textarea');
		$formCra = $formBuilder2->getForm();	

		$request = $this->container->get('request');
		$session = $request->getSession();  													
		$cle     = $session->get('cleuser');
		$salaries   = $em->find('GCNAFNAFBundle:Ressource', $cle);
		$nameuser   = $salaries->getNom();
	    $prenomuser = $salaries->getPrenom();
		$profil     = $salaries->getIdProf();
		
		if ($request->getMethod() == 'POST') 
		{
			$formCra->bind($request);
			if ($formCra->isValid()) 
			{	
			$form_user = $formCra->getData();
			$myduree=$form_user->getDuree();
				if($myduree>1){
				$msg="la duree doit etre inferieur ou egale a un jour !";
				}
				else{
				$s=$cra->setSalarie($cle);
				$em->persist($cra);
				$em->flush();
				return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_user_listeCRA'));	}
			}
		}	
		//*********affechage****************** 		
		$myq = $em->createQueryBuilder();
		$myq->select('c.id as IdCra,c.salarie,c.tache,c.date,c.duree,c.remarque,t.id as IDtache,t.libelle,t.dated,t.datef,t.projet,p.id as IDProj,p.projet as NomProj')
			->from('GCNAFNAFBundle:Cra', 'c')
			->from('GCNAFNAFBundle:ProjetChef', 'p')	
			->from('GCNAFNAFBundle:Taches', 't')
			->where('t.projet = p.id')
			->andWhere('t.id = c.tache')
			->andWhere('c.salarie = :cle')	
			->orderBy('c.date', 'DESC');			
		$myq->setParameter('cle', $cle);	
		$resu = $myq->getQuery();               
		$entities = $resu->getResult();												
		
		$datecourant= new \DateTime("now");
		
		return $this->container->get('templating')->renderResponse('GCNAFNAFBundle:Cra:new.html.twig',
		array('formProjet' => $formProjet->createView(),'formCra' => $formCra->createView(),
		'name'=> $nameuser ,'prenom'=> $prenomuser,'profil'=>$profil,'id'=>$cle	,'entities'=>$entities,'msg'=>$msg,'datecourant'=>$datecourant,
		));	
	}
	
}// end classe
