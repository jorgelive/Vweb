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

    private $igvRate = 18;
    private $cuentaDiferido = '189011';

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
        $columnaspecs[] = array('nombre' => 'VALOR_NETO');
        $columnaspecs[] = array('nombre' => 'VALOR_TAX');
        $columnaspecs[] = array('nombre' => 'VALOR_IMPUESTOEXTRA');
        $columnaspecs[] = array('nombre' => 'VALOR_TOTAL');
        $columnaspecs[] = array('nombre' => 'MONEDA');
        $columnaspecs[] = array('nombre' => 'FEC_SERVICIO', 'tipo' => 'exceldate');
        $columnaspecs[] = array('nombre' => 'FEC_EMISION', 'tipo' => 'exceldate');
        $columnaspecs[] = array('nombre' => 'FEC_RECEPCION', 'tipo' => 'exceldate');
        $columnaspecs[] = array('nombre' => 'FEC_CONTABLE', 'tipo' => 'exceldate');
        $columnaspecs[] = array('nombre' => 'DESCRIPCION');
        $columnaspecs[] = array('nombre' => 'NRO_DOCUMENTO_ASOC');
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
            ->setCamposCustom(['FILE_1', 'FILE_2', 'FILE_3', 'FILE_4', 'FILE_5', 'FILE_6', 'FILE_7', 'FILE_8', 'FILE_9', 'FILE_10', 'FILE_11', 'FILE_12', 'FILE_13', 'FILE_14', 'FILE_15', 'FILE_16', 'FILE_17', 'FILE_18', 'FILE_19', 'FILE_20'])
            ->setDescartarBlanco(true)
            ->setTrimEspacios(true);


        if (!$archivoInfo->parseExcel()) {
            $this->setMensajes($archivoInfo->getMensajes());
            $this->setMensajes('El archivo no se puede procesar');
            return array('formulario' => $formulario->createView(), 'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        } else {
            $this->setMensajes($archivoInfo->getMensajes());
        }

        if (empty($archivoInfo->getExistentesCustomRaw())) {
            $this->setMensajes('No hay files para procesar');
            return array('formulario' => $formulario->createView(), 'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }

        $i = 0;

        foreach ($archivoInfo->getExistentesRaw() as $nroLinea => $linea):

            if (!isset($linea['FEC_EMISION']) || empty($linea['FEC_EMISION'])) {//sumatoria de formato peru rail
                //$this->setMensajes('La linea ' . $linea['excelRowNumber'] . ' no tiene el formato correcto en la columna fecha de emision, posiblemente es una fila de sumatoria.');
                continue;
            }

            if (!isset($linea['TIPO_PROCESO']) || empty($linea['TIPO_PROCESO'])) {//sumatoria de formato peru rail
                $this->setMensajes('La linea ' . $linea['excelRowNumber'] . ' no tiene el tipo de proceso definido.');
                continue;
            }

            if (!isset($linea['MONEDA']) || empty($linea['MONEDA'])) {//sumatoria de formato peru rail
                $this->setMensajes('La linea ' . $linea['excelRowNumber'] . ' no tiene la moneda definida.');
                continue;
            }

            if (!empty($archivoInfo->getExistentesCustomRaw()[$nroLinea])) {
                $preproceso[$i]['Files'] = array_unique($archivoInfo->getExistentesCustomRaw()[$nroLinea]);
            } else {
                $this->setMensajes('La linea ' . $linea['excelRowNumber'] . ' no tiene numero de file.');
                continue;
            }

            //predefinimos el tipo, si vacio se toma fecha de documento.
            $preproceso[$i]['TipoProceso'] = $linea['TIPO_PROCESO'];

            //Cabecera
            if (isset($linea['VALOR_NETO'])) {
                $preproceso[$i]['NetoTotal'] = $linea['VALOR_NETO'];
            }
            if (isset($linea['VALOR_TAX'])) {
                $preproceso[$i]['TaxTotal'] = $linea['VALOR_TAX'];
            }
            if (isset($linea['VALOR_IMPUESTOEXTRA'])) {
                $preproceso[$i]['ImpuestoExtraTotal'] = $linea['VALOR_IMPUESTOEXTRA'];
            }
            if (isset($linea['VALOR_TOTAL'])) {
                $preproceso[$i]['MontoTotal'] = $linea['VALOR_TOTAL'];
            }

            $preproceso[$i]['ruc'] = $linea['COD_PROVEEDOR'];
            $preproceso[$i]['ServicioDate'] = $linea['FEC_SERVICIO'];
            $preproceso[$i]['TaxDate'] = $linea['FEC_EMISION'];
            if (isset($linea['FEC_RECEPCION'])) {
                $preproceso[$i]['u_syp_fecrec'] = $linea['FEC_RECEPCION'];
            }

            if (isset($linea['FEC_CONTABLE'])) {
                $preproceso[$i]['DocDate'] = $linea['FEC_CONTABLE'];
            }
            $preproceso[$i]['Currency'] = str_replace(['SD', 'SOL'], ['S$', 'S/'], $linea['MONEDA']);

            $preproceso[$i]['U_SYP_MDSD'] = $this->parseDocNum($linea['NRO_DOCUMENTO'])[0];
            $preproceso[$i]['U_SYP_MDCD'] = $this->parseDocNum($linea['NRO_DOCUMENTO'])[1];
            $preproceso[$i]['Comments'] = $linea['DESCRIPCION'];

            if(isset($linea['NRO_DOCUMENTO_ASOC']) && !empty($linea['NRO_DOCUMENTO_ASOC'])){
                $preproceso[$i]['U_SYP_MDSO'] = $this->parseDocNum($linea['NRO_DOCUMENTO_ASOC'])[0];
                $preproceso[$i]['U_SYP_MDCO'] = $this->parseDocNum($linea['NRO_DOCUMENTO_ASOC'])[1];
            }

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
        $columnaspecs[] = array('nombre' => 'FEC_EMISION', 'tipo' => 'exceldate');
        $columnaspecs[] = array('nombre' => 'FEC_VIAJE', 'tipo' => 'exceldate');
        $columnaspecs[] = array('nombre' => 'FEC_CONTABLE', 'tipo' => 'exceldate');
        $columnaspecs[] = array('nombre' => 'noProcess');
        $columnaspecs[] = array('nombre' => 'noProcess');
        $columnaspecs[] = array('nombre' => 'NRO_DOCUMENTO');
        $columnaspecs[] = array('nombre' => 'noProcess');
        $columnaspecs[] = array('nombre' => 'NOMBRE_PAX');
        $columnaspecs[] = array('nombre' => 'noProcess');
        $columnaspecs[] = array('nombre' => 'VALOR_NETO');
        $columnaspecs[] = array('nombre' => 'VALOR_TAX');
        $columnaspecs[] = array('nombre' => 'VALOR_TOTAL');
        $columnaspecs[] = array('nombre' => 'NUM_FILE');
        $columnaspecs[] = array('nombre' => 'RESERVA');
        $columnaspecs[] = array('nombre' => 'TIPO_PROCESO');

        $archivoInfo = $this->get('gopro_main_archivoexcel')
            ->setArchivoBase($repositorio, $archivoEjecutar, $operacion)
            ->setArchivo()
            ->setSkipRows(1)
            ->setParametrosReader($tablaSpecs, $columnaspecs)
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

                $fechaGuiaArray[]['FECHA_GUIA'] = $linea['FEC_VIAJE'] . '-' . $linea['NOMBRE_PAX'];

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

        $i = 0;

        foreach ($archivoInfo->getExistentesRaw() as $linea):

            //sumatoria de formato peru rail o deteccion de filas vacias
            if (!isset($linea['FEC_EMISION'])) {
                continue;
            }

            if (strpos($linea['NOMBRE_PAX'], '-') == 4 && substr($linea['NOMBRE_PAX'], 0, 2) == '20') {
                $preproceso[$i]['Files'][] = substr($linea['NOMBRE_PAX'], 0, 10);
                $linea['NOMBRE_PAX'] = substr($linea['NOMBRE_PAX'], 11, strlen($linea['NOMBRE_PAX']) - 11);
            } else {
                $fechaGuiaCadena = $linea['FEC_VIAJE'] . '-' . $linea['NOMBRE_PAX'];
                if (!isset($fileGuiaIndizadoMulti) || !isset($fileGuiaIndizadoMulti[$fechaGuiaCadena])) {
                    $this->setMensajes('Los files del guia en la linea ' . $linea['excelRowNumber'] . ' no pudieron ser obtenidos.');
                    continue;
                }

                foreach ($fileGuiaIndizadoMulti[$fechaGuiaCadena] as $fileGuia):
                    $preproceso[$i]['Files'][] = $fileGuia['NUM_FILE'];
                endforeach;

                if (!isset($preproceso[$i]['Files'])) {
                    $this->setMensajes('La linea ' . $linea['excelRowNumber'] . ' no tiene numero de file.');
                    continue;
                }

                //forzamos codigo de igv
                $preproceso[$i]['TaxCode'] = 'IGV';
            }

            //predefinimos el tipo.
            if (!is_numeric($linea['TIPO_PROCESO'])) {
                $preproceso[$i]['TipoProceso'] = 1;
            } else {
                $preproceso[$i]['TipoProceso'] = $linea['TIPO_PROCESO'];
            }

            //fecha del servicio necesario para diferidos
            $preproceso[$i]['ServicioDate'] = $linea['FEC_VIAJE'];
            //Cabecera
            $preproceso[$i]['NetoTotal'] = $linea['VALOR_NETO'];
            $preproceso[$i]['TaxTotal'] = $linea['VALOR_TAX'];
            $preproceso[$i]['MontoTotal'] = $linea['VALOR_TOTAL'];

            $preproceso[$i]['ruc'] = '20431871808'; //peruRail
            $preproceso[$i]['TaxDate'] = $linea['FEC_EMISION'];
            $preproceso[$i]['DocDate'] = $linea['FEC_CONTABLE'];
            $preproceso[$i]['u_syp_fecrec'] = $preproceso[$i]['TaxDate'];
            $preproceso[$i]['Currency'] = 'US$'; //siempre dolares

            $preproceso[$i]['U_SYP_MDSD'] = $this->parseDocNum($linea['NRO_DOCUMENTO'])[0];
            $preproceso[$i]['U_SYP_MDCD'] = $this->parseDocNum($linea['NRO_DOCUMENTO'])[1];
            $preproceso[$i]['Comments'] = $linea['NOMBRE_PAX'];

            //fecha de recepcion es la fecha de documento para este caso
            $preproceso[$i]['U_SYP_TIPOBOLETO'] = 1;

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
     * @Route("/incarail/{archivoEjecutar}", name="gopro_vipac_dbproceso_procesosap_incarail", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function incarailAction(Request $request, $archivoEjecutar)
    {

        $operacion = 'vipac_dbproceso_procesosap_incarail';
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
        $columnaspecs[] = array('nombre' => 'VALOR_NETO');
        $columnaspecs[] = array('nombre' => 'VALOR_TAX');
        $columnaspecs[] = array('nombre' => 'VALOR_IMPUESTOEXTRA');
        $columnaspecs[] = array('nombre' => 'VALOR_TOTAL');
        $columnaspecs[] = array('nombre' => 'MONEDA');
        $columnaspecs[] = array('nombre' => 'FEC_SERVICIO', 'tipo' => 'exceldate');
        $columnaspecs[] = array('nombre' => 'FEC_EMISION', 'tipo' => 'exceldate');
        $columnaspecs[] = array('nombre' => 'FEC_RECEPCION', 'tipo' => 'exceldate');
        $columnaspecs[] = array('nombre' => 'FEC_CONTABLE', 'tipo' => 'exceldate');
        $columnaspecs[] = array('nombre' => 'DESCRIPCION');
        $columnaspecs[] = array('nombre' => 'FILE_1');


        $archivoInfo = $this->get('gopro_main_archivoexcel')
            ->setArchivoBase($repositorio, $archivoEjecutar, $operacion)
            ->setArchivo()
            ->setSkipRows(1)
            ->setParametrosReader($tablaSpecs, $columnaspecs)
//            ->setCamposCustom(['FILE_1', 'FILE_2', 'FILE_3', 'FILE_4', 'FILE_5', 'FILE_6', 'FILE_7', 'FILE_8', 'FILE_9', 'FILE_10', 'FILE_11', 'FILE_12', 'FILE_13', 'FILE_14', 'FILE_15', 'FILE_16', 'FILE_17', 'FILE_18', 'FILE_19', 'FILE_20'])
            ->setDescartarBlanco(true)
            ->setTrimEspacios(true);


        if (!$archivoInfo->parseExcel()) {
            $this->setMensajes($archivoInfo->getMensajes());
            $this->setMensajes('El archivo no se puede procesar');
            return array('formulario' => $formulario->createView(), 'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        } else {
            $this->setMensajes($archivoInfo->getMensajes());
        }

        if (empty($archivoInfo->getExistentesCustomRaw())) {
            $this->setMensajes('No hay files para procesar');
            return array('formulario' => $formulario->createView(), 'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $this->getMensajes());
        }

        $i = 0;

        foreach ($archivoInfo->getExistentesRaw() as $nroLinea => $linea):

            //deteccion de filas vacias
            if (!isset($linea['FEC_EMISION'])) {
                continue;
            }

            if (!empty($archivoInfo->getExistentesCustomRaw()[$nroLinea])) {
                $preproceso[$i]['Files'] = array_unique($archivoInfo->getExistentesCustomRaw()[$nroLinea]);
            } else {
                $this->setMensajes('La linea ' . $linea['excelRowNumber'] . ' no tiene numero de file.');
                continue;
            }

            //predefinimos el tipo, si vacio se toma fecha de documento.
            $preproceso[$i]['TipoProceso'] = $linea['TIPO_PROCESO'];
            //fecha del servicio necesario para diferidos
            $preproceso[$i]['ServicioDate'] = $linea['FEC_SERVICIO'];
            //Cabecera
            if (isset($linea['VALOR_NETO'])) {
                $preproceso[$i]['NetoTotal'] = $linea['VALOR_NETO'];
            }
            if (isset($linea['VALOR_TAX'])) {
                $preproceso[$i]['TaxTotal'] = $linea['VALOR_TAX'];
            }
            if (isset($linea['VALOR_IMPUESTOEXTRA'])) {
                $preproceso[$i]['ImpuestoExtraTotal'] = $linea['VALOR_IMPUESTOEXTRA'];
            }
            if (isset($linea['VALOR_TOTAL'])) {
                $preproceso[$i]['MontoTotal'] = $linea['VALOR_TOTAL'];
            }

            $preproceso[$i]['ruc'] = $linea['COD_PROVEEDOR'];
            if (isset($linea['FEC_CONTABLE'])) {
                $preproceso[$i]['DocDate'] = $linea['FEC_CONTABLE'];
            }
            $preproceso[$i]['TaxDate'] = $linea['FEC_EMISION'];
            $preproceso[$i]['Currency'] = str_replace('SD', 'S$', $linea['MONEDA']);

            $preproceso[$i]['U_SYP_MDSD'] = $this->parseDocNum($linea['NRO_DOCUMENTO'])[0];
            $preproceso[$i]['U_SYP_MDCD'] = $this->parseDocNum($linea['NRO_DOCUMENTO'])[1];
            $preproceso[$i]['Comments'] = $linea['DESCRIPCION'];

            if (isset($linea['FEC_RECEPCION'])) {
                $preproceso[$i]['u_syp_fecrec'] = $linea['FEC_RECEPCION'];
            }

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
        $now = new \DateTime('now');
        $nowString = $now->format('Y-m-d');

        $query = $this->getDoctrine()->getManager()->createQuery("SELECT tipo FROM GoproVipacDbprocesoBundle:Docsaptipo tipo INDEX BY tipo.id");
        $docSapTipos = $query->getArrayResult();

        $codigosRetencionDetraccionFormater = function ($value) {
            if (empty($value)) {
                return null;
            }

            return $value;
        };

        $this->seekAndStack($docSapTipos, ['codigosretencion', 'codigosretencion'], ['codigoretencion', 'codigodetraccion'], ['WTCode', 'WTCode'], [$codigosRetencionDetraccionFormater, $codigosRetencionDetraccionFormater]);

        $retencionInfo = $this->container->get('gopro_dbproceso_proceso');
        $retencionInfo->setConexion($this->container->get('doctrine.dbal.erp_connection'));
        $retencionInfo->setTabla('OWHT');
        $retencionInfo->setSchema('dbo');
        $retencionInfo->setCamposSelect([
            'WTCode',
            'WTName',
            'U_SYP_PORC'
        ]);

        $retencionInfoIndizado = array();

        if (empty($this->getStack('codigosretencion'))) {
            $this->setMensajes('No hay codigos de retencion para procesar');
        } else {
            $retencionInfo->setQueryVariables($this->getStack('codigosretencion'));
            $retencionInfo->setWhereCustom("Inactive = 'N'");

            if (!$retencionInfo->ejecutarSelectQuery() || empty($retencionInfo->getExistentesRaw())) {
                $this->setMensajes($retencionInfo->getMensajes());
                $this->setMensajes('No existe ninguno de los tipos de cambio');
            } else {
                $this->setMensajes($retencionInfo->getMensajes());
            }

            $retencionInfoIndizado = $retencionInfo->getExistentesIndizados();
        }


        foreach ($preproceso as $nroLinea => $linea ) {

            if (!isset($docSapTipos[$linea['TipoProceso']])) {
                $this->setMensajes('El tipo de proceso no puede ser encontrado en la DB para la fila ' . $linea['excelRowNumber']);
                unset($preproceso[$nroLinea]);
                continue;
            }

            if(isset($linea['TaxDate']) && !empty($linea['TaxDate'])
                && (empty($this->getStack('emisionFechasAux')) || !in_array($linea['TaxDate'], $this->getStack('emisionFechasAux')))
            ) {
                $this->setStack('emisionFechas', $linea['TaxDate'], 'RateDate');
                $this->setStack('emisionFechasAux', $linea['TaxDate']);
            }

            if(isset($linea['Files']) && !empty($linea['Files'] && is_array($linea['Files']))) {
                foreach($linea['Files'] as $file){
                    if(empty($this->getStack('filesAux')) || !in_array($file, $this->getStack('filesAux'))){
                        $this->setStack('files', $file, 'NUM_FILE');
                        $this->setStack('filesAux', $file);
                    }
                }

            }

            if(isset($linea['ruc']) && !empty($linea['ruc'])
                && (empty($this->getStack('rucsAux')) || !in_array($linea['ruc'], $this->getStack('rucsAux')))
            ) {
                $this->setStack('rucs', $linea['ruc'], 'LicTradNum');
                $this->setStack('rucsAux', $linea['ruc']);
            }

            if(isset($linea['ruc']) && !empty($linea['ruc'])
                && isset($linea['U_SYP_MDSO']) && !empty($linea['U_SYP_MDSO'])
                && isset($linea['U_SYP_MDCO']) && !empty($linea['U_SYP_MDCO'])
                && (empty($this->getStack('asociadosAux'))
                    || !in_array($linea['ruc'] . '-' . $linea['U_SYP_MDSO'] . '-' . $linea['U_SYP_MDCO'], $this->getStack('asociadosAux'))
                )
            ) {
                $this->setStack('asociados', $linea['ruc'] . '-' . $linea['U_SYP_MDSO'] . '-' . $linea['U_SYP_MDCO'], 'rucNumDoc');
                $this->setStack('asociadosAux', $linea['ruc'] . '-' . $linea['U_SYP_MDSO'] . '-' . $linea['U_SYP_MDCO']);
                $tipoCreditoDebito = 'credito';
            }else{
                $tipoCreditoDebito = 'debito';
            }

            if(!isset($linea['DocDate']) || empty($linea['DocDate'])){
                $linea['DocDate'] = $nowString;
                $preproceso[$nroLinea]['DocDate'] = $nowString;
            }

            if(empty($this->getStack('seriesAux')) || !in_array($docSapTipos[$linea['TipoProceso']]['prefijoserie' . $tipoCreditoDebito] . date('ym', strtotime($linea['DocDate'])), $this->getStack('seriesAux'))) {
                $this->setStack('series', $docSapTipos[$linea['TipoProceso']]['prefijoserie' . $tipoCreditoDebito] . date('ym', strtotime($linea['DocDate'])), 'SeriesName');
                $this->setStack('seriesAux', $docSapTipos[$linea['TipoProceso']]['prefijoserie' . $tipoCreditoDebito] . date('ym', strtotime($linea['DocDate'])));
            }
        }

        $tcInfo = $this->container->get('gopro_dbproceso_proceso');
        $tcInfo->setConexion($this->container->get('doctrine.dbal.erp_connection'));
        $tcInfo->setTabla('ORTT');
        $tcInfo->setSchema('dbo');
        $tcInfo->setCamposSelect([
            'RateDate',
            'Rate'
        ]);

        $tcInfoFormateado = array();

        if (empty($this->getStack('emisionFechas'))) {
            $this->setMensajes('No hay fechas para procesar el tipo de cambio');
        } else {
            $tcInfo->setQueryVariables($this->getStack('emisionFechas'), 'whereSelect', ['RateDate' => 'exceldate']);
            $tcInfo->setWhereCustom("Currency = 'US$'");

            if (!$tcInfo->ejecutarSelectQuery() || empty($tcInfo->getExistentesRaw())) {
                $this->setMensajes($tcInfo->getMensajes());
                $this->setMensajes('No existe ninguno de los tipos de cambio');
            } else {
                $this->setMensajes($tcInfo->getMensajes());
            }

            foreach ($tcInfo->getExistentesIndizados() as $key => $value) {
                $tcInfoFormateado[date('Y-m-d', strtotime($key))] = $value;
            }
        }

        $filesInfo = $this->container->get('gopro_dbproceso_proceso');
        $filesInfo->setConexion($this->container->get('doctrine.dbal.vipac_connection'));
        $filesInfo->setTabla('DBP_PROCESO_CARGADORCP_FILE');
        $filesInfo->setSchema('VWEB');
        $filesInfo->setCamposSelect([
            'NUM_FILE',
            'NOMBRE',
            'NUMERO_PAX',
            'MERCADO',
            'COD_SAP',
            'PAIS_FILE',
            'COD_PAIS',
            'DOMICILIADO'
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

        $asociadosInfo = $this->container->get('gopro_dbproceso_proceso');
        $asociadosInfo->setConexion($this->container->get('doctrine.dbal.erp_connection'));
        $asociadosInfo->setTabla('VwebDocAsoc');
        $asociadosInfo->setSchema('dbo');
        $asociadosInfo->setCamposSelect([
            'fechaDocumento',
            'LicTradNum',
            'rucNumDoc',
            'U_SYP_MDTD',
            'U_SYP_MDSD',
            'U_SYP_MDCD'
        ]);

        $asociadosInfoIndizado = array();

        if (empty($this->getStack('asociados'))) {
            $this->setMensajes('La pila de documentos asociados esta vacia, esto es normal en el caso de que no se este ingresando ninguna nota de crédito');
        } else {

            $asociadosInfo->setQueryVariables($this->getStack('asociados'));

            if (!$asociadosInfo->ejecutarSelectQuery() || empty($asociadosInfo->getExistentesRaw())) {
                $this->setMensajes($asociadosInfo->getMensajes());
                $this->setMensajes('No existe ninguno de los documentos asociados en la lista');
            } else {
                $this->setMensajes($asociadosInfo->getMensajes());
            }

            $asociadosInfoIndizado = $asociadosInfo->getExistentesIndizados();
        }

        $seriesInfo = $this->container->get('gopro_dbproceso_proceso');
        $seriesInfo->setConexion($this->container->get('doctrine.dbal.erp_connection'));
        $seriesInfo->setTabla('NNM1');
        $seriesInfo->setSchema('dbo');
        $seriesInfo->setCamposSelect([
            'SeriesName',
            'Series'
        ]);

        if (empty($this->getStack('series'))) {
            $this->setMensajes('La pila de series esta vacia');
        }else{
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
            'ExtraDays',
            'DebPayAcct',
            'U_SYP_AGENRE',
            'U_SYP_SNBUEN'
        ]);

        $proveedoresInfoIndizado = array();

        if (empty($this->getStack('rucs'))) {
            $this->setMensajes('La pila de rucs esta vacia');
        } else {
            $proveedoresInfo->setQueryVariables($this->getStack('rucs'));
            if (!$proveedoresInfo->ejecutarSelectQuery() || empty($proveedoresInfo->getExistentesRaw())) {
                $this->setMensajes($proveedoresInfo->getMensajes());
                $this->setMensajes('No existe ninguno de los proveedores en la lista');
            } else {
                $this->setMensajes($proveedoresInfo->getMensajes());
            }

            $proveedoresInfoIndizado = $proveedoresInfo->getExistentesIndizados();
        }

        $resultadoCab = array();

        $resultadoDet = array();

        $resultadoRet = array();

        $nroLineaDet = 0;

        $i = 1;

        foreach ($preproceso as $nroLinea => $linea):


            if (!isset($linea['u_syp_fecrec']) && empty($linea['u_syp_fecrec'])) {
                $linea['u_syp_fecrec'] = $nowString;
            }

            if (!isset($linea['ServicioDate']) && empty($linea['ServicioDate'])) {
                $this->setMensajes('La fecha del servicio debe ser ingresada en la linea ' . $linea['excelRowNumber']);
                continue;
            }

            if (!isset($linea['TaxDate']) && empty($linea['TaxDate'])) {
                $this->setMensajes('La fecha del documento debe ser ingresada en la linea ' . $linea['excelRowNumber']);
                continue;
            }

            $mesServicio = date('m', strtotime($linea['ServicioDate']));
            $anoServicio = date('y', strtotime($linea['ServicioDate']));
            $mesContable = date('m', strtotime($linea['DocDate']));
            $anoContable = date('y', strtotime($linea['DocDate']));

            $esDiferido = false;

            $esGrabado = false;

            if(!empty($docSapTipos[$linea['TipoProceso']]['forzargravado'])){
                $esGrabado = true;
            }

            $razonGrabado = array();
            //deteccion de files domiciliados y calculo del total de pasajeros de todos los files
            foreach ($linea['Files'] as $file):
                $this->setSuma('numeroPax', $fileInfoIndizado[$file]['NUMERO_PAX']);
                //var_dump($fileInfoIndizado[$file]['DOMICILIADO']);
                //var_dump($fileInfoIndizado[$file]['COD_SAP']);
                if ($fileInfoIndizado[$file]['DOMICILIADO'] == 1){
                    $esGrabado = true;
                    $razonGrabado[] = $file . ' es domiciliado';
                }

                if ($fileInfoIndizado[$file]['COD_SAP'] == 'SOP'){
                    $esGrabado = true;
                    $razonGrabado[] = $file . ' es venta opcional';
                }
            endforeach;

            //die;

            $numPax = $this->getSuma('numeroPax');
            $numFiles = count($linea['Files']);

            if (intval($anoServicio) * 12 + intval($mesServicio) > intval($anoContable) * 12 + intval($mesContable)
                && $esGrabado == false
            ) {
                $esDiferido = true;
            } elseif (intval($anoServicio) * 12 + intval($mesServicio) > intval($anoContable) * 12 + intval($mesContable)){
                $this->setMensajes('El documento de la fila ' . $linea['excelRowNumber'] . ' podria diferirse pero es gravado: ' . implode(', ', $razonGrabado). '.');
            }

            if (!empty($esDiferido)) {
                $cuenta = $this->cuentaDiferido;
                $appendDetalle = $mesServicio . '/' . $anoServicio . '/' . $docSapTipos[$linea['TipoProceso']]['cuenta'] . ' ';
            } else {
                $cuenta = $docSapTipos[$linea['TipoProceso']]['cuenta'];
                $appendDetalle = '';
            }

            if (!empty($docSapTipos[$linea['TipoProceso']]['codigoigvnogravado'])
                && !empty($docSapTipos[$linea['TipoProceso']]['codigoigvnogravadodif'])
                && !empty($docSapTipos[$linea['TipoProceso']]['codigoigvgravado'])
            ){
                $igv = $this->igvRate;
            }else{
                $igv = 0;
            }

            $linea['MontoTotal'] = doubleval(str_replace(',', '', $linea['MontoTotal']));

            if (!isset($linea['ImpuestoExtraTotal'])) {
                $linea['ImpuestoExtraTotal'] = 0;
            } else {
                $linea['ImpuestoExtraTotal'] = doubleval(str_replace(',', '', $linea['ImpuestoExtraTotal']));
            }

            if (!isset($linea['NetoTotal'])) {
                if (!isset($linea['MontoTotal'])) {
                    $this->setMensajes('No se puede calcular el neto para la fila ' . $linea['excelRowNumber']);
                    continue;
                }
                $linea['NetoTotal'] = round(doubleval($linea['MontoTotal'] - $linea['ImpuestoExtraTotal']) / (1 + $igv / 100), 2);
                $this->setMensajes('Se esta calculando el impuesto utilizando ' . $igv . '% en la linea: ' . $linea['excelRowNumber']);
            } else {
                $linea['NetoTotal'] = doubleval(str_replace(',', '', $linea['NetoTotal']));
            }

            if (!isset($linea['TaxTotal'])) {
                if ($igv = 0) {
                    $linea['TaxTotal'] = 0;
                } else {
                    if (!isset($linea['MontoTotal']) || !isset($linea['NetoTotal'])) {
                        $this->setMensajes('No se puede calcular el IGV para la fila ' . $linea['excelRowNumber']);
                        continue;
                    }
                    $linea['TaxTotal'] = round($linea['MontoTotal'] - $linea['ImpuestoExtraTotal'] - $linea['NetoTotal'], 2);
                }
            } else {
                $linea['TaxTotal'] = doubleval(str_replace(',', '', $linea['TaxTotal']));
            }

            $linea['DividedNetoTotal'] = round($linea['NetoTotal'] / $numPax, 2);
            $linea['DividedTaxTotal'] = round($linea['TaxTotal'] / $numPax, 2);
            $linea['DividedImpuestoExtraTotal'] = round($linea['ImpuestoExtraTotal'] / $numPax, 2);

            if (intval(round(1000 * $linea['MontoTotal'])) != intval(round(1000 * ($linea['NetoTotal'] + $linea['TaxTotal'] + $linea['ImpuestoExtraTotal'])))) {
                $this->setMensajes('La suma de los montos parciales no es igual al monto total en la linea ' . $linea['excelRowNumber']);
                continue;
            }

            $resultadoCab[$nroLinea]['DocNum'] = $i;
            $resultadoCab[$nroLinea]['CardCode'] = $proveedoresInfoIndizado{$linea['ruc']}['CardCode'];
            $resultadoCab[$nroLinea]['DocType'] = 'dDocument_Service';
            $resultadoCab[$nroLinea]['DocDate'] = $this->container->get('gopro_main_variableproceso')->exceldate($linea['DocDate'], 'to');
            $resultadoCab[$nroLinea]['TaxDate'] = $this->container->get('gopro_main_variableproceso')->exceldate($linea['TaxDate'], 'to');
            $resultadoCab[$nroLinea]['DocDueDate'] = strval(intval($this->container->get('gopro_main_variableproceso')->exceldate($linea['u_syp_fecrec'], 'to')) + filter_var($proveedoresInfoIndizado{$linea['ruc']}['ExtraDays'], FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 0]]));
            $resultadoCab[$nroLinea]['Currency'] = $linea['Currency'];
            if ($linea['Currency'] == 'US$') {
                $resultadoCab[$nroLinea]['ControlAccount'] = substr($proveedoresInfoIndizado{$linea['ruc']}['DebPayAcct'], 0, -1) . '2';
            } else {
                $resultadoCab[$nroLinea]['ControlAccount'] = substr($proveedoresInfoIndizado{$linea['ruc']}['DebPayAcct'], 0, -1) . '1';
            }

            $coeficienteMoneda = 1;

            if ($linea['Currency'] == 'US$') {
                if (isset($tcInfoFormateado[$linea['TaxDate']])) {

                    $coeficienteMoneda = $tcInfoFormateado[$linea['TaxDate']]['Rate'];
                    $resultadoCab[$nroLinea]['DocRate'] = $tcInfoFormateado[$linea['TaxDate']]['Rate'];
                } else {
                    $resultadoCab[$nroLinea]['DocRate'] = 'TC no ingresado';
                }

            } else {
                $resultadoCab[$nroLinea]['DocRate'] = '';
            }
            $resultadoCab[$nroLinea]['DocTotal'] = $linea['MontoTotal'];

            if(isset($linea['U_SYP_MDSO']) && !empty($linea['U_SYP_MDSO'])
                && isset($linea['U_SYP_MDCO']) && !empty($linea['U_SYP_MDCO'])
            ){
                $tipoCreditoDebito = 'credito';
            }else{
                $tipoCreditoDebito = 'debito';
            }

            if (isset($seriesInfoIndizado[$docSapTipos[$linea['TipoProceso']]['prefijoserie' . $tipoCreditoDebito] . date('ym', strtotime($this->container->get('gopro_main_variableproceso')->exceldate($linea['DocDate'])))])) {
                $resultadoCab[$nroLinea]['Series'] = $seriesInfoIndizado[$docSapTipos[$linea['TipoProceso']]['prefijoserie' . $tipoCreditoDebito] . date('ym', strtotime($linea['DocDate']))]['Series'];
            } else {
                $resultadoCab[$nroLinea]['Series'] = 'La serie SAP ' . $docSapTipos[$linea['TipoProceso']]['prefijoserie' . $tipoCreditoDebito] . date('ym', strtotime($linea['DocDate'])) . ' no existe.';
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
            $resultadoCab[$nroLinea]['u_syp_biesrvadq'] = '5';
            //fecha rige
            $resultadoCab[$nroLinea]['u_syp_fecrec'] = $this->container->get('gopro_main_variableproceso')->exceldate($linea['u_syp_fecrec'], 'to');

            isset($linea['U_SYP_TIPOBOLETO']) ? $resultadoCab[$nroLinea]['U_SYP_TIPOBOLETO'] = $linea['U_SYP_TIPOBOLETO'] : $resultadoCab[$nroLinea]['U_SYP_TIPOBOLETO'] = '';

            $resultadoCab[$nroLinea]['U_SYP_MDTO'] = '';
            $resultadoCab[$nroLinea]['U_SYP_FECHAREF'] = '';
            $resultadoCab[$nroLinea]['U_SYP_MDSO'] = '';
            $resultadoCab[$nroLinea]['U_SYP_MDCO'] = '';

            if($tipoCreditoDebito == 'credito'
                && isset($asociadosInfoIndizado[$linea['ruc'] . '-' . $linea['U_SYP_MDSO'] . '-' . $linea['U_SYP_MDCO']])){
                $resultadoCab[$nroLinea]['U_SYP_MDTO'] = $asociadosInfoIndizado[$linea['ruc'] . '-' . $linea['U_SYP_MDSO'] . '-' . $linea['U_SYP_MDCO']]['U_SYP_MDTD'];
                $resultadoCab[$nroLinea]['U_SYP_FECHAREF'] = $asociadosInfoIndizado[$linea['ruc'] . '-' . $linea['U_SYP_MDSO'] . '-' . $linea['U_SYP_MDCO']]['fechaDocumento'];
                $resultadoCab[$nroLinea]['U_SYP_MDSO'] = $linea['U_SYP_MDSO'];
                $resultadoCab[$nroLinea]['U_SYP_MDCO'] = $linea['U_SYP_MDCO'];
            }elseif($tipoCreditoDebito == 'credito'){
                $this->setMensajes('El documento asociado no puede ser encontrado para la linea: ' . $linea['excelRowNumber']);
            }

            $resultadoCab[$nroLinea]['U_SYP_DET_RET'] = 'N';
            $resultadoCab[$nroLinea]['U_SYP_COD_DET'] = '';
            $resultadoCab[$nroLinea]['U_SYP_NOM_DETR'] = '';
            $resultadoCab[$nroLinea]['U_SYP_PORC_DETR'] = '';

            //sobrescribimos la retencion
            if (!empty($docSapTipos[$linea['TipoProceso']]['codigoretencion']) && $coeficienteMoneda * $linea['MontoTotal'] >= $docSapTipos[$linea['TipoProceso']]['montoretencion']
                && !($proveedoresInfoIndizado{$linea['ruc']}['U_SYP_AGENRE'] == 'Y' || $proveedoresInfoIndizado{$linea['ruc']}['U_SYP_SNBUEN'] == 'Y')
            ) {
                $resultadoCab[$nroLinea]['U_SYP_DET_RET'] = substr($docSapTipos[$linea['TipoProceso']]['codigoretencion'], 0, 1);
                $resultadoCab[$nroLinea]['U_SYP_COD_DET'] = $docSapTipos[$linea['TipoProceso']]['codigoretencion'];
                $resultadoCab[$nroLinea]['U_SYP_NOM_DETR'] = substr($retencionInfoIndizado[$docSapTipos[$linea['TipoProceso']]['codigoretencion']]['WTName'], 0, 31);
                $resultadoCab[$nroLinea]['U_SYP_PORC_DETR'] = intval($retencionInfoIndizado[$docSapTipos[$linea['TipoProceso']]['codigoretencion']]['U_SYP_PORC']);
            }

            //sobrescribimos la detraccion
            if (!empty($docSapTipos[$linea['TipoProceso']]['codigodetraccion']) && $coeficienteMoneda * $linea['MontoTotal'] >= $docSapTipos[$linea['TipoProceso']]['montodetraccion']
                && !($proveedoresInfoIndizado{$linea['ruc']}['U_SYP_AGENRE'] == 'Y' || $proveedoresInfoIndizado{$linea['ruc']}['U_SYP_SNBUEN'] == 'Y')
            ) {
                //limpiamos el valor total
                $resultadoCab[$nroLinea]['DocTotal'] = '';
                $resultadoCab[$nroLinea]['U_SYP_DET_RET'] = substr($docSapTipos[$linea['TipoProceso']]['codigodetraccion'], 0, 1);
                $resultadoCab[$nroLinea]['U_SYP_COD_DET'] = $docSapTipos[$linea['TipoProceso']]['codigodetraccion'];
                $resultadoCab[$nroLinea]['U_SYP_NOM_DETR'] = substr($retencionInfoIndizado[$docSapTipos[$linea['TipoProceso']]['codigodetraccion']]['WTName'], 0, 31);
                $resultadoCab[$nroLinea]['U_SYP_PORC_DETR'] = intval($retencionInfoIndizado[$docSapTipos[$linea['TipoProceso']]['codigodetraccion']]['U_SYP_PORC']);

            }

            $j = 1;
            $k = 1;

            foreach ($linea['Files'] as $file):
                $numFileFormat = str_replace('-', '0', $file);
                $numFileFormat = substr($numFileFormat, 2, strlen($numFileFormat - 2));
                $resultadoDet[$nroLineaDet]['DocNum'] = $i;
                $resultadoDet[$nroLineaDet]['LineNum'] = $j;
                $resultadoDet[$nroLineaDet]['u_syp_tipoServ'] = '';
                $resultadoDet[$nroLineaDet]['Dscription'] = '';
                $resultadoDet[$nroLineaDet]['AcctCode'] = $cuenta;

                $resultadoDet[$nroLineaDet]['Currency'] = $linea['Currency'];

                if ($k < $numFiles) {
                    $resultadoDet[$nroLineaDet]['LineNetoTotal'] = $linea['DividedNetoTotal'] * $fileInfoIndizado[$file]['NUMERO_PAX'];
                    $resultadoDet[$nroLineaDet]['LineTaxTotal'] = $linea['DividedTaxTotal'] * $fileInfoIndizado[$file]['NUMERO_PAX'];

                    $this->setSuma('neto', $resultadoDet[$nroLineaDet]['LineNetoTotal']);
                    $this->setSuma('impuesto', $resultadoDet[$nroLineaDet]['LineTaxTotal']);

                } else {
                    $resultadoDet[$nroLineaDet]['LineNetoTotal'] = $linea['NetoTotal'] - $this->getSuma('neto');
                    $resultadoDet[$nroLineaDet]['LineTaxTotal'] = $linea['TaxTotal'] - $this->getSuma('impuesto');
                }

                if ($igv > 0) {
                    if (isset($linea['TaxCode'])) {
                        $resultadoDet[$nroLineaDet]['VatGroup'] = $linea['TaxCode'];
                        $resultadoDet[$nroLineaDet]['TaxCode'] = $linea['TaxCode'];
                    } elseif ($esGrabado === true) {
                        $resultadoDet[$nroLineaDet]['VatGroup'] = $docSapTipos[$linea['TipoProceso']]['codigoigvgravado'];
                        $resultadoDet[$nroLineaDet]['TaxCode'] = $docSapTipos[$linea['TipoProceso']]['codigoigvgravado']; //IGV
                    } else {
                        if (!empty($esDiferido)) {
                            $resultadoDet[$nroLineaDet]['VatGroup'] = $docSapTipos[$linea['TipoProceso']]['codigoigvnogravadodif']; // DNGD_IGV
                            $resultadoDet[$nroLineaDet]['TaxCode'] = $docSapTipos[$linea['TipoProceso']]['codigoigvnogravadodif'];
                        } else {
                            $resultadoDet[$nroLineaDet]['VatGroup'] = $docSapTipos[$linea['TipoProceso']]['codigoigvnogravado']; //DNGR_IGV
                            $resultadoDet[$nroLineaDet]['TaxCode'] = $docSapTipos[$linea['TipoProceso']]['codigoigvnogravado']; //'DNGR_IGV';
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

                if (!$this->getUser()->getDependencia() || empty($this->getUser()->getDependencia()->getAbreviatura())) {
                    $resultadoDet[$nroLineaDet]['OcrCode3'] = 'VERIFIQUE LA ABREVIATURA DE LA DEPENDENCIA';
                } else {
                    $resultadoDet[$nroLineaDet]['OcrCode3'] = $this->getUser()->getDependencia()->getAbreviatura();
                }

                $resultadoDet[$nroLineaDet]['OcrCode4'] = $docSapTipos[$linea['TipoProceso']]['tiposervicio'];
                $resultadoDet[$nroLineaDet]['OcrCode5'] = '100';

                if (!empty($resultadoCab[$nroLinea]['U_SYP_COD_DET'])) {
                    $resultadoDet[$nroLineaDet]['WtLiable'] = 'Y';

                    $resultadoRet[$nroLineaDet]['DocNum'] = $i;
                    $resultadoRet[$nroLineaDet]['LineNum'] = $j;
                    $resultadoRet[$nroLineaDet]['WTCode'] = $resultadoCab[$nroLinea]['U_SYP_COD_DET'];

                    if ($linea['Currency'] == 'US$') {
                        $resultadoRet[$nroLineaDet]['TaxbleAmnt'] = '';
                        $resultadoRet[$nroLineaDet]['WTAmnt'] = '';
                        $resultadoRet[$nroLineaDet]['TxblAmntFC'] = $resultadoDet[$nroLineaDet]['LineNetoTotal'];
                        $resultadoRet[$nroLineaDet]['WTAmntFC'] = round($resultadoDet[$nroLineaDet]['LineNetoTotal'] * $resultadoCab[$nroLinea]['U_SYP_PORC_DETR'] / 100, 2);

                    } else {
                        $resultadoRet[$nroLineaDet]['TaxbleAmnt'] = $resultadoDet[$nroLineaDet]['LineNetoTotal'];
                        $resultadoRet[$nroLineaDet]['WTAmnt'] = round($resultadoDet[$nroLineaDet]['LineNetoTotal'] * $resultadoCab[$nroLinea]['U_SYP_PORC_DETR'] / 100, 2);
                        $resultadoRet[$nroLineaDet]['TxblAmntFC'] = '';
                        $resultadoRet[$nroLineaDet]['WTAmntFC'] = '';
                    }

                } else {
                    $resultadoDet[$nroLineaDet]['WtLiable'] = 'N';
                }
                //$resultadoDet[$nroLineaDet]['excelRowNumber'] = $linea['excelRowNumber'];

                $j++;
                $nroLineaDet++;

                if ($linea['ImpuestoExtraTotal'] > 0) {
                    $resultadoDet[$nroLineaDet] = $resultadoDet[$nroLineaDet - 1];
                    $resultadoDet[$nroLineaDet]['LineNum'] = $j;
                    $resultadoDet[$nroLineaDet]['VatGroup'] = 'EXE_IGV';
                    $resultadoDet[$nroLineaDet]['TaxCode'] = 'EXE_IGV';
                    $resultadoDet[$nroLineaDet]['LineTaxTotal'] = 0;
                    if ($k < $numFiles) {
                        $resultadoDet[$nroLineaDet]['LineNetoTotal'] = $linea['DividedImpuestoExtraTotal'] * $fileInfoIndizado[$file]['NUMERO_PAX'];
                        $this->setSuma('impuestoextra', $resultadoDet[$nroLineaDet]['LineNetoTotal']);

                    } else {
                        $resultadoDet[$nroLineaDet]['LineNetoTotal'] = $linea['ImpuestoExtraTotal'] - $this->getSuma('impuestoextra');
                    }

                    $j++;
                    $nroLineaDet++;
                }
                $k++;

            endforeach;

            $this->resetSuma('numeroPax');
            $this->resetSuma('neto');
            $this->resetSuma('impuesto');
            $this->resetSuma('impuestoextra');
            //$resultadoCab[$nroLinea]['excelRowNumber'] = $linea['excelRowNumber'];

            $i++;

        endforeach;

        $encabezadosCab = [
            'DocNum',
            'CardCode',
            'DocType',
            'DocDate',
            'TaxDate',
            'DocDueDate',
            'DocCurrency',
            'ControlAccount',
            'DocRate',
            'DocTotal',
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
            'U_SYP_PORC_DETR'
        ];

        $encabezadosCabSec = [
            'DocNum',
            'CardCode',
            'DocType',
            'DocDate',
            'TaxDate',
            'DocDueDate',
            'DocCur',
            'CtlAccount',
            'DocRate',
            'DocTotal',
            'Series',
            'U_SYP_MDTD',
            'U_SYP_MDSD',
            'U_SYP_MDCD',
            'U_SYP_STATUS',
            'JrnlMemo',
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
            'U_SYP_PORC_DETR'
        ];

        $encabezadosDet = [
            'ParentKey',
            'LineNum',
            'u_syp_tipoServ',
            'ItemDescription',
            'AccountCode',
            'Currency',
            'LineTotal',
            'TaxTotal',
            'VatGroup',
            'TaxCode',
            'CostingCode',
            'CostingCode2',
            'CostingCode3',
            'CostingCode4',
            'CostingCode5',
            'WtLiable'
        ];

        $encabezadosDetSec = [
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
            'WtLiable'
        ];

        $encabezadosRet = [
            'ParentKey',
            'LineNum',
            'WTCode',
            'TaxableAmount',
            'WTAmount',
            'TaxableAmountFC',
            'WTAmountFC'
        ];

        $encabezadosRetSec = [
            'DocNum',
            'LineNum',
            'WTCode',
            'TaxbleAmnt',
            'WTAmnt',
            'TxblAmntFC',
            'WTAmntFC'
        ];

        $archivoGenerado = $this->get('gopro_main_archivoexcel');

        $archivoGenerado
            ->setArchivo()
            ->setParametrosWriter("SAP-" . $nombreArchivo)
            ->setFila($encabezadosCab, 'A4')
            ->setFila($encabezadosCabSec, 'A5')
            ->setTabla($resultadoCab, 'A6')
            ->setFormatoColumna(['yyyymmdd' => ['d', 'e', 'f', 'v'], '@' => ['sz']])
            ->setHoja(2)
            ->setFila($encabezadosDet, 'A4')
            ->setFila($encabezadosDetSec, 'A5')
            ->setTabla($resultadoDet, 'A6')
            ->setHoja(3);

        if (!empty($resultadoRet)) {
            $archivoGenerado
                ->setFila($encabezadosRet, 'A4')
                ->setFila($encabezadosRetSec, 'A5')
                ->setTabla($resultadoRet, 'A6')
                ->setHoja(4);
        }

        $archivoGenerado
            ->setColumna($this->getMensajes(), 'A1')
            ->setHoja(1);


        return $archivoGenerado->getArchivo();
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
