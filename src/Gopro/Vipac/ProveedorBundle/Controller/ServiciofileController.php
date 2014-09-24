<?php

namespace Gopro\Vipac\ProveedorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\Vipac\ProveedorBundle\Entity\Serviciofile;
use Gopro\Vipac\ProveedorBundle\Form\ServiciofileType;

use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Action\MassAction;
use APY\DataGridBundle\Grid\Action\DeleteMassAction;
use APY\DataGridBundle\Grid\Action\RowAction;

/**
 * Serviciofile controller.
 *
 * @Route("/serviciofile")
 */
class ServiciofileController extends Controller
{

    /**
     * Lists all Serviciofile entities.
     *
     * @Route("/", name="gopro_vipac_proveedor_serviciofile")
     * @Method({"POST","GET"})
     * @Template()
     */
    public function indexAction()
    {
        $source = new Entity('GoproVipacProveedorBundle:Serviciofile');

        $grid = $this->get('grid');

        $mostrarAction = new RowAction('mostrar', 'gopro_vipac_proveedor_serviciofile_show');
        $mostrarAction->setRouteParameters(array('id'));
        $grid->addRowAction($mostrarAction);

        $editarAction = new RowAction('editar', 'gopro_vipac_proveedor_serviciofile_edit');
        $editarAction->setRouteParameters(array('id'));
        $grid->addRowAction($editarAction);

        $grid->setSource($source);

        return $grid->getGridResponse();
    }


    /**
     * Creates a new Serviciofile entity.
     *
     * @Route("/create", name="gopro_vipac_proveedor_serviciofile_create")
     * @Method("POST")
     * @Template("GoproVipacProveedorBundle:Serviciofile:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Serviciofile();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_proveedor_serviciofile_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Serviciofile entity.
    *
    * @param Serviciofile $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Serviciofile $entity)
    {
        $form = $this->createForm(new ServiciofileType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_proveedor_serviciofile_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Serviciofile entity.
     *
     * @Route("/new", name="gopro_vipac_proveedor_serviciofile_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Serviciofile();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }


    /**
     * Finds and displays a Serviciofile entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_serviciofile_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Serviciofile')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el file.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Serviciofile entity.
     *
     * @Route("/{id}/edit", name="gopro_vipac_proveedor_serviciofile_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Serviciofile')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el file.');
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
    * Creates a form to edit a Serviciofile entity.
    *
    * @param Serviciofile $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Serviciofile $entity)
    {
        $form = $this->createForm(new ServiciofileType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_proveedor_serviciofile_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Serviciofile entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_serviciofile_update")
     * @Method("PUT")
     * @Template("GoproVipacProveedorBundle:Serviciofile:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Serviciofile')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el file.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_proveedor_serviciofile_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Serviciofile entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_serviciofile_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproVipacProveedorBundle:Serviciofile')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar el file.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gopro_vipac_proveedor_serviciofile'));
    }

    /**
     * Creates a form to delete a Serviciofile entity by id.
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
                'action'=>$this->generateUrl('gopro_vipac_proveedor_serviciofile_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
