<?php

namespace Gopro\InventarioBundle\Controller;

use Gopro\MainBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\InventarioBundle\Entity\Servicio;
use Gopro\InventarioBundle\Form\ServicioType;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Servicio controller.
 *
 * @Route("/servicio")
 */
class ServicioController extends BaseController
{

    /**
     * Lists all Servicio entities.
     *
     * @Route("/", name="gopro_inventario_servicio")
     * @Method({"GET","POST"})
     * @Template()
     */
    public function indexAction()
    {
        $source = new Entity('GoproInventarioBundle:Servicio');

        $grid = $this->get('grid');

        $mostrarAction = new RowAction('mostrar', 'gopro_inventario_servicio_show');
        $mostrarAction->setRouteParameters(array('id'));
        $grid->addRowAction($mostrarAction);

        $editarAction = new RowAction('editar', 'gopro_inventario_servicio_edit');
        $editarAction->setRouteParameters(array('id'));
        $grid->addRowAction($editarAction);

        $grid->setSource($source);

        return $grid->getGridResponse();
    }

    /**
     * @Route("/lista", name="gopro_inventario_servicio_lista")
     * @Method("GET")
     * @Template()
     */
    public function listaservicioAction()
    {
        $em = $this->getDoctrine()->getManager();

        $servicios = $em->createQueryBuilder()
            ->addSelect('s')
            ->from('GoproInventarioBundle:Servicio', 's')
            ->leftJoin('s.item', 'i')
            ->addSelect('i')
            ->leftJoin('i.dependencia', 'd')
            ->addSelect('d')
            ->leftJoin('i.users', 'u')
            ->addSelect('u')
            ->leftJoin('i.componentes','c', 'WITH', 'c.componentetipo=1')
            ->addSelect('c')
            ->leftJoin('c.caracteristicas','ca')
            ->addSelect('ca')
            ->leftJoin('ca.caracteristicatipo','ct')
            ->addSelect('ct')
            ->where($em->createQueryBuilder()->expr()->eq('s.serviciotipo', 1))
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult();

        if (empty($servicios)) {
            throw $this->createNotFoundException('No se encontraton resultados.');
        }

        $itemsAnoDependencia=array();
        foreach($servicios as $servicio):
            $ano=$servicio->getTiempo()->format('Y');
            $dependencia=$servicio->getItem()->getDependencia()->getId();
            $itemsAnoDependencia[$ano][$dependencia]['dependencia']=$servicio->getItem()->getDependencia();
            $itemsAnoDependencia[$ano][$dependencia]['items'][$servicio->getItem()->getId()]['item']=$servicio->getItem();
            $itemsAnoDependencia[$ano][$dependencia]['items'][$servicio->getItem()->getId()]['servicios'][]=$servicio;
        endforeach;

        foreach($itemsAnoDependencia as $ano=>$itemsDependencia):
            foreach($itemsDependencia as $dependencia=>$items):
                $resultado=array();
                $dependenciaNombre = $items['dependencia']->getNombre();
                $iItems=0;
                foreach($items['items'] as $item):
                    $resultado[$iItems][]=$iItems+1;
                    $resultado[$iItems][]=$item['item']->getItemtipo()->getNombre();
                    $userList=array();
                    foreach($item['item']->getUsers() as $user):
                        $userList[]=$user->getFirstName().' '.$user->getLastName();
                    endforeach;
                    $resultado[$iItems][]=implode(' | ',$userList);
                    $resultado[$iItems][]=$item['item']->getCodigo();
                    $areaList=array();
                    foreach($item['item']->getAreas() as $area):
                        $areaList[]=$area->getNombre();
                    endforeach;
                    $resultado[$iItems][]=implode(' | ',$areaList);
                    $itemList=array();
                    foreach($item['item']->getComponentes() as $componete):
                        foreach($componete->getCaracteristicas() as $caracteristica):
                            if(in_array($caracteristica->getCaracteristicatipo()->getId(),[1,2,3])){
                                $itemList[$caracteristica->getCaracteristicatipo()->getId()]=$caracteristica->getContenido();
                            }
                        endforeach;

                    endforeach;
                    for($iCaracteristicatipo=1;$iCaracteristicatipo<=3;$iCaracteristicatipo++):
                        if(!empty($itemList[$iCaracteristicatipo])){
                            $resultado[$iItems][]=$itemList[$iCaracteristicatipo];
                        }else{
                            $resultado[$iItems][]='';
                        }
                    endfor;
                    if(!empty($item['item']->getServicios()[0])){
                        $resultado[$iItems][]=$item['item']->getServicios()[0]->getUser()->getFirstName().' '.$item['item']->getServicios()[0]->getUser()->getLastName();
                    }else{
                        $resultado[$iItems][]='';
                    }
                    $serviciosList=array();
                    foreach($item['item']->getServicios() as $servicio):
                        if($servicio->getTiempo()->format('d')>=15){
                            $quincena=2;
                        }else{
                            $quincena=1;
                        }
                        if($servicio->getServicioestado()->getId()==1){
                            $marca='P';
                        }else{
                            $marca='✓';
                        }
                        if($servicio->getTiempo()->format('Y')==$ano){
                            $serviciosList[(($servicio->getTiempo()->format('m')-1)*2)+$quincena]=$marca;
                        }
                    endforeach;
                    for($iServicios=1;$iServicios<=24;$iServicios++):
                        if(isset($serviciosList[$iServicios])){
                            $resultado[$iItems][]=$serviciosList[$iServicios];
                        }else{
                            $resultado[$iItems][]='';
                        }
                    endfor;

                    $iItems++;
                endforeach;

                $archivoGenerado=$this->get('gopro_main_archivoexcel')
                    ->setArchivoBase($this->getDoctrine()->getRepository('GoproMainBundle:Archivo'),2,'inventario_servicio_lista')
                    ->setArchivo()
                    ->setParametrosWriter('F-SIS-01-'.$ano.'_'.$dependenciaNombre)
                    ->setCeldas(['B5'=>'AÑO: '.$ano,'G5'=> ($ano-1).'-12-10'])
                    ->setTabla($resultado,'A9');

                $archivos[]=[
                    'path'=>$archivoGenerado->getArchivo('archivo'),
                    'nombre'=>$archivoGenerado->getNombre().'.'.$archivoGenerado->getTipo()
                ];
            endforeach;
        endforeach;
        if(empty($archivos)){
            throw $this->createNotFoundException('No se pueden generar los archivos.');
        }

        return $this->get('gopro_main_archivozip')
            ->setParametros($archivos,'listamantenimientos_'.time())
            ->setArchivo()
            ->getArchivo();

    }

    /**
     * @Route("/generar/{ano}/{semestre}", name="gopro_inventario_servicio_generar")
     * @Method("GET")
     */
    public function generarAction($ano,$semestre)
    {
        if(!in_array($semestre,[1,2])||!in_array($ano,[2013,2014,2015,2016,2017,2018,2019,2020])){
            return $this->redirect($this->generateUrl('gopro_inventario_servicio'));

        }
        $em = $this->getDoctrine()->getManager();
        $estadoEjecutado=$em->getRepository('GoproInventarioBundle:Servicioestado')->find(2);
        $estadoPlanificado=$em->getRepository('GoproInventarioBundle:Servicioestado')->find(1);
        $tipo=$em->getRepository('GoproInventarioBundle:Serviciotipo')->find(1);
        $ejecutor=$em->getRepository('GoproUserBundle:User')->find(1);

        if(!is_object($estadoEjecutado)||!is_object($estadoPlanificado)||!is_object($tipo)||!is_object($ejecutor)){
            return $this->redirect($this->generateUrl('gopro_inventario_servicio'));

        }

        $dependencias=$em->createQueryBuilder()
            ->select('d.id')
            ->from('GoproUserBundle:Dependencia','d','d.id')
            ->orderBy('d.id')
            ->getQuery()
            ->getArrayResult();

        $fechaInicio=new \DateTime($ano.'-0'.((($semestre-1)*6)+1).'-01');

        if($semestre==1){
            $fechaFin=new \DateTime($ano.'-07-01');
        }else{
            $fechaFin=new \DateTime(($ano+1).'-01-01');
        }

        foreach(array_keys($dependencias) as $dependencia):
            $items = $em->createQueryBuilder()
                ->addSelect('i')
                ->from('GoproInventarioBundle:Item', 'i')
                ->leftJoin('i.componentes','c', 'WITH', 'c.componentetipo=1 and c.componenteestado=1')
                ->addSelect('c')
                ->leftJoin('i.servicios', 's', 'WITH', 's.tiempo>=:fechaInicio and s.tiempo<:fechaFin')
                ->addSelect('s')
                ->orderBy('i.id', 'ASC')
                ->where($em->createQueryBuilder()->expr()->eq('i.dependencia', ':dependencia'))
                ->setParameter('dependencia', $dependencia)
                ->setParameter('fechaInicio', $fechaInicio)
                ->setParameter('fechaFin', $fechaFin)
                ->getQuery()
                ->getArrayResult();
            $periodo=(180/count($items));
            $fecha=new \DateTime($ano.'-0'.((($semestre-1)*6)+1).'-01');
            foreach($items as $key => $item):
                if($key%2==0){
                    $diasAdd=round($periodo,0,PHP_ROUND_HALF_UP);
                }else{
                    $diasAdd=floor($periodo);
                }
                $fecha->add(new \DateInterval('P'.$diasAdd.'D'));
                if(empty($item['servicios'])){
                    if(!empty($item['componentes'])) {
                        foreach ($item['componentes'] as $componente):
                            if (empty($componente['fechacompra']) || $componente['fechacompra'] < $fechaInicio){
                                ${'servicio' . $key} = new Servicio();
                                ${'servicio' . $key}
                                    ->setItem($em->getRepository('GoproInventarioBundle:Item')->find($item['id']))
                                    ->setTiempo(clone $fecha)
                                    ->setServiciotipo($tipo)
                                    ->setUser($ejecutor)
                                    ->setDescripcion('Limpieza interna y externa, optimizacion de programas y archivos, actualización de software.');
                                if ($fecha > new \DateTime()) {
                                    ${'servicio' . $key}->setServicioestado($estadoPlanificado);
                                } else {
                                    ${'servicio' . $key}->setServicioestado($estadoEjecutado);
                                }
                                $em->persist(${'servicio' . $key});
                            }
                        endforeach;
                    }
                }
            endforeach;
        endforeach;
        $em->flush();
        return $this->redirect($this->generateUrl('gopro_inventario_servicio'));
    }


}
