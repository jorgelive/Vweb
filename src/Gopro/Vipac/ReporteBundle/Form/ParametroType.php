<?php

namespace Gopro\Vipac\ReporteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametroType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sentencia')
            ->add('nombre')
            ->add('contenido')
            ->add('user',null, array('label' => 'Usuario'))
            ->add('publico',null, array('label' => 'Público', 'required'=>false))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gopro\Vipac\ReporteBundle\Entity\Parametro'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gopro_vipac_reportebundle_parametro';
    }
}
