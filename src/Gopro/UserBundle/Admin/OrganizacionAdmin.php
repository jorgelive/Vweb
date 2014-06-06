<?php

namespace Gopro\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class OrganizacionAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('nombre')
            ->add('ruc')
            ->add('email')
            ->add('direccion')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('nombre')
            ->add('ruc')
            ->add('email')
            ->add('direccion')
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
            ->add('ruc')
            ->add('email')
            ->add('direccion')
            ->add('dependencias', 'sonata_type_model', array(
                'required' => false,
                'expanded' => true,
                'multiple' => true
            ),
                array(
                    'allow_delete' => true
                ))
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
            ->add('ruc')
            ->add('email')
            ->add('direccion')
        ;
    }

    public function prePersist($organizacion)
    {
        $this->preUpdate($organizacion,true);
    }


    public function preUpdate($organizacion,$agregar=false)
    {
        $organizacion->setDependencias($organizacion->getDependencias());
        foreach($organizacion->getDependencias() as $dependencia){
            $dependencia->setOrganizacion($organizacion);
        }
        if($agregar===false){
            $repositorio=$this->getConfigurationPool()->getContainer()->get('doctrine')->getRepository('GoproUserBundle:Dependencia');
            $dependenciasRepositorio=$repositorio->findBy(['organizacion'=>$organizacion->getId()]);

            foreach($dependenciasRepositorio as $dr):
                if(!$organizacion->getDependencias()->contains($dr)){
                    $dr->setOrganizacion(null);
                }
            endforeach;

        }

    }
}
