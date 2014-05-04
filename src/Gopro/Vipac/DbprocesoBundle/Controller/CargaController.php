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
        $usuario=$this->get('security.context')->getToken()->getUser();

        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $usuario, 'operacion' => 'carga_generico'));

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

        if($archivoEjecutar!==null){
            $archivoAlmacenado=$repositorio->find($archivoEjecutar);
        }
        if(isset($archivoAlmacenado)&&$archivoAlmacenado->getOperacion()=='carga_generico'){

            $tablaSpecs=array();
            $columnaSpecs=array();
            $valores=array();
            $valoresDescartados=array();
            $archivoProcesado=$this->get('gopro_dbproceso_comun_archivo')->parseExcel(false,false,$archivoAlmacenado->getAbsolutePath());//para limitar pasar los valores
            //print_r($archivoProcesado);
            extract($archivoProcesado);

            $carga=$this->get('gopro_dbproceso_comun_cargador');
            $carga->setTablaSpecs($tablaSpecs);
            $carga->setColumnaSpecs($columnaSpecs);
            $carga->setValores($valores);

            $mensajes=$carga->getMensajes();
            //$mensajes = $this->get('gopro_dbproceso_comun_cargador')->cargadorGenerico($tablaSpecs,$columnaSpecs,$valores);


        }else{
            $mensajes[]='El archivo no existe';
        }

        return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados, 'mensajes' => $mensajes);
    }

    /**
     * @Route("/excel")
     * @Template()
     */
    public function excelAction()
    {
        $conn = $this->get('doctrine.dbal.default_connection');
        $paises = $conn->fetchAll('SELECT * FROM reservas.pais');

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Viapac")
            ->setTitle("Documento Generado")
            ->setDescription("Documento generado para descargar");
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Codigo')
            ->setCellValue('B1', 'Nombre');

        $phpExcelObject->setActiveSheetIndex(0)->fromArray($paises, NULL, 'A2');
        $phpExcelObject->getActiveSheet()->setTitle('Hoja de datos');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=archivo.xlsx');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }
}
