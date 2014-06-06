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
            ->with('Organizacion')
            ->add('dependencia', 'sonata_type_model', array(
                'required' => false,
                'expanded' => false,
                'multiple' => false,'label' => 'Dependencia'
            ))
            ->end()
    ;
    }
}