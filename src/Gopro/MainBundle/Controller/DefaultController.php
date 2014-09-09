<?php

namespace Gopro\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return array('name' => 'Vipac');
    }

    /**
     * @Route("/sidebar")
     * @Template()
     */
    public function sidebarAction()
    {
        $items=array(
            array('nombre'=>'Generador de firmas','route'=>'gopro_vipac_extra_firma'),
            array('nombre'=>'Calculadora por File','route'=>'gopro_vipac_dbproceso_proceso_calxfile'),
            array('nombre'=>'Calculadora por Reserva','route'=>'gopro_vipac_dbproceso_proceso_calxreserva'),
            array('nombre'=>'Cheque','route'=>'gopro_vipac_dbproceso_proceso_cheque'),
            array('nombre'=>'Cargador Generico','route'=>'gopro_vipac_dbproceso_carga_generico'),
            array('nombre'=>'Reclasificador de CC','route'=>'gopro_vipac_dbproceso_proceso_calcc'),
            array('nombre'=>'Cargador de CP','route'=>'gopro_vipac_dbproceso_proceso_cargadorcp'),
            array('nombre'=>'Tipos de documentos CP','route'=>'gopro_vipac_dbproceso_doccptipo'),
            array('nombre'=>'Reportes','route'=>'gopro_vipac_reporte_sentencia'),
            array('nombre'=>'Inventario Items','route'=>'gopro_inventario_item'),
            array('nombre'=>'Inventario Componentes','route'=>'gopro_inventario_componente'),
            array('nombre'=>'Inventario Caracteristicas','route'=>'gopro_inventario_componentecaracteristica'),
            array('nombre'=>'Servicios tecnicos','route'=>'gopro_inventario_servicio'),
            array('nombre'=>'Registro de Proveedor','route'=>'gopro_vipac_proveedor_informacion'),
        );
        return array('items'=> $items);
    }
}
