<?php

namespace Gopro\InventarioBundle\Controller;

use Gopro\MainBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\InventarioBundle\Entity\Itemtipo;
use Gopro\InventarioBundle\Form\ItemtipoType;

/**
 * Itemtipo controller.
 *
 * @Route("/itemtipo")
 */
class ItemtipoController extends BaseController
{

    /**
     * Lists all Itemtipo entities.
     *
     * @Route("/", name="gopro_inventario_itemtipo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GoproInventarioBundle:Itemtipo')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Itemtipo entity.
     *
     * @Route("/create", name="gopro_inventario_itemtipo_create")
     * @Method("POST")
     * @Template("GoproInventarioBundle:Itemtipo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Itemtipo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_inventario_itemtipo_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Itemtipo entity.
    *
    * @param Itemtipo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Itemtipo $entity)
    {
        $form = $this->createForm(new ItemtipoType(), $entity, array(
            'action' => $this->generateUrl('gopro_inventario_itemtipo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Itemtipo entity.
     *
     * @Route("/new", name="gopro_inventario_itemtipo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Itemtipo();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Itemtipo entity.
     *
     * @Route("/{id}", name="gopro_inventario_itemtipo_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Itemtipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Itemtipo.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Itemtipo entity.
     *
     * @Route("/{id}/edit", name="gopro_inventario_itemtipo_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Itemtipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Itemtipo.');
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
    * Creates a form to edit a Itemtipo entity.
    *
    * @param Itemtipo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Itemtipo $entity)
    {
        $form = $this->createForm(new ItemtipoType(), $entity, array(
            'action' => $this->generateUrl('gopro_inventario_itemtipo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Itemtipo entity.
     *
     * @Route("/{id}", name="gopro_inventario_itemtipo_update")
     * @Method("PUT")
     * @Template("GoproInventarioBundle:Itemtipo:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Itemtipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Itemtipo.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_inventario_itemtipo_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Itemtipo entity.
     *
     * @Route("/{id}", name="gopro_inventario_itemtipo_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproInventarioBundle:Itemtipo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar la entidad Itemtipo.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gopro_inventario_itemtipo'));
    }

    /**
     * Creates a form to delete a Itemtipo entity by id.
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
                'action'=>$this->generateUrl('gopro_inventario_itemtipo_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
