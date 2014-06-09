<?php

namespace Gopro\Vipac\MainBundle\Controller;

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
            array('nombre'=>'Calculadora por File','route'=>'proceso_calxfile'),
            array('nombre'=>'Calculadora por Reserva','route'=>'proceso_calxreserva'),
            array('nombre'=>'Cheque','route'=>'proceso_cheque'),
            array('nombre'=>'Cargador Generico','route'=>'carga_generico'),
            array('nombre'=>'Reclasificador de CC','route'=>'proceso_calcc'),
            array('nombre'=>'Cargador de CP','route'=>'proceso_cargadorcp'),
            array('nombre'=>'Tipos de documentos CP','route'=>'doccptipo'),
            array('nombre'=>'Vencimiento CP','route'=>'reporte_vencimientocp'),


        );
        return array('items'=> $items);
    }


}
