<?php

namespace Gopro\Vipac\DbprocesoBundle\Controller;

use Gopro\Vipac\DbprocesoBundle\Entity\Archivo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Gopro\Vipac\DbprocesoBundle\Comun\Archivo as ArchivoOpe;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class CargaController extends Controller
{
    /**
     * @Route("/carga/index/{name}", name="gopro_vipac_dbproceso_carga_index")
     * @Template()
     */
    public function indexAction($pais)
    {

        return array('paises' => $pais);
    }

    /**
     * @Route("/carga/generico/{archivoEjecutar}", name="gopro_vipac_dbproceso_carga_generico", defaults={"archivoEjecutar" = null})
     * @Template()
     */
    public function genericoAction(Request $request,$archivoEjecutar)
    {
        $mensajes=array();
        $usuario=$this->get('security.context')->getToken()->getUser();

        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $usuario, 'operacion' => 'carga_generico'),array('creado' => 'DESC'));

        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();

        $formulario->handleRequest($request);

        if ($formulario->isValid()){
            $archivo->setUsuario($usuario);
            $archivo->setOperacion('carga_generico');
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_carga_generico'));
        }

        $procesoArchivo=$this->get('gopro_dbproceso_comun_archivo');

        if($procesoArchivo->validarArchivo($repositorio,$archivoEjecutar,'carga_generico')===true){

            $procesoArchivo->setParametros(null,null);
            $mensajes=$procesoArchivo->getMensajes();
            //print_r($archivoProcesado);
            if($procesoArchivo->parseExcel()!==false){
                //print_r($procesoArchivo->getTablaSpecs());
                //print_r($procesoArchivo->getColumnaSpecs());
                $carga=$this->get('gopro_dbproceso_comun_cargador');
                $carga->setParametros($procesoArchivo->getTablaSpecs(),$procesoArchivo->getColumnaSpecs(),$procesoArchivo->getValores(),$this->container->get('doctrine.dbal.default_connection'));
                $carga->cargaGenerica();
                $mensajes=array_merge($mensajes,$carga->getMensajes());
            }else{
                $mensajes=array_merge($mensajes,array('El archivo no se puede procesar'));
            }
            //$mensajes = $this->get('gopro_dbproceso_comun_cargador')->cargadorGenerico($tablaSpecs,$columnaSpecs,$valores);
        }
        $mensajes=array_merge($mensajes,$procesoArchivo->getMensajes());
        return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $mensajes);
    }




}
