<?php

namespace Gopro\Vipac\DbprocesoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ParametrosType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $destinoChOp = array('pantalla'=>'Pantalla','archivo'=>'Archivo');
        $destinoCh=array('choices'=>$destinoChOp,'multiple'=>false,'expanded'=>true);
        $tipoChOp = array('detallado'=>'Detallado','resumido'=>'Resumido');
        $tipoCh=array('choices'=>$tipoChOp,'multiple'=>false,'expanded'=>true);

        $builder
            ->add('fechaInicio','date',array(
                'input'  => 'datetime',
                'widget' => 'single_text',
                'attr' => array('class' => 'datePicker-0--1')
            ))
            ->add('fechaFin','date',array(
                'input'  => 'datetime',
                'widget' => 'single_text',
                'attr' => array('class' => 'datePicker-0--1')
            ))
            ->add('destino', 'choice', $destinoCh)
            ->add('tipo', 'choice', $tipoCh)
        ;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'gopro_vipac_dbprocesobundle_parametros';
    }
}
