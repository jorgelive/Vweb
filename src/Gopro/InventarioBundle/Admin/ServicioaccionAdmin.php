<?php

namespace Gopro\InventarioBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ServicioaccionAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('servicio')
            ->add('tiempo')
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
            ->add('servicio')
            ->add('tiempo', null, array('label' => 'Realizada en', 'format' => 'Y-m-d H:i'))
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
        if ($this->getRoot()->getClass() != 'Gopro\InventarioBundle\Entity\Servicio') {
            $formMapper
                ->add('servicio')
            ;
        }
        $formMapper
            ->add('tiempo', 'sonata_type_datetime_picker', array('label' => 'Realizada en'))
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
            ->add('servicio')
            ->add('tiempo', null, array('label' => 'Realizado en', 'format' => 'Y-m-d H:i'))
            ->add('contenido')
        ;
    }
}
