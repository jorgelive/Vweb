<?php

namespace Gopro\InventarioBundle\Controller;

use Gopro\MainBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\InventarioBundle\Entity\Caracteristica;
use Gopro\InventarioBundle\Form\CaracteristicaType;

use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Action\MassAction;
use APY\DataGridBundle\Grid\Action\DeleteMassAction;
use APY\DataGridBundle\Grid\Action\RowAction;

/**
 * Caracteristica controller.
 *
 * @Route("/caracteristica")
 */
class CaracteristicaController extends BaseController
{

    /**
     * Lists all Caracteristica entities.
     *
     * @Route("/", name="gopro_inventario_caracteristica")
     * @Method({"POST","GET"})
     * @Template()
     */
    public function indexAction()
    {
        $source = new Entity('GoproInventarioBundle:Caracteristica');

        $grid = $this->get('grid');

        $mostrarAction = new RowAction('mostrar', 'gopro_inventario_caracteristica_show');
        $mostrarAction->setRouteParameters(array('id'));
        $grid->addRowAction($mostrarAction);

        $editarAction = new RowAction('editar', 'gopro_inventario_caracteristica_edit');
        $editarAction->setRouteParameters(array('id'));
        $grid->addRowAction($editarAction);

        $mostrarItemAction = new RowAction('mostrar item', 'gopro_inventario_item_show');
        $mostrarItemAction->setRouteParameters(array('componente.item.id'));
        $mostrarItemAction->setRouteParametersMapping(array('componente.item.id' => 'id'));
        $grid->addRowAction($mostrarItemAction);

        $editarItemAction = new RowAction('editar item', 'gopro_inventario_item_edit');
        $editarItemAction->setRouteParameters(array('componente.item.id'));
        $editarItemAction->setRouteParametersMapping(array('componente.item.id' => 'id'));
        $grid->addRowAction($editarItemAction);

        $grid->setSource($source);

        return $grid->getGridResponse();

        /*$em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GoproInventarioBundle:Caracteristica')->findAll();

        return array(
            'entities' => $entities,
        );*/
    }
    /**
     * Creates a new Caracteristica entity.
     *
     * @Route("/create", name="gopro_inventario_caracteristica_create")
     * @Method("POST")
     * @Template("GoproInventarioBundle:Caracteristica:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Caracteristica();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_inventario_caracteristica_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Caracteristica entity.
    *
    * @param Caracteristica $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Caracteristica $entity)
    {
        $form = $this->createForm(new CaracteristicaType(), $entity, array(
            'action' => $this->generateUrl('gopro_inventario_caracteristica_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Caracteristica entity.
     *
     * @Route("/new/{componente_id}", requirements={"componente_id" = "\d+"}, name="gopro_inventario_caracteristica_new", defaults={"componente_id" = null})
     * @Method("GET")
     * @Template()
     */
    public function newAction($componente_id)
    {
        $entity = new Caracteristica();

        if(!empty($componente_id)){
            $em = $this->getDoctrine()->getManager();
            $componente = $em->getRepository('GoproInventarioBundle:Componente')->find($componente_id);
            if(is_object($componente)){
                $entity->setComponente($componente);
            }

        }


        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Caracteristica entity.
     *
     * @Route("/{id}", name="gopro_inventario_caracteristica_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Caracteristica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Caracteristica.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Caracteristica entity.
     *
     * @Route("/{id}/edit", name="gopro_inventario_caracteristica_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Caracteristica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Caracteristica.');
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
    * Creates a form to edit a Caracteristica entity.
    *
    * @param Caracteristica $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Caracteristica $entity)
    {
        $form = $this->createForm(new CaracteristicaType(), $entity, array(
            'action' => $this->generateUrl('gopro_inventario_caracteristica_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Caracteristica entity.
     *
     * @Route("/{id}", name="gopro_inventario_caracteristica_update")
     * @Method("PUT")
     * @Template("GoproInventarioBundle:Caracteristica:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Caracteristica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Caracteristica.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_inventario_caracteristica_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Caracteristica entity.
     *
     * @Route("/{id}", name="gopro_inventario_caracteristica_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproInventarioBundle:Caracteristica')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar la entidad Caracteristica.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gopro_inventario_caracteristica'));
    }

    /**
     * Creates a form to delete a Caracteristica entity by id.
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
                'action'=>$this->generateUrl('gopro_inventario_caracteristica_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
