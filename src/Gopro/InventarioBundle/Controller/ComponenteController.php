<?php

namespace Gopro\InventarioBundle\Controller;

use Gopro\MainBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\InventarioBundle\Entity\Componente;
use Gopro\InventarioBundle\Form\ComponenteType;

use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Action\MassAction;
use APY\DataGridBundle\Grid\Action\DeleteMassAction;
use APY\DataGridBundle\Grid\Action\RowAction;

/**
 * Componente controller.
 *
 * @Route("/componente")
 */
class ComponenteController extends BaseController
{

    /**
     * Lists all Componente entities.
     *
     * @Route("/", name="gopro_inventario_componente")
     * @Method({"POST","GET"})
     * @Template()
     */
    public function indexAction()
    {
        $source = new Entity('GoproInventarioBundle:Componente');

        $grid = $this->get('grid');

        $mostrarAction = new RowAction('mostrar', 'gopro_inventario_componente_show');
        $mostrarAction->setRouteParameters(array('id'));
        $grid->addRowAction($mostrarAction);

        $editarAction = new RowAction('editar', 'gopro_inventario_componente_edit');
        $editarAction->setRouteParameters(array('id'));
        $grid->addRowAction($editarAction);

        $mostrarItemAction = new RowAction('mostrar item', 'gopro_inventario_item_show');
        $mostrarItemAction->setRouteParameters(array('item.id'));
        $mostrarItemAction->setRouteParametersMapping(array('item.id' => 'id'));
        $grid->addRowAction($mostrarItemAction);

        $editarItemAction = new RowAction('editar item', 'gopro_inventario_item_edit');
        $editarItemAction->setRouteParameters(array('item.id'));
        $editarItemAction->setRouteParametersMapping(array('item.id' => 'id'));
        $grid->addRowAction($editarItemAction);

        $grid->setSource($source);

        return $grid->getGridResponse();

    }
    /**
     * Creates a new Componente entity.
     *
     * @Route("/create", name="gopro_inventario_componente_create")
     * @Method("POST")
     * @Template("GoproInventarioBundle:Componente:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Componente();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_inventario_componente_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Componente entity.
    *
    * @param Componente $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Componente $entity)
    {
        $form = $this->createForm(new ComponenteType(), $entity, array(
            'action' => $this->generateUrl('gopro_inventario_componente_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Componente entity.
     *
     * @Route("/new/{item_id}", requirements={"item_id" = "\d+"}, name="gopro_inventario_componente_new", defaults={"item_id" = null} )
     * @Method("GET")
     * @Template()
     */
    public function newAction($item_id)
    {
        $entity = new Componente();
        if(!empty($item_id)){
            $em = $this->getDoctrine()->getManager();
            $item = $em->getRepository('GoproInventarioBundle:Item')->find($item_id);
            if(is_object($item)){
                $entity->setItem($item);
            }

        }

        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Componente entity.
     *
     * @Route("/{id}/", name="gopro_inventario_componente_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Componente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Componente.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Componente entity.
     *
     * @Route("/{id}/edit", name="gopro_inventario_componente_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Componente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Componente.');
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
    * Creates a form to edit a Componente entity.
    *
    * @param Componente $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Componente $entity)
    {
        $form = $this->createForm(new ComponenteType(), $entity, array(
            'action' => $this->generateUrl('gopro_inventario_componente_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Componente entity.
     *
     * @Route("/{id}", name="gopro_inventario_componente_update")
     * @Method("PUT")
     * @Template("GoproInventarioBundle:Componente:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Componente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Componente.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_inventario_componente_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Componente entity.
     *
     * @Route("/{id}", name="gopro_inventario_componente_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproInventarioBundle:Componente')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar la entidad Componente.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gopro_inventario_componente'));
    }

    /**
     * Creates a form to delete a Componente entity by id.
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
                'action'=>$this->generateUrl('gopro_inventario_componente_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
