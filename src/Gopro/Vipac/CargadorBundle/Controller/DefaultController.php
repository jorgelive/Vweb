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
    public function indexAction($pais)
    {

        return array('paises' => $pais);
    }

    /**
     * @Route("/carga", name="gopro_vipac_cargador_default_carga")
     * @Template()
     */
    public function cargaAction(Request $request)
    {
        $usuario=$this->get('security.context')->getToken()->getUser();

        $repositorio = $this->getDoctrine()->getRepository('GoproVipacCargadorBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $usuario, 'operacion' => 'cargadorgenerico'));



        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();

        $formulario->handleRequest($request);

        if ($formulario->isValid()){
            $archivo->setUsuario($usuario);
            $archivo->setOperacion('cargadorgenerico');
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_cargador_default_carga'));
        }

        return array('formulario' => $formulario->createView(),'archivosAlmacenados' => $archivosAlmacenados);
    }

    /**
     * @Route("/cargadorgenerico", name="gopro_vipac_cargador_default_cargadorgenerico")
     * @Template()
     */
    public function cargadorgenericoAction()
    {
        $tablaSpecs=array();
        $columnaSpecs=array();
        $valores=array();
        $archivo=$this->get('gopro_comun_archivo')->parseExcel(false,false);//para limitar pasar los valores

        extract($archivo);

        $mensajes = $this->get('gopro_comun_cargador')->ejecutar($tablaSpecs,$columnaSpecs,$valores);

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
