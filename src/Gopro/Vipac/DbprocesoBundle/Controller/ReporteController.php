<?php

namespace Gopro\Vipac\DbprocesoBundle\Controller;

use Gopro\Vipac\DbprocesoBundle\Form\ParametrosType;
use Gopro\Vipac\MainBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Reporte controller.
 *
 * @Route("/reporte")
 */
class ReporteController extends BaseController
{

    /**
     * @Route("/index", name="proceso_index")
     * @Template()
     */
    public function indexAction(){


    }

    /**
     * @Route("/vencimientocp", name="reporte_vencimientocp")
     * @Template()
     */
    public function VencimientocpAction(Request $request)
    {
        $datos = array();
        $formulario = $this->createForm(new ParametrosType(), $datos, array(
            'action' => $this->generateUrl('reporte_vencimientocp'),
            'method' => 'POST',
        ));

        $formulario->add('submit', 'submit', array('label' => 'Procesar'));

        if ($request->getMethod() != 'POST') {
            return array('formulario' => $formulario->createView(), 'mensajes' => $this->getMensajes());
        }

        $formulario->handleRequest($request);
        if (!$formulario->isValid()){
            $this->setMensajes('Los parametros no son correctos');
            return array('formulario' => $formulario->createView(),'mensajes' => $this->getMensajes());

        }
        $parametros = $formulario->getData();
        print_r($parametros);
        $inicioText='2014-05-01';
        $finText='2014-05-20';
        $destino='archivo';
        $fechaInicio = new \DateTime($inicioText);
        $fechaFin = new \DateTime($finText);
        $diferencia = $fechaFin->diff($fechaInicio);
        $numDias=$diferencia->format('%d')+1;

        if($fechaFin<$fechaInicio){
            $this->setMensajes('La fecha de inicio es mayor a la fecha de fin');
            return array('formulario' => $formulario->createView(),'mensajes' => $this->getMensajes());
        }
        if($numDias>31){
            $this->setMensajes('El periodo es muy largo');
            return array('formulario' => $formulario->createView(),'mensajes' => $this->getMensajes());
        }

        $selectQuery="SELECT * FROM VIAPAC.VVW_DOCCP_VENCIMIENTO WHERE VVW_DOCCP_VENCIMIENTO.FECHA_VENCIMIENTO >= to_date(:fechaInicio,'yyyy-mm-dd') AND VVW_DOCCP_VENCIMIENTO.FECHA_VENCIMIENTO <= to_date(:fechaFin,'yyyy-mm-dd')";
        if ($this->getUser()->hasGroup('Cusco')) {
            $selectQuery.=" AND LOCALIDAD ='Cusco'";
        }else{
            $selectQuery.=" AND LOCALIDAD ='Lima'";
        }
        $statement = $this->container->get('doctrine.dbal.vipac_connection')->prepare($selectQuery);
        $statement->bindValue('fechaInicio',$fechaInicio->format('Y-m-d'));
        $statement->bindValue('fechaFin',$fechaFin->format('Y-m-d'));
        if(!$statement->execute()){
            $this->setMensajes('Hubo un error en la ejecucion de la consulta');
            return array('formulario' => $formulario->createView(),'mensajes' => $this->getMensajes());
        }

        $existentesRaw=$statement->fetchAll();

        if(empty($existentesRaw)){
            $this->setMensajes('No hay resultados');
            return array('formulario' => $formulario->createView(),'mensajes' => $this->getMensajes());
        }

        foreach($existentesRaw as $fila => $valores):
            $fechaProceso=new \DateTime($inicioText);
            $resultados[$fila]['COD_PROVEEDOR']=$valores['PROVEEDOR'];
            $resultados[$fila]['NOMBRE_PROVEEDOR']=$valores['NOMBRE'];
            $resultados[$fila]['ASIENTO']=$valores['ASIENTO'];
            $resultados[$fila]['TIPO_DOCUMENTO']=$valores['TIPO'];
            $resultados[$fila]['NRO_DOCUMENTO']=$valores['DOCUMENTO'];
            for($i=0;$i<$numDias;$i++){
                $fechaVencimiento = new \DateTime($valores['FECHA_VENCIMIENTO']);
                if($fechaProceso==$fechaVencimiento){
                    if($valores['MONEDA']='SOL'){
                        $resultados[$fila][$fechaProceso->format('Y-m-d').' SOL']=$valores['SALDO'];
                        $resultados[$fila][$fechaProceso->format('Y-m-d').' USD']='';
                    }else{
                        $resultado[$fila][$fechaProceso->format('Y-m-d').' SOL']='';
                        $resultados[$fila][$fechaProceso->format('Y-m-d').' USD']=$valores['SALDO'];
                    }
                }else{
                    $resultados[$fila][$fechaProceso->format('Y-m-d').' SOL']='';
                    $resultados[$fila][$fechaProceso->format('Y-m-d').' USD']='';
                }
                $fechaProceso->add(new \DateInterval('P1D'));
            }

        endforeach;
        $encabezado=array_keys($resultados[0]);
        //print_r($encabezado);
        //print_r($resultado);

        if($destino='archivo'){
            $archivoGenerado=$this->get('gopro_dbproceso_comun_archivo');
            $archivoGenerado->setParametrosWriter('Reporte_'.$inicioText.'_'.$finText,$encabezado,$this->container->get('gopro_dbproceso_comun_variable')->utf($resultados));
            $archivoGenerado->setArchivoGenerado();
            return $archivoGenerado->getArchivoGenerado();
        }

        return array('formulario' => $formulario->createView(), 'mensajes' => $this->getMensajes());



    }


}
