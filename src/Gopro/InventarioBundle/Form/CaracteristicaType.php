<?php

namespace Gopro\InventarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gopro\InventarioBundle\Form\EventListener\CaracteristicaCamposSubscriber;

class CaracteristicaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contenido')
            ->add('caracteristicatipo',null, array('label' => 'Tipo de característica'))
            ->addEventSubscriber(new CaracteristicaCamposSubscriber());
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gopro\InventarioBundle\Entity\Caracteristica'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gopro_inventariobundle_caracteristica';
    }
}
