<?php

namespace Gopro\InventarioBundle\Controller;
use Gopro\MainBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\InventarioBundle\Entity\Item;
use Gopro\InventarioBundle\Form\ItemType;

use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Action\MassAction;
use APY\DataGridBundle\Grid\Action\DeleteMassAction;
use APY\DataGridBundle\Grid\Action\RowAction;

/**
 * Item controller.
 *
 * @Route("/item")
 */
class ItemController extends BaseController
{

    /**
     * Lists all Item entities.
     *
     * @Route("/index", name="gopro_inventario_item")
     * @Method({"POST","GET"})
     * @Template()
     */
    public function indexAction()
    {
        $source = new Entity('GoproInventarioBundle:Item');

        $grid = $this->get('grid');

        $mostrarAction = new RowAction('mostrar', 'gopro_inventario_item_show');
        $mostrarAction->setRouteParameters(array('id'));
        $grid->addRowAction($mostrarAction);

        $editarAction = new RowAction('editar', 'gopro_inventario_item_edit');
        $editarAction->setRouteParameters(array('id'));
        $grid->addRowAction($editarAction);

        $servicioAction = new RowAction('servicio', 'gopro_inventario_item_servicio');
        $servicioAction->setRouteParameters(array('id'));
        $grid->addRowAction($servicioAction);

        $grid->setSource($source);

        return $grid->getGridResponse();
    }
    /**
     * Creates a new Item entity.
     *
     * @Route("/create", name="gopro_inventario_item_create")
     * @Method("POST")
     * @Template("GoproInventarioBundle:Item:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Item();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_inventario_item_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Item entity.
    *
    * @param Item $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Item $entity)
    {
        $form = $this->createForm(new ItemType(), $entity, array(
            'action' => $this->generateUrl('gopro_inventario_item_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Item entity.
     *
     * @Route("/new", name="gopro_inventario_item_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Item();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Item entity.
     *
     * @Route("/{id}/edit", name="gopro_inventario_item_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Item.');
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
     * @Route("/{id}/servicio", name="gopro_inventario_item_servicio")
     * @Method("GET")
     * @Template()
     */
    public function servicioAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $items = $em->createQueryBuilder()
            ->addSelect('i')
            ->from('GoproInventarioBundle:Item', 'i')
            ->leftJoin('i.servicios', 's')
            ->addSelect('s')
            ->leftJoin('i.componentes','c', 'WITH', 'c.componentetipo=1')
            ->addSelect('c')
            ->leftJoin('c.componentecaracteristicas','cc')
            ->addSelect('cc')
            ->leftJoin('cc.caracteristica','ca')
            ->addSelect('ca')
            ->orderBy('i.id', 'ASC');

        if(is_numeric($id)){
            $items=$items->where($em->createQueryBuilder()->expr()->eq('i.id', ':item'))
                ->setParameter('item', $id);

        }elseif($id!='todo'){
            throw $this->createNotFoundException('No se puede procesar este identificador.');
        }

        $items=$items->getQuery()
            ->getResult();

        if (empty($items)) {
            throw $this->createNotFoundException('No se encontraton resultados.');
        }

        $archivos=array();
        foreach($items as $item):
            $componenteCadena='';
            if(!empty($item->getComponentes()[0])){
                $componentePrincipal=$item->getComponentes()[0];

                foreach($componentePrincipal->getComponentecaracteristicas() as $componentecaracteristica):
                    $componenteCadena .= $componentecaracteristica->getCaracteristica()->getNombre().': ';
                    $componenteCadena .= $componentecaracteristica->getContenido().'. ';
                endforeach;

            }
            $mantenimientos=array();
            foreach($item->getServicios() as $key => $servicio):
                $mantenimientos[$key][]=$key+1;
                $mantenimientos[$key][]=$servicio->getFecha()->format('Y-m-d');
                $mantenimientos[$key][]=$servicio->getServiciotipo()->getNombre().': '.$servicio->getDescripcion();
                if($servicio->getServiciotipo()->getId()==1){
                    $mantenimientos[$key][]='';
                    $mantenimientos[$key][]='X';
                }else{
                    $mantenimientos[$key][]='X';
                    $mantenimientos[$key][]='';
                }
                $mantenimientos[$key][]= $servicio->getUser()->getNombre();
            endforeach;

            $archivoGenerado=$this->get('gopro_main_archivoexcel')
                ->setArchivoBase($this->getDoctrine()->getRepository('GoproMainBundle:Archivo'),1,'inventario_item_servicio')
                ->setArchivo()
                ->setParametrosWriter('F-SIS-02-'.$item->getDependencia()->getNombre().'_'.$item->getCodigo())
                ->setCeldas(['texto'=>['C4'=>$componenteCadena,'C5'=>$item->getCodigo()]])
                ->setTabla($mantenimientos,'A9');

            if($id!='todo'){
                return $archivoGenerado->getArchivo();
            }

            $archivos[]=[
                'path'=>$archivoGenerado->getArchivo('archivo'),
                'nombre'=>$archivoGenerado->getNombre().'.'.$archivoGenerado->getTipo()
            ];

        endforeach;

        if(empty($archivos)){
            throw $this->createNotFoundException('No se pueden generar los archivos.');
        }

        return $this->get('gopro_main_archivozip')
            ->setParametros($archivos,'mantenimientos_'.time())
            ->setArchivo()
            ->getArchivo();

    }

    /**
     * Finds and displays a Item entity.
     *
     * @Route("/{id}", name="gopro_inventario_item_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Item.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Item entity.
    *
    * @param Item $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Item $entity)
    {
        $form = $this->createForm(new ItemType(), $entity, array(
            'action' => $this->generateUrl('gopro_inventario_item_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Item entity.
     *
     * @Route("/{id}", name="gopro_inventario_item_update")
     * @Method("PUT")
     * @Template("GoproInventarioBundle:Item:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproInventarioBundle:Item')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la entidad Item.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_inventario_item_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Item entity.
     *
     * @Route("/{id}", name="gopro_inventario_item_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproInventarioBundle:Item')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar la entidad Item.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('gopro_inventario_item'));
    }

    /**
     * Creates a form to delete a Item entity by id.
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
                'action'=>$this->generateUrl('gopro_inventario_item_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
