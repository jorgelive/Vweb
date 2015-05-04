<?php
namespace Gopro\UserBundle\Admin;

use Sonata\UserBundle\Admin\Model\UserAdmin as SonataUserAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class UserAdmin extends SonataUserAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);

        $formMapper
            ->with('Organización')
            ->add('dependencia', 'sonata_type_model', array(
                'required' => false,
                'expanded' => false,
                'multiple' => false,'label' => 'Dependencia'
            ))
            ->add('area', 'sonata_type_model', array(
                'required' => false,
                'expanded' => false,
                'multiple' => false,'label' => 'Area'
            ))
            ->end()
        ;
    }
}