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
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('componente')
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
            ->add('id')
            ->add('componente')
            ->add('caracteristicatipo', null, array('label' => 'Tipo'))
            ->add('contenido')
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
            ->add('componente')
            ->add('caracteristicatipo', null, array('label' => 'Tipo'))
            ->add('contenido')
        ;
    }
}