<?php

namespace GCNAF\NAFBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Session\Session;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use GCNAF\NAFBundle\Entity\Taches;
use GCNAF\NAFBundle\Entity\ProjetChef;
use GCNAF\NAFBundle\Entity\Ressource;
use GCNAF\NAFBundle\Form\TachesType;

/**
 * Taches controller.
 *
 * @Route("/naf")
 */
class TachesController extends Controller
{

    /**
     * Lists all Taches entities.
     *
     * @Route("/", name="naf")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {	
        $em = $this->getDoctrine()->getManager();     		
		$myq = $em->createQueryBuilder();
		$myq->select('t.id as IDtache,t.libelle,t.dated,t.datef,t.projet,p.id,p.projet')
			->from('GCNAFNAFBundle:Taches', 't')
			->from('GCNAFNAFBundle:ProjetChef', 'p')	
			->where('t.projet = p.id')	
			->orderBy('t.dated', 'DESC');			
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
     * Creates a new Taches entity.
     *
     * @Route("/", name="naf_create")
     * @Method("POST")
     * @Template("GCNAFNAFBundle:Taches:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Taches();
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
    * Creates a form to create a Taches entity.
    *
    * @param Taches $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Taches $entity)
    {
        $form = $this->createForm(new TachesType(), $entity, array(
            'action' => $this->generateUrl('naf_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Taches entity.
     *
     * @Route("/new", name="naf_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Taches();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Taches entity.
     *
     * @Route("/{id}", name="naf_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GCNAFNAFBundle:Taches')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Taches entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Taches entity.
     *
     * @Route("/{id}/edit", name="naf_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GCNAFNAFBundle:Taches')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Taches entity.');
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
    * Creates a form to edit a Taches entity.
    *
    * @param Taches $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Taches $entity)
    {
        $form = $this->createForm(new TachesType(), $entity, array(
            'action' => $this->generateUrl('naf_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Taches entity.
     *
     * @Route("/{id}", name="naf_update")
     * @Method("PUT")
     * @Template("GCNAFNAFBundle:Taches:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GCNAFNAFBundle:Taches')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Taches entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('naf_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Taches entity.
     *
     * @Route("/{id}", name="naf_delete")
     * @Method("DELETE")
     */	  
    public function deleteAction($id)
    {      
		$em = $this->getDoctrine()->getManager();
		$entity = $em->getRepository('GCNAFNAFBundle:Taches')->find($id);
		if (!$entity) {
			throw $this->createNotFoundException('Tache introuvable');
		}
		$em->remove($entity);
		$em->flush();	  
		return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_admin_listeTaches'));
    }

    /**
     * Creates a form to delete a Taches entity by id.
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
	
	 public function ajouterAction()
	{
		$request = $this->container->get('request');
		$session = $request->getSession();
		$name   = $session->get('nomAdmin');
		$prenom = $session->get('preAdmin');		
		$em = $this->container->get('doctrine')->getManager();
		$erreur='';	
		
		$projets=array();
		$query1=$this->getDoctrine()->getRepository('GCNAFNAFBundle:ProjetChef')->findAll();
		foreach ($query1 as $ref1) {
		$projets[$ref1->getId()]=$ref1->getProjet();
		}			

		$taches = new Taches();
		$formBuilder = $this->createFormBuilder($taches);
		$formBuilder								
			->add('projet', 'choice', array('choices' => $projets,'empty_value' =>'Choisissez un projet'))
			->add('libelle','text')
            ->add('dated','date', array('format' => 'yyyy-MM-dd','years' => range(2010 ,2030) ))
            ->add('datef','date', array('format' => 'yyyy-MM-dd','years' => range(2010 ,2030) ));		
		$form = $formBuilder->getForm();		
		if ($request->getMethod() == 'POST') 
		{
			$form->bind($request);
			if ($form->isValid()) 
			{
			$form_user = $form->getData();
			$dateD = $form_user->getDated();	
			$dateF = $form_user->getDatef();			
				if ($dateD < $dateF) 
				{	
				$em->persist($taches);
				$em->flush();
				return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_admin_listeTaches'));
				}
				else {$erreur='La date debut de la tache doit etre inferieure a la date de fin de cette tache!';}
			}
		}				
		return $this->container->get('templating')->renderResponse(
		'GCNAFNAFBundle:Taches:new.html.twig',
		array(
		'form' => $form->createView(),	
		'erreur' => $erreur,	
		'name'=> $name ,'prenom'=> $prenom,				
		));							
  }
  
 	 public function modifierAction($id)
	{
		$request = $this->container->get('request');
		$session = $request->getSession();
		$name   = $session->get('nomAdmin');
		$prenom = $session->get('preAdmin');		
		$em = $this->container->get('doctrine')->getManager();
		$erreur='';	
		
		$projets=array();
		$query1=$this->getDoctrine()->getRepository('GCNAFNAFBundle:ProjetChef')->findAll();
		foreach ($query1 as $ref1) {
		$projets[$ref1->getId()]=$ref1->getProjet();
		}			
		$taches = $em->find('GCNAFNAFBundle:Taches', $id);
		$formBuilder = $this->createFormBuilder($taches);
		$formBuilder								
			->add('projet', 'choice', array('choices' => $projets,'empty_value' =>'Choisissez un projet'))
			->add('libelle','text')
            ->add('dated','date', array('format' => 'yyyy-MM-dd','years' => range(2010 ,2030) ))
            ->add('datef','date', array('format' => 'yyyy-MM-dd','years' => range(2010 ,2030) ));		
		$form = $formBuilder->getForm();		
		if ($request->getMethod() == 'POST') 
		{
			$form->bind($request);
			if ($form->isValid()) 
			{
			$form_user = $form->getData();
			$dateD = $form_user->getDated();	
			$dateF = $form_user->getDatef();			
				if ($dateD < $dateF) 
				{	
				$em->persist($taches);
				$em->flush();
				return new RedirectResponse($this->container->get('router')->generate('GCNAFNAFBundle_admin_listeTaches'));
				}
				else {$erreur='La date debut de la tache doit etre inferieure a la date de fin de cette tache!';}
			}
		}				
		return $this->container->get('templating')->renderResponse(
		'GCNAFNAFBundle:Taches:edit.html.twig',
		array(
		'form' => $form->createView(),	
		'erreur' => $erreur,	
		'name'=> $name ,'prenom'=> $prenom,	
		));							
 }
}// fin classe
