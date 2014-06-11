<?php

namespace Gopro\Vipac\ExtraBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FirmaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $oficinaChOp = array('reducto'=>'Reducto','lamar'=>'La Mar','cusco'=>'Cusco','app'=>'Arequipa');
        $oficinaCh=array('choices'=>$oficinaChOp,'multiple'=>false,'expanded'=>true);
        $idiomaChOp = array('es'=>'Español','en'=>'Inglés','pt'=>'Portugués');
        $idiomaCh=array('choices'=>$idiomaChOp,'multiple'=>false,'expanded'=>true);

        $builder
            ->add('nombre', 'text')
            ->add('e-mail', 'text')
            ->add('cargo', 'text')
            ->add('anexo', 'text', array('required' => false))
            ->add('opcional', 'text', array('required' => false))
            ->add('saludo', 'text', array('required' => false))
            ->add('oficina', 'choice', $oficinaCh)
            ->add('idioma', 'choice', $idiomaCh)
        ;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'gopro_vipac_extrabundle_firma';
    }
}
