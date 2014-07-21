<?php

namespace Gopro\Vipac\ReporteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\Vipac\ReporteBundle\Entity\Parametro;
use Gopro\Vipac\ReporteBundle\Form\ParametroType;

/**
 * Parametro controller.
 *
 * @Route("/parametro")
 */
class ParametroController extends Controller
{

    /**
     * Lists all Parametro entities.
     *
     * @Route("/", name="gopro_vipac_reporte_parametro")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

            $entities = $em->getRepository('GoproVipacReporteBundle:Parametro')->findAll();


        return array(
            'entities' => $entities,
        );
    }


    /**
     * Creates a new Parametro entity.
     *
     * @Route("/create", name="gopro_vipac_reporte_parametro_create")
     * @Method("POST")
     * @Template("GoproVipacReporteBundle:Parametro:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Parametro();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_reporte_parametro_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Parametro entity.
    *
    * @param Parametro $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Parametro $entity)
    {
        $form = $this->createForm(new ParametroType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_reporte_parametro_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Parametro entity.
     *
     * @Route("/new", name="gopro_vipac_reporte_parametro_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Parametro();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }


    /**
     * Finds and displays a Parametro entity.
     *
     * @Route("/{id}", name="gopro_vipac_reporte_parametro_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacReporteBundle:Parametro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el parametro.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Parametro entity.
     *
     * @Route("/{id}/edit", name="gopro_vipac_reporte_parametro_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacReporteBundle:Parametro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el parametro.');
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
    * Creates a form to edit a Parametro entity.
    *
    * @param Parametro $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Parametro $entity)
    {
        $form = $this->createForm(new ParametroType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_reporte_parametro_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Parametro entity.
     *
     * @Route("/{id}", name="gopro_vipac_reporte_parametro_update")
     * @Method("PUT")
     * @Template("GoproVipacReporteBundle:Parametro:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacReporteBundle:Parametro')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el parametro.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_reporte_parametro_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Parametro entity.
     *
     * @Route("/{id}", name="gopro_vipac_reporte_parametro_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproVipacReporteBundle:Parametro')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar el parametro.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gopro_vipac_reporte_parametro'));
    }

    /**
     * Creates a form to delete a Parametro entity by id.
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
                'action'=>$this->generateUrl('gopro_vipac_reporte_parametro_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
