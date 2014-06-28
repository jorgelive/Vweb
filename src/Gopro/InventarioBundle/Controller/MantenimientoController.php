<?php

namespace Gopro\InventarioBundle\Controller;

use Gopro\Vipac\MainBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\InventarioBundle\Entity\Mantenimiento;
use Gopro\InventarioBundle\Form\MantenimientoType;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Mantenimiento controller.
 *
 * @Route("/mantenimiento")
 */
class MantenimientoController extends BaseController
{

    /**
     * Lists all Mantenimiento entities.
     *
     * @Route("/", name="gopro_inventario_mantenimiento")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GoproInventarioBundle:Mantenimiento')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Mantenimiento entity.
     *
     * @Route("/", name="gopro_inventario_mantenimiento_create")
     * @Method("POST")
     * @Template("GoproInventarioBundle:Mantenimiento:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Mantenimiento();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_inventario_mantenimiento_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Mantenimiento entity.
    *
    * @param Mantenimiento $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Mantenimiento $entity)
    {
        $form = $this->createForm(new MantenimientoType(), $entity, array(
            'action' => $this->generateUrl('gopro_inventario_mantenimiento_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Mantenimiento entity.
     *
     * @Route("/new", name="gopro_inventario_mantenimiento_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Mantenimiento();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/generar/{ano}/{semestre}", name="gopro_inventario_mantenimiento_generar")
     * @Method("GET")
     */
    public function generarAction($ano,$semestre)
    {
        if(!in_array($semestre,[1,2])||!in_array($ano,[2013,2014,2015,2016,2017,2018,2019,2020])){
            return $this->redirect($this->generateUrl('gopro_inventario_mantenimiento'));

        }
        $em = $this->getDoctrine()->getManager();
        $qb=$em->createQueryBuilder();
        $estadoEjecutado=$em->getRepository('GoproInventarioBundle:Mantenimientoestado')->find(2);
        $estadoPlanificado=$em->getRepository('GoproInventarioBundle:Mantenimientoestado')->find(1);
        $tipo=$em->getRepository('GoproInventarioBundle:Mantenimientotipo')->find(2);
        $ejecutor=$em->getRepository('GoproUserBundle:User')->find(1);
        $qbApl=clone $qb;

        $dependencias=$qbApl
            ->select('d.id')
            ->from('GoproUserBundle:Dependencia','d','d.id')
            ->orderBy('d.id')
            ->getQuery()
            ->getArrayResult();

        $fechaInicio=new \DateTime($ano.'-0'.((($semestre-1)*6)+1).'-01');
        if($semestre==1){
            $fechaFin=new \DateTime($ano.'-07-01');
        }else{
            $fechaFin=new \DateTime(($ano+1).'-01-01');
        }



        foreach(array_keys($dependencias) as $dependencia):
            $qbApl = clone $qb;
            $items = $qbApl
                ->addSelect('i')
                ->from('GoproInventarioBundle:Item', 'i')
                ->leftJoin('i.mantenimientos', 'm', 'WITH', 'm.fecha>=:fechaInicio and m.fecha<:fechaFin')
                ->addSelect('m')
                ->orderBy('i.id', 'ASC')
                ->where($qbApl->expr()->eq('i.dependencia', ':dependencia'))
                ->setParameter('dependencia', $dependencia)
                ->setParameter('fechaInicio', $fechaInicio)
                ->setParameter('fechaFin', $fechaFin)
                ->getQuery()
                ->getArrayResult();
            $periodo=(180/count($items));
            $fecha=new \DateTime($ano.'-0'.((($semestre-1)*6)+1).'-01');
            foreach($items as $key => $item):
                if($key%2==0){
                    $diasAdd=round($periodo,0,PHP_ROUND_HALF_UP);
                }else{
                    $diasAdd=floor($periodo);
                }
                $fecha->add(new \DateInterval('P'.$diasAdd.'D'));
                if(empty($item['mantenimientos'])){
                    ${'mantenimiento'.$key} = new Mantenimiento();
                    ${'mantenimiento'.$key}
                        ->setItem($em->getRepository('GoproInventarioBundle:Item')->find($item['id']))
                        ->setFecha(clone $fecha)
                        ->setMantenimientotipo($tipo)
                        ->setUser($ejecutor)
                        ->setDescripcion('Limpieza interna y externa, optimizacion de programas y archivos, actualización de software.')
                    ;
                    if($fecha > new \DateTime()){
                        ${'mantenimiento'.$key}->setMantenimientoestado($estadoPlanificado);
                    }else{
                        ${'mantenimiento'.$key}->setMantenimientoestado($estadoEjecutado);
                    }
                    $em->persist(${'mantenimiento'.$key});
                }
            endforeach;
        endforeach;
        $em->flush();
        return $this->redirect($this->generateUrl('gopro_inventario_mantenimiento'));
    }

    /**
     * Finds and displays a Mantenimiento entity.
     *
     * @Route("/{id}", name="gopro_inventario_mantenimiento_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Mantenimiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Mantenimiento.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Mantenimiento entity.
     *
     * @Route("/{id}/edit", name="gopro_inventario_mantenimiento_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Mantenimiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Mantenimiento.');
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
    * Creates a form to edit a Mantenimiento entity.
    *
    * @param Mantenimiento $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Mantenimiento $entity)
    {
        $form = $this->createForm(new MantenimientoType(), $entity, array(
            'action' => $this->generateUrl('gopro_inventario_mantenimiento_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Mantenimiento entity.
     *
     * @Route("/{id}", name="gopro_inventario_mantenimiento_update")
     * @Method("PUT")
     * @Template("GoproInventarioBundle:Mantenimiento:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Mantenimiento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Mantenimiento.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_inventario_mantenimiento_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Mantenimiento entity.
     *
     * @Route("/{id}", name="gopro_inventario_mantenimiento_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproInventarioBundle:Mantenimiento')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar la entidad Mantenimiento.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gopro_inventario_mantenimiento'));
    }

    /**
     * Creates a form to delete a Mantenimiento entity by id.
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
                'action'=>$this->generateUrl('gopro_inventario_mantenimiento_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
