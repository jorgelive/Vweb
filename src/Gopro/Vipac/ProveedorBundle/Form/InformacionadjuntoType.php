<?php

namespace Gopro\Vipac\ProveedorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gopro\Vipac\ProveedorBundle\Form\EventListener\AgregarInformacionadjuntoInformacionSubscriber;


class InformacionadjuntoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('adjuntotipo')
            ->add('archivo',null,array(
                'required' => true,
            ))
            ->addEventSubscriber(new AgregarInformacionadjuntoInformacionSubscriber());
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gopro\Vipac\ProveedorBundle\Entity\Informacionadjunto'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gopro_vipav_proveedorbundle_informacionadjunto';
    }
}
