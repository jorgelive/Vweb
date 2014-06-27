<?php

namespace Gopro\InventarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gopro\InventarioBundle\Form\EventListener\AgregarCampoFechabajaSubscriber;

class ComponenteType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('item')
            ->add('componentetipo',null, array('label' => 'Tipo'))
            ->add('componenteestado',null, array('label' => 'Estado'))
            ->add('fechacompra',null, array(
                'label' => 'Compra',
                'input'  => 'datetime',
                'widget' => 'single_text',
                'attr' => array('class' => 'datePicker')
            ))
            ->add('fechafingarantia',null, array(
                'label' => 'Fin de garantía',
                'input'  => 'datetime',
                'widget' => 'single_text',
                'attr' => array('class' => 'datePicker')
            ))

            ->addEventSubscriber(new AgregarCampoFechabajaSubscriber());
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
