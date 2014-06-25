<?php

namespace Gopro\Vipac\ReporteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
            ->add('areas')
            ->add('descripcion',null, array('label' => 'Descripción'))
            ->add('contenido')
            ->add('campos', 'collection', array('label'=>false,'type' => new CampoType(), 'options' => array('label' => false)));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
