<?php

namespace Gopro\Vipac\DbprocesoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DoccptipoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('tipo')
            ->add('subtipo',null, array('label' => 'Sub Tipo'))
            ->add('subtotal',null, array('label' => 'Sub Total'))
            ->add('impuesto1',null, array('label' => 'Impuesto 1','required' => false))
            ->add('impuesto2',null, array('label' => 'Impuesto 2','required' => false))
            ->add('rubro1',null, array('label' => 'Rubro 1'))
            ->add('rubro2',null, array('label' => 'Rubro 2'))
            ->add('rubro2porcentaje',null, array('label' => 'Rubro 2 %'))
            ->add('retencion',null, array('label' => 'Retención'))
            ->add('codretencion',null, array('label' => 'Código de Retención'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gopro\Vipac\DbprocesoBundle\Entity\Doccptipo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gopro_vipac_dbprocesobundle_doccptipo';
    }
}
