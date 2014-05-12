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
            array('nombre'=>'Calculadora','route'=>'gopro_vipac_dbproceso_proceso_cheque'),
            array('nombre'=>'Cheque','route'=>'gopro_vipac_dbproceso_proceso_cheque'),
            array('nombre'=>'Cargador Generico','route'=>'gopro_vipac_dbproceso_carga_generico')
        );
        return array('items'=> $items);
    }


}
