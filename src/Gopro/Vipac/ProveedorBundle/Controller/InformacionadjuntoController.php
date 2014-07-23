<?php

namespace Gopro\Vipac\ProveedorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\Vipac\ProveedorBundle\Entity\Informacionadjunto;
use Gopro\Vipac\ProveedorBundle\Form\InformacionadjuntoType;

/**
 * Informacionadjunto controller.
 *
 * @Route("/informacionadjunto")
 */
class InformacionadjuntoController extends Controller
{

    /**
     * Lists all Informacionadjunto entities.
     *
     * @Route("/", name="gopro_vipac_proveedor_informacionadjunto")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GoproVipacProveedorBundle:Informacionadjunto')->findAll();

        return array(
            'entities' => $entities,
        );
    }


    /**
     * Creates a new Informacionadjunto entity.
     *
     * @Route("/create", name="gopro_vipac_proveedor_informacionadjunto_create")
     * @Method("POST")
     * @Template("GoproVipacProveedorBundle:Informacionadjunto:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Informacionadjunto();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_proveedor_informacionadjunto_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Informacionadjunto entity.
    *
    * @param Informacionadjunto $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Informacionadjunto $entity)
    {
        $form = $this->createForm(new InformacionadjuntoType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_proveedor_informacionadjunto_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Informacionadjunto entity.
     *
     * @Route("/new", name="gopro_vipac_proveedor_informacionadjunto_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Informacionadjunto();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }


    /**
     * Finds and displays a Informacionadjunto entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_informacionadjunto_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Informacionadjunto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el informacionadjunto.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Informacionadjunto entity.
     *
     * @Route("/{id}/edit", name="gopro_vipac_proveedor_informacionadjunto_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Informacionadjunto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el informacionadjunto.');
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
    * Creates a form to edit a Informacionadjunto entity.
    *
    * @param Informacionadjunto $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Informacionadjunto $entity)
    {
        $form = $this->createForm(new InformacionadjuntoType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_proveedor_informacionadjunto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Informacionadjunto entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_informacionadjunto_update")
     * @Method("PUT")
     * @Template("GoproVipacProveedorBundle:Informacionadjunto:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Informacionadjunto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el informacionadjunto.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_proveedor_informacionadjunto_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Informacionadjunto entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_informacionadjunto_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproVipacProveedorBundle:Informacionadjunto')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar el informacionadjunto.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gopro_vipac_proveedor_informacionadjunto'));
    }

    /**
     * Creates a form to delete a Informacionadjunto entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->get('form.factory')->createNamedBuilder(
            'deleteForm',
            'form',
            null,
            [
                'action'=>$this->generateUrl('gopro_vipac_proveedor_informacionadjunto_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
