<?php

namespace Gopro\Vipac\ProveedorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gopro\Vipac\ProveedorBundle\Form\EventListener\AgregarInformacioncaracteristicaInformacionSubscriber;

class ComprobanteType extends AbstractType
{
     /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('serie')
            ->add('numero')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gopro\Vipac\ProveedorBundle\Entity\Comprobante'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gopro_vipac_proveedorbundle_comprobante';
    }
}
