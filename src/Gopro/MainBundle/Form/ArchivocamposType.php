<?php

namespace Gopro\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ArchivocamposType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('operacion','hidden')
        ;
    }
    
    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'archivo';//suplantando
    }
}
