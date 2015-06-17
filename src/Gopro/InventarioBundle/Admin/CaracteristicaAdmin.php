<?php

namespace Gopro\InventarioBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CaracteristicaAdmin extends Admin
{
    /**
     * Orden Predeterminado del datagrid
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'componente.item.nombre'
    );

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('componente.item', null, array('label' => 'Item'))
            ->add('componente')
            ->add('componente.componenteestado', null, array('label' => 'Estado'))
            ->add('caracteristicatipo', null, array('label' => 'Tipo'))
            ->add('contenido')

        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('componente.item', null, array('label' => 'Item'))
            ->add('componente')
            ->add('componente.componenteestado', null, array('label' => 'Estado'))
            ->add('caracteristicatipo', null, array('label' => 'Tipo'))
            ->add('contenido', null, array('editable' => true))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        if ($this->getRoot()->getClass() != 'Gopro\InventarioBundle\Entity\Item' && $this->getRoot()->getClass() != 'Gopro\InventarioBundle\Entity\Componente' ) {
            $formMapper
                ->add('componente')
            ;
        }

        $formMapper
            ->add('caracteristicatipo', null, array('label' => 'Tipo'))
            ->add('contenido')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('componente.item', null, array('label' => 'Item'))
            ->add('componente')
            ->add('componente.componenteestado', null, array('label' => 'Estado'))
            ->add('caracteristicatipo', null, array('label' => 'Tipo'))
            ->add('contenido')
        ;
    }
}
