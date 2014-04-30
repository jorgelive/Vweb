<?php

namespace Gopro\Vipac\CargadorBundle\Controller;

use Gopro\Vipac\CargadorBundle\Entity\Archivo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Gopro\Vipac\CargadorBundle\Comun\Archivo as ArchivoOpe;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class DefaultController extends Controller
{
    /**
     * @Route("/index/{name}", name="gopro_vipac_cargador_default_index")
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

    /**
     * @Route("/upload", name="gopro_vipac_cargador_default_upload")
     * @Template()
     */
    public function uploadAction(Request $request)
    {
        $archivo = new Archivo();
        $form = $this->createFormBuilder($archivo)
            ->add('name')
            ->add('file')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()){
            $archivo->setUsuario($this->get('security.context')->getToken()->getUser());
            $archivo->setOperacion('cargadorgenerico');
            $em = $this->getDoctrine()->getManager();
            $archivo->upload();
            $em->persist($archivo);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_cargador_default_upload'));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/cargadorgenerico", name="gopro_vipac_cargador_default_cargadorgenerico")
     * @Template()
     */
    public function cargadorgenericoAction()
    {
        $archivo=$this->get('gopro_comun_archivo')->parseExcel();//new ArchivoOpe();

        extract($archivo);

        if(isset($tablaSpecs['tipo'])&&in_array($tablaSpecs['tipo'],Array('IU','UI','I','U'))&&isset($valores)&&isset($tablaSpecs)&&isset($columnaSpecs)&&!empty($valores)&&!empty($tablaSpecs)&&!empty($columnaSpecs)){
            $mensajes=$this->get('gopro_comun_database')->dbPreProcess($tablaSpecs,$columnaSpecs,$valores);
        }elseif(isset($tablaSpecs['tipo'])&&!in_array($tablaSpecs['tipo'],Array('IU','UI','I','U'))){
            $mensajes=array('No se definio correctamente el tipo de proceso');
        }else{
            $mensajes=array('No existe informacion necesaria para el proceso');
        }
        return array('mensajes' => $mensajes);
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
