<?php

namespace Gopro\MainBundle\Controller;

use Gopro\MainBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\MainBundle\Entity\Archivo;
use Gopro\MainBundle\Form\ArchivoType;

/**
 * Archivo controller.
 *
 * @Route("/archivo")
 */
class ArchivoController extends BaseController
{

    /**
     * Lists all Archivo entities.
     *
     * @Route("/", name="gopro_main_archivo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GoproMainBundle:Archivo')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Archivo entity.
     *
     * @Route("/", name="gopro_main_archivo_create")
     * @Method("POST")
     * @Template("GoproMainBundle:Archivo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Archivo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()||$request->isXMLHttpRequest()) {
            $entity->setUsuario($this->getUserName());
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();
            if ($request->isXMLHttpRequest()){
                return new JsonResponse([
                    'mensaje'=>['exito'=>'si','titulo'=>'Exito','texto'=>'El archivo se ha agregado'],
                    'archivo'=>[
                        'id'=>$entity->getId(),
                        'nombre'=>$entity->getNombre(),
                        'creado'=>$entity->getCreado(),
                        'procesarRoute'=>$this->get('router')->generate('gopro_'.$entity->getOperacion(), array('archivoEjecutar' => $entity->getId())),
                        'borrarRoute'=>$this->get('router')->generate('gopro_main_archivo_delete', array('id' => $entity->getId())),
                    ]
                ]);
            }
            return $this->redirect($this->generateUrl('gopro_main_archivo_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Archivo entity.
    *
    * @param Archivo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Archivo $entity)
    {
        $form = $this->createForm(new ArchivoType(), $entity, array(
            'action' => $this->generateUrl('gopro_main_archivo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Archivo entity.
     *
     * @Route("/new", name="gopro_main_archivo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Archivo();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Archivo entity.
     *
     * @Route("/{id}", name="gopro_main_archivo_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproMainBundle:Archivo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se encuentra o no tiene permiso sobre el archivo.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Archivo entity.
     *
     * @Route("/{id}/edit", name="gopro_main_archivo_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproMainBundle:Archivo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se encuentra o no tiene permiso sobre el archivo.');
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
    * Creates a form to edit a Archivo entity.
    *
    * @param Archivo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Archivo $entity)
    {
        $form = $this->createForm(new ArchivoType(), $entity, array(
            'action' => $this->generateUrl('gopro_main_archivo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Archivo entity.
     *
     * @Route("/{id}", name="gopro_main_archivo_update")
     * @Method("PUT")
     * @Template("GoproMainBundle:Archivo:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproMainBundle:Archivo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se encuentra o no tiene permiso sobre el archivo.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_main_archivo_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Archivo entity.
     *
     * @Route("/{id}", name="gopro_main_archivo_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()||$request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproMainBundle:Archivo')->findOneBy(['id'=>$id,'usuario'=>$this->getUserName()]);

            if(!$entity&&$request->isXMLHttpRequest()){
                return new JsonResponse(['mensaje'=>['exito'=>'no','titulo'=>'Fallo','texto'=>'No existe el archivo']]);
            }elseif (!$entity) {
                throw $this->createNotFoundException('No se encuentra o no tiene permiso sobre el archivo.');
            }

            $em->remove($entity);
            $em->flush();

            if ($request->isXMLHttpRequest()){
                return new JsonResponse(['mensaje'=>['exito'=>'si','titulo'=>'Exito','texto'=>'Se ha eliminado el archivo']]);
            }
        }
        return $this->redirect($this->generateUrl('gopro_main_archivo'));
    }

    /**
     * Creates a form to delete a Archivo entity by id.
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
                'action'=>$this->generateUrl('gopro_main_archivo_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
