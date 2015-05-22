<?php

namespace Gopro\InventarioBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ItemAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('nombre')
            ->add('codigo', null, array('label' => 'Código'))
            ->add('dependencia')
            ->add('itemtipo', null, array('label' => 'Tipo'))
            ->add('users', null, array('label' => 'Usuarios'))
            ->add('areas', null, array('label' => 'Areas'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('nombre', null, array('editable' => true))
            ->add('codigo', null, array('label' => 'Código', 'editable' => true))
            ->add('dependencia', null, array('editable' => true))
            ->add('itemtipo', null, array('label' => 'Tipo', 'editable' => true))
            ->add('users', 'sonata_type_model', array('label' => 'Usuarios', 'associated_tostring' => 'getUserName'))
            ->add('areas', 'sonata_type_model', array('label' => 'Areas', 'associated_tostring' => 'getNombre'))
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
            ->add('nombre')
            ->add('codigo', null, array('label' => 'Código'))
            ->add('dependencia')
            ->add('itemtipo', null, array('label' => 'Tipo'))
            ->add('areas', 'sonata_type_model', array(
                    'expanded' => false,
                    'multiple' => true,
                    'required' => false
                )
            )
            ->add('users', 'sonata_type_model', array(
                    'expanded' => false,
                    'multiple' => true,
                    'required' => false,
                    'label' => 'Usuarios'
                )
            )
            ->add('componentes', 'sonata_type_collection', array('by_reference' => false),array(
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
            ->add('nombre')
            ->add('dependencia')
            ->add('itemtipo')
            ->add('users', 'sonata_type_model', array('label' => 'Usuarios', 'associated_tostring' => 'getUserName'))
            ->add('areas', 'sonata_type_model', array('label' => 'Areas', 'associated_tostring' => 'getNombre'))
            ->add('componentes', 'sonata_type_collection', array('label' => 'Componentes')            )
        ;
    }
}
