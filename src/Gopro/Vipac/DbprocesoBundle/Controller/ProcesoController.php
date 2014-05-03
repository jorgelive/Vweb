<?php

namespace Gopro\Vipac\DbprocesoBundle\Controller;

use Gopro\Vipac\DbprocesoBundle\Entity\Archivo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Gopro\Vipac\DbprocesoBundle\Comun\Archivo as ArchivoOpe;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class ProcesoController extends Controller
{


    /**
     * @Route("/proceso/cheque/{archivoEjecutar}", name="gopro_vipac_dbproceso_proceso_cheque", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function chequeAction(Request $request,$archivoEjecutar)
    {
        $usuario=$this->get('security.context')->getToken()->getUser();

        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $usuario, 'operacion' => 'proceso_cheque'));

        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();

        $formulario->handleRequest($request);

        if ($formulario->isValid()){
            $archivo->setUsuario($usuario);
            $archivo->setOperacion('proceso_cheque');
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_carga_generico'));
        }

        if($archivoEjecutar!==null){
            $archivoAlmacenado=$repositorio->find($archivoEjecutar);
        }
        if(isset($archivoAlmacenado)&&$archivoAlmacenado->getOperacion()=='proceso_cheque'){

            $tablaSpecs=array();
            $columnaSpecs=array();
            $valores=array();
            $columnaspecs[0]=array('nombre'=>'fecha','llave'=>'si','tipo'=>'exceldate');
            $columnaspecs[1]=null;
            $columnaspecs[2]=array('nombre'=>'luis-jorge','llave'=>'no','tipo'=>'file');
            $archivoProcesado=$this->get('gopro_dbproceso_comun_archivo')->parseExcel(false,$columnaspecs,$archivoAlmacenado->getAbsolutePath());//para limitar pasar los valores
            foreach($archivoProcesado['valores'] as $row):

            endforeach;
            extract($archivoProcesado);
            print_r($archivoProcesado);
            //$mensajes = $this->get('gopro_dbproceso_comun_cargador')->ejecutar($tablaSpecs,$columnaSpecs,$valores);

        }else{
            $mensajes[]='El archivo no existe';
        }

        return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $mensajes);
    }


}
