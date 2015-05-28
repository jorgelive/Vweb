<?php

namespace Gopro\InventarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Gopro\InventarioBundle\Form\EventListener\ComponenteCamposSubscriber;

class ComponenteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('item')
            ->add('componentetipo',null, array('label' => 'Tipo'))
            ->add('componenteestado',null, array('label' => 'Estado'))
            ->add('fechacompra',null, array(
                'label' => 'Compra',
                'input'  => 'datetime',
                'widget' => 'single_text',
                'attr' => array('class' => 'datePicker-0--1')
            ))
            ->add('fechafingarantia',null, array(
                'label' => 'Fin de garantía',
                'input'  => 'datetime',
                'widget' => 'single_text',
                'attr' => array('class' => 'datePicker-0--1')
            ))
            ->add('softwares', 'entity', array(
                'multiple' => true,   // Multiple selection allowed
                'expanded' => true,   // Render as checkboxes
                'property' => 'nombre', // Assuming that the entity has a "name" property
                'class'    => 'Gopro\InventarioBundle\Entity\Software'
            ))
            ->addEventSubscriber(new ComponenteCamposSubscriber());
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gopro\InventarioBundle\Entity\Componente'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gopro_inventariobundle_componente';
    }
}
