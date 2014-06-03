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

/**
 * Doccptipo controller.
 *
 * @Route("/proceso")
 */
class ProcesoController extends BaseController
{

    /**
     * @Route("/index", name="gopro_vipac_dbproceso_proceso_index")
     * @Template()
     */
    public function indexAction(){


    }

    /**
     * @Route("/cheque/{archivoEjecutar}", name="gopro_vipac_dbproceso_proceso_cheque", defaults={"archivoEjecutar" = null})
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
        if(!$carga->setParametros($procesoArchivo->getTablaSpecs(),$procesoArchivo->getColumnaSpecs(),$procesoArchivo->getExistentesRaw(),$this->container->get('doctrine.dbal.vipac_connection'))){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('Los parametros de carga no son correctos');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }
        $carga->ejecutar();
        $existente=$carga->getProceso()->getExistentesIndizados();

        if(empty($existente)){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('No existen datos para generar archivo');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }

        foreach($procesoArchivo->getExistentesIndizados() as $key=>$valores):
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

    //Cuenta contable normal del impuesto 2 64.1.1.1.01
    //El subtotal cambia de cuenta si es diferido
    /**
     * @Route("/cargadorcp/{archivoEjecutar}", name="gopro_vipac_dbproceso_proceso_cargadorcp", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function cargadorcpAction(Request $request,$archivoEjecutar)
    {
        $operacion='proceso_cargadorcp';
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

        $archivoInfo=$this->get('gopro_dbproceso_comun_archivo');
        if(!$archivoInfo->validarArchivo($repositorio,$archivoEjecutar,$operacion)){
            $this->setMensajes($archivoInfo->getMensajes());
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }
        $tablaSpecs=array('schema'=>'VIAPAC',"nombre"=>'PROVEEDOR','tipo'=>'S');
        $columnaspecs[]=array('nombre'=>'TIPO','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'DIFERIDO','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'PROVEEDOR','llave'=>'si');
        $columnaspecs[]=array('nombre'=>'DOCUMENTO','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'MONTO','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'MONEDA','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FECHA_RIGE','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FECHA_DOCUMENTO','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FECHA_CONTABLE','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'APLICACION','llave'=>'no','proceso'=>'no');
        //$columnaspecs[]=array('nombre'=>'VOUCHER','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'DOCUMENTO_ASOCIADO','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_1','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_2','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_3','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_4','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_5','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_6','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_7','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_8','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_9','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_10','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_11','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_12','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_13','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_14','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_15','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_16','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_17','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_18','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_19','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'FILE_20','llave'=>'no','proceso'=>'no');
        $columnaspecs[]=array('nombre'=>'CONDICION_PAGO','llave'=>'no');

        $archivoInfo->setParametros($tablaSpecs,$columnaspecs);
        $archivoInfo->setCamposCustom(['FILE_1','FILE_2','FILE_3','FILE_4','FILE_5','FILE_6','FILE_7','FILE_8','FILE_9','FILE_10','FILE_11','FILE_12','FILE_13','FILE_14','FILE_15','FILE_16','FILE_17','FILE_18','FILE_19','FILE_20']);
        $archivoInfo->setDescartarBlanco(true);
        if(!$archivoInfo->parseExcel()){
            $this->setMensajes($archivoInfo->getMensajes());
            $this->setMensajes('El archivo no se puede procesar');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }

        $datosProveedor=$this->get('gopro_dbproceso_comun_cargador');
        if(!$datosProveedor->setParametros($archivoInfo->getTablaSpecs(),$archivoInfo->getColumnaSpecs(),$archivoInfo->getExistentesRaw(),$this->container->get('doctrine.dbal.vipac_connection'))){
            $this->setMensajes($archivoInfo->getMensajes());
            $this->setMensajes($datosProveedor->getMensajes());
            $this->setMensajes('Los parametros de carga no son correctos');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }
        $datosProveedor->ejecutar();
        if(empty($datosProveedor->getProceso()->getExistentesRaw())){
            $this->setMensajes($archivoInfo->getMensajes());
            $this->setMensajes($datosProveedor->getMensajes());
            $this->setMensajes('No existe ningun proveedor de los ingresados');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }

        $filesMulti=$archivoInfo->getExistentesCustomRaw();

        if(!empty($filesMulti)){
            array_walk_recursive($filesMulti,[$this,'setStack'],['files','NUM_FILE']);
        }

        $filesInfo=$this->container->get('gopro_dbproceso_comun_proceso');
        $filesInfo->setConexion($this->container->get('doctrine.dbal.vipac_connection'));
        $filesInfo->setTabla('VVW_FILE_MERCADO_SINGLEKEY');
        $filesInfo->setSchema('RESERVAS');
        $filesInfo->setCamposSelect([
            'NUM_FILE',
            'NOMBRE',
            'NUM_PAX',
            'MERCADO',
            'CENTRO_COSTO',
            'PAIS_FILE'
        ]);

        if(!empty($this->getStack('files'))){
            $filesInfo->setQueryVariables($this->getStack('files'));

            if(!$filesInfo->ejecutarSelectQuery()||empty($filesInfo->getExistentesRaw())){
                $this->setMensajes($archivoInfo->getMensajes());
                $this->setMensajes($filesInfo->getMensajes());
                $this->setMensajes('No existe ninguno de los files en la lista');
                return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

            }
        }

        $generarExcel=true;

        $query = $this->getDoctrine()->getManager()->createQuery("SELECT tipo FROM GoproVipacDbprocesoBundle:Doccptipo tipo INDEX BY tipo.id");
        $docCpTipos = $query->getArrayResult();
        $result=array();


        foreach($archivoInfo->getExistentesRaw() as $nroLinea => $linea):
            $dataCP[$nroLinea]=$linea;


            if(!empty($archivoInfo->getExistentesCustomRaw()[$nroLinea])){
                $dataCP[$nroLinea]['FILES']=array_unique(array_flip($archivoInfo->getExistentesCustomRaw()[$nroLinea]));
            }
            $dataCP[$nroLinea]['CONDICION_PAGO']=$datosProveedor->getProceso()->getExistentesIndizados()[$dataCP[$nroLinea]['PROVEEDOR']]['CONDICION_PAGO'];
            if(isset($docCpTipos[$dataCP[$nroLinea]['TIPO']])){
                $dataCP[$nroLinea]['CONDICIONES']=$docCpTipos[$dataCP[$nroLinea]['TIPO']];
            }else{
                $this->setMensajes('El tipo de documento establecido para la linea: '.$nroLinea.', no existe');
                $generarExcel=false;
            }

            if(isset($dataCP[$nroLinea]['FILES'])){
                foreach($dataCP[$nroLinea]['FILES'] as $nroFile => $posicion):
                    if(isset($filesInfo->getExistentesIndizados()[$nroFile])){
                        $dataCP[$nroLinea]['FILES'][$nroFile]=$filesInfo->getExistentesIndizados()[$nroFile];

                    }else{
                        $this->setMensajes('El numero de file: '.$nroFile.', de la linea: '.$nroLinea.', no existe');
                        $generarExcel=false;
                    }
                endforeach;
            }else{
                $dataCP[$nroLinea]['FILES']=['ND'=>['NOMBRE'=>'ND','CENTRO_COSTO'=>'0.00.00.00','MERCADO'=>'ND','PAIS_FILE'=>'ND','NUM_PAX'=>1]];
            }


            $dataCP[$nroLinea]['RUBROS']=$this->setRubros($dataCP[$nroLinea]['CONDICIONES'],$dataCP[$nroLinea]['MONTO']);
            if(empty($dataCP[$nroLinea]['RUBROS'])){
                $this->setMensajes('El tipo de documento establecido para la linea: '.$nroLinea.', no puede ser procesado');
                $generarExcel=false;
            }
            array_walk_recursive($dataCP[$nroLinea]['FILES'], [$this, 'setCantidadTotal'],['totalPax','NUM_PAX']);
            $dataCP[$nroLinea]['TOTAL_PAX']=$this->getCantidadTotal('totalPax');
            $this->resetCantidadTotal('totalPax');

            $result[$nroLinea]['PROVEEDOR']=$dataCP[$nroLinea]['PROVEEDOR'];
            $result[$nroLinea]['TIPO']=$dataCP[$nroLinea]['CONDICIONES']['tipo'];
            $result[$nroLinea]['DOCUMENTO']=$dataCP[$nroLinea]['DOCUMENTO'];
            $result[$nroLinea]['FECHA_DOCUMENTO']=$dataCP[$nroLinea]['FECHA_DOCUMENTO'];
            $result[$nroLinea]['FECHA_RIGE']=$dataCP[$nroLinea]['FECHA_RIGE'];
            $result[$nroLinea]['APLICACION']=$dataCP[$nroLinea]['APLICACION'];
            $result[$nroLinea]['SUBTOTAL']=$dataCP[$nroLinea]['RUBROS']['subtotal'];
            $result[$nroLinea]['SUBTOTAL_CUENTA']=$dataCP[$nroLinea]['CONDICIONES']['subtotal'];
            if(!empty($dataCP[$nroLinea]['RUBROS']['impuesto1'])){
                $result[$nroLinea]['IMPUESTO1']=$dataCP[$nroLinea]['RUBROS']['impuesto1'];
            }else{
                $result[$nroLinea]['IMPUESTO1']='';
            }
            if(!empty($dataCP[$nroLinea]['RUBROS']['impuesto2'])){
                $result[$nroLinea]['IMPUESTO2']=$dataCP[$nroLinea]['RUBROS']['impuesto2'];
            }else{
                $result[$nroLinea]['IMPUESTO2']='';
            }
            if(!empty($dataCP[$nroLinea]['CONDICIONES']['impuesto2'])){
                if(empty($dataCP[$nroLinea]['DIFERIDO'])){
                    $result[$nroLinea]['IMPUESTO2_CUENTA']='64.1.1.1.01';
                }else{
                    $result[$nroLinea]['IMPUESTO2_CUENTA']='IMP2DIFF';
                }
            }else{
                $result[$nroLinea]['IMPUESTO2_CUENTA']='';
            }
            if(!empty($dataCP[$nroLinea]['RUBROS']['rubro1'])){
                $result[$nroLinea]['RUBRO1']=$dataCP[$nroLinea]['RUBROS']['rubro1'];
            }else{
                $result[$nroLinea]['RUBRO1']='';
            }
            $result[$nroLinea]['RUBRO1_CUENTA']=$dataCP[$nroLinea]['CONDICIONES']['rubro1'];
            if(!empty($dataCP[$nroLinea]['RUBROS']['rubro2'])){
                $result[$nroLinea]['RUBRO2']=$dataCP[$nroLinea]['RUBROS']['rubro2'];
            }else{
                $result[$nroLinea]['RUBRO2']='';
            }
            $result[$nroLinea]['RUBRO2_CUENTA']=$dataCP[$nroLinea]['CONDICIONES']['rubro2'];
            $result[$nroLinea]['MONTO']=$dataCP[$nroLinea]['MONTO'];
            $result[$nroLinea]['MONEDA']=$dataCP[$nroLinea]['MONEDA'];
            $result[$nroLinea]['CONDICION_PAGO']=$dataCP[$nroLinea]['CONDICION_PAGO'];
            $result[$nroLinea]['SUBTIPO']=$dataCP[$nroLinea]['CONDICIONES']['subtipo'];
            $result[$nroLinea]['FECHA_CONTABLE']=$dataCP[$nroLinea]['FECHA_CONTABLE'];
            if(!empty($dataCP[$nroLinea]['RUBROS']['impuesto1'])){
                $result[$nroLinea]['RUBRO6']='001';
            }elseif(!empty($dataCP[$nroLinea]['RUBROS']['impuesto2'])){
                $result[$nroLinea]['RUBRO6']='003';
            }else{
                $result[$nroLinea]['RUBRO6']='';
            }

            if ($this->getUser()->hasGroup('Cusco')) {
                $result[$nroLinea]['RUBRO7']='CUZCO';
                $mercadoSufijo='.CU.OP';
            }else{
                $result[$nroLinea]['RUBRO7']='LIMA';
                $mercadoSufijo='.LI.OP';
            }
            if (!empty($dataCP[$nroLinea]['VOUCHER'])) {
                $result[$nroLinea]['RUBRO8']=$dataCP[$nroLinea]['VOUCHER'];
            }else{
                $result[$nroLinea]['RUBRO8']='N';
            }
            if (($dataCP[$nroLinea]['MONTO']>=700&&$result[$nroLinea]['TIPO']!='RHP')||$dataCP[$nroLinea]['MONTO']>1500) {
                $result[$nroLinea]['RUBRO10']=$dataCP[$nroLinea]['CONDICIONES']['retencion'];
                $result[$nroLinea]['RETENCION']=$dataCP[$nroLinea]['CONDICIONES']['codretencion'];
            }else{
                $result[$nroLinea]['RUBRO10']='';
                $result[$nroLinea]['RETENCION']='';
            }
            if (!empty($dataCP[$nroLinea]['DOCUMENTO_ASOCIADO'])) {
                $result[$nroLinea]['TIPO_REFERENCIA']='FAC';
                $result[$nroLinea]['DOC_REFERENCIA']=$dataCP[$nroLinea]['DOCUMENTO_ASOCIADO'];
            }else{
                $result[$nroLinea]['TIPO_REFERENCIA']='';
                $result[$nroLinea]['DOC_REFERENCIA']='';
            }

            $i=1;
            foreach($dataCP[$nroLinea]['FILES'] as $nroFile => $file):
                $result[$nroLinea]['FILE'.$i]=$nroFile;
                if(empty($file['CENTRO_COSTO'])&&!empty($file['PAIS_FILE'])){
                    $result[$nroLinea]['FILE'.$i.'_CC']=$file['PAIS_FILE'];
                }elseif(!empty($file['CENTRO_COSTO'])&&$file['CENTRO_COSTO']=='0.00.00.00'){
                    $result[$nroLinea]['FILE'.$i.'_CC']=$file['CENTRO_COSTO'];
                }elseif(!empty($file['CENTRO_COSTO'])){
                    $result[$nroLinea]['FILE'.$i.'_CC']=$file['CENTRO_COSTO'].$mercadoSufijo;
                }else{
                    $result[$nroLinea]['FILE'.$i.'_CC']='';
                }
                foreach($dataCP[$nroLinea]['RUBROS'] as $nombreRubro => $montoRubro):
                    $montoProcesado=0;
                    if($i<count($dataCP[$nroLinea]['FILES'])){

                        if(!empty($montoRubro)&&!empty($dataCP[$nroLinea]['TOTAL_PAX'])){
                            $montoProcesado=round($montoRubro/$dataCP[$nroLinea]['TOTAL_PAX']*$file['NUM_PAX'],2);
                            $this->setCantidadTotal($montoProcesado,null,[$nombreRubro,null]);
                        }
                    }else{
                        $montoProcesado=$montoRubro-$this->getCantidadTotal($nombreRubro);
                    }
                    if(isset($dataCP[$nroLinea]['FILES'][$nroFile]['montos'])){
                        $dataCP[$nroLinea]['FILES'][$nroFile]['montos'][$nombreRubro]=$montoProcesado;
                    }

                    if($nombreRubro!='impuesto1'){
                        if(!empty($montoProcesado)){
                            $result[$nroLinea]['FILE'.$i.'_'.$nombreRubro]=$montoProcesado;
                        }else{
                            $result[$nroLinea]['FILE'.$i.'_'.$nombreRubro]='';
                        }

                    }
                endforeach;
                $i++;
            endforeach;
            $this->resetCantidadTotal('subtotal');
            $this->resetCantidadTotal('impuesto1');
            $this->resetCantidadTotal('impuesto2');
            $this->resetCantidadTotal('rubro1');
            $this->resetCantidadTotal('rubro2');
        endforeach;

        if($generarExcel===false){
            $this->setMensajes($archivoInfo->getMensajes());
            $this->setMensajes($datosProveedor->getMensajes());
            $this->setMensajes('No se general el achivo, existen observaciones');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }
        $encabezados = ['PROVEEDOR',
            'TIPO',
            'DOCUMENTO',
            'FECHA DOCUMENTO',
            'FECHA RIGE',
            'APLICACION',
            'SUBTOTAL',
            'CUENTA CONTABLE',
            'IMPUESTO1',
            'IMPUESTO2',
            'CUENTA CONTABLE IGV NO GRAVADO',
            'RUBRO 1(EXONERADO)',
            'CUENTA CONTABLE EXONERADO',
            'RUBRO 2',
            'CUENTA CONTABLE INAFECTO',
            'MONTO',
            'MONEDA',
            'CONDICION_PAGO',
            'SUBTIPO',
            'FECHA CONTABLE',
            'RUBRO 6',
            'DOC RUBRO 7',
            'DOC RUBRO 8',
            'DOC RUBRO 10',
            'DOC RETENCIÓN',
            'TIPO REFERENCIA',
            'DOC REFERENCIA',

        ];
        for ($i=1;$i<=20;$i++){
            $encabezados[]='FILE '.$i;
            $encabezados[]='CENTRO DE COSTO  '.$i;
            $encabezados[]='DISTRIBUCION MONTO '.$i;
            $encabezados[]='DISTRIBUCION EXONERADO '.$i;
            $encabezados[]='DISTRIBUCION IMPUESTO NO GRAVADO '.$i;
            $encabezados[]='DISTRIBUCION INAFECTO '.$i;
        }

        $respuesta=$this->get('gopro_dbproceso_comun_archivo')->escribirExcel($archivoInfo->getArchivoValido()->getNombre(),$encabezados,$this->container->get('gopro_dbproceso_comun_variable')->utf($result),['d','e','t']);
        return $respuesta;
    }

    /*
     * @param array $condiciones
     * @param double $monto
     * @return array
     */
    private function setRubros($condiciones,$monto){
        $igv=18;
        if(
            !empty($condiciones['subtotal'])
            &&empty($condiciones['impuesto1'])
            &&empty($condiciones['impuesto2'])
            &&empty($condiciones['rubro1'])
            &&empty($condiciones['rubro2'])
        ){
            $rubros['subtotal']=round($monto,2);
            $rubros['impuesto1']=0;
            $rubros['impuesto2']=0;
            $rubros['rubro1']=0;
            $rubros['rubro2']=0;
        }elseif(
            !empty($condiciones['subtotal'])
            &&!empty($condiciones['impuesto1'])
            &&empty($condiciones['impuesto2'])
            &&empty($condiciones['rubro1'])
            &&empty($condiciones['rubro2'])
        ){
            $rubros['subtotal']=round($monto/(1+$igv/100),2);
            $rubros['impuesto1']=round($monto-$rubros['subtotal'],2);
            $rubros['impuesto2']=0;
            $rubros['rubro1']=0;
            $rubros['rubro2']=0;
        }elseif(
            !empty($condiciones['subtotal'])
            &&empty($condiciones['impuesto1'])
            &&!empty($condiciones['impuesto2'])
            &&empty($condiciones['rubro1'])
            &&empty($condiciones['rubro2'])
        ){
            $rubros['subtotal']=round($monto/(1+$igv/100),2);
            $rubros['impuesto1']=0;
            $rubros['impuesto2']=round($monto-$rubros['subtotal'],2);
            $rubros['rubro1']=0;
            $rubros['rubro2']=0;
        }elseif(//solo rubro 1
            empty($condiciones['subtotal'])
            &&empty($condiciones['impuesto1'])
            &&empty($condiciones['impuesto2'])
            &&!empty($condiciones['rubro1'])
            &&empty($condiciones['rubro2'])
        ){
            $rubros['subtotal']=0;
            $rubros['impuesto1']=0;
            $rubros['impuesto2']=0;
            $rubros['rubro1']=round($monto,2);
            $rubros['rubro2']=0;
        }elseif(
            empty($condiciones['subtotal'])
            &&empty($condiciones['impuesto1'])
            &&empty($condiciones['impuesto2'])
            &&empty($condiciones['rubro1'])
            &&!empty($condiciones['rubro2'])
        ){
            $rubros['subtotal']=0;
            $rubros['impuesto1']=0;
            $rubros['impuesto2']=0;
            $rubros['rubro1']=0;
            $rubros['rubro2']=round($monto,2);
        }elseif(//restaurantes nacional
            !empty($condiciones['subtotal'])
            &&!empty($condiciones['impuesto1'])
            &&empty($condiciones['impuesto2'])
            &&empty($condiciones['rubro1'])
            &&!empty($condiciones['rubro2'])
            &&!empty($condiciones['rubro2porcentaje'])
        ){
            $rubros['subtotal']=round($monto/(1+$igv/100+$condiciones['rubro2porcentaje']/100),2);
            $rubros['impuesto1']=round($rubros['subtotal']/$igv*100,2);
            $rubros['impuesto2']=0;
            $rubros['rubro1']=0;
            $rubros['rubro2']=round($monto-$rubros['subtotal']-$rubros['impuesto1'],2);
        }elseif(//restaurantes extranjero
            !empty($condiciones['subtotal'])
            &&empty($condiciones['impuesto1'])
            &&!empty($condiciones['impuesto2'])
            &&empty($condiciones['rubro1'])
            &&!empty($condiciones['rubro2'])
            &&!empty($condiciones['rubro2porcentaje'])
        ){
            $rubros['subtotal']=round($monto/(1+$igv/100+$condiciones['rubro2porcentaje']/100),2);
            $rubros['impuesto1']=0;
            $rubros['impuesto2']=round($rubros['subtotal']/$igv*100,2);
            $rubros['rubro1']=0;
            $rubros['rubro2']=round($monto-$rubros['subtotal']-$rubros['impuesto2'],2);
        }

        if(isset($rubros)){
            return $rubros;
        }else{
            return array();
        }
    }



    /**
     * @Route("/calcc/{archivoEjecutar}", name="gopro_vipac_dbproceso_proceso_calcc", defaults={"archivoEjecutar" = null})
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
        $procesoArchivo->setCamposCustom(['CREDITO_LOCAL','CREDITO_DOLAR','DOCUMENTO']);

        if(!$procesoArchivo->parseExcel()){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes('El archivo no se puede procesar');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }
        $carga=$this->get('gopro_dbproceso_comun_cargador');
        if(!$carga->setParametros($procesoArchivo->getTablaSpecs(),$procesoArchivo->getColumnaSpecs(),$procesoArchivo->getExistentesRaw(),$this->container->get('doctrine.dbal.vipac_connection'))){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('Los parametros de carga no son correctos');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }
        $carga->getProceso()->setCamposCustom(['ANO','NUM_FILE_FISICO']);
        $carga->ejecutar();

        if(empty($carga->getProceso()->getExistentesCustomRaw())){
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
        $serviciosHoteles->setQueryVariables($carga->getProceso()->getExistentesCustomRaw());
        if(!$serviciosHoteles->ejecutarSelectQuery()||empty($serviciosHoteles->getExistentesRaw())){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('No existen datos para generar archivo');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }
        foreach($carga->getProceso()->getExistentesRaw() as $valor):
            if(
                isset($serviciosHoteles->getExistentesIndizadosMulti()[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']])
                &&isset($procesoArchivo->getExistentesCustomIndizados()[$valor['ASIENTO']])
                &&isset($procesoArchivo->getExistentesCustomIndizados()[$valor['ASIENTO']]['CREDITO_DOLAR'])
                &&!empty($procesoArchivo->getExistentesCustomIndizados()[$valor['ASIENTO']]['CREDITO_DOLAR'])
                &&isset($procesoArchivo->getExistentesCustomIndizados()[$valor['ASIENTO']]['CREDITO_LOCAL'])
                &&!empty($procesoArchivo->getExistentesCustomIndizados()[$valor['ASIENTO']]['CREDITO_LOCAL'])
                &&isset($procesoArchivo->getExistentesCustomIndizados()[$valor['ASIENTO']]['DOCUMENTO'])
                &&!empty($procesoArchivo->getExistentesCustomIndizados()[$valor['ASIENTO']]['DOCUMENTO'])
            ){
                $preResultado[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']]=$valor;
                $preResultado[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']]=array_merge($preResultado[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']],$procesoArchivo->getExistentesCustomIndizados()[$valor['ASIENTO']]);
                $preResultado[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']]['items']=$serviciosHoteles->getExistentesIndizadosMulti()[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']];
                array_walk_recursive($preResultado[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']]['items'], [$this, 'setCantidadTotal'],['montoTotal','MONTO']);
                $preResultado[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']]['sumaMonto']=$this->getCantidadTotal('montoTotal');
                if($this->getCantidadTotal('montoTotal')==0){
                    $coeficiente=0;
                }else{
                    $coeficiente=$preResultado[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']]['CREDITO_DOLAR']/$this->getCantidadTotal('montoTotal');
                }
                $preResultado[$valor['ANO'].'|'.$valor['NUM_FILE_FISICO']]['coeficiente']=$coeficiente;
                $this->resetCantidadTotal('montoTotal');
            }else{
                if(empty($valor['ANO'])||empty($valor['NUM_FILE_FISICO'])){
                    $this->setMensajes('No hay resultados para el CC: '.$valor['ASIENTO']);
                }else{
                    $this->setMensajes('No hay resultados para el CC: '.$valor['ASIENTO'].', con file:'.$valor['ANO'].'-'.$valor['NUM_FILE_FISICO']);
                }
            }
        endforeach;
        if(empty($preResultado)){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('No hay datos para procesar los resultados');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }
        $i=0;
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
                $resultado[$i]['DOCUMENTO']=$valor['DOCUMENTO'];
                $resultado[$i]['MONTO_PRORRATEADO']=$item['MONTO']*$valor['coeficiente'];
                $resultado[$i]['MONTO_PRORRATEADO_LOCAL']=$resultado[$i]['MONTO_PRORRATEADO']*$valor['CREDITO_LOCAL']/$valor['CREDITO_DOLAR'];
                $resultado[$i]['COEFICIENTE']=$valor['coeficiente'];
                //
                $i++;
            endforeach;
        endforeach;
        if(empty($resultado)){
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
     * @Route("/calxfile/{archivoEjecutar}", name="gopro_vipac_dbproceso_proceso_calxfile", defaults={"archivoEjecutar" = null})
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
        if(!$carga->setParametros($procesoArchivo->getTablaSpecs(),$procesoArchivo->getColumnaSpecs(),$procesoArchivo->getExistentesRaw(),$this->container->get('doctrine.dbal.vipac_connection'))){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('Los parametros de carga no son correctos');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }
        $carga->ejecutar();
        $existente=$carga->getProceso()->getExistentesIndizados();

        if(empty($existente)){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('No existen datos para generar archivo');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }

        foreach($procesoArchivo->getExistentesIndizados() as $key=>$valores):
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
     * @Route("/calxreserva/{archivoEjecutar}", name="gopro_vipac_dbproceso_proceso_calxreserva", defaults={"archivoEjecutar" = null})
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
        if(!$carga->setParametros($procesoArchivo->getTablaSpecs(),$procesoArchivo->getColumnaSpecs(),$procesoArchivo->getExistentesRaw(),$this->container->get('doctrine.dbal.vipac_connection'))){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('los parametros de carga no son correctos');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }
        $carga->ejecutar();
        $existente=$carga->getProceso()->getExistentesIndizados();

        if(empty($existente)){
            $this->setMensajes($procesoArchivo->getMensajes());
            $this->setMensajes($carga->getMensajes());
            $this->setMensajes('No existen datos para generar archivo');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }

        foreach($procesoArchivo->getExistentesIndizados() as $key=>$valores):
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
     * @Route("/editararchivo", name="gopro_vipac_dbproceso_proceso_editararchivo")
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
    //@TODO: implementar funcion
    /**
     * @Route("/agregararchivo", name="gopro_vipac_dbproceso_proceso_agregararchivo")
     * @Method({"POST"})
     * @Template()
     */
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
