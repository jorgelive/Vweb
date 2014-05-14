<?php

namespace Gopro\Vipac\DbprocesoBundle\Controller;

use Gopro\Vipac\DbprocesoBundle\Entity\Archivo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Gopro\Vipac\DbprocesoBundle\Comun\Archivo as ArchivoOpe;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class CargaController extends Controller
{
    /**
     * @Route("/carga/index/{name}", name="gopro_vipac_dbproceso_carga_index")
     * @Template()
     */
    public function indexAction($pais)
    {

        return array('paises' => $pais);
    }

    /**
     * @Route("/carga/generico/{archivoEjecutar}", name="gopro_vipac_dbproceso_carga_generico", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function genericoAction(Request $request,$archivoEjecutar)
    {
        $mensajes=array();
        $usuario=$this->get('security.context')->getToken()->getUser();

        if(!is_string($usuario)){
            $usuario=$usuario->getUsername();
        }

        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $usuario, 'operacion' => 'carga_generico'),array('creado' => 'DESC'));

        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();

        $formulario->handleRequest($request);

        if ($formulario->isValid()){
            $archivo->setUsuario($usuario);
            $archivo->setOperacion('carga_generico');
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_carga_generico'));
        }

        $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');

        if($procesoArchivo->validarArchivo($repositorio,$archivoEjecutar,'carga_generico')===true){

            $procesoArchivo->setParametros(null,null);
            $mensajes=$procesoArchivo->getMensajes();
            //print_r($archivoProcesado);
            if($procesoArchivo->parseExcel()!==false){
                //print_r($procesoArchivo->getTablaSpecs());
                //print_r($procesoArchivo->getColumnaSpecs());
                $carga=$this->get('gopro_dbproceso_comun_cargador');
                $carga->setParametros($procesoArchivo->getTablaSpecs(),$procesoArchivo->getColumnaSpecs(),$procesoArchivo->getValores(),$this->container->get('doctrine.dbal.vipac_connection'));
                $carga->ejecutar();
                $mensajes=array_merge($mensajes,$carga->getMensajes());
            }else{
                $mensajes=array_merge($mensajes,array('El archivo no se puede procesar'));
            }
            //$mensajes = $this->get('gopro_dbproceso_comun_cargador')->cargadorGenerico($tablaSpecs,$columnaSpecs,$valores);
        }
        $mensajes=array_merge($mensajes,$procesoArchivo->getMensajes());
        return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $mensajes);
    }

    /**
     * @Route("/carga/arreglartc/{archivoEjecutar}", name="gopro_vipac_dbproceso_carga_arreglartc", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function arreglartcAction(Request $request,$archivoEjecutar)
    {
        $mensajes=array();
        $usuario=$this->get('security.context')->getToken()->getUser();

        if(!is_string($usuario)){
            $usuario=$usuario->getUsername();
        }

        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $usuario, 'operacion' => 'carga_arreglartc'),array('creado' => 'DESC'));

        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();
        $formulario->handleRequest($request);
        if ($formulario->isValid()){
            $archivo->setUsuario($usuario);
            $archivo->setOperacion('carga_arreglartc');
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_carga_arreglartc'));
        }
        $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');
        if($procesoArchivo->validarArchivo($repositorio,$archivoEjecutar,'carga_arreglartc')===true){
            $tablaDocCp=array('schema'=>'VIAPAC',"nombre"=>'DOCUMENTOS_CP');
            $columnaDocCp[0]=array('nombre'=>'ASIENTO','llave'=>'si');
            $columnaDocCp[1]=array('nombre'=>'MONTO','llave'=>'no');
            $columnaDocCp[2]=array('nombre'=>'FECHA','llave'=>'no');
            $columnaDocCp[3]=array('nombre'=>'MONEDA','llave'=>'no');
            $procesoArchivo->setParametros($tablaDocCp,$columnaDocCp);
            $mensajes=$procesoArchivo->getMensajes();
            if($procesoArchivo->parseExcel()!==false){
                $documentoCp=$this->get('gopro_dbproceso_comun_cargador');
                $documentoCp->setParametros($procesoArchivo->getTablaSpecs(),$procesoArchivo->getColumnaSpecs(),$procesoArchivo->getValores(),$this->container->get('doctrine.dbal.vipac_connection'));
                $documentoCp->ejecutar();
                $exiDocumentoCp=$documentoCp->getExistente();
                $tablaAsiDi=array(
                    'schema'=>'VIAPAC',
                    'nombre'=>'ASIENTO_DE_DIARIO',
                    'tipo'=>'S',
                    'columnasProceso'=>Array('ASIENTO','TOTAL_DEBITO_DOL','TOTAL_CREDITO_DOL','TOTAL_CONTROL_DOL','FECHA'),
                    'llaves'=>Array('ASIENTO')
                );
                $columnaAsiDi['ASIENTO']=array('nombre'=>'ASIENTO','llave'=>'si');
                $columnaAsiDi['TOTAL_DEBITO_DOL']=array('nombre'=>'TOTAL_DEBITO_DOL','llave'=>'no');
                $columnaAsiDi['TOTAL_CREDITO_DOL']=array('nombre'=>'TOTAL_CREDITO_DOL','llave'=>'no');
                $columnaAsiDi['TOTAL_CONTROL_DOL']=array('nombre'=>'TOTAL_CONTROL_DOL','llave'=>'no');
                $columnaAsiDi['FECHA']=array('nombre'=>'FECHA','llave'=>'no');
                $asiDi=$this->get('gopro_dbproceso_comun_cargador');
                $asiDi->setParametros($tablaAsiDi,$columnaAsiDi,$procesoArchivo->getValores(),$this->container->get('doctrine.dbal.vipac_connection'));
                $asiDi->ejecutar();

                $exiAsiDi=$asiDi->getExistente();

                $tablaDi=array(
                    'schema'=>'VIAPAC',
                    'nombre'=>'DIARIO',
                    'tipo'=>'S',
                    'columnasProceso'=>Array('ASIENTO','CONSECUTIVO','DEBITO_DOLAR','CREDITO_DOLAR'),
                    'llaves'=>Array('ASIENTO','CONSECUTIVO')
                );
                $columnaDi['ASIENTO']=array('nombre'=>'ASIENTO','llave'=>'si');
                $columnaDi['CONSECUTIVO']=array('nombre'=>'CONSECUTIVO','llave'=>'no');//no en lista
                $columnaDi['DEBITO_DOLAR']=array('nombre'=>'DEBITO_DOLAR','llave'=>'no');
                $columnaDi['CREDITO_DOLAR']=array('nombre'=>'CREDITO_DOLAR','llave'=>'no');
                $di=$this->get('gopro_dbproceso_comun_cargador');
                $di->setParametros($tablaDi,$columnaDi,$procesoArchivo->getValores(),$this->container->get('doctrine.dbal.vipac_connection'));
                $di->ejecutar();
                $exiDi=$di->getExistente();
                foreach ($exiDi as $key => $valores):
                    $keyArray=explode('|',$key);
                    $resultado[$keyArray[0]]['DIARIO'][$keyArray[1]]=$valores;
                    $resultado[$keyArray[0]]['ASIENTO_DE_DIARIO']=$exiAsiDi[$keyArray[0]];
                    $resultado[$keyArray[0]]['DOCUMENTOS_CP']=$exiDocumentoCp[$keyArray[0]];
                    $fechas[]=$exiAsiDi[$keyArray[0]]['FECHA'];
                    $fechas[]=$exiDocumentoCp[$keyArray[0]]['FECHA'];
                endforeach;
                $i=0;
                foreach(array_unique($fechas) as $fecha):
                    $fechaBuscar[$i]['FECHA']=$fecha;
                    $fechaBuscar[$i]['TIPO_CAMBIO']='TCV';
                    $i++;
                endforeach;

                $tablaTc=array(
                    'schema'=>'VIAPAC',
                    'nombre'=>'TIPO_CAMBIO_HIST',
                    'tipo'=>'S',
                    'columnasProceso'=>Array('FECHA','TIPO_CAMBIO','MONTO'),
                    'llaves'=>Array('FECHA')
                );
                $columnaTc['FECHA']=array('nombre'=>'FECHA','llave'=>'si');
                $columnaTc['TIPO_CAMBIO']=array('nombre'=>'TIPO_CAMBIO','llave'=>'si');//en lista no llave
                $columnaTc['MONTO']=array('nombre'=>'MONTO','llave'=>'no');
                $tc=$this->get('gopro_dbproceso_comun_cargador');
                $tc->setParametros($tablaTc,$columnaTc,$fechaBuscar,$this->container->get('doctrine.dbal.vipac_connection'));
                $tc->ejecutar();
                $exiTc=$tc->getExistente();
                foreach ($resultado as $codigoAsiento => $procesoTablas):
                    $procesar='si';
                    if(!isset($resultado[$codigoAsiento]['DOCUMENTOS_CP']['FECHA'])||!isset($resultado[$codigoAsiento]['ASIENTO_DE_DIARIO']['FECHA'])){
                        $mensajes=array_merge($mensajes,array('No existe la fecha para: '.$codigoAsiento));
                        $procesar='no';
                    }else{
                        if(!isset($exiTc[$resultado[$codigoAsiento]['DOCUMENTOS_CP']['FECHA']])||!isset($exiTc[$resultado[$codigoAsiento]['ASIENTO_DE_DIARIO']['FECHA']])){
                            $mensajes=array_merge($mensajes,array('No hay tipo de cambio para la fecha para: '.$resultado[$codigoAsiento]['DOCUMENTOS_CP']['FECHA']));
                            $procesar='no';
                        }else{
                            $tipoCambioDocumentoCp=$exiTc[$resultado[$codigoAsiento]['DOCUMENTOS_CP']['FECHA']]['MONTO'];
                            $tipoCambioAsiDi=$exiTc[$resultado[$codigoAsiento]['ASIENTO_DE_DIARIO']['FECHA']]['MONTO'];
                        }
                    };
                    if($procesar=='si'&& $resultado[$codigoAsiento]['DOCUMENTOS_CP']['MONEDA']!='USD'){
                        $procesar='no';
                        $mensajes=array_merge($mensajes,array('la moneda no es dolar para: '.$codigoAsiento));
                    }
                    if($procesar=='si'){
                        foreach($procesoTablas as $tablaNombre => $contenido):
                            if($tablaNombre=='DOCUMENTOS_CP'){
                                $monto = round($contenido['MONTO']*$tipoCambioDocumentoCp,2);
                                $updateQuery='UPDATE VIAPAC.'.$tablaNombre.' set MONTO_LOCAL=:MONTO, SALDO_LOCAL=:MONTO, TIPO_CAMBIO_MONEDA=:TC, TIPO_CAMBIO_DOLAR=:TC, TIPO_CAMBIO_PROV=:TC, TIPO_CAMB_ACT_LOC=:TC, TIPO_CAMB_ACT_DOL=:TC, TIPO_CAMB_ACT_PROV=:TC WHERE ASIENTO=:ASIENTO';
                                $statement = $this->container->get('doctrine.dbal.vipac_connection')->prepare($updateQuery);
                                $statement->bindValue('MONTO',$monto);
                                $statement->bindValue('TC',$tipoCambioDocumentoCp);
                                $statement->bindValue('ASIENTO',$codigoAsiento);
                                $statement->execute();
                                $mensajes=array_merge($mensajes,array('Actualizando en: '.$tablaNombre.', para:'.$codigoAsiento));
                            }elseif($tablaNombre=='ASIENTO_DE_DIARIO'){
                                $montoDebito = round($contenido['TOTAL_DEBITO_DOL']*$tipoCambioAsiDi,2);
                                $montoCredito = round($contenido['TOTAL_CREDITO_DOL']*$tipoCambioAsiDi,2);
                                $montoControl = round($contenido['TOTAL_CONTROL_DOL']*$tipoCambioAsiDi,2);
                                $updateQuery='UPDATE VIAPAC.'.$tablaNombre.' set TOTAL_DEBITO_LOC=:TOTAL_DEBITO_LOC, TOTAL_CREDITO_LOC=:TOTAL_CREDITO_LOC, TOTAL_CONTROL_LOC=:TOTAL_CONTROL_LOC WHERE ASIENTO=:ASIENTO';
                                $statement = $this->container->get('doctrine.dbal.vipac_connection')->prepare($updateQuery);
                                $statement->bindValue('TOTAL_DEBITO_LOC',$montoDebito);
                                $statement->bindValue('TOTAL_CREDITO_LOC',$montoCredito);
                                $statement->bindValue('TOTAL_CONTROL_LOC',$montoControl);
                                $statement->bindValue('ASIENTO',$codigoAsiento);
                                $statement->execute();
                                $mensajes=array_merge($mensajes,array('Actualizando en: '.$tablaNombre.', para:'.$codigoAsiento));
                            }elseif($tablaNombre=='DIARIO'){
                                foreach($contenido as $consecutivo => $item):
                                    if(!empty($item['DEBITO_DOLAR'])){
                                        $monto = round($item['DEBITO_DOLAR']*$tipoCambioAsiDi,2);
                                        $updateQuery='UPDATE VIAPAC.'.$tablaNombre.' set DEBITO_LOCAL=:DEBITO_LOCAL WHERE ASIENTO=:ASIENTO AND CONSECUTIVO=:CONSECUTIVO';
                                        $statement = $this->container->get('doctrine.dbal.vipac_connection')->prepare($updateQuery);
                                        $statement->bindValue('DEBITO_LOCAL',$monto);
                                        $statement->bindValue('ASIENTO',$codigoAsiento);
                                        $statement->bindValue('CONSECUTIVO',$consecutivo);
                                        $statement->execute();
                                        $mensajes=array_merge($mensajes,array('Actualizando en: '.$tablaNombre.', para: '.$codigoAsiento.' ,item:'.$consecutivo));
                                    }else{
                                        $monto = round($item['CREDITO_DOLAR']*$tipoCambioAsiDi,2);
                                        $updateQuery='UPDATE VIAPAC.'.$tablaNombre.' set CREDITO_LOCAL=:CREDITO_LOCAL WHERE ASIENTO=:ASIENTO AND CONSECUTIVO=:CONSECUTIVO';
                                        $statement = $this->container->get('doctrine.dbal.vipac_connection')->prepare($updateQuery);
                                        $statement->bindValue('CREDITO_LOCAL',$monto);
                                        $statement->bindValue('ASIENTO',$codigoAsiento);
                                        $statement->bindValue('CONSECUTIVO',$consecutivo);
                                        $statement->execute();
                                        $mensajes=array_merge($mensajes,array('Actualizando en: '.$tablaNombre.', para: '.$codigoAsiento.' ,item:'.$consecutivo));
                                    }
                                endforeach;
                            }
                        endforeach;
                    }
                endforeach;
                $mensajes=array_merge($mensajes,array('No existen datos para generar archivo'));
            }else{
                $mensajes=array_merge($mensajes,array('El archivo no se puede procesar'));
            }
        }
        $mensajes=array_merge($mensajes,$procesoArchivo->getMensajes());
        return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $mensajes);
    }




}
