<?php

namespace Gopro\InventarioBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ServicioAdmin extends Admin
{

    private $securityContext;

    /**
     * Orden Predeterminado del datagrid
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'tiempo'
    );

    public function setSecurityContext($securityContext) {
        $this->securityContext = $securityContext;
    }

    public function getSecurityContext() {
        return $this->securityContext;
    }

    /**
     * Default Entity values
     *
     */
    public function getNewInstance()
    {
        $instance = parent::getNewInstance();
        $instance->setUser($this->getSecurityContext()->getToken()->getUser());

        return $instance;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('item')
            ->add('tiempo', null, array('label' => 'Solicitado en', 'format' => 'Y-m-d hh:mm'))
            ->add('servicioestado', null, array('label' => 'Estado'))
            ->add('serviciotipo', null, array('label' => 'Tipo'))
            ->add('descripcion', null, array('label' => 'Descripción'))
            ->add('user', null, array('label' => 'Solicitado a'))
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
            ->add('tiempo', null, array('label' => 'Solicitado en', 'format' => 'Y-m-d h:i'))
            ->add('servicioestado', null, array('label' => 'Estado'))
            ->add('serviciotipo', null, array('label' => 'Tipo'))
            ->add('descripcion', null, array('label' => 'Descripción', 'editable' => true))
            ->add('user', null, array('label' => 'Solicitado a'))
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
            ->add('item')
            ->add('tiempo', 'sonata_type_datetime_picker', array('label' => 'Solicitado en'))
            ->add('servicioestado', null, array('label' => 'Estado'))
            ->add('serviciotipo', null, array('label' => 'Tipo'))
            ->add('descripcion', null, array('label' => 'Descripción'))
            ->add('user', null, array('label' => 'Solicitado a'))
            ->add('servicioacciones','sonata_type_collection', array(
                'label' => 'Acciones',
                'by_reference' => false),array(
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
            ->add('tiempo', null, array('label' => 'Solicitado en', 'format' => 'Y-m-d h:i'))
            ->add('servicioestado', null, array('label' => 'Estado'))
            ->add('serviciotipo', null, array('label' => 'Tipo'))
            ->add('descripcion', null, array('label' => 'Descripción'))
            ->add('user', null, array('label' => 'Solicitado a'))
            ->add('servicioacciones', null, array('label' => 'Acciones'))
        ;
    }
}
