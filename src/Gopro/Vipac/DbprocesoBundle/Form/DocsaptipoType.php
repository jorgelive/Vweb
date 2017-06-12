<?php

namespace Gopro\Vipac\DbprocesoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocsaptipoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('tiposunat',null, array('label' => 'Tipo Sunat'))
            ->add('tiposap',null, array('label' => 'Tipo Sap'))
            ->add('exoneradoigv',null, array('label' => 'Exonerado IGV'))
            ->add('cuenta',null, array('label' => 'Cuenta'))
            //->add('impuesto2',null, array('label' => 'Impuesto 2','required' => false))
            ->add('tiposervicio',null, array('label' => 'Tipo de servicio'))
            //->add('rubro2',null, array('label' => 'Rubro 2'))
            //->add('rubro2porcentaje',null, array('label' => 'Rubro 2 %'))
            //->add('retencion',null, array('label' => 'Retención'))
            //->add('codretencion',null, array('label' => 'Código de Retención'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gopro\Vipac\DbprocesoBundle\Entity\Docsaptipo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gopro_vipac_dbprocesobundle_docsaptipo';
    }
}
