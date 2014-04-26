<?php

namespace Gopro\Vipac\Bundle\CargadorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        $conn = $this->get('doctrine.dbal.default_connection');
        $paises = $conn->fetchAll('SELECT * FROM reservas.pais');
        //print_r($array);
        //$sql = "SELECT * FROM reservas.paises WHERE";
        //$stmt = $this->connection->prepare($sql);
        //$stmt->execute();


        //return $bar;
        return array('paises' => $paises);
    }
}
