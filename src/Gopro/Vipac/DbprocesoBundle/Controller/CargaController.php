<?php

namespace Gopro\Vipac\DbprocesoBundle\Controller;

use Gopro\Vipac\DbprocesoBundle\Entity\Archivo;
use Gopro\Vipac\MainBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Gopro\Vipac\DbprocesoBundle\Comun\Archivo as ArchivoOpe;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * Doccptipo controller.
 *
 * @Route("/carga")
 */
class CargaController extends BaseController
{
    /**
     * @Route("/index/{name}", name="gopro_vipac_dbproceso_carga_index")
     * @Template()
     */
    public function indexAction($pais)
    {

        return array('paises' => $pais);
    }

    /**
     * @Route("/generico/{archivoEjecutar}", name="gopro_vipac_dbproceso_carga_generico", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function genericoAction(Request $request,$archivoEjecutar)
    {

        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $this->getUserName(), 'operacion' => 'carga_generico'),array('creado' => 'DESC'));

        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();

        $formulario->handleRequest($request);
        if ($formulario->isValid()){
            $archivo->setUsuario($this->getUserName());
            $archivo->setOperacion('carga_generico');
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_carga_generico'));
        }
        $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');
        if(!$procesoArchivo->validarArchivo($repositorio,$archivoEjecutar,'carga_generico')){
            $this->setMensajes($procesoArchivo->getMensajes());
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }
        $procesoArchivo->setParametros(null,null);
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
        $this->setMensajes($procesoArchivo->getMensajes());
        $this->setMensajes($carga->getMensajes());
        return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $mensajes);
    }

    /**
     * @Route("/arreglartc/{archivoEjecutar}", name="gopro_vipac_dbproceso_carga_arreglartc", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function arreglartcAction(Request $request,$archivoEjecutar)
    {

        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $this->getUserName(), 'operacion' => 'carga_arreglartc'),array('creado' => 'DESC'));
        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();
        $formulario->handleRequest($request);
        if ($formulario->isValid()){
            $archivo->setUsuario($this->getUserName());
            $archivo->setOperacion('carga_arreglartc');
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_carga_arreglartc'));
        }
        $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');
        if(!$procesoArchivo->validarArchivo($repositorio,$archivoEjecutar,'carga_arreglartc')){
            $this->setMensajes($procesoArchivo->getMensajes());
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }

        $tablaDocCp=array('schema'=>'VIAPAC',"nombre"=>'DOCUMENTOS_CP');
        $columnaDocCp[0]=array('nombre'=>'ASIENTO','llave'=>'si');
        $columnaDocCp[1]=array('nombre'=>'MONTO','llave'=>'no');
        $columnaDocCp[2]=array('nombre'=>'FECHA_DOCUMENTO','llave'=>'no');
        $columnaDocCp[3]=array('nombre'=>'MONEDA','llave'=>'no');
        $procesoArchivo->setParametros($tablaDocCp,$columnaDocCp);
        $mensajes=$procesoArchivo->getMensajes();
        if($procesoArchivo->parseExcel()!==false){
            $documentoCp=$this->get('gopro_dbproceso_comun_cargador');
            $documentoCp->setParametros($procesoArchivo->getTablaSpecs(),$procesoArchivo->getColumnaSpecs(),$procesoArchivo->getValores(),$this->container->get('doctrine.dbal.vipac_connection'));
            $documentoCp->ejecutar();
            $exiDocumentoCp=$documentoCp->getExistenteIndex();
            if(empty($exiDocumentoCp)){
                $mensajes=array_merge($mensajes,array('No existen los asientos en Documentos CP'));
                return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $mensajes);

            }
            $tablaAsiDi=array(
                'schema'=>'VIAPAC',
                'nombre'=>'ASIENTO_DE_DIARIO',
                'columnasProceso'=>Array('ASIENTO','TOTAL_DEBITO_DOL','TOTAL_CREDITO_DOL','TOTAL_CONTROL_DOL','FECHA')
            );
            $columnaAsiDi['ASIENTO']=array('nombre'=>'ASIENTO','llave'=>'si');
            $asiDi=$this->get('gopro_dbproceso_comun_cargador');
            $asiDi->setParametros($tablaAsiDi,$columnaAsiDi,$procesoArchivo->getValores(),$this->container->get('doctrine.dbal.vipac_connection'));
            $asiDi->prepararSelect();
            $asiDi->ejecutarSelectQuery();
            $exiAsiDi=$asiDi->getExistenteIndex();
            if(empty($exiAsiDi)){
                $mensajes=array_merge($mensajes,array('No existen los asientos en Asiento de Diario, posiblemente fueron mayorizados'));
                return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $mensajes);

            }
            $tablaDi=array(
                'schema'=>'VIAPAC',
                'nombre'=>'DIARIO',
                'columnasProceso'=>Array('ASIENTO','CONSECUTIVO','DEBITO_DOLAR','CREDITO_DOLAR')
            );
            $columnaDi['ASIENTO']=array('nombre'=>'ASIENTO','llave'=>'si');
            $di=$this->get('gopro_dbproceso_comun_cargador');
            $di->setParametros($tablaDi,$columnaDi,$procesoArchivo->getValores(),$this->container->get('doctrine.dbal.vipac_connection'));
            $di->prepararSelect();
            $di->ejecutarSelectQuery();

            $exiDi=$di->getExistenteIndex();
            if(empty($exiDi)){
                $mensajes=array_merge($mensajes,array('No existen los asientos en el Diario, posiblemente fueron mayorizados'));
                return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $mensajes);

            }
            foreach ($exiDi as $key => $valores):
                $keyArray=explode('|',$key);
                $resultado[$keyArray[0]]['DIARIO'][$keyArray[1]]=$valores;
                $resultado[$keyArray[0]]['ASIENTO_DE_DIARIO']=$exiAsiDi[$keyArray[0]];
                $resultado[$keyArray[0]]['DOCUMENTOS_CP']=$exiDocumentoCp[$keyArray[0]];
                $fechas[]=$exiDocumentoCp[$keyArray[0]]['FECHA_DOCUMENTO'];

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
                'columnasProceso'=>Array('FECHA','TIPO_CAMBIO','MONTO')
            );
            $columnaTc['FECHA']=array('nombre'=>'FECHA','llave'=>'si');
            $tc=$this->get('gopro_dbproceso_comun_cargador');
            $tc->setParametros($tablaTc,$columnaTc,$fechaBuscar,$this->container->get('doctrine.dbal.vipac_connection'));
            $tc->prepararSelect();
            $tc->ejecutarSelectQuery();
            $exiTc=$tc->getExistenteIndex();
            foreach ($resultado as $codigoAsiento => $procesoTablas):
                $procesar='si';
                if(!isset($resultado[$codigoAsiento]['DOCUMENTOS_CP']['FECHA_DOCUMENTO'])){
                    $mensajes=array_merge($mensajes,array('No existe la fecha para: '.$codigoAsiento));
                    $procesar='no';
                }else{
                    if(!isset($exiTc[$resultado[$codigoAsiento]['DOCUMENTOS_CP']['FECHA_DOCUMENTO']])){
                        $mensajes=array_merge($mensajes,array('No hay tipo de cambio para la fecha para: '.$resultado[$codigoAsiento]['DOCUMENTOS_CP']['FECHA_DOCUMENTO']));
                        $procesar='no';
                    }else{
                        $tipoCambio=$exiTc[$resultado[$codigoAsiento]['DOCUMENTOS_CP']['FECHA_DOCUMENTO']]['MONTO'];
                    }
                };
                if($procesar=='si'&& $resultado[$codigoAsiento]['DOCUMENTOS_CP']['MONEDA']!='USD'){
                    $procesar='no';
                    $mensajes=array_merge($mensajes,array('la moneda no es dolar para: '.$codigoAsiento));
                }

                if($procesar=='si'){
                    foreach($procesoTablas as $tablaNombre => $contenido):
                        if($tablaNombre=='DOCUMENTOS_CP'){
                            $monto = round($contenido['MONTO']*$tipoCambio,2);
                            $updateQuery='UPDATE VIAPAC.'.$tablaNombre.' set MONTO_LOCAL=:MONTO, SALDO_LOCAL=:MONTO, TIPO_CAMBIO_MONEDA=:TC, TIPO_CAMBIO_DOLAR=:TC, TIPO_CAMBIO_PROV=:TC, TIPO_CAMB_ACT_LOC=:TC, TIPO_CAMB_ACT_DOL=:TC, TIPO_CAMB_ACT_PROV=:TC WHERE ASIENTO=:ASIENTO';
                            $statement = $this->container->get('doctrine.dbal.vipac_connection')->prepare($updateQuery);
                            $statement->bindValue('MONTO',$monto);
                            $statement->bindValue('TC',$tipoCambio);
                            $statement->bindValue('ASIENTO',$codigoAsiento);
                            $statement->execute();
                            $mensajes=array_merge($mensajes,array('Actualizando en: '.$tablaNombre.', para:'.$codigoAsiento));
                        }elseif($tablaNombre=='ASIENTO_DE_DIARIO'){
                            $montoDebito = round($contenido['TOTAL_DEBITO_DOL']*$tipoCambio,2);
                            $montoCredito = round($contenido['TOTAL_CREDITO_DOL']*$tipoCambio,2);
                            $montoControl = round($contenido['TOTAL_CONTROL_DOL']*$tipoCambio,2);
                            $updateQuery='UPDATE VIAPAC.'.$tablaNombre.' set TOTAL_DEBITO_LOC=:TOTAL_DEBITO_LOC, TOTAL_CREDITO_LOC=:TOTAL_CREDITO_LOC, TOTAL_CONTROL_LOC=:TOTAL_CONTROL_LOC WHERE ASIENTO=:ASIENTO';
                            $statement = $this->container->get('doctrine.dbal.vipac_connection')->prepare($updateQuery);
                            $statement->bindValue('TOTAL_DEBITO_LOC',$montoDebito);
                            $statement->bindValue('TOTAL_CREDITO_LOC',$montoCredito);
                            $statement->bindValue('TOTAL_CONTROL_LOC',$montoControl);
                            $statement->bindValue('ASIENTO',$codigoAsiento);
                            $statement->execute();
                            $mensajes=array_merge($mensajes,array('Actualizando en: '.$tablaNombre.', para:'.$codigoAsiento));
                        }elseif($tablaNombre=='DIARIO'){
                            asort($contenido);
                            if(empty($contenido[1]['CREDITO_DOLAR'])){
                                $mensajes=array_merge($mensajes,array('No se actualiza: '.$tablaNombre.', para:'.$codigoAsiento.', el primer item no es credito'));
                            }else{
                                $contador=1;
                                $montoTotalCredito=0;
                                $montoTotalDebito=0;
                                foreach($contenido as $consecutivo => $item):
                                    if(empty($item['DEBITO_DOLAR'])){
                                        $monto = round($item['CREDITO_DOLAR']*$tipoCambio,2);
                                        $montoTotalCredito=$monto;
                                        $updateQuery='UPDATE VIAPAC.'.$tablaNombre.' set CREDITO_LOCAL=:CREDITO_LOCAL WHERE ASIENTO=:ASIENTO AND CONSECUTIVO=:CONSECUTIVO';
                                        $statement = $this->container->get('doctrine.dbal.vipac_connection')->prepare($updateQuery);
                                        $statement->bindValue('CREDITO_LOCAL',$monto);
                                        $statement->bindValue('ASIENTO',$codigoAsiento);
                                        $statement->bindValue('CONSECUTIVO',$consecutivo);
                                        $statement->execute();
                                        $mensajes=array_merge($mensajes,array('Actualizando en: '.$tablaNombre.', para: '.$codigoAsiento.' ,item:'.$consecutivo));

                                    }else{
                                        if(count($contenido)==$contador){
                                            $monto=$montoTotalCredito-$montoTotalDebito;
                                        }else{
                                            $monto = round($item['DEBITO_DOLAR']*$tipoCambio,2);
                                            $montoTotalDebito=$montoTotalDebito+$monto;
                                        }
                                        $updateQuery='UPDATE VIAPAC.'.$tablaNombre.' set DEBITO_LOCAL=:DEBITO_LOCAL WHERE ASIENTO=:ASIENTO AND CONSECUTIVO=:CONSECUTIVO';
                                        $statement = $this->container->get('doctrine.dbal.vipac_connection')->prepare($updateQuery);
                                        $statement->bindValue('DEBITO_LOCAL',$monto);
                                        $statement->bindValue('ASIENTO',$codigoAsiento);
                                        $statement->bindValue('CONSECUTIVO',$consecutivo);
                                        $statement->execute();
                                        $mensajes=array_merge($mensajes,array('Actualizando en: '.$tablaNombre.', para: '.$codigoAsiento.' ,item:'.$consecutivo));
                                    }
                                    $contador++;
                                endforeach;
                            }
                        }
                    endforeach;
                }
            endforeach;

            $mensajes=array_merge($mensajes,array('No existen datos para generar archivo'));
        }else{
            $mensajes=array_merge($mensajes,array('El archivo no se puede procesar'));
        }

        $mensajes=array_merge($mensajes,$procesoArchivo->getMensajes());
        return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $mensajes);
    }
}
