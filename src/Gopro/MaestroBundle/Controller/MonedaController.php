<?php

namespace Gopro\MaestroBundle\Controller;

use Gopro\MainBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\MaestroBundle\Entity\Moneda;
use Gopro\MaestroBundle\Form\MonedaType;

/**
 * Moneda controller.
 *
 * @Route("/moneda")
 */
class MonedaController extends BaseController
{

    /**
     * Lists all Moneda entities.
     *
     * @Route("/", name="gopro_maestro_moneda")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GoproMaestroBundle:Moneda')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Moneda entity.
     *
     * @Route("/create", name="gopro_maestro_moneda_create")
     * @Method("POST")
     * @Template("GoproMaestroBundle:Moneda:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Moneda();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()||$request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            if ($request->isXMLHttpRequest()){
                return new JsonResponse([
                    'mensaje'=>['exito'=>'si','titulo'=>'Exito','texto'=>'la moneda se ha agregado'],
                    'moneda'=>[
                        'id'=>$entity->getId(),
                        'nombre'=>$entity->getNombre(),
                        'creado'=>$entity->getCreado(),
                        'procesarRoute'=>$this->get('router')->generate('gopro_'.$entity->getOperacion(), array('monedaEjecutar' => $entity->getId())),
                        'borrarRoute'=>$this->get('router')->generate('gopro_maestro_moneda_delete', array('id' => $entity->getId())),
                    ]
                ]);
            }
            return $this->redirect($this->generateUrl('gopro_maestro_moneda_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Moneda entity.
    *
    * @param Moneda $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Moneda $entity)
    {
        $form = $this->createForm(MonedaType::class, $entity, array(
            'action' => $this->generateUrl('gopro_maestro_moneda_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Moneda entity.
     *
     * @Route("/new", name="gopro_maestro_moneda_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Moneda();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Moneda entity.
     *
     * @Route("/{id}", name="gopro_maestro_moneda_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproMaestroBundle:Moneda')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se encuentra o no tiene permiso sobre la moneda.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Moneda entity.
     *
     * @Route("/{id}/edit", name="gopro_maestro_moneda_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproMaestroBundle:Moneda')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se encuentra o no tiene permiso sobre el moneda.');
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
    * Creates a form to edit a Moneda entity.
    *
    * @param Moneda $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Moneda $entity)
    {
        $form = $this->createForm(MonedaType::class, $entity, array(
            'action' => $this->generateUrl('gopro_maestro_moneda_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Moneda entity.
     *
     * @Route("/{id}", name="gopro_maestro_moneda_update")
     * @Method("PUT")
     * @Template("GoproMaestroBundle:Moneda:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproMaestroBundle:Moneda')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se encuentra o no tiene permiso sobre la moneda.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_maestro_moneda_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Moneda entity.
     *
     * @Route("/{id}", name="gopro_maestro_moneda_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()||$request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproMaestroBundle:Moneda')->findOneBy(['id'=>$id,'user'=>$this->getUser()]);

            if(!$entity&&$request->isXMLHttpRequest()){
                return new JsonResponse(['mensaje'=>['exito'=>'no','titulo'=>'Fallo','texto'=>'No existe la moneda']]);
            }elseif (!$entity) {
                throw $this->createNotFoundException('No se encuentra o no tiene permiso sobre el moneda.');
            }

            $em->remove($entity);
            $em->flush();

            if ($request->isXMLHttpRequest()){
                return new JsonResponse(['mensaje'=>['exito'=>'si','titulo'=>'Exito','texto'=>'Se ha eliminado la moneda']]);
            }
        }
        return $this->redirect($this->generateUrl('gopro_maestro_moneda'));
    }

    /**
     * Creates a form to delete a Moneda entity by id.
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
                'action'=>$this->generateUrl('gopro_maestro_moneda_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
