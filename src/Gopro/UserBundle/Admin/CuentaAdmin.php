<?php

namespace Gopro\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CuentaAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('user', null, array('label' => 'Usuario'))
            ->add('cuentatipo', null, array('label' => 'Tipo de cuenta'))
            ->add('nombre', null, array('label' => 'Contraseña'))
            ->add('password')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('user', null, array('label' => 'Usuario'))
            ->add('cuentatipo', null, array('label' => 'Tipo de cuenta'))
            ->add('nombre', null, array('editable' => true))
            ->add('password', null, array('editable' => true, 'label' => 'Contraseña'))
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
            ->add('user', null, array('label' => 'Usuario'))
            ->add('cuentatipo', null, array('label' => 'Contraseña'))
            ->add('nombre')
            ->add('password', null, array('label' => 'Contraseña'))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('user', null, array('label' => 'Usuario'))
            ->add('cuentatipo', null, array('label' => 'Tipo de cuenta'))
            ->add('nombre')
            ->add('password', null, array('label' => 'Contraseña'))
        ;
    }
}
