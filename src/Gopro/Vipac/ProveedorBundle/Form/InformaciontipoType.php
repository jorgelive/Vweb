<?php

namespace Gopro\Vipac\ProveedorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class InformaciontipoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gopro\Vipac\ProveedorBundle\Entity\Informaciontipo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gopro_vipav_proveedorbundle_informaciontipo';
    }
}
