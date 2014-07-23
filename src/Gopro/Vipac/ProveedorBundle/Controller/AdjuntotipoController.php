<?php

namespace Gopro\Vipac\ProveedorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\Vipac\ProveedorBundle\Entity\Adjuntotipo;
use Gopro\Vipac\ProveedorBundle\Form\AdjuntotipoType;

/**
 * Adjuntotipo controller.
 *
 * @Route("/adjuntotipo")
 */
class AdjuntotipoController extends Controller
{

    /**
     * Lists all Adjuntotipo entities.
     *
     * @Route("/", name="gopro_vipac_proveedor_adjuntotipo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GoproVipacProveedorBundle:Adjuntotipo')->findAll();

        return array(
            'entities' => $entities,
        );
    }


    /**
     * Creates a new Adjuntotipo entity.
     *
     * @Route("/create", name="gopro_vipac_proveedor_adjuntotipo_create")
     * @Method("POST")
     * @Template("GoproVipacProveedorBundle:Adjuntotipo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Adjuntotipo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_proveedor_adjuntotipo_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Adjuntotipo entity.
    *
    * @param Adjuntotipo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Adjuntotipo $entity)
    {
        $form = $this->createForm(new AdjuntotipoType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_proveedor_adjuntotipo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Adjuntotipo entity.
     *
     * @Route("/new", name="gopro_vipac_proveedor_adjuntotipo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Adjuntotipo();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }


    /**
     * Finds and displays a Adjuntotipo entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_adjuntotipo_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Adjuntotipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el adjuntotipo.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Adjuntotipo entity.
     *
     * @Route("/{id}/edit", name="gopro_vipac_proveedor_adjuntotipo_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Adjuntotipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el adjuntotipo.');
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
    * Creates a form to edit a Adjuntotipo entity.
    *
    * @param Adjuntotipo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Adjuntotipo $entity)
    {
        $form = $this->createForm(new AdjuntotipoType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_proveedor_adjuntotipo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Adjuntotipo entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_adjuntotipo_update")
     * @Method("PUT")
     * @Template("GoproVipacProveedorBundle:Adjuntotipo:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Adjuntotipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el adjuntotipo.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_proveedor_adjuntotipo_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Adjuntotipo entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_adjuntotipo_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproVipacProveedorBundle:Adjuntotipo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar el adjuntotipo.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gopro_vipac_proveedor_adjuntotipo'));
    }

    /**
     * Creates a form to delete a Adjuntotipo entity by id.
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
                'action'=>$this->generateUrl('gopro_vipac_proveedor_adjuntotipo_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
