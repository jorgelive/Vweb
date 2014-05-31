<?php

namespace Gopro\Vipac\DbprocesoBundle\Controller;

use Gopro\Vipac\DbprocesoBundle\Entity\Archivo;
use Gopro\Vipac\MainBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class ArchivoController extends BaseController
{

    /**
     * @Route("/archivo/index", name="gopro_vipac_dbproceso_archivo_index")
     * @Template()
     */
    public function indexAction(){
        $repositorio = $this->getDoctrine()->getRepository('GoproVipacDbprocesoBundle:Archivo');
        $archivosAlmacenados=$repositorio->findBy(array('usuario' => $this->getUserName()),array('creado' => 'DESC'));
        //print_r($archivosAlmacenados);

        foreach($archivosAlmacenados as $nro => $archivo):
            $respuesta['files'][$nro]['url']=$archivo->getWebPath();
            $respuesta['files'][$nro]['name']=$archivo->getNombre();
            $respuesta['files'][$nro]['operacion']=$archivo->getOperacion();
            $respuesta['files'][$nro]['id']=$archivo->getId();
            $respuesta['files'][$nro]['deleteUrl']=$archivo->getId();
            $respuesta['files'][$nro]['deleteType']='DELETE';
        endforeach;

        return new JsonResponse($respuesta);

    }

    /**
     * @Route("/archivo/borrar/{id}", name="gopro_vipac_dbproceso_archivo_borrar")
     * @Method({"DELETE"})
     * @Template()
     */
    public function borrarAction(Request $request, $id)
    {
        if (!$request->isXMLHttpRequest()){
            throw new NotFoundHttpException("No se encontro la página");
        }

        $usuario=$this->get('security.context')->getToken()->getUser();
        if(!is_string($usuario)){
            $usuario=$usuario->getUsername();
        }
        $em = $this->getDoctrine()->getManager();
        $archivo = $em->getRepository('GoproVipacDbprocesoBundle:Archivo')->find($id);

        if(empty($archivo)||$archivo->getUsuario()!=$usuario){
            return new JsonResponse(array('exito'=>'no','mensaje'=>'No existe el archivo'));

        }
        $em->remove($archivo);
        $em->flush();
        return new JsonResponse(array('exito'=>'si','mensaje'=>'Se ha eliminado el archivo'));
    }

    //@TODO: implementar funcion
    /**
     * @Route("/archivo/editar", name="gopro_vipac_dbproceso_archivo_editar")
     * @Method({"POST"})
     * @Template()
     */
    public function editarAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()){
            throw new NotFoundHttpException("No se encontro la página");
        }
        $usuario=$this->get('security.context')->getToken()->getUser();
        if(!is_string($usuario)){
            $usuario=$usuario->getUsername();
        }
        $em = $this->getDoctrine()->getManager();
        $archivo = $em->getRepository('GoproVipacDbprocesoBundle:Archivo')->find($request->request->get('id'));

        if(empty($archivo)||$archivo->getUsuario()!=$usuario){
            return new JsonResponse(array('exito'=>'no','mensaje'=>'No existe el archivo'));
        }
        $em->remove($archivo);
        $em->flush();
        return new JsonResponse(array('exito'=>'si','mensaje'=>'Se ha eliminado el archivo'));
    }

    /**
     * @Route("/archivo/agregar", name="gopro_vipac_dbproceso_archivo_agregar")
     * @Method({"POST"})
     * @Template()
     */
    public function agregarAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()){
            throw new NotFoundHttpException("No se encontro la página");
        }
        $archivo = new Archivo();
        $formulario = $this->createFormBuilder($archivo)
            ->add('nombre')
            ->add('file')
            ->getForm();
        $formulario->handleRequest($request);

        if ($formulario->isValid()){
            $archivo->setUsuario($this->getUserName());
            $em = $this->getDoctrine()->getManager();
            $em->persist($archivo);
            $em->flush();
            return $this->redirect($this->generateUrl('gopro_vipac_dbproceso_'.$operacion));
        }
    }
}
