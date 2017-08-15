<?php

namespace Gopro\Vipac\DbprocesoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('prefijoseriedebito',null, array('label' => 'Prefijo serie', 'required' => false))
            ->add('prefijoseriecredito',null, array('label' => 'Prefijo serie crédito', 'required' => false))
            ->add('forzargravado',null, array('label' => 'Forzar Grabado'))
            ->add('codigoigvnogravado',null, array('label' => 'IGV no gravado', 'required' => false))
            ->add('codigoigvnogravadodif',null, array('label' => 'IGV no gravado dif', 'required' => false))
            ->add('codigoigvgravado',null, array('label' => 'IGV gravado', 'required' => false))
            ->add('montoretencion',null, array('label' => 'Monto de Retención', 'required' => false))
            ->add('codigoretencion',null, array('label' => 'Código de Retención', 'required' => false))
            ->add('montodetraccion',null, array('label' => 'Monto de Detracción', 'required' => false))
            ->add('codigodetraccion',null, array('label' => 'Código de Detracción', 'required' => false))
            ->add('cuenta',null, array('label' => 'Cuenta'))
            ->add('tiposervicio',null, array('label' => 'Tipo de servicio'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
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
