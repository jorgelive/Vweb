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
     * @Route("/vencimientocp", name="gopro_vipac_dbproceso_reporte_vencimientocp")
     * @Template()
     */
    public function vencimientocpAction(Request $request)
    {
        $datos = array();
        $formulario = $this->createForm(new ParametrosType(), $datos, array(
            'action' => $this->generateUrl('gopro_vipac_dbproceso_reporte_vencimientocp'),
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
        $destino='archivo';
        $fechaInicio = $parametros['fechaInicio'];
        $fechaFin = $parametros['fechaFin'];
        $diferencia = $fechaInicio->diff($fechaFin);
        $numDias=$diferencia->format("%r%a")+1;
        if($numDias<=0){
            $this->setMensajes('La fecha de inicio es mayor a la fecha de fin');
            return array('formulario' => $formulario->createView(),'mensajes' => $this->getMensajes());
        }
        if($numDias>60){
            $this->setMensajes('El periodo es muy largo');
            return array('formulario' => $formulario->createView(),'mensajes' => $this->getMensajes());
        }
        if($parametros['tipo']=='resumido'){
            $selectQuery='SELECT PROVEEDOR, NOMBRE, FECHA_VENCIMIENTO, MONEDA, sum(SALDO) SALDO FROM VIAPAC.VVW_DOCCP_VENCIMIENTO';
        }else{
            $selectQuery='SELECT * FROM VIAPAC.VVW_DOCCP_VENCIMIENTO';
        }

        $selectQuery.=" WHERE FECHA_VENCIMIENTO >= to_date(:fechaInicio,'yyyy-mm-dd') AND FECHA_VENCIMIENTO <= to_date(:fechaFin,'yyyy-mm-dd')";

        if ($this->getUser()->hasGroup('Cusco')) {
            $selectQuery.=" AND LOCALIDAD ='Cusco'";
        }else{
            $selectQuery.=" AND LOCALIDAD ='Lima'";
        }

        if($parametros['tipo']=='resumido'){
            $selectQuery.=' group by PROVEEDOR, NOMBRE, FECHA_VENCIMIENTO, MONEDA';
        }
        $statement = $this->container->get('doctrine.dbal.vipac_connection')->prepare($selectQuery);
        $statement->bindValue('fechaInicio',$fechaInicio->format('Y-m-d'));
        $statement->bindValue('fechaFin',$fechaFin->format('Y-m-d'));
        if(!$statement->execute()){
            $this->setMensajes('Hubo un error en la ejecucion de la consulta');
            return array('formulario' => $formulario->createView(),'mensajes' => $this->getMensajes());
        }

        $existentesRaw=$this->container->get('gopro_dbproceso_comun_variable')->utf($statement->fetchAll());

        if(empty($existentesRaw)){
            $this->setMensajes('No hay resultados');
            return array('formulario' => $formulario->createView(),'mensajes' => $this->getMensajes());
        }

        foreach($existentesRaw as $fila => $valores):
            $fechaProceso = clone $fechaInicio;
            $resultados[$fila]['COD_PROVEEDOR']=$valores['PROVEEDOR'];
            $resultados[$fila]['NOMBRE_PROVEEDOR']=$valores['NOMBRE'];
            if($parametros['tipo']!='resumido'){
                $resultados[$fila]['ASIENTO']=$valores['ASIENTO'];
                $resultados[$fila]['TIPO_DOCUMENTO']=$valores['TIPO'];
                $resultados[$fila]['NRO_DOCUMENTO']=$valores['DOCUMENTO'];
            }
            for($i=0;$i<$numDias;$i++){
                $fechaVencimiento = new \DateTime($valores['FECHA_VENCIMIENTO']);
                if($fechaProceso==$fechaVencimiento){
                    if($valores['MONEDA']=='SOL'){
                        $resultados[$fila][$fechaProceso->format('Y-m-d').' SOL']=$valores['SALDO'];
                        $resultados[$fila][$fechaProceso->format('Y-m-d').' USD']='';
                    }else{
                        $resultados[$fila][$fechaProceso->format('Y-m-d').' SOL']='';
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

        if($destino='archivo'){
            $archivoGenerado=$this->get('gopro_dbproceso_comun_archivo');
            $archivoGenerado->setParametrosWriter('Reporte_'.$fechaInicio->format('Y-M-d').'_'.$fechaFin->format('Y-M-d'),$resultados,$encabezado);
            $archivoGenerado->setAnchoColumna(['A'=>12,'B'=>'auto','2:'=>12]);
            return $archivoGenerado->getArchivo();
        }
        return array('formulario' => $formulario->createView(), 'mensajes' => $this->getMensajes());
    }
}
