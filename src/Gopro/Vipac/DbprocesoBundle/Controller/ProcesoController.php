<?php

namespace Gopro\Vipac\DbprocesoBundle\Controller;

use Gopro\Vipac\DbprocesoBundle\Entity\Archivo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * @Route("/proceso/calxfile/{archivoEjecutar}", name="gopro_vipac_dbproceso_proceso_calxfile", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function calxfileAction(Request $request,$archivoEjecutar)
    {
        $mensajes=array();
        $usuario=$this->get('security.context')->getToken()->getUser();

        if(!is_string($usuario)){
            $usuario=$usuario->getUsername();
        }
        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $usuario, 'operacion' => 'proceso_calxfile'),array('creado' => 'DESC'));
        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();
        $formulario->handleRequest($request);
        if ($formulario->isValid()){
            $archivo->setUsuario($usuario);
            $archivo->setOperacion('proceso_calxfile');
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_proceso_calxfile'));
        }
        $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');
        if($procesoArchivo->validarArchivo($repositorio,$archivoEjecutar,'proceso_calxfile')===true){
            $tablaSpecs=array('schema'=>'RESERVAS',"nombre"=>'VVW_FILE_PRINCIPAL_MERCADO');
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

    /**
     * @Route("/proceso/calxreserva/{archivoEjecutar}", name="gopro_vipac_dbproceso_proceso_calxreserva", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function calxreservaAction(Request $request,$archivoEjecutar)
    {
        $mensajes=array();
        $usuario=$this->get('security.context')->getToken()->getUser();

        if(!is_string($usuario)){
            $usuario=$usuario->getUsername();
        }
        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $usuario, 'operacion' => 'proceso_calxreserva'),array('creado' => 'DESC'));
        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();
        $formulario->handleRequest($request);
        if ($formulario->isValid()){
            $archivo->setUsuario($usuario);
            $archivo->setOperacion('proceso_calxreserva');
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_proceso_calxreserva'));
        }
        $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');
        if($procesoArchivo->validarArchivo($repositorio,$archivoEjecutar,'proceso_calxreserva')===true){
            $tablaSpecs=array('schema'=>'RESERVAS',"nombre"=>'VVW_FILE_SERVICIOS_MERCADO');
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

    /**
     * @Route("/proceso/borrararchivo", name="gopro_vipac_dbproceso_proceso_borrararchivo")
     * @Method({"POST"})
     * @Template()
     */
    public function borrarArchivoAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()){
            throw new NotFoundHttpException("No se encontro la página");
        }

        $usuario=$this->get('security.context')->getToken()->getUser();
        if(!is_string($usuario)){
            $usuario=$usuario->getUsername();
        }
        $em = $this->getDoctrine()->getManager();
        $archivo = $em->getRepository('GoproVipacDbprocesoBundle:Archivo')->find($request->request->get('id'));

        if(empty($archivo)||$archivo->getUsuario()!=$usuario){
            return new JsonResponse(array('exito'=>'no','mensaje'=>'No existe el archivo'));

        }
        $em->remove($archivo);
        $em->flush();
        return new JsonResponse(array('exito'=>'si','mensaje'=>'Se ha eliminado el archivo'));
    }

    //@TODO: implementar funcion
    /**
     * @Route("/proceso/editararchivo", name="gopro_vipac_dbproceso_proceso_editararchivo")
     * @Method({"POST"})
     * @Template()
     */
    public function editarArchivoAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()){
            throw new NotFoundHttpException("No se encontro la página");
        }

        $usuario=$this->get('security.context')->getToken()->getUser();
        if(!is_string($usuario)){
            $usuario=$usuario->getUsername();
        }
        $em = $this->getDoctrine()->getManager();
        $archivo = $em->getRepository('GoproVipacDbprocesoBundle:Archivo')->find($request->request->get('id'));

        if(empty($archivo)||$archivo->getUsuario()!=$usuario){
            return new JsonResponse(array('exito'=>'no','mensaje'=>'No existe el archivo'));

        }
        $em->remove($archivo);
        $em->flush();
        return new JsonResponse(array('exito'=>'si','mensaje'=>'Se ha eliminado el archivo'));
    }


}
