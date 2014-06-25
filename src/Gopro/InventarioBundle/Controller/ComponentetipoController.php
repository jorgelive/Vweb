<?php

namespace Gopro\InventarioBundle\Controller;

use Gopro\Vipac\MainBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\InventarioBundle\Entity\Componentetipo;
use Gopro\InventarioBundle\Form\ComponentetipoType;

/**
 * Componentetipo controller.
 *
 * @Route("/componentetipo")
 */
class ComponentetipoController extends BaseController
{

    /**
     * Lists all Componentetipo entities.
     *
     * @Route("/", name="gopro_inventario_componentetipo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GoproInventarioBundle:Componentetipo')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Componentetipo entity.
     *
     * @Route("/", name="gopro_inventario_componentetipo_create")
     * @Method("POST")
     * @Template("GoproInventarioBundle:Componentetipo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Componentetipo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_inventario_componentetipo_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Componentetipo entity.
    *
    * @param Componentetipo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Componentetipo $entity)
    {
        $form = $this->createForm(new ComponentetipoType(), $entity, array(
            'action' => $this->generateUrl('gopro_inventario_componentetipo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Componentetipo entity.
     *
     * @Route("/new", name="gopro_inventario_componentetipo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Componentetipo();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Componentetipo entity.
     *
     * @Route("/{id}", name="gopro_inventario_componentetipo_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Componentetipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Componentetipo.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Componentetipo entity.
     *
     * @Route("/{id}/edit", name="gopro_inventario_componentetipo_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Componentetipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Componentetipo.');
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
    * Creates a form to edit a Componentetipo entity.
    *
    * @param Componentetipo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Componentetipo $entity)
    {
        $form = $this->createForm(new ComponentetipoType(), $entity, array(
            'action' => $this->generateUrl('gopro_inventario_componentetipo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Componentetipo entity.
     *
     * @Route("/{id}", name="gopro_inventario_componentetipo_update")
     * @Method("PUT")
     * @Template("GoproInventarioBundle:Componentetipo:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Componentetipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Componentetipo.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_inventario_componentetipo_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Componentetipo entity.
     *
     * @Route("/{id}", name="gopro_inventario_componentetipo_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproInventarioBundle:Componentetipo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar la entidad Componentetipo.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gopro_inventario_componentetipo'));
    }

    /**
     * Creates a form to delete a Componentetipo entity by id.
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
                'action'=>$this->generateUrl('gopro_inventario_componentetipo_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
