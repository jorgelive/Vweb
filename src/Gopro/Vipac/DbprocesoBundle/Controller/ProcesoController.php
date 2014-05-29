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
        $operacion='proceso_cheque';
        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $this->getUserName(), 'operacion' => $operacion),array('creado' => 'DESC'));

        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();

        $formulario->handleRequest($request);

        if ($formulario->isValid()){
            $archivo->setUsuario($this->getUserName());
            $archivo->setOperacion($operacion);
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_'.$operacion));
        }

        $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');
        if(!$procesoArchivo->validarArchivo($repositorio,$archivoEjecutar,$operacion)){
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
     * @Route("/proceso/calcc/{archivoEjecutar}", name="gopro_vipac_dbproceso_proceso_calcc", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function calccAction(Request $request,$archivoEjecutar)
    {

        $operacion='proceso_calcc';
        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $this->getUserName(), 'operacion' => $operacion),array('creado' => 'DESC'));

        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();

        $formulario->handleRequest($request);

        if ($formulario->isValid()){
            $archivo->setUsuario($this->getUserName());
            $archivo->setOperacion($operacion);
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_'.$operacion));
        }

        $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');
        if(!$procesoArchivo->validarArchivo($repositorio,$archivoEjecutar,$operacion)){
            $this->setMensajes($procesoArchivo->getMensajes());
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }

        $tablaSpecs=array('schema'=>'VIAPAC',"nombre"=>'VVW_DOCUMENTOS_CC','tipo'=>'S');
        $columnaspecs[0]=array('nombre'=>'CUENTA_CONTABLE','llave'=>'no','proceso'=>'no');
        $columnaspecs[1]=array('nombre'=>'DESCRIPCION','llave'=>'no','proceso'=>'no');
        $columnaspecs[2]=array('nombre'=>'ASIENTO','llave'=>'si');
        $columnaspecs[3]=array('nombre'=>'TIPO_DE_DOCUMENTO','llave'=>'no','proceso'=>'no');
        $columnaspecs[4]=array('nombre'=>'DOCUMENTO','llave'=>'no','proceso'=>'no');
        $columnaspecs[5]=array('nombre'=>'REFERENCIA','llave'=>'no','proceso'=>'no');
        $columnaspecs[6]=array('nombre'=>'DEBITO_LOCAL','llave'=>'no','proceso'=>'no');
        $columnaspecs[7]=array('nombre'=>'DEBITO_DOLAR','llave'=>'no','proceso'=>'no');
        $columnaspecs[8]=array('nombre'=>'CREDITO_LOCAL','llave'=>'no','proceso'=>'no');
        $columnaspecs[9]=array('nombre'=>'CREDITO_DOLAR','llave'=>'no','proceso'=>'no');
        $columnaspecs[10]=array('nombre'=>'CENTRO_COSTO','llave'=>'no','proceso'=>'no');
        $columnaspecs[11]=array('nombre'=>'TIPO_ASIENTO','llave'=>'no','proceso'=>'no');
        $columnaspecs[12]=array('nombre'=>'FECHA','llave'=>'no','tipo'=>'exceldate','proceso'=>'no');
        $columnaspecs[13]=array('nombre'=>'DESCRIPCION','llave'=>'no','proceso'=>'no');
        $columnaspecs[14]=array('nombre'=>'NIT','llave'=>'no','proceso'=>'no');
        $columnaspecs[15]=array('nombre'=>'NOMBRE','llave'=>'no','proceso'=>'no');
        $columnaspecs[16]=array('nombre'=>'FUENTE','llave'=>'no','proceso'=>'no');
        $columnaspecs[17]=array('nombre'=>'NOTAS','llave'=>'no','proceso'=>'no');
        $columnaspecs[18]=array('nombre'=>'DEBITO_UNIDADES','llave'=>'no','proceso'=>'no');
        $columnaspecs[19]=array('nombre'=>'CREDITO_UNIDADES','llave'=>'no','proceso'=>'no');
        $columnaspecs[20]=array('nombre'=>'ANO','llave'=>'no');
        $columnaspecs[21]=array('nombre'=>'NUM_FILE_FISICO','llave'=>'no');
        $columnaspecs[22]=array('nombre'=>'CLIENTE','llave'=>'no');
        $columnaspecs[23]=array('nombre'=>'MONTO_DOLAR','llave'=>'no');

        $procesoArchivo->setParametros($tablaSpecs,$columnaspecs);
        $procesoArchivo->setCamposCustom(['CREDITO_LOCAL','CREDITO_DOLAR']);

        if(!$procesoArchivo->parseExcel()){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes('El archivo no se puede procesar');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }
        $carga=$this->get('gopro_dbproceso_comun_cargador');
        if(!$carga->setParametros($procesoArchivo->getTablaSpecs(),$procesoArchivo->getColumnaSpecs(),$procesoArchivo->getValoresRaw(),$this->container->get('doctrine.dbal.vipac_connection'))){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('Los parametros de carga no son correctos');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }
        $carga->getProceso()->setCamposCustom(['ANO','NUM_FILE_FISICO']);
        $carga->ejecutar();

        if(empty($carga->getProceso()->getExistenteCustom())){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('No existen datos para generar archivo');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }

        $serviciosHoteles=$this->container->get('gopro_dbproceso_comun_proceso');
        $serviciosHoteles->setConexion($this->container->get('doctrine.dbal.vipac_connection'));
        $serviciosHoteles->setTabla('VVW_UNION_HOTEL_SERVICIO');
        $serviciosHoteles->setSchema('RESERVAS');
        $serviciosHoteles->setCamposSelect([
            'ANO',
            'NUM_FILE_FISICO',
            'NOM_FILE',
            'NUM_PAX',
            'COD_CONTACTO',
            'NOM_CONTACTO',
            'RAZON_SOCIAL',
            'COD_PAIS',
            'NOM_PAIS',
            'COD_MERCADO',
            'NOMBRE_MERCADO',
            'CENTRO_COSTO',
            'COD_SERVICIO',
            'NOM_SERVICIO',
            'IND_PRIVADO',
            'COD_OPERADOR',
            'NOM_OPERADOR',
            'FEC_INICIO',
            'FEC_FIN',
            'ESTADO',
	        'MONTO',
	        'CUENTA'
        ]);
        $serviciosHoteles->setQueryVariables($carga->getProceso()->getExistenteCustom());
        if(!$serviciosHoteles->ejecutarSelectQuery()||empty($serviciosHoteles->getExistenteRaw())){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('No existen datos para generar archivo');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }
        //print_r($procesoArchivo->getValoresCustomIndizados());
        foreach($carga->getProceso()->getExistenteRaw() as $valor):
            if(isset($serviciosHoteles->getExistenteIndexMulti()[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']])&&isset($procesoArchivo->getValoresCustomIndizados()[$valor['ASIENTO']])){
                $preResultado[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']]=$valor;
                $preResultado[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']]=array_merge($preResultado[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']],$procesoArchivo->getValoresCustomIndizados()[$valor['ASIENTO']]);
                $preResultado[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']]['items']=$serviciosHoteles->getExistenteIndexMulti()[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']];
                array_walk_recursive($preResultado[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']]['items'], array($this, 'setMonto'),'MONTO');

                $preResultado[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']]['sumaMonto']=$this->getMonto();
                if($this->getMonto()==0){
                    $coeficiente=0;
                }else{
                    $coeficiente=$preResultado[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']]['CREDITO_DOLAR']/$this->getMonto();
                }
                $preResultado[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']]['coeficiente']=$coeficiente;
                $this->resetMonto();
            }else{
                $this->setMensajes('No hay resultados para el file: '.$valor['ANO'].'-'.$valor['NUM_FILE_FISICO']);
            }
        endforeach;
        if(!isset($preResultado)||empty($preResultado)){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('No hay datos para procesar los resultados');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }
        $i=0;
        //print_r($preResultado);
        foreach($preResultado as $valor):
            foreach($valor['items'] as $item):
                $resultado[$i]=$item;
                $resultado[$i]['NUM_FILE_FISICO']=$valor['NUM_FILE_FISICO'];
                $resultado[$i]['ANO']=$valor['ANO'];
                $resultado[$i]['ASIENTO']=$valor['ASIENTO'];
                $resultado[$i]['CLIENTE']=$valor['CLIENTE'];
                $resultado[$i]['MONTO_DOLAR']=$valor['MONTO_DOLAR'];
                $resultado[$i]['CREDITO_DOLAR']=$valor['CREDITO_DOLAR'];
                $resultado[$i]['CREDITO_LOCAL']=$valor['CREDITO_LOCAL'];
                $resultado[$i]['MONTO_PRORRATEADO']=$item['MONTO']*$valor['coeficiente'];
                $resultado[$i]['MONTO_PRORRATEADO_LOCAL']=$resultado[$i]['MONTO_PRORRATEADO']*$valor['CREDITO_LOCAL']/$valor['CREDITO_DOLAR'];
                $resultado[$i]['COEFICIENTE']=$valor['coeficiente'];
                //
                $i++;
            endforeach;
        endforeach;
        if(!isset($resultado)||empty($resultado)){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('No hay resultados');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }
        $this->setMensajes($procesoArchivo->getMensajes());
        $this->setMensajes($carga->getMensajes());
        foreach($this->getMensajes() as $mensaje):
            $resultado[]['mensaje']=$mensaje;
        endforeach;


        $encabezados=array_keys($resultado[0]);
        $respuesta=$this->get('gopro_dbproceso_comun_archivo')->escribirExcel($procesoArchivo->getArchivoValido()->getNombre(),$encabezados,$this->container->get('gopro_dbproceso_comun_variable')->utf($resultado));
        return $respuesta;

    }





    /**
     * @Route("/proceso/calxfile/{archivoEjecutar}", name="gopro_vipac_dbproceso_proceso_calxfile", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function calxfileAction(Request $request,$archivoEjecutar)
    {

        $operacion='proceso_calxfile';
        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $this->getUserName(), 'operacion' => $operacion),array('creado' => 'DESC'));
        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();
        $formulario->handleRequest($request);

        if ($formulario->isValid()){
            $archivo->setUsuario($this->getUserName());
            $archivo->setOperacion($operacion);
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_'.$operacion));
        }

        $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');
        if(!$procesoArchivo->validarArchivo($repositorio,$archivoEjecutar,$operacion)){
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
        $operacion='proceso_calxreserva';
        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $this->getUserName(), 'operacion' => $operacion),array('creado' => 'DESC'));
        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();
        $formulario->handleRequest($request);

        if ($formulario->isValid()){
            $archivo->setUsuario($this->getUserName());
            $archivo->setOperacion($operacion);
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_'.$operacion));
        }

        $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');
        if(!$procesoArchivo->validarArchivo($repositorio,$archivoEjecutar,$operacion)){
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

    public function agregarArchivoAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()){
            throw new NotFoundHttpException("No se encontro la página");
        }
        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();
        $formulario->handleRequest($request);

        if ($formulario->isValid()){
            $archivo->setUsuario($this->getUserName());
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_'.$operacion));
        }
    }



}
