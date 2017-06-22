<?php

namespace Gopro\Vipac\DbprocesoBundle\Controller;

use Gopro\MainBundle\Form\ArchivocamposType;
use Gopro\MainBundle\Entity\Archivo;
use Gopro\MainBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Proceso controller.
 *
 * @Route("/procesosap")
 */
class ProcesosapController extends BaseController
{

    /**
     * @Route("/generico/{archivoEjecutar}", name="gopro_vipac_dbproceso_procesosap_generico", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function genericoAction(Request $request, $archivoEjecutar)
    {

        $operacion = 'vipac_dbproceso_procesosap_generico';
        $repositorio = $this->getDoctrine()->getRepository('GoproMainBundle:Archivo');
        $archivosAlmacenados = $repositorio->findBy(array('user' => $this->getUser(), 'operacion' => $operacion), array('creado' => 'DESC'));

        $opciones = array('operacion' => $operacion);
        $formulario = $this->createForm(new ArchivocamposType(), $opciones, array(
            'action' => $this->generateUrl('gopro_main_archivo_create'),
        ));

        $formulario->handleRequest($request);

        if (empty($archivoEjecutar)) {
            $this->setMensajes('No se ha definido ningun archivo');
            return array('formulario' => $formulario->createView(), 'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }

        $tablaSpecs = array('filasDescartar' => 1);
        $columnaspecs[] = array('nombre' => 'TIPO_PROCESO');
        $columnaspecs[] = array('nombre' => 'COD_PROVEEDOR');
        $columnaspecs[] = array('nombre' => 'NRO_DOCUMENTO');
        $columnaspecs[] = array('nombre' => 'VALOR_TOTAL');
        $columnaspecs[] = array('nombre' => 'MONEDA');
        $columnaspecs[] = array('nombre' => 'FEC_SERVICIO');
        $columnaspecs[] = array('nombre' => 'FEC_EMISION');
        $columnaspecs[] = array('nombre' => 'FEC_RECEPCION');
        $columnaspecs[] = array('nombre' => 'FEC_CONTABLE');
        $columnaspecs[] = array('nombre' => 'DESCRIPCION');
        $columnaspecs[] = array('nombre' => 'FILE_1');
        $columnaspecs[] = array('nombre' => 'FILE_2');
        $columnaspecs[] = array('nombre' => 'FILE_3');
        $columnaspecs[] = array('nombre' => 'FILE_4');
        $columnaspecs[] = array('nombre' => 'FILE_5');
        $columnaspecs[] = array('nombre' => 'FILE_6');
        $columnaspecs[] = array('nombre' => 'FILE_7');
        $columnaspecs[] = array('nombre' => 'FILE_8');
        $columnaspecs[] = array('nombre' => 'FILE_9');
        $columnaspecs[] = array('nombre' => 'FILE_10');
        $columnaspecs[] = array('nombre' => 'FILE_11');
        $columnaspecs[] = array('nombre' => 'FILE_12');
        $columnaspecs[] = array('nombre' => 'FILE_13');
        $columnaspecs[] = array('nombre' => 'FILE_14');
        $columnaspecs[] = array('nombre' => 'FILE_15');
        $columnaspecs[] = array('nombre' => 'FILE_16');
        $columnaspecs[] = array('nombre' => 'FILE_17');
        $columnaspecs[] = array('nombre' => 'FILE_18');
        $columnaspecs[] = array('nombre' => 'FILE_19');
        $columnaspecs[] = array('nombre' => 'FILE_20');

        $archivoInfo = $this->get('gopro_main_archivoexcel')
            ->setArchivoBase($repositorio, $archivoEjecutar, $operacion)
            ->setArchivo()
            ->setSkipRows(1)
            ->setParametrosReader($tablaSpecs, $columnaspecs)
            ->setCamposCustom(['FILE_1','FILE_2','FILE_3','FILE_4','FILE_5','FILE_6','FILE_7','FILE_8','FILE_9','FILE_10','FILE_11','FILE_12','FILE_13','FILE_14','FILE_15','FILE_16','FILE_17','FILE_18','FILE_19','FILE_20'])
            ->setDescartarBlanco(true)
            ->setTrimEspacios(true);


        if (!$archivoInfo->parseExcel()) {
            $this->setMensajes($archivoInfo->getMensajes());
            $this->setMensajes('El archivo no se puede procesar');
            return array('formulario' => $formulario->createView(), 'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        } else {
            $this->setMensajes($archivoInfo->getMensajes());
        }

        $filesMulti = $archivoInfo->getExistentesCustomRaw();

        if(empty($filesMulti)){
            $this->setMensajes('No hay files para procesar');
            return array('formulario' => $formulario->createView(), 'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }

        array_walk_recursive($filesMulti,[$this,'setStackForWalk'],['files','NUM_FILE']);

        if(empty($this->getStack('files'))) {
            $this->setMensajes('No se pudieron apilar los fies');
            return array('formulario' => $formulario->createView(), 'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }
        $filesInfo=$this->container->get('gopro_dbproceso_proceso');
        $filesInfo->setConexion($this->container->get('doctrine.dbal.vipac_connection'));
        $filesInfo->setTabla('DBP_PROCESO_CARGADORCP_FILE');
        $filesInfo->setSchema('VWEB');
        $filesInfo->setCamposSelect([
            'NUM_FILE',
            'NOMBRE',
            'NUM_PAX',
            'MERCADO',
            'CENTRO_COSTO',
            'PAIS_FILE'
        ]);

        $filesInfo->setQueryVariables($this->getStack('files'));

        if(!$filesInfo->ejecutarSelectQuery()||empty($filesInfo->getExistentesRaw())){
            $this->setMensajes('No existe ninguno de los files en la lista');
            return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }


        $now = new \DateTime('now');

        $i = 0;

        foreach ($archivoInfo->getExistentesRaw() as $nroLinea => $linea):

            if (!isset($linea['FEC_EMISION'])) {//sumatoria de formato peru rail
                $this->setMensajes('La linea ' . $linea['excelRowNumber'] . ' no tiene el formato correcto en la columna fecha de emision, posiblemente es una fila de sumatoria.');
                continue;
            }

            if(!empty($archivoInfo->getExistentesCustomRaw()[$nroLinea])){
                $preproceso[$i]['Files']=array_unique($archivoInfo->getExistentesCustomRaw()[$nroLinea]);
            } else {
                $this->setMensajes('La linea ' . $linea['excelRowNumber'] . ' no tiene numero de file.');
                continue;
            }

            //predefinimos el tipo, si vacio se toma fecha de documento.
            $preproceso[$i]['TipoProceso'] = $linea['TIPO_PROCESO'];
            //fecha del servicio necesario para diferidos
            $preproceso[$i]['ServicioDate'] = $linea['FEC_SERVICIO'];
            //Cabecera
            $preproceso[$i]['DocTotal'] = $linea['VALOR_TOTAL'];
            //$preproceso[$i]['DocTaxTotal'] = $linea['VALOR_IGV'];

            $preproceso[$i]['ruc'] = $linea['COD_PROVEEDOR'];
            $preproceso[$i]['DocDate'] = $linea['FEC_CONTABLE'];
            $preproceso[$i]['TaxDate'] = $linea['FEC_EMISION'];
            $preproceso[$i]['DocDueDate'] = $preproceso[$i]['DocDate'];
            $preproceso[$i]['Currency'] = str_replace('SD', 'S$', $linea['MONEDA']);

            $preproceso[$i]['U_SYP_MDSD'] = $this->parseDocNum($linea['NRO_DOCUMENTO'])[0];
            $preproceso[$i]['U_SYP_MDCD'] = $this->parseDocNum($linea['NRO_DOCUMENTO'])[1];
            $preproceso[$i]['Comments'] = $linea['DESCRIPCION'];

            $preproceso[$i]['u_syp_fecrec'] = $linea['FEC_RECEPCION'];

            //detalle
            $preproceso[$i]['excelRowNumber'] = $linea['excelRowNumber'];

            $i++;

        endforeach;

        if (empty($preproceso)) {
            $this->setMensajes('No se preproceso ningun elemento');
            return array('formulario' => $formulario->createView(), 'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }

        return $this->generarExcel($archivoInfo->getArchivoBase()->getNombre(), $preproceso);
    }

    /**
     * @Route("/perurail/{archivoEjecutar}", name="gopro_vipac_dbproceso_procesosap_perurail", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function perurailAction(Request $request, $archivoEjecutar)
    {

        $operacion = 'vipac_dbproceso_procesosap_perurail';
        $repositorio = $this->getDoctrine()->getRepository('GoproMainBundle:Archivo');
        $archivosAlmacenados = $repositorio->findBy(array('user' => $this->getUser(), 'operacion' => $operacion), array('creado' => 'DESC'));

        $opciones = array('operacion' => $operacion);
        $formulario = $this->createForm(new ArchivocamposType(), $opciones, array(
            'action' => $this->generateUrl('gopro_main_archivo_create'),
        ));

        $formulario->handleRequest($request);

        if (empty($archivoEjecutar)) {
            $this->setMensajes('No se ha definido ningun archivo');
            return array('formulario' => $formulario->createView(), 'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }

        $tablaSpecs = array('filasDescartar' => 1);
        $columnaspecs[] = array('nombre' => 'noProcess');
        $columnaspecs[] = array('nombre' => 'FEC_EMISION');
        $columnaspecs[] = array('nombre' => 'FEC_VIAJE');
        $columnaspecs[] = array('nombre' => 'noProcess');
        $columnaspecs[] = array('nombre' => 'noProcess');
        $columnaspecs[] = array('nombre' => 'NRO_DOCUMENTO');
        $columnaspecs[] = array('nombre' => 'noProcess');
        $columnaspecs[] = array('nombre' => 'NOMBRE_PAX');
        $columnaspecs[] = array('nombre' => 'noProcess');
        $columnaspecs[] = array('nombre' => 'VALOR_NETO');
        $columnaspecs[] = array('nombre' => 'VALOR_IGV');
        $columnaspecs[] = array('nombre' => 'VALOR_TOTAL');
        $columnaspecs[] = array('nombre' => 'NUM_FILE');
        $columnaspecs[] = array('nombre' => 'RESERVA');
        $columnaspecs[] = array('nombre' => 'TIPO_PROCESO');

        $archivoInfo = $this->get('gopro_main_archivoexcel')
            ->setArchivoBase($repositorio, $archivoEjecutar, $operacion)
            ->setArchivo()
            ->setSkipRows(1)
            ->setParametrosReader($tablaSpecs, $columnaspecs)
            //->setCamposCustom(['NUM_FILE'])
            ->setDescartarBlanco(true)
            ->setTrimEspacios(true);


        if (!$archivoInfo->parseExcel()) {
            $this->setMensajes($archivoInfo->getMensajes());
            $this->setMensajes('El archivo no se puede procesar');
            return array('formulario' => $formulario->createView(), 'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        } else {
            $this->setMensajes($archivoInfo->getMensajes());
        }



        foreach ($archivoInfo->getExistentesRaw() as $linea):

            if (isset($linea['NOMBRE_PAX']) && strpos($linea['NOMBRE_PAX'], '-') === false) {

                $fechaGuiaArray[]['FECHA_GUIA'] = $this->container->get('gopro_main_variableproceso')->exceldate($linea['FEC_VIAJE']) . '-' . $linea['NOMBRE_PAX'];

            }

        endforeach;

        if (isset($fechaGuiaArray)) {
            $filesGuia = $this->container->get('gopro_dbproceso_proceso');
            $filesGuia->setConexion($this->container->get('doctrine.dbal.vipac_connection'));
            $filesGuia->setTabla('DBP_PROCESOSAP_PERURAIL_FG');
            $filesGuia->setSchema('VWEB');
            $filesGuia->setCamposSelect([
                'FECHA_GUIA',
                'NUM_FILE',
                'COD_GUIA',
                'FECHA'
            ]);

            $filesGuia->setQueryVariables($fechaGuiaArray);

            if (!$filesGuia->ejecutarSelectQuery() || empty($filesGuia->getExistentesRaw())) {
                $this->setMensajes($filesGuia->getMensajes());
                $this->setMensajes('No se puede encontrar ningúno de los files para los guias.');
            } else {
                $this->setMensajes($filesGuia->getMensajes());
            }

            $fileGuiaIndizadoMulti = $filesGuia->getExistentesIndizadosMulti();

        }

        $now = new \DateTime('now');

        $i = 0;

        foreach ($archivoInfo->getExistentesRaw() as $linea):

            if (!isset($linea['FEC_EMISION'])) {//sumatoria de formato peru rail
                $this->setMensajes('La linea ' . $linea['excelRowNumber'] . ' no tiene el formato correcto en la columna fecha de emision, posiblemente es una fila de sumatoria.');
                continue;
            }

            if (strpos($linea['NOMBRE_PAX'], '-') == 4 && substr($linea['NOMBRE_PAX'], 0, 2) == '20'){
                $preproceso[$i]['Files'][] = substr($linea['NOMBRE_PAX'], 0, 10);
                $linea['NOMBRE_PAX'] = substr($linea['NOMBRE_PAX'], 11, strlen($linea['NOMBRE_PAX']) - 11);
            }else{
                $fechaGuiaCadena = $this->container->get('gopro_main_variableproceso')->exceldate($linea['FEC_VIAJE']) . '-' . $linea['NOMBRE_PAX'];
                if (!isset($fileGuiaIndizadoMulti) || !isset($fileGuiaIndizadoMulti[$fechaGuiaCadena])) {
                    $this->setMensajes('Los files del guia en la linea ' . $linea['excelRowNumber'] . ' no pudieron ser obtenidos.');
                    continue;
                }

                foreach ($fileGuiaIndizadoMulti[$fechaGuiaCadena] as $fileGuia):
                    $preproceso[$i]['Files'][] = $fileGuia['NUM_FILE'];
                endforeach;

                if(!isset($preproceso[$i]['Files'])){
                    $this->setMensajes('La linea ' . $linea['excelRowNumber'] . ' no tiene numero de file.');
                    continue;
                }

                //forzamos codigo de igv
                $preproceso[$i]['ForceTaxCode'] = 'IGV';
            }

            //predefinimos el tipo.
            if(!is_numeric($linea['TIPO_PROCESO'])){
                $preproceso[$i]['TipoProceso'] = 1;
            }else{
                $preproceso[$i]['TipoProceso'] = $linea['TIPO_PROCESO'];
            }

            //fecha del servicio necesario para diferidos
            $preproceso[$i]['ServicioDate'] = $linea['FEC_VIAJE'];
            //Cabecera
            $preproceso[$i]['DocTotal'] = $linea['VALOR_TOTAL'];
            $preproceso[$i]['DocTaxTotal'] = $linea['VALOR_IGV'];

            $preproceso[$i]['ruc'] = '20431871808'; //peruRail
            $preproceso[$i]['DocDate'] = $this->container->get('gopro_main_variableproceso')->exceldate($now->format('Y-m-d'), 'to');
            $preproceso[$i]['TaxDate'] = $linea['FEC_EMISION'];
            $preproceso[$i]['Currency'] = 'US$'; //siempre dolares

            $preproceso[$i]['U_SYP_MDSD'] = $this->parseDocNum($linea['NRO_DOCUMENTO'])[0];
            $preproceso[$i]['U_SYP_MDCD'] = $this->parseDocNum($linea['NRO_DOCUMENTO'])[1];
            $preproceso[$i]['Comments'] = $linea['NOMBRE_PAX'];

            //fecha de recepcion es la fecha de documento para este caso
            $preproceso[$i]['u_syp_fecrec'] = $preproceso[$i]['DocDate'];

            //detalle
            $preproceso[$i]['excelRowNumber'] = $linea['excelRowNumber'];

            $i++;

        endforeach;

        if (empty($preproceso)) {
            $this->setMensajes('No se preproceso ningun elemento');
            return array('formulario' => $formulario->createView(), 'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());

        }

        return $this->generarExcel($archivoInfo->getArchivoBase()->getNombre(), $preproceso);
    }


    private function generarExcel($nombreArchivo, $preproceso)
    {


        $query = $this->getDoctrine()->getManager()->createQuery("SELECT tipo FROM GoproVipacDbprocesoBundle:Docsaptipo tipo INDEX BY tipo.id");
        $docSapTipos = $query->getArrayResult();

        $seriesFormater = function($value){
            return 'FCP' . date('ym', strtotime($this->container->get('gopro_main_variableproceso')->exceldate($value)));
        };

        $this->seekAndStack($preproceso, ['emisionFechas', 'files', 'series', 'rucs'], ['TaxDate', 'Files', 'TaxDate', 'ruc'], ['FECHA', 'NUM_FILE', 'SeriesName', 'LicTradNum'], [NULL, NULL, $seriesFormater, NULL]);

        $tcInfo = $this->container->get('gopro_dbproceso_proceso');
        $tcInfo->setConexion($this->container->get('doctrine.dbal.vipac_connection'));
        $tcInfo->setTabla('TIPO_CAMBIO_EXACTUS');
        $tcInfo->setSchema('RESERVAS');
        $tcInfo->setCamposSelect([
            'TIPO_CAMBIO',
            'FECHA',
            'MONTO',
        ]);

        $tcInfoFormateado = array();

        if (empty($this->getStack('emisionFechas'))) {
            $this->setMensajes('No hay fechas para procesar el tipo de cambio');
        } else{
            $tcInfo->setQueryVariables($this->getStack('emisionFechas'), 'whereSelect', ['FECHA' => 'exceldate']);
            $tcInfo->setWhereCustom("TIPO_CAMBIO = 'TCV'");

            if (!$tcInfo->ejecutarSelectQuery() || empty($tcInfo->getExistentesRaw())) {
                $this->setMensajes($tcInfo->getMensajes());
                $this->setMensajes('No existe ninguno de los tipos de cambio');
            } else {
                $this->setMensajes($tcInfo->getMensajes());
            }

            foreach ($tcInfo->getExistentesIndizados() as $key => $value) {
                $tcInfoFormateado[$this->get('gopro_main_variableproceso')->exceldate($key, 'to')] = $value;
            }
        }

        $filesInfo = $this->container->get('gopro_dbproceso_proceso');
        $filesInfo->setConexion($this->container->get('doctrine.dbal.vipac_connection'));
        $filesInfo->setTabla('DBP_PROCESO_CARGADORCP_FILE');
        $filesInfo->setSchema('VWEB');
        $filesInfo->setCamposSelect([
            'NUM_FILE',
            'NOMBRE',
            'NUM_PAX',
            'MERCADO',
            'COD_SAP',
            'PAIS_FILE',
            'COD_PAIS'
        ]);

        $fileInfoIndizado = array();

        if (empty($this->getStack('files'))) {
            $this->setMensajes('La pila de files esta vacia');
        } else {

            $filesInfo->setQueryVariables($this->getStack('files'));

            if (!$filesInfo->ejecutarSelectQuery() || empty($filesInfo->getExistentesRaw())) {
                $this->setMensajes($filesInfo->getMensajes());
                $this->setMensajes('No existe ninguno de los files en la lista');
            } else {
                $this->setMensajes($filesInfo->getMensajes());
            }

            $fileInfoIndizado = $filesInfo->getExistentesIndizados();
        }

        $seriesInfo = $this->container->get('gopro_dbproceso_proceso');
        $seriesInfo->setConexion($this->container->get('doctrine.dbal.erp_connection'));
        $seriesInfo->setTabla('NNM1');
        $seriesInfo->setSchema('dbo');
        $seriesInfo->setCamposSelect([
            'SeriesName',
            'Series'
        ]);

        $seriesInfoIndizado = array();

        if (empty($this->getStack('series'))) {
            $this->setMensajes('La pila de series esta vacia');
        } else {
            $seriesInfo->setQueryVariables($this->getStack('series'));
            if (!$seriesInfo->ejecutarSelectQuery() || empty($seriesInfo->getExistentesRaw())) {
                $this->setMensajes($seriesInfo->getMensajes());
                $this->setMensajes('No existe ninguno de las series en la lista');
            } else {
                $this->setMensajes($seriesInfo->getMensajes());
            }

            $seriesInfoIndizado = $seriesInfo->getExistentesIndizados();
        }

        $proveedoresInfo = $this->container->get('gopro_dbproceso_proceso');
        $proveedoresInfo->setConexion($this->container->get('doctrine.dbal.erp_connection'));
        $proveedoresInfo->setTabla('VwebProveedor');
        $proveedoresInfo->setSchema('dbo');
        $proveedoresInfo->setCamposSelect([
            'CardCode',
            'LicTradNum',
            'ExtraDays'
        ]);

        $proveedoresInfoIndizado = array();

        if (empty($this->getStack('rucs'))) {
            $this->setMensajes('La pila de rucs esta vacia');
        } else {
            $proveedoresInfo->setQueryVariables($this->getStack('rucs'));
            $proveedoresInfo->setWhereCustom("validFor = 'Y'");
            if (!$proveedoresInfo->ejecutarSelectQuery() || empty($proveedoresInfo->getExistentesRaw())) {
                $this->setMensajes($proveedoresInfo->getMensajes());
                $this->setMensajes('No existe ninguno de los proveedores en la lista');
            } else {
                $this->setMensajes($proveedoresInfo->getMensajes());
            }

            $proveedoresInfoIndizado = $proveedoresInfo->getExistentesIndizados();
        }

        //print_r($proveedoresInfoIndizado); die;

        $resultadoCab = array();

        $resultadoDet = array();

        $nroLineaDet = 0;
        $i = 1;
        foreach ($preproceso as $nroLinea => $linea):

            $esDiferido = false;
            $mesServicio = '';
            $anoServicio = '';

            if (isset($linea['ServicioDate'])) {
                $mesServicio = date('m', strtotime($this->container->get('gopro_main_variableproceso')->exceldate($linea['ServicioDate'])));
                $anoServicio = date('y', strtotime($this->container->get('gopro_main_variableproceso')->exceldate($linea['ServicioDate'])));
                $mesDocumento = date('m', strtotime($this->container->get('gopro_main_variableproceso')->exceldate($linea['TaxDate'])));
                $anoDocumento = date('y', strtotime($this->container->get('gopro_main_variableproceso')->exceldate($linea['TaxDate'])));
                if (intval($anoServicio) * 12 + intval($mesServicio) > intval($anoDocumento) * 12 + intval($mesDocumento)) {
                    $esDiferido = true;
                }
            }

            if (!isset($docSapTipos[$linea['TipoProceso']])) {
                $this->setMensajes('El tipo de proceso no puede ser encontrado en la DB');
                continue;
            }

            if (!empty($esDiferido)) {
                $cuenta = '189011';
                $appendDetalle = $mesServicio . '/' . $anoServicio . '/' . $docSapTipos[$linea['TipoProceso']]['cuenta'] . ' ';
            } else {
                $cuenta = $docSapTipos[$linea['TipoProceso']]['cuenta'];
                $appendDetalle = '';
            }

            if (!isset($linea['DocTaxTotal'])) {
                $linea['DocTaxTotal'] = round(doubleval($linea['DocTotal']) / 1.18, 2);
            }

            $linea['CantFiles'] = count($linea['Files']);

            $linea['DividedDocTotal'] = round($linea['DocTotal'] / $linea['CantFiles'], 2);
            $linea['DividedDocTaxTotal'] = round($linea['DocTaxTotal'] / $linea['CantFiles'], 2);

            $resultadoCab[$nroLinea]['DocNum'] = $i;
            $resultadoCab[$nroLinea]['CardCode'] = $proveedoresInfoIndizado{$linea['ruc']}['CardCode'];
            $resultadoCab[$nroLinea]['DocType'] = 'dDocument_Service';
            $resultadoCab[$nroLinea]['DocDate'] = $linea['DocDate'];
            $resultadoCab[$nroLinea]['TaxDate'] = $linea['TaxDate'];
            //todo tabla para credito
            $resultadoCab[$nroLinea]['DocDueDate'] = strval(intval($linea['u_syp_fecrec']) + filter_var($proveedoresInfoIndizado{$linea['ruc']}['ExtraDays'], FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 0]]));
            $resultadoCab[$nroLinea]['Currency'] = $linea['Currency'];
            if ($linea['Currency'] == 'US$') {
                $resultadoCab[$nroLinea]['ControlAccount'] = 421202;
            } else {
                $resultadoCab[$nroLinea]['ControlAccount'] = 421201;
            }

            if (isset($tcInfoFormateado[$linea['TaxDate']])) {
                $resultadoCab[$nroLinea]['DocRate'] = $tcInfoFormateado[$linea['TaxDate']]['MONTO'];
            } else {
                $resultadoCab[$nroLinea]['DocRate'] = 'TC no ingresado';
            }
            if (isset($seriesInfoIndizado['FCP' . date('ym', strtotime($this->container->get('gopro_main_variableproceso')->exceldate($linea['TaxDate'])))])){
                $resultadoCab[$nroLinea]['Series'] = $seriesInfoIndizado['FCP' . date('ym', strtotime($this->container->get('gopro_main_variableproceso')->exceldate($linea['TaxDate'])))]['Series'];
            } else {
                $resultadoCab[$nroLinea]['Series'] = 'La serie SAP FCP' . date('ym', strtotime($this->container->get('gopro_main_variableproceso')->exceldate($linea['TaxDate']))) . ' no existe.';
            }

            $resultadoCab[$nroLinea]['U_SYP_MDTD'] = $docSapTipos[$linea['TipoProceso']]['tiposunat'];
            $resultadoCab[$nroLinea]['U_SYP_MDSD'] = $linea['U_SYP_MDSD'];
            $resultadoCab[$nroLinea]['U_SYP_MDCD'] = $linea['U_SYP_MDCD'];
            $resultadoCab[$nroLinea]['U_SYP_STATUS'] = 'V';
            $resultadoCab[$nroLinea]['JrnlMemo'] = substr($appendDetalle . $linea['Comments'], 0, 49);

            $resultadoCab[$nroLinea]['NumAtCard'] = $docSapTipos[$linea['TipoProceso']]['tiposunat'] . "-" . $linea['U_SYP_MDSD'] . "-" . $linea['U_SYP_MDCD'];
            $resultadoCab[$nroLinea]['Comments'] = $appendDetalle . $linea['Comments'];
            //tipos sap
            $resultadoCab[$nroLinea]['u_syp_tcompra'] = $docSapTipos[$linea['TipoProceso']]['tiposap'];
            $resultadoCab[$nroLinea]['u_syp_tpoper'] = '02';
            $resultadoCab[$nroLinea]['u_syp_biesrvadq'] = '05';
            //fecha rige
            $resultadoCab[$nroLinea]['u_syp_fecrec'] = $linea['u_syp_fecrec'];
            $resultadoCab[$nroLinea]['U_SYP_TIPOBOLETO'] = '';
            $resultadoCab[$nroLinea]['U_SYP_MDTO'] = '';
            $resultadoCab[$nroLinea]['U_SYP_FECHAREF'] = '';
            $resultadoCab[$nroLinea]['U_SYP_MDSO'] = '';
            $resultadoCab[$nroLinea]['U_SYP_MDCO'] = '';
            $resultadoCab[$nroLinea]['U_SYP_DET_RET'] = 'N';
            $resultadoCab[$nroLinea]['U_SYP_COD_DET'] = '';
            $resultadoCab[$nroLinea]['U_SYP_NOM_DETR'] = '';
            $resultadoCab[$nroLinea]['U_SYP_PORC_DETR'] = '';

            $j = 1;
            //print_r($linea['Files']);
            foreach ($linea['Files'] as $file):
                $numFileFormat = str_replace('-', '', $file);
                $numFileFormat = substr($numFileFormat, 2, strlen($numFileFormat - 2));
                $resultadoDet[$nroLineaDet]['DocNum'] = $i;
                $resultadoDet[$nroLineaDet]['LineNum'] = $j;
                $resultadoDet[$nroLineaDet]['u_syp_tipoServ'] = '';
                $resultadoDet[$nroLineaDet]['Dscription'] = '';
                $resultadoDet[$nroLineaDet]['AcctCode'] = $cuenta;

                $resultadoDet[$nroLineaDet]['Currency'] = $linea['Currency'];

                if ($j < $linea['CantFiles']) {
                    //echo 'cant:' . $linea['CantFiles'] . ' ' . $j .'<br>';
                    $resultadoDet[$nroLineaDet]['LineTotal'] = $linea['DividedDocTotal'];
                    $resultadoDet[$nroLineaDet]['TaxTotal'] = $linea['DividedDocTaxTotal'];

                    $this->setCantidadTotal($linea['DividedDocTotal'], null, ['valor', null]);
                    $this->setCantidadTotal($linea['DividedDocTaxTotal'], null, ['impuesto', null]);

                } else {
                    $resultadoDet[$nroLineaDet]['LineTotal'] = $linea['DocTotal'] - $this->getCantidadTotal('valor');
                    $resultadoDet[$nroLineaDet]['TaxTotal'] = $linea['DocTaxTotal'] - $this->getCantidadTotal('impuesto');
                }
                //print_r($fileInfoIndizado);
                if (empty($docSapTipos[$linea['TipoProceso']]['exoneradoigv'])) {
                    if (isset($linea['ForceTaxCode'])) {
                        $resultadoDet[$nroLineaDet]['VatGroup'] = $linea['ForceTaxCode'];
                        $resultadoDet[$nroLineaDet]['TaxCode'] = $linea['ForceTaxCode'];
                    } elseif ($fileInfoIndizado[$file]['COD_PAIS'] == 'PE') {
                        $resultadoDet[$nroLineaDet]['VatGroup'] = 'IGV';
                        $resultadoDet[$nroLineaDet]['TaxCode'] = 'IGV';
                    } else {
                        if (!empty($esDiferido)) {
                            $resultadoDet[$nroLineaDet]['VatGroup'] = 'DNGD_IGV';
                            $resultadoDet[$nroLineaDet]['TaxCode'] = 'DNGD_IGV';
                        } else {
                            $resultadoDet[$nroLineaDet]['VatGroup'] = 'DNGR_IGV';
                            $resultadoDet[$nroLineaDet]['TaxCode'] = 'DNGR_IGV';
                        }
                    }

                } else {
                    $resultadoDet[$nroLineaDet]['VatGroup'] = 'EXE_IGV';
                    $resultadoDet[$nroLineaDet]['TaxCode'] = 'EXE_IGV';
                }

                $resultadoDet[$nroLineaDet]['OcrCode'] = $numFileFormat;

                if (isset($fileInfoIndizado[$file])) {
                    $resultadoDet[$nroLineaDet]['OcrCode2'] = $fileInfoIndizado[$file]['COD_SAP'];
                } else {
                    $this->setMensajes('El file ' . $file . ' de la fila ' . $linea['excelRowNumber'] . ' no puede ser procesado');
                    $resultadoDet[$nroLineaDet]['OcrCode2'] = '';
                }

                if ($this->getUser()->hasGroup('Cusco')) {
                    $resultadoDet[$nroLineaDet]['OcrCode3'] = 'CUZ';
                } elseif ($this->getUser()->hasGroup('Lima')) {
                    $resultadoDet[$nroLineaDet]['OcrCode3'] = 'LIM';
                } else {
                    $resultadoDet[$nroLineaDet]['OcrCode3'] = 'USERNOGROUP';
                }
                $resultadoDet[$nroLineaDet]['OcrCode4'] = $docSapTipos[$linea['TipoProceso']]['tiposervicio'];
                $resultadoDet[$nroLineaDet]['OcrCode5'] = '100';
                $resultadoDet[$nroLineaDet]['WtLiable'] = 'N';

                $resultadoDet[$nroLineaDet]['excelRowNumber'] = $linea['excelRowNumber'];

                $j++;
                $nroLineaDet++;

            endforeach;

            $this->resetCantidadTotal('valor');
            $this->resetCantidadTotal('impuesto');

            $resultadoCab[$nroLinea]['excelRowNumber'] = $linea['excelRowNumber'];

            $i++;

        endforeach;

        $encabezadosCab = [
            'DocNum',
            'CardCode', //proveedor
            'DocType',
            'DocDate',
            'TaxDate',
            'DocDueDate',
            'DocCurrency',
            'ControlAccount',
            'DocRate',
            'Series',
            'U_SYP_MDTD',
            'U_SYP_MDSD',
            'U_SYP_MDCD',
            'U_SYP_STATUS',
            'JournalMemo',
            'NumAtCard',
            'Comments',
            'u_syp_tcompra',
            'u_syp_tpoper',
            'u_syp_biesrvadq',
            'u_syp_fecrec',
            'U_SYP_TIPOBOLETO',
            'U_SYP_MDTO',
            'U_SYP_FECHAREF',
            'U_SYP_MDSO',
            'U_SYP_MDCO',
            'U_SYP_DET_RET',
            'U_SYP_COD_DET',
            'U_SYP_NOM_DETR',
            'U_SYP_PORC_DETR',
            'excelRowNumber'
        ];

        $encabezadosDet = [
            'DocNum',
            'LineNum',
            'u_syp_tipoServ',
            'Dscription',
            'AcctCode',
            'Currency',
            'LineTotal',
            'TaxTotal',
            'VatGroup',
            'TaxCode',
            'OcrCode',
            'OcrCode2',
            'OcrCode3',
            'OcrCode4',
            'OcrCode5',
            'WtLiable',
            'excelRowNumber'
        ];

        $archivoGenerado = $this->get('gopro_main_archivoexcel');

        return $archivoGenerado
            ->setArchivo()
            ->setParametrosWriter("SAP-" . $nombreArchivo)
            ->setFila($encabezadosCab, 'A1')
            ->setTabla($resultadoCab, 'A2')
            ->setHoja(2)
            ->setFila($encabezadosDet, 'A1')
            ->setTabla($resultadoDet, 'A2')
            ->setHoja(3)
            ->setColumna($this->getMensajes(), 'A1')
            ->setHoja(1)
            ->setFormatoColumna(['yyyy-mm-dd' => ['d', 'e', 'f', 'u'], '@' => ['sz']])
            ->getArchivo();
    }

    /*
     * @param string $numeroDocumento
     * @return array
    */
    private function parseDocNum($numeroDocumento)
    {

        if (strpos($numeroDocumento, '-') === false) {
            $resultado[] = '0000';
            $resultado[] = $numeroDocumento;
        } else {
            $resultado = explode('-', $numeroDocumento);
        }
        return $resultado;
    }


}
