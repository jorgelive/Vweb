<?php

namespace Gopro\Vipac\ReporteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SentenciaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('groups', null,  array(
                'label' => 'Grupos',
                'multiple' => true,
                'expanded' => true
            ))
            ->add('descripcion',null, array('label' => 'Descripción'))
            ->add('contenido')
            ->add('campos', 'collection', array(
                'label'=>false,
                'type' => new CampoType(),
                'options' => array(
                    'label' => false,
                    'data_class' => 'Gopro\Vipac\ReporteBundle\Entity\Campo'
                )
            ));
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gopro\Vipac\ReporteBundle\Entity\Sentencia'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gopro_vipac_reportebundle_sentencia';
    }
}
