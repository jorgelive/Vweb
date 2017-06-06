<?php

namespace Gopro\Vipac\DbprocesoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\Vipac\DbprocesoBundle\Entity\Docsaptipo;
use Gopro\Vipac\DbprocesoBundle\Form\DocsaptipoType;

/**
 * @Route("/docsaptipo")
 */
class DocsaptipoController extends Controller
{

    /**
     * @Route("/", name="gopro_vipac_dbproceso_docsaptipo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GoproVipacDbprocesoBundle:Docsaptipo')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * @Route("/create", name="gopro_vipac_dbproceso_docsaptipo_create")
     * @Method("POST")
     * @Template("GoproVipacDbprocesoBundle:Docsaptipo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Docsaptipo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_docsaptipo_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * @param Docsaptipo $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Docsaptipo $entity)
    {
        $form = $this->createForm(new DocsaptipoType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_dbproceso_docsaptipo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * @Route("/new", name="gopro_vipac_dbproceso_docsaptipo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Docsaptipo();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/{id}", name="gopro_vipac_dbproceso_docsaptipo_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacDbprocesoBundle:Docsaptipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No existe el tipo de Documento Sap.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/{id}/edit", name="gopro_vipac_dbproceso_docsaptipo_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacDbprocesoBundle:Docsaptipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No existe el tipo de Documento Sap.');
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
    * @param Docsaptipo $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Docsaptipo $entity)
    {
        $form = $this->createForm(new DocsaptipoType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_dbproceso_docsaptipo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * @Route("/{id}", name="gopro_vipac_dbproceso_docsaptipo_update")
     * @Method("PUT")
     * @Template("GoproVipacDbprocesoBundle:Docsaptipo:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacDbprocesoBundle:Docsaptipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No existe el tipo de Documento Sap.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_docsaptipo_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * @Route("/{id}", name="gopro_vipac_dbproceso_docsaptipo_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproVipacDbprocesoBundle:Docsaptipo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No existe el tipo de Documento Sap.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_docsaptipo'));
    }

    /**
     * @param mixed $id el id de la entidad
     * @return \Symfony\Component\Form\Form el formulario
     */
    private function createDeleteForm($id)
    {
        return $this->get('form.factory')->createNamedBuilder(
            'deleteForm',
            'form',
            null,
            [
                'action'=>$this->generateUrl('gopro_vipac_dbproceso_docsaptipo_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
