<?php

namespace Gopro\InventarioBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ComponenteAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('item')
            ->add('componentetipo', null, array('label' => 'Tipo'))
            ->add('fechacompra', null, array('label' => 'Fecha de compra'))
            ->add('fechafingarantia', null, array('label' => 'Fin de garantia'))
            ->add('componenteestado', null, array('label' => 'Estado'))
            ->add('fechabaja', null, array('label' => 'Fecha de baja'))
            ->add('softwares', null, array('label' => 'Software instalado'))
            ->add('caracteristicas.contenido', null, array('label' => 'Caracteristicas'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('item')
            ->add('componentetipo', null, array('label' => 'Tipo'))
            ->add('fechacompra', null, array('label' => 'Fecha de compra', 'format' => 'Y-m-d'))
            ->add('fechafingarantia', null, array('label' => 'Fin de garantia','format' => 'Y-m-d'))
            ->add('componenteestado', null, array('label' => 'Estado'))
            ->add('fechabaja', null, array('label' => 'Fecha de baja', 'format' => 'Y-m-d'))
            ->add('softwares','sonata_type_model', array('label' => 'Software instalado', 'associated_tostring' => 'getNombre'))
            ->add('caracteristicas', 'sonata_type_collection', array('label' => 'Caracteristicas'))
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
        //if (!$this->isChild()) {
        if ($this->getRoot()->getClass() != 'Gopro\InventarioBundle\Entity\Item') {
            $formMapper
                ->add('item')
            ;
        }
        $formMapper
            ->add('componentetipo', null, array('label' => 'Tipo'))
            ->add('fechacompra', 'sonata_type_date_picker', array('label' => 'Fecha de compra', 'required' => false))
            ->add('fechafingarantia', 'sonata_type_date_picker', array('label' => 'Fin de garantia', 'required' => false))
            ->add('componenteestado', null, array('label' => 'Estado'))
            ->add('fechabaja', 'sonata_type_date_picker', array('label' => 'Fecha de baja', 'required' => false))
            ->add('softwares', 'sonata_type_model', array(
                'label' => 'Software instalado',
                'expanded' => false,
                'multiple' => true,
                'required' => false
                )
            )
            ->add('caracteristicas','sonata_type_collection', array('by_reference' => false),array(
                'edit' => 'inline',
                'inline' => 'table'
                )
            )
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('item')
            ->add('componentetipo', null, array('label' => 'Tipo'))
            ->add('fechacompra', null, array('label' => 'Fecha de compra', 'format' => 'Y-m-d'))
            ->add('fechafingarantia', null, array('label' => 'Fin de garantia','format' => 'Y-m-d'))
            ->add('componenteestado', null, array('label' => 'Estado'))
            ->add('fechabaja', null, array('label' => 'Fecha de baja', 'format' => 'Y-m-d'))
            ->add('softwares','sonata_type_model', array('label' => 'Software instalado', 'associated_tostring' => 'getNombre'))
            ->add('caracteristicas', null, array('label' => 'Caracteristicas'))
        ;
    }
}
