<?php

namespace Gopro\Vipac\DbprocesoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\Vipac\DbprocesoBundle\Entity\Doccptipo;
use Gopro\Vipac\DbprocesoBundle\Form\DoccptipoType;

/**
 * @Route("/doccptipo")
 */
class DoccptipoController extends Controller
{

    /**
     * @Route("/", name="gopro_vipac_dbproceso_doccptipo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GoproVipacDbprocesoBundle:Doccptipo')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * @Route("/create", name="gopro_vipac_dbproceso_doccptipo_create")
     * @Method("POST")
     * @Template("GoproVipacDbprocesoBundle:Doccptipo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Doccptipo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_doccptipo_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * @param Doccptipo $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Doccptipo $entity)
    {
        $form = $this->createForm(DoccptipoType::class, $entity, array(
            'action' => $this->generateUrl('gopro_vipac_dbproceso_doccptipo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * @Route("/new", name="gopro_vipac_dbproceso_doccptipo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Doccptipo();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/{id}", name="gopro_vipac_dbproceso_doccptipo_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacDbprocesoBundle:Doccptipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No existe el tipo de Documento CP.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/{id}/edit", name="gopro_vipac_dbproceso_doccptipo_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacDbprocesoBundle:Doccptipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No existe el tipo de Documento CP.');
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
    * @param Doccptipo $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Doccptipo $entity)
    {
        $form = $this->createForm(DoccptipoType::class, $entity, array(
            'action' => $this->generateUrl('gopro_vipac_dbproceso_doccptipo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * @Route("/{id}", name="gopro_vipac_dbproceso_doccptipo_update")
     * @Method("PUT")
     * @Template("GoproVipacDbprocesoBundle:Doccptipo:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacDbprocesoBundle:Doccptipo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No existe el tipo de Documento CP.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_doccptipo_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * @Route("/{id}", name="gopro_vipac_dbproceso_doccptipo_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproVipacDbprocesoBundle:Doccptipo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No existe el tipo de Documento CP.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_doccptipo'));
    }

    /**
     * @param mixed $id el id de la entidad
     * @return \Symfony\Component\Form\Form el formulario
     */
    private function createDeleteForm($id)
    {
        return $this->get('form.factory')->createNamedBuilder(
            'deleteForm',
            'form',
            null,
            [
                'action'=>$this->generateUrl('gopro_vipac_dbproceso_doccptipo_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
