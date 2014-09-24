<?php

namespace Gopro\Vipac\ProveedorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InformacionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('informaciontipo',null, array('label' => 'Tipo'))
            ->add('ruc',null, array('label' => 'RUC'))
            ->add('informacioncaracteristicas', 'collection', array(
                'label'=>false,
                'type' => new InformacioncaracteristicaType(),
                'options' => array('label' => false),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gopro\Vipac\ProveedorBundle\Entity\Informacion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gopro_vipac_proveedorbundle_informacion';
    }
}
