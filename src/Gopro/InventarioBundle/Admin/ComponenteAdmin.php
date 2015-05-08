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
            ->add('fechacompra', null, array('label' => 'Fecha de compra'))
            ->add('fechafingarantia', null, array('label' => 'Fin de garantia'))
            ->add('componentetipo', null, array('label' => 'Tipo'))
            ->add('componenteestado', null, array('label' => 'Estado'))
            ->add('fechabaja', null, array('label' => 'Fecha de baja'))
            ->add('softwares', null, array('label' => 'Software instalado'))
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
            ->add('fechacompra', null, array('label' => 'Fecha de compra', 'format' => 'Y-m-d'))
            ->add('fechafingarantia', null, array('label' => 'Fin de garantia','format' => 'Y-m-d'))
            ->add('componentetipo', null, array('label' => 'Tipo'))
            ->add('componenteestado', null, array('label' => 'Estado'))
            ->add('fechabaja', null, array('label' => 'Fecha de baja', 'format' => 'Y-m-d'))
            ->add('softwares','sonata_type_model', array('label' => 'Software instalado', 'associated_tostring' => 'getNombre'))
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
        $formMapper
            ->add('fechacompra', 'sonata_type_date_picker', array('label' => 'Fecha de compra'))
            ->add('fechafingarantia', 'sonata_type_date_picker', array('label' => 'Fin de garantia','required' => false))
            ->add('componentetipo', null, array('label' => 'Tipo'))
            ->add('item')
            ->add('componenteestado', null, array('label' => 'Estado'))
            ->add('fechabaja', 'sonata_type_date_picker', array('label' => 'Fecha de baja','required' => false))
            ->add('softwares', 'sonata_type_model', array(
                'label' => 'Software instalado',
                'expanded' => false,
                'multiple' => true,
                'required' => false
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
            ->add('fechacompra', null, array('label' => 'Fecha de compra', 'format' => 'Y-m-d'))
            ->add('fechafingarantia', null, array('label' => 'Fin de garantia','format' => 'Y-m-d'))
            ->add('componentetipo', null, array('label' => 'Tipo'))
            ->add('componenteestado', null, array('label' => 'Estado'))
            ->add('fechabaja', null, array('label' => 'Fecha de baja', 'format' => 'Y-m-d'))
            ->add('softwares','sonata_type_model', array('label' => 'Software instalado', 'associated_tostring' => 'getNombre'))
        ;
    }
}
