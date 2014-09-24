<?php

namespace Gopro\Vipac\ProveedorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\Vipac\ProveedorBundle\Entity\Comprobante;
use Gopro\Vipac\ProveedorBundle\Form\ComprobanteType;

use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Action\MassAction;
use APY\DataGridBundle\Grid\Action\DeleteMassAction;
use APY\DataGridBundle\Grid\Action\RowAction;

/**
 * Comprobante controller.
 *
 * @Route("/comprobante")
 */
class ComprobanteController extends Controller
{

    /**
     * Lists all Comprobante entities.
     *
     * @Route("/", name="gopro_vipac_proveedor_comprobante")
     * @Method({"POST","GET"})
     * @Template()
     */
    public function indexAction()
    {
        $source = new Entity('GoproVipacProveedorBundle:Comprobante');

        $grid = $this->get('grid');

        $mostrarAction = new RowAction('mostrar', 'gopro_vipac_proveedor_comprobante_show');
        $mostrarAction->setRouteParameters(array('id'));
        $grid->addRowAction($mostrarAction);

        $editarAction = new RowAction('editar', 'gopro_vipac_proveedor_comprobante_edit');
        $editarAction->setRouteParameters(array('id'));
        $grid->addRowAction($editarAction);

        $grid->setSource($source);

        return $grid->getGridResponse();
    }

    /**
     * Creates a new Comprobante entity.
     *
     * @Route("/create", name="gopro_vipac_proveedor_comprobante_create")
     * @Method("POST")
     * @Template("GoproVipacProveedorBundle:Comprobante:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Comprobante();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_proveedor_comprobante_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Comprobante entity.
    *
    * @param Comprobante $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Comprobante $entity)
    {
        $form = $this->createForm(new ComprobanteType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_proveedor_comprobante_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Comprobante entity.
     *
     * @Route("/new", name="gopro_vipac_proveedor_comprobante_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Comprobante();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }


    /**
     * Finds and displays a Comprobante entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_comprobante_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Comprobante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el comprobante.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Comprobante entity.
     *
     * @Route("/{id}/edit", name="gopro_vipac_proveedor_comprobante_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Comprobante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el comprobante.');
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
    * Creates a form to edit a Comprobante entity.
    *
    * @param Comprobante $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Comprobante $entity)
    {
        $form = $this->createForm(new ComprobanteType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_proveedor_comprobante_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Comprobante entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_comprobante_update")
     * @Method("PUT")
     * @Template("GoproVipacProveedorBundle:Comprobante:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Comprobante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el comprobante.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_proveedor_comprobante_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Comprobante entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_comprobante_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproVipacProveedorBundle:Comprobante')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar el comprobante.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gopro_vipac_proveedor_comprobante'));
    }

    /**
     * Creates a form to delete a Comprobante entity by id.
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
                'action'=>$this->generateUrl('gopro_vipac_proveedor_comprobante_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
