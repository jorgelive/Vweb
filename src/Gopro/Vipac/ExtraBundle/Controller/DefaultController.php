<?php

namespace Gopro\Vipac\ExtraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/firma", name="gopro_vipac_extra_firma")
     * @Template()
     */
    public function firmaAction(Request $request)
    {
        $datos = array();
        $resultado=array();
        $oficinaChOp = array('reducto'=>'Reducto','lamar'=>'La Mar','cusco'=>'Cusco','app'=>'Arequipa');
        $oficinaCh=array('choices'=>$oficinaChOp,'multiple'=>false,'expanded'=>true);
        $idiomaChOp = array('es'=>'Español','en'=>'Inglés','pt'=>'Portugués');
        $idiomaCh=array('choices'=>$idiomaChOp,'multiple'=>false,'expanded'=>true);

        $formulario = $this->createFormBuilder($datos)
            ->add('Nombre', 'text')
            ->add('Cargo', 'text')
            ->add('Anexo', 'text')
            ->add('Opcional', 'text')
            ->add('Oficina', 'choice', $oficinaCh)
            ->add('Idioma', 'choice', $idiomaCh)
            ->getForm();

        if ($request->isMethod('POST')) {
            $formulario->handleRequest($request);

            // $data is a simply array with your form fields
            // like "query" and "category" as defined above.
            $data = $formulario->getData();
            $resultado=<<<EOT
My name is "$name". I am printing some $foo->foo.
Now, I am printing some {$foo->bar[1]}.
This should print a capital 'A': \x41
EOT;

        }


        return array('formulario' => $formulario->createView(),'resultado' => $resultado);
    }

    private function getTraduccion($variable,$idioma){
        $var['local']['reducto']= "Av. Paseo de la República 6010 - Piso 7 - Lima 18, Perú";
        $var['local']['lamar']="Av. la Mar 163 - Lima 18, Perú";
        $var['local']['cusco']="Av. El Sol 817 - Cusco, Perú";
        $var['local']['aqp']="Calle Palacio Viejo 216 Of. 101 Cercado - Arequipa, Perú";
        $var['telefono']['reducto']="+51 1 610 1900";
        $var['telefono']['lamar']="+51 1 610 1900";
        $var['telefono']['cusco']="+51 84 221744";
        $var['telefono']['aqp']="+51 54 612267";
        $var['ambiental']['es']="Piensa en el planeta antes de imprimir. Reduce. Reusa. Recicla";
        $var['ambiental']['en']="Think before printing. Reduce. Reuse. Recycle.";
        $var['ambiental']['pt']="Pense no planeta antes de imprimir. Reduzir. Reutilizar. Reciclar.";
        $var['frase']['es']="frase_esp.jpg";
        $var['frase']['en']="frase_eng.jpg";
        $var['frase']['pt']="frase_por.jpg";
        $var['publi']['es']="publi_esp.png";
        $var['publi']['en']="publi_eng.png";
        $var['publi']['pt']="publi_por.png";
        $var['siguenos']['es']="siguenos.png";
        $var['siguenos']['en']="follow.png";
        $var['siguenos']['pt']="siguenos_por.png";

        return $var[$variable][$idioma];

    }
}
