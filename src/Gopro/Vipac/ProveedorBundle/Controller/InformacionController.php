<?php

namespace Gopro\Vipac\ProveedorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Gopro\MainBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\Vipac\ProveedorBundle\Entity\Informacion;
use Gopro\Vipac\ProveedorBundle\Entity\Informacionadjunto;
use Gopro\Vipac\ProveedorBundle\Form\InformacionType;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Informacion controller.
 *
 * @Route("/informacion")
 */
class InformacionController extends BaseController
{

    /**
     * Lists all Informacion entities.
     *
     * @Route("/", name="gopro_vipac_proveedor_informacion")
     * @Method("GET")
     * @Secure(roles="ROLE_STAFF")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GoproVipacProveedorBundle:Informacion')->findAll();
        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Informacion entity.
     *
     * @Route("/create", name="gopro_vipac_proveedor_informacion_create")
     * @Method("POST")
     * @Secure(roles="ROLE_ADMIN")
     * @Template("GoproVipacProveedorBundle:Informacion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Informacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setContenido($this->container->get('gopro_main_variableproceso')->sanitizeQuery($entity->getContenido()));
            $em = $this->getDoctrine()->getManager();
            $informacionadjuntos = $this->getClauses($form->getData()->getContenido())['informacionadjuntos'];
            foreach($informacionadjuntos as $informacionadjunto){
                $informacionadjuntoEntity=new Informacionadjunto();
                $informacionadjuntoEntity->setNombre($informacionadjunto);
                $informacionadjuntoEntity->setNombremostrar($informacionadjunto);
                $informacionadjuntoEntity->setInformacion($entity);
                $entity->getInformacionadjuntos()->add($informacionadjuntoEntity);
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_proveedor_informacion_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Informacion entity.
    *
    * @param Informacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Informacion $entity)
    {
        $form = $this->createForm(new InformacionType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_proveedor_informacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Informacion entity.
     *
     * @Route("/new", name="gopro_vipac_proveedor_informacion_new")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Informacion();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Informacion entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_informacion_show")
     * @Method({"GET","POST"})
     * @Secure(roles="ROLE_STAFF")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Informacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar el informacionadjunto.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Displays a form to edit an existing Informacion entity.
     *
     * @Route("/{id}/edit", name="gopro_vipac_proveedor_informacion_edit")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Informacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la informacion SQL.');
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
    * Creates a form to edit a Informacion entity.
    *
    * @param Informacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Informacion $entity)
    {
        $form = $this->createForm(new InformacionType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_proveedor_informacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Informacion entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_informacion_update")
     * @Method("PUT")
     * @Secure(roles="ROLE_ADMIN")
     * @Template("GoproVipacProveedorBundle:Informacion:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacProveedorBundle:Informacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la informacion SQL.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setContenido($this->container->get('gopro_main_variableproceso')->sanitizeQuery($entity->getContenido()));
            $informacionadjuntos = $this->getClauses($editForm->getData()->getContenido())['informacionadjuntos'];
            $informacionadjuntosExistentes=$em->getRepository('GoproVipacProveedorBundle:Informacionadjunto')->findBy(['informacion'=>$entity->getId()]);

            foreach($informacionadjuntosExistentes as $informacionadjuntoExistente):
                if(!in_array($informacionadjuntoExistente->getNombre(),$informacionadjuntos)){
                    $em->remove($informacionadjuntoExistente);
                }else{
                    $key = array_search($informacionadjuntoExistente->getNombre(),$informacionadjuntos);
                    if($key!==false){
                        unset($informacionadjuntos[$key]);
                    }
                }
            endforeach;

            foreach($informacionadjuntos as $informacionadjunto){

                $informacionadjuntoEntity=new Informacionadjunto();
                $informacionadjuntoEntity->setNombre($informacionadjunto);
                $informacionadjuntoEntity->setNombremostrar(ucwords(strtolower($informacionadjunto)));
                $informacionadjuntoEntity->setInformacion($entity);
                $entity->getInformacionadjuntos()->add($informacionadjuntoEntity);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_proveedor_informacion_edit', array('id' => $id)));
        }
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Informacion entity.
     *
     * @Route("/{id}", name="gopro_vipac_proveedor_informacion_delete")
     * @Secure(roles="ROLE_ADMIN")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproVipacProveedorBundle:Informacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar la informacion SQL.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('informacion'));
    }

    /**
     * Creates a form to delete a Informacion entity by id.
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
                'action'=>$this->generateUrl('gopro_vipac_proveedor_informacion_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
