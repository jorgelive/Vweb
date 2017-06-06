<?php

namespace Gopro\Vipac\ReporteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Gopro\Vipac\ReporteBundle\Form\EventListener\AgregarCampoSentenciaSubscriber;
use Gopro\Vipac\ReporteBundle\Form\EventListener\AgregarCampoNombreSubscriber;

class CampoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombremostrar',null, array('label' => 'Nombre a mostrar'))
            ->add('predeterminado')
            ->add('tipo')
            ->addEventSubscriber(new AgregarCampoNombreSubscriber())
            ->addEventSubscriber(new AgregarCampoSentenciaSubscriber());
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gopro\Vipac\ReporteBundle\Entity\Campo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gopro_vipac_reportebundle_campo';
    }
}
