<?php

namespace Gopro\Vipac\DbprocesoBundle\Controller;

use Gopro\Vipac\DbprocesoBundle\Entity\Archivo;
use Gopro\Vipac\MainBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class ProcesoController extends BaseController
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
        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $this->getUserName(), 'operacion' => 'proceso_cheque'),array('creado' => 'DESC'));

        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();

        $formulario->handleRequest($request);

        if ($formulario->isValid()){
            $archivo->setUsuario($this->getUserName());
            $archivo->setOperacion('proceso_cheque');
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_proceso_cheque'));
        }

        $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');
        if(!$procesoArchivo->validarArchivo($repositorio,$archivoEjecutar,'proceso_cheque')){
            $this->setMensajes($procesoArchivo->getMensajes());
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }

        $tablaSpecs=array('schema'=>'RESERVAS',"nombre"=>'VVW_FILES_MERCADO');
        $columnaspecs[0]=array('nombre'=>'FECHA','llave'=>'no','tipo'=>'exceldate','proceso'=>'no');
        $columnaspecs[1]=null;
        $columnaspecs[2]=array('nombre'=>'ANO-NUM_FILE','llave'=>'si','tipo'=>'file');
        $columnaspecs[3]=null;
        $columnaspecs[4]=array('nombre'=>'MONTO','llave'=>'no','proceso'=>'no');
        $columnaspecs[5]=array('nombre'=>'CENTRO_COSTO','llave'=>'no');
        $procesoArchivo->setParametros($tablaSpecs,$columnaspecs);

        if(!$procesoArchivo->parseExcel()){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes('El archivo no se puede procesar');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }
        $carga=$this->get('gopro_dbproceso_comun_cargador');
        if(!$carga->setParametros($procesoArchivo->getTablaSpecs(),$procesoArchivo->getColumnaSpecs(),$procesoArchivo->getValores(),$this->container->get('doctrine.dbal.vipac_connection'))){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('Los parametros de carga no son correctos');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }
        $carga->ejecutar();
        $existente=$carga->getProceso()->getExistenteIndex();

        if(empty($existente)){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('No existen datos para generar archivo');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }

        foreach($procesoArchivo->getValoresIndizados() as $key=>$valores):
            if (!array_key_exists($key, $existente)) {
                $this->setMensajes($procesoArchivo->getMensajes());
                $this->setMensajes($carga->getMensajes());
                $this->setMensajes('El valor '.$key.' no se encuentra en la base de datos');
                return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
            }else{
                foreach($valores as $valor):
                    $fusion[]=array_replace_recursive($valor,$existente[$key]);
                endforeach;
            }
        endforeach;

        foreach($fusion as $fusionPart):
            if(!isset($resultados[$fusionPart['CENTRO_COSTO']])){
                $resultados[$fusionPart['CENTRO_COSTO']]=0;
            }
            $resultados[$fusionPart['CENTRO_COSTO']]=$resultados[$fusionPart['CENTRO_COSTO']]+$fusionPart['MONTO'];
        endforeach;

        $this->setMensajes($procesoArchivo->getMensajes());
        $this->setMensajes($carga->getMensajes());
        return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'resultados'=>$resultados ,'mensajes' => $mensajes);
    }

    /**
     * @Route("/proceso/calxfile/{archivoEjecutar}", name="gopro_vipac_dbproceso_proceso_calxfile", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function calxfileAction(Request $request,$archivoEjecutar)
    {

        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $this->getUserName(), 'operacion' => 'proceso_calxfile'),array('creado' => 'DESC'));
        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();
        $formulario->handleRequest($request);

        if ($formulario->isValid()){
            $archivo->setUsuario($this->getUserName());
            $archivo->setOperacion('proceso_calxfile');
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_proceso_calxfile'));
        }

        $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');
        if(!$procesoArchivo->validarArchivo($repositorio,$archivoEjecutar,'proceso_calxfile')){
            $this->setMensajes($procesoArchivo->getMensajes());
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }

        $tablaSpecs=array('schema'=>'RESERVAS',"nombre"=>'VVW_FILE_PRINCIPAL_MERCADO');
        $procesoArchivo->setParametros($tablaSpecs,null);

        if(!$procesoArchivo->parseExcel()){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes('El archivo no se puede procesar');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }

        $carga=$this->get('gopro_dbproceso_comun_cargador');
        if(!$carga->setParametros($procesoArchivo->getTablaSpecs(),$procesoArchivo->getColumnaSpecs(),$procesoArchivo->getValores(),$this->container->get('doctrine.dbal.vipac_connection'))){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('Los parametros de carga no son correctos');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }
        $carga->ejecutar();
        $existente=$carga->getProceso()->getExistenteIndex();

        if(empty($existente)){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('No existen datos para generar archivo');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }

        foreach($procesoArchivo->getValoresIndizados() as $key=>$valores):
            if (!array_key_exists($key, $existente)) {
                $existente[$key]['mensaje']='No se encuentra en la BD';
            }
            foreach($valores as $valor):
                $fusion[]=array_replace_recursive($valor,$existente[$key]);
            endforeach;
        endforeach;

        $encabezados=array_keys($fusion[0]);
        $respuesta=$this->get('gopro_dbproceso_comun_archivo')->escribirExcel($procesoArchivo->getArchivoValido()->getNombre(),$encabezados,$fusion);
        return $respuesta;
    }

    /**
     * @Route("/proceso/calxreserva/{archivoEjecutar}", name="gopro_vipac_dbproceso_proceso_calxreserva", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function calxreservaAction(Request $request,$archivoEjecutar)
    {

        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $this->getUserName(), 'operacion' => 'proceso_calxreserva'),array('creado' => 'DESC'));
        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();
        $formulario->handleRequest($request);

        if ($formulario->isValid()){
            $archivo->setUsuario($this->getUserName());
            $archivo->setOperacion('proceso_calxreserva');
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_proceso_calxreserva'));
        }

        $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');
        if(!$procesoArchivo->validarArchivo($repositorio,$archivoEjecutar,'proceso_calxreserva')){
            $this->setMensajes($procesoArchivo->getMensajes());
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }

        $tablaSpecs=array('schema'=>'RESERVAS',"nombre"=>'VVW_FILE_SERVICIOS_MERCADO');
        $procesoArchivo->setParametros($tablaSpecs,null);

        if(!$procesoArchivo->parseExcel()){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes('El archivo no se puede procesar');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }

        $carga=$this->get('gopro_dbproceso_comun_cargador');
        if(!$carga->setParametros($procesoArchivo->getTablaSpecs(),$procesoArchivo->getColumnaSpecs(),$procesoArchivo->getValores(),$this->container->get('doctrine.dbal.vipac_connection'))){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('los parametros de carga no son correctos');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }
        $carga->ejecutar();
        $existente=$carga->getProceso()->getExistenteIndex();

        if(empty($existente)){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('No existen datos para generar archivo');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }

        foreach($procesoArchivo->getValoresIndizados() as $key=>$valores):
            if (!array_key_exists($key, $existente)) {
                $existente[$key]['mensaje']='No se encuentra en la BD';
            }
            foreach($valores as $valor):
                $fusion[]=array_replace_recursive($valor,$existente[$key]);
            endforeach;
        endforeach;

        $encabezados=array_keys($fusion[0]);
        $respuesta=$this->get('gopro_dbproceso_comun_archivo')->escribirExcel($procesoArchivo->getArchivoValido()->getNombre(),$encabezados,$fusion);
        return $respuesta;
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
