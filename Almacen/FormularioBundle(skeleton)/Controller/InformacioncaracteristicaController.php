<?php

namespace Gopro\Vipac\ProveedorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\Vipac\ProveedorBundle\Entity\Informacioncaracteristica;
use Gopro\Vipac\ProveedorBundle\Form\InformacioncaracteristicaType;

/**
 * Informacioncaracteristica controller.
 *
 * @Route("/informacioncaracteristica")
 */
class InformacioncaracteristicaController extends Controller
{

    /**
     * Lists all Informacioncaracteristica entities.
     *
     * @Route("/", name="gopro_vipac_proveedor_informacioncaracteristica")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GoproVipacProveedorBundle:Informacioncaracteristica')->findAll();

        return array(
            'entities' => $entities,
        );
    }


    /**
     * Creates a new Informacioncaracteristica entity.
     *
     * @Route("/create", name="gopro_vipac_proveedor_informacioncaracteristica_create")
     * @Method("POST")
     * @Template("GoproVipacProveedorBundle:Informacioncaracteristica:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Informacioncaracteristica();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_proveedor_informacioncaracteristica_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Informacioncaracteristica entity.
    *
    * @param Informacioncaracteristica $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Informacioncaracteristica $entity)
    {
        $form = $this->createForm(new InformacioncaracteristicaType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_proveedor_informacioncaracteristica_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Informacioncaracteristica entity.
     *
     * @Route("/new", name="gopro_vipac_proveedor_informacioncaracteristica_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Informacioncaracteristica();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }


    /**
     * Finds and displays a Informacioncaracteristica entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_informacioncaracteristica_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Informacioncaracteristica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el informacioncaracteristica.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Informacioncaracteristica entity.
     *
     * @Route("/{id}/edit", name="gopro_vipac_proveedor_informacioncaracteristica_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Informacioncaracteristica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el informacioncaracteristica.');
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
    * Creates a form to edit a Informacioncaracteristica entity.
    *
    * @param Informacioncaracteristica $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Informacioncaracteristica $entity)
    {
        $form = $this->createForm(new InformacioncaracteristicaType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_proveedor_informacioncaracteristica_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Informacioncaracteristica entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_informacioncaracteristica_update")
     * @Method("PUT")
     * @Template("GoproVipacProveedorBundle:Informacioncaracteristica:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Informacioncaracteristica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el informacioncaracteristica.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_proveedor_informacioncaracteristica_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Informacioncaracteristica entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_informacioncaracteristica_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproVipacProveedorBundle:Informacioncaracteristica')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar el informacioncaracteristica.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gopro_vipac_proveedor_informacioncaracteristica'));
    }

    /**
     * Creates a form to delete a Informacioncaracteristica entity by id.
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
                'action'=>$this->generateUrl('gopro_vipac_proveedor_informacioncaracteristica_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
