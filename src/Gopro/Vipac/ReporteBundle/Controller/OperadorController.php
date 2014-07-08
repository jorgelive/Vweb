<?php

namespace Gopro\Vipac\ReporteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\Vipac\ReporteBundle\Entity\Operador;
use Gopro\Vipac\ReporteBundle\Form\OperadorType;

/**
 * Operador controller.
 *
 * @Route("/operador")
 */
class OperadorController extends Controller
{

    /**
     * Lists all Operador entities.
     *
     * @Route("/", name="gopro_vipac_reporte_operador")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GoproVipacReporteBundle:Operador')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Operador entity.
     *
     * @Route("/create", name="gopro_vipac_reporte_operador_create")
     * @Method("POST")
     * @Template("GoproVipacReporteBundle:Operador:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Operador();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_reporte_operador_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Operador entity.
    *
    * @param Operador $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Operador $entity)
    {
        $form = $this->createForm(new OperadorType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_reporte_operador_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Operador entity.
     *
     * @Route("/new", name="gopro_vipac_reporte_operador_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Operador();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Operador entity.
     *
     * @Route("/{id}", name="gopro_vipac_reporte_operador_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacReporteBundle:Operador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el operador de comparación.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Operador entity.
     *
     * @Route("/{id}/edit", name="gopro_vipac_reporte_operador_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacReporteBundle:Operador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el operador de comparación.');
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
    * Creates a form to edit a Operador entity.
    *
    * @param Operador $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Operador $entity)
    {
        $form = $this->createForm(new OperadorType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_reporte_operador_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Operador entity.
     *
     * @Route("/{id}", name="gopro_vipac_reporte_operador_update")
     * @Method("PUT")
     * @Template("GoproVipacReporteBundle:Operador:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacReporteBundle:Operador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el operador de comparación.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_reporte_operador_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Operador entity.
     *
     * @Route("/{id}", name="gopro_vipac_reporte_operador_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproVipacReporteBundle:Operador')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar el operador de comparación.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gopro_vipac_reporte_operador'));
    }

    /**
     * Creates a form to delete a Operador entity by id.
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
                'action'=>$this->generateUrl('gopro_vipac_reporte_operador_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
