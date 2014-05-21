<?php

namespace Gopro\Vipac\DbprocesoBundle\Controller;

use Gopro\Vipac\DbprocesoBundle\Entity\Archivo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class ProcesoController extends Controller
{

    /**
     * @Route("/index", name="gopro_vipac_dbproceso_proceso_index")
     * @Template()
     */
    public function indexAction(){


    }

    /**
     * @Route("/proceso/cheque/{archivoEjecutar}", name="gopro_vipac_dbproceso_proceso_cheque", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function chequeAction(Request $request,$archivoEjecutar)
    {
        $mensajes=array();
        $resultados=array();
        $usuario=$this->get('security.context')->getToken()->getUser();

        if(!is_string($usuario)){
            $usuario=$usuario->getUsername();
        }

        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $usuario, 'operacion' => 'proceso_cheque'),array('creado' => 'DESC'));

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
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_proceso_cheque'));
        }

        $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');
        if($procesoArchivo->validarArchivo($repositorio,$archivoEjecutar,'proceso_cheque')===true){
            $tablaSpecs=array('schema'=>'RESERVAS',"nombre"=>'VVW_FILES_MERCADO');
            $columnaspecs[0]=array('nombre'=>'FECHA','llave'=>'no','tipo'=>'exceldate','proceso'=>'no');
            $columnaspecs[1]=null;
            $columnaspecs[2]=array('nombre'=>'ANO-NUM_FILE','llave'=>'si','tipo'=>'file');
            $columnaspecs[3]=null;
            $columnaspecs[4]=array('nombre'=>'MONTO','llave'=>'no','proceso'=>'no');
            $columnaspecs[5]=array('nombre'=>'CENTRO_COSTO','llave'=>'no');
            $procesoArchivo->setParametros($tablaSpecs,$columnaspecs);
            $mensajes=$procesoArchivo->getMensajes();
            if($procesoArchivo->parseExcel()!==false){
                $carga=$this->get('gopro_dbproceso_comun_cargador');
                $carga->setParametros($procesoArchivo->getTablaSpecs(),$procesoArchivo->getColumnaSpecs(),$procesoArchivo->getValores(),$this->container->get('doctrine.dbal.vipac_connection'));
                $carga->ejecutar();
                $existente=$carga->getExistente();
                $valido=true;
                foreach($procesoArchivo->getValoresIndizados() as $key=>$valores):
                    if (!array_key_exists($key, $existente)) {
                        $mensajes=array_merge($mensajes,array('El valor '.$key.' no se encuentra en la base de datos'));
                        $valido=false;
                    }else{
                        foreach($valores as $valor):
                            $fusion[]=array_replace_recursive($valor,$existente[$key]);
                        endforeach;
                    }
                endforeach;
                if($valido===true){
                    foreach($fusion as $fusionPart):
                        if(!isset($resultados[$fusionPart['CENTRO_COSTO']])){
                            $resultados[$fusionPart['CENTRO_COSTO']]=0;
                        }
                        $resultados[$fusionPart['CENTRO_COSTO']]=$resultados[$fusionPart['CENTRO_COSTO']]+$fusionPart['MONTO'];
                    endforeach;

                }
                $mensajes=array_merge($mensajes,$carga->getMensajes());
            }else{
                $mensajes=array_merge($mensajes,array('El archivo no se puede procesar'));
            }
        }
        $mensajes=array_merge($mensajes,$procesoArchivo->getMensajes());
        return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'resultados'=>$resultados ,'mensajes' => $mensajes);
    }

    /**
     * @Route("/proceso/calculadora/{archivoEjecutar}", name="gopro_vipac_dbproceso_proceso_calculadora", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function calculadoraAction(Request $request,$archivoEjecutar)
    {
        $mensajes=array();
        $usuario=$this->get('security.context')->getToken()->getUser();

        if(!is_string($usuario)){
            $usuario=$usuario->getUsername();
        }

        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $usuario, 'operacion' => 'proceso_calculadora'),array('creado' => 'DESC'));

        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();
        $formulario->handleRequest($request);
        if ($formulario->isValid()){
            $archivo->setUsuario($usuario);
            $archivo->setOperacion('proceso_calculadora');
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_proceso_calculadora'));
        }
        $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');
        if($procesoArchivo->validarArchivo($repositorio,$archivoEjecutar,'proceso_calculadora')===true){
            $tablaSpecs=array('schema'=>'RESERVAS',"nombre"=>'VVW_FILES_MERCADO');
            $procesoArchivo->setParametros($tablaSpecs,null);
            $mensajes=$procesoArchivo->getMensajes();

            if($procesoArchivo->parseExcel()!==false){
                $carga=$this->get('gopro_dbproceso_comun_cargador');
                $carga->setParametros($procesoArchivo->getTablaSpecs(),$procesoArchivo->getColumnaSpecs(),$procesoArchivo->getValores(),$this->container->get('doctrine.dbal.vipac_connection'));
                $carga->ejecutar();
                $existente=$carga->getExistenteIndex();
                foreach($procesoArchivo->getValoresIndizados() as $key=>$valores):
                    if (!array_key_exists($key, $existente)) {
                        $mensajes=array_merge($mensajes,array('El valor '.$key.' no se encuentra en la base de datos'));
                        $existente[$key]['mensaje']='No se encuentra en la BD';
                    }
                    foreach($valores as $valor):
                        $fusion[]=array_replace_recursive($valor,$existente[$key]);
                    endforeach;
                endforeach;
                if(isset($fusion[0])&&!empty($fusion[0])){
                    $encabezados=array_keys($fusion[0]);
                    $respuesta=$this->get('gopro_dbproceso_comun_archivo')->escribirExcel($procesoArchivo->getArchivoValido()->getNombre(),$encabezados,$fusion);
                    return $respuesta;
                }
                $mensajes=array_merge($mensajes,array('No existen datos para generar archivo'));
            }else{
                $mensajes=array_merge($mensajes,array('El archivo no se puede procesar'));
            }
        }
        $mensajes=array_merge($mensajes,$procesoArchivo->getMensajes());
        return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $mensajes);
    }

}
