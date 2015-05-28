<?php

namespace Gopro\InventarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServicioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcion',null, array('label' => 'Descripción'))
            ->add('fecha',null,array(
                'input'  => 'datetime',
                'widget' => 'single_text',
                'attr' => array('class' => 'datePicker-0--1')
            ))
            ->add('item')
            ->add('serviciotipo',null, array('label' => 'Tipo'))
            ->add('servicioestado',null, array('label' => 'Estado'))
            ->add('user',null, array('label' => 'Ejecutor'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gopro\InventarioBundle\Entity\Servicio'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gopro_inventariobundle_servicio';
    }
}
