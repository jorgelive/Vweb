<?php

namespace Gopro\Vipac\ReporteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Gopro\MainBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\Vipac\ReporteBundle\Entity\Sentencia;
use Gopro\Vipac\ReporteBundle\Entity\Campo;
use Gopro\Vipac\ReporteBundle\Form\SentenciaType;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Sentencia controller.
 *
 * @Route("/sentencia")
 */
class SentenciaController extends BaseController
{

    private $valoresBind;

    /**
     * @return array
     */
    private function getValoresBind(){
        return $this->valoresBind;
    }

    /**
     * @param string $campo
     * @param string $valor
     * @return string
     */
    private function setValoresBind($campo,$valor){
        $this->valoresBind[':v'.substr(sha1($this->container->get('gopro_main_variableproceso')->sanitizeQuery($campo.$valor)),0,28)]=$valor;
        return ':v'.substr(sha1($this->container->get('gopro_main_variableproceso')->sanitizeQuery($campo.$valor)),0,28);
    }

    /**
     * @param string $sql
     *
     * @return array
     */
    private function getClauses($sql){
        $clauses=array();
        if(strtoupper(substr($sql,0,6))!='SELECT'){
            return $clauses;
        }
        if (preg_match('/SELECT (.*?) FROM/i', $sql, $select)) {
            $campos = explode(",",$select[1]);
            $campos = array_map('trim', $campos);
            $clauses['campos']=array_unique($campos);
        }

        if (preg_match('/FROM(.*?)$/i', $sql, $from)) {
            $tabla = explode(' ',trim($from[1]));
            $tabla = trim($tabla[0]);
            $clauses['tabla']=$tabla;
        }

        if (preg_match('/WHERE(.*?)$/i', $sql, $where)) {
            $condiciones = trim($where[0]);
            $clauses['condiciones']=' '.$condiciones;
        }else{
            $clauses['condiciones']='';
        }

        return $clauses;
    }

    /**
     * Lists all Sentencia entities.
     *
     * @Route("/", name="gopro_vipac_reporte_sentencia")
     * @Method("GET")
     * @Secure(roles="ROLE_STAFF")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('GoproVipacReporteBundle:Sentencia')->findAll();
        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Sentencia entity.
     *
     * @Route("/create", name="gopro_vipac_reporte_sentencia_create")
     * @Method("POST")
     * @Secure(roles="ROLE_ADMIN")
     * @Template("GoproVipacReporteBundle:Sentencia:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Sentencia();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setContenido($this->container->get('gopro_main_variableproceso')->sanitizeQuery($entity->getContenido()));
            $em = $this->getDoctrine()->getManager();
            $campos = $this->getClauses($form->getData()->getContenido())['campos'];
            foreach($campos as $campo){
                $campoEntity=new Campo();
                $campoEntity->setNombre($campo);
                $campoEntity->setNombremostrar($campo);
                $campoEntity->setSentencia($entity);
                $entity->getCampos()->add($campoEntity);
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_reporte_sentencia_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Sentencia entity.
    *
    * @param Sentencia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Sentencia $entity)
    {
        $form = $this->createForm(new SentenciaType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_reporte_sentencia_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Sentencia entity.
     *
     * @Route("/new", name="gopro_vipac_reporte_sentencia_new")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Sentencia();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Sentencia entity.
     *
     * @Route("/{id}", name="gopro_vipac_reporte_sentencia_show")
     * @Method({"GET","POST"})
     * @Secure(roles="ROLE_STAFF")
     * @Template()
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('GoproVipacReporteBundle:Sentencia')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la sentencia SQL.');
        }
        $deleteForm = $this->createDeleteForm($id);
        $campos=array();
        $camposMostrar=array();
        $camposDropdown=array();
        $tipos=array();
        $operadores=array();
        $camposSQL=$this->getClauses($entity->getContenido())['campos'];
        if(!empty($entity->getCampos())){
            foreach ($entity->getCampos() as $campoEntity):
                $camposPorNombre[$campoEntity->getNombre()]=$campoEntity->getNombremostrar();
                if(!empty($campoEntity->getTipo())&&!empty($campoEntity->getTipo()->getOperadores())){
                        $campos[$campoEntity->getId()]=$campoEntity->getNombre();
                        $camposMostrar[$campoEntity->getId()]=$campoEntity->getNombremostrar();
                        $tipos[$campoEntity->getId()][$campoEntity->getTipo()->getId()]=$campoEntity->getTipo()->getNombre();
                        foreach ($campoEntity->getTipo()->getOperadores() as $operadorEntity):
                            $operadores[$campoEntity->getId()][$operadorEntity->getId()]=$operadorEntity->getNombre();
                        endforeach;
                };
            endforeach;
        }

        $encabezados=array();
        foreach($camposSQL as $campoSQL):
            if(empty($camposPorNombre[$campoSQL])){
                throw $this->createNotFoundException('No existe el encabezado: '.$campoSQL.'.');
            }
            $key=array_search($campoSQL,$campos);
            if(!empty($key)){
                $camposDropdown[]=array('key'=>$key,'valor'=>$camposMostrar[$key]);
            }
            $encabezados[]=$camposPorNombre[$campoSQL];
        endforeach;
        $limite=100;
        $destino=null;
        if(!empty($request->request->all()['parametrosForm']['limite'])){
            $limite=$request->request->all()['parametrosForm']['limite'];
        }

        if(!empty($request->request->all()['parametrosForm']['destino'])){
            $destino=$request->request->all()['parametrosForm']['destino'];
        }

        $parametrosForm = $this->parametrosForm($id,json_encode($camposDropdown),json_encode($tipos),json_encode($operadores),json_encode(['ASC'=>'Ascendente','DESC'=>'Descendente']),$destino,$limite);

        if (
            $request->getMethod() == 'POST'
            &&!empty($campos)
        ){
            $operadoresLista = [
                1=>' = ',
                2=>' != ',
                3=>' IN ',
                4=>' LIKE ',
                5=>' LIKE ',
                6=>' LIKE ',
                7=>' >= ',
                8=>' <= '

            ];

            $ordenesString = '';
            $ordenesArray=array();
            if(!empty($request->request->all()['parametrosForm']['orden'])){
                foreach($request->request->all()['parametrosForm']['orden'] as $nroOrden => $orden):
                    if(
                        empty($campos[$orden['campo']])
                        ||empty($orden['orden'])
                    ){
                        $this->setMensajes('Se ejecuta el proceso sin ordenamiento.');
                        break;
                    }
                    $ordenAplicado[]=['campo'=>$orden['campo'],'orden'=>$orden['orden']];
                    $parametrosForm->add('ordenaplicado', 'hidden', array('data' => json_encode($ordenAplicado)));
                    $ordenesArrayPart=array();
                    $ordenesArrayPart[0]=$campos[$orden['campo']];
                    $ordenesArrayPart[1]=$orden['orden'];
                    $ordenesArray[$nroOrden]=implode(' ',$ordenesArrayPart);
                endforeach;
            }

            if(!empty($ordenesArray)){
                $ordenesString = ' ORDER BY '.implode(', ',$ordenesArray);
            }
            
            
            $filtrosString = '';
            $filtroArray=array();
            if(!empty($request->request->all()['parametrosForm']['filtro'])){
                foreach($request->request->all()['parametrosForm']['filtro'] as $nroFiltro => $filtro):
                    if(
                        empty($campos[$filtro['campo']])
                        ||empty($operadores[$filtro['campo']][$filtro['operador']])
                        ||empty($filtro['valor'])
                    ){
                        $this->setMensajes('Se ejecuta el proceso sin condiciones.');
                        break;
                    }
                    $filtroAplicado[]=['campo'=>$filtro['campo'],'operador'=>$filtro['operador'],'valor'=>$filtro['valor']];
                    $parametrosForm->add('filtroaplicado', 'hidden', array('data' => json_encode($filtroAplicado)));

                    if(empty($operadores[$filtro['campo']][$filtro['operador']])){
                        $this->setMensajes('No existe el operador');
                        return array(
                            'entity' => $entity,
                            'parametros_form'  => $parametrosForm->createView(),
                            'delete_form' => $deleteForm->createView(),
                            'mensajes' => $this->getMensajes()
                        );
                    }
                    $filtroArrayPart=array();
                    if(isset($tipos[$filtro['campo']][1])){
                        $filtroArrayPart[0]='UPPER(';
                        $filtroArrayPart[1]=$campos[$filtro['campo']];
                        $filtroArrayPart[2]=')';
                        $filtroArrayPart[3]=$operadoresLista[$filtro['operador']];
                        if($filtro['operador']==3){
                            $valores=explode('|',$filtro['valor']);
                            $valoresIn=array();
                            foreach ($valores as $valor):
                                $valoresIn[]='UPPER('.$this->setValoresBind($campos[$filtro['campo']],$valor).')';
                            endforeach;
                            $filtroArrayPart[4]='(';
                            $filtroArrayPart[5]=implode(',',$valoresIn);
                            $filtroArrayPart[6]=')';
                        }else{
                            $filtroArrayPart[4]='UPPER(';
                            if($filtro['operador']==4){
                                $filtroArrayPart[5]=$this->setValoresBind($campos[$filtro['campo']],'%'.$filtro['valor'].'%');
                            }elseif($filtro['operador']==5){
                                $filtroArrayPart[5]=$this->setValoresBind($campos[$filtro['campo']],$filtro['valor'].'%');
                            }elseif($filtro['operador']==6){
                                $filtroArrayPart[5]=$this->setValoresBind($campos[$filtro['campo']],'%'.$filtro['valor']);
                            }else{
                                $filtroArrayPart[5]=$this->setValoresBind($campos[$filtro['campo']],$filtro['valor']);
                            }

                            $filtroArrayPart[6]=')';
                        }
                    }elseif(isset($tipos[$filtro['campo']][2])){
                        $filtroArrayPart[0]='NVL(';
                        $filtroArrayPart[1]=$campos[$filtro['campo']];
                        $filtroArrayPart[2]=',0)';
                        $filtroArrayPart[3]=$operadoresLista[$filtro['operador']];
                        if($filtro['operador']==3){
                            $valores=explode('|',$filtro['valor']);
                            $valoresIn=array();
                            foreach ($valores as $valor):
                                $valoresIn[]=$this->setValoresBind($campos[$filtro['campo']],$valor);
                            endforeach;
                            $filtroArrayPart[4]='(';
                            $filtroArrayPart[5]=implode(',',$valoresIn);
                            $filtroArrayPart[6]=')';
                        }elseif($filtro['operador']==4||$filtro['operador']==5||$filtro['operador']==6){
                            $this->setMensajes('El operador no es valido para numeros.');
                            return array(
                                'entity' => $entity,
                                'parametros_form'  => $parametrosForm->createView(),
                                'delete_form' => $deleteForm->createView(),
                                'mensajes' => $this->getMensajes()
                            );
                        }else{
                            $filtroArrayPart[4]='';
                            $filtroArrayPart[5]=$this->setValoresBind($campos[$filtro['campo']],$filtro['valor']);
                            $filtroArrayPart[6]='';
                        }
                    }elseif(isset($tipos[$filtro['campo']][3])){
                        $filtroArrayPart[0]='TRUNC(';
                        $filtroArrayPart[1]=$campos[$filtro['campo']];
                        $filtroArrayPart[2]=')';
                        $filtroArrayPart[3]=$operadoresLista[$filtro['operador']];
                        if($filtro['operador']==3){
                            $this->setMensajes('El operador no es valido para fechas.');
                            return array(
                                'entity' => $entity,
                                'parametros_form'  => $parametrosForm->createView(),
                                'delete_form' => $deleteForm->createView(),
                                'mensajes' => $this->getMensajes()
                            );
                        }elseif($filtro['operador']==4||$filtro['operador']==5||$filtro['operador']==6){
                            $this->setMensajes('El operador no es valido para fechas.');
                            return array(
                                'entity' => $entity,
                                'parametros_form'  => $parametrosForm->createView(),
                                'delete_form' => $deleteForm->createView(),
                                'mensajes' => $this->getMensajes()
                            );
                        }else{
                            $filtroArrayPart[4]='TO_DATE(';
                            $filtroArrayPart[5]=$this->setValoresBind($campos[$filtro['campo']],$filtro['valor']);
                            $filtroArrayPart[6]=",'yyyy-mm-dd')";
                        }
                    }
                    $filtroArray[$nroFiltro]=implode('',$filtroArrayPart);
                endforeach;
            }

            if(!empty($filtroArray)){
                if(empty($this->getClauses($entity->getContenido())['campos'])){
                    $palabraWhere=' WHERE ';
                }else{
                    $palabraWhere=' AND ';
                }
                $filtrosString = $palabraWhere.implode(' AND ',$filtroArray);
            }

            $limiteSQL=$this->setValoresBind('limite',$limite);
            $selectQuery = 'select * from ( select '.implode(', ',$this->getClauses($entity->getContenido())['campos']).' FROM '.$this->getClauses($entity->getContenido())['tabla'].$this->getClauses($entity->getContenido())['condiciones'].$filtrosString.$ordenesString.' ) WHERE ROWNUM <= '.$limiteSQL;
            $statement = $this->container->get('doctrine.dbal.vipac_connection')->prepare($selectQuery);

            foreach($this->getValoresBind() as $campoBind => $valorBind ):
                $statement->bindValue($campoBind,$valorBind);
            endforeach;
            if(!$statement->execute()){
                $this->setMensajes('Hubo un error en la ejecucion de la consulta.');
                return array(
                    'entity' => $entity,
                    'parametros_form'  => $parametrosForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'mensajes' => $this->getMensajes()
                );
            }
            $resultados=$this->container->get('gopro_main_variableproceso')->utf($statement->fetchAll());

            if($destino=='archivo'){
                $archivoGenerado=$this->get('gopro_main_archivoexcel');
                return $archivoGenerado
                    ->setArchivo()
                    ->setParametrosWriter('Reporte_'.(new \DateTime())->format('Y-m-d H:i:s'),$resultados,$encabezados)
                    ->getArchivo();
            }else{
                return array(
                    'entity' => $entity,
                    'parametros_form'  => $parametrosForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'mensajes' => $this->getMensajes(),
                    'encabezados'=> $encabezados,
                    'resultados'=>$resultados
                );

            }
        }
        return array(
            'entity' => $entity,
            'parametros_form'  => $parametrosForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'mensajes' => $this->getMensajes()
        );
    }

    /**
     * @param mixed $id The entity id
     * @param array $campos
     * @param array $tipos
     * @param array $operadores
     * @param array $ordenes
     * @param string $destino
     * @param int $limite
     * @return \Symfony\Component\Form\Form The form
     */
    private function parametrosForm($id,$campos,$tipos,$operadores,$ordenes,$destino=null,$limite=null)
    {
        $destinoChOp = array('pantalla'=>'Pantalla','archivo'=>'Archivo');
        $destinoCh=array('choices'=>$destinoChOp,'multiple'=>false,'expanded'=>true,'data'=>$destino);
        $limiteChOp = array(500=>'500',1000=>'1000',5000=>'5000');
        $limiteCh=array('choices'=>$limiteChOp,'multiple'=>false,'expanded'=>false,'label'=>'Límite','data'=>$limite);
        return $this->get('form.factory')->createNamedBuilder(
            'parametrosForm',
            'form',
            null,
           [
               'action'=>$this->generateUrl('gopro_vipac_reporte_sentencia_show', ['id' => $id]),
               'method'=>'POST',
               'attr'=>['id'=>'parametrosForm']
           ])
            //$this->createFormBuilder(null,['attr'=>['name'=>'parametrosForm','id'=>'parametrosForm']])
            ->add('campos', 'hidden', array('data' => $campos))
            ->add('tipos', 'hidden', array('data' => $tipos))
            ->add('operadores', 'hidden', array('data' => $operadores))
            ->add('ordenes', 'hidden', array('data' => $ordenes))
            ->add('destino', 'choice', $destinoCh)
            ->add('limite', 'choice', $limiteCh)
            ->add('submit', 'submit', array('label' => 'Generar'))
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Sentencia entity.
     *
     * @Route("/{id}/edit", name="gopro_vipac_reporte_sentencia_edit")
     * @Method("GET")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacReporteBundle:Sentencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la sentencia SQL.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Sentencia entity.
    *
    * @param Sentencia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Sentencia $entity)
    {
        $form = $this->createForm(new SentenciaType(), $entity, array(
            'action' => $this->generateUrl('gopro_vipac_reporte_sentencia_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Sentencia entity.
     *
     * @Route("/{id}", name="gopro_vipac_reporte_sentencia_update")
     * @Method("PUT")
     * @Secure(roles="ROLE_ADMIN")
     * @Template("GoproVipacReporteBundle:Sentencia:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('GoproVipacReporteBundle:Sentencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se puede encontrar la sentencia SQL.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setContenido($this->container->get('gopro_main_variableproceso')->sanitizeQuery($entity->getContenido()));
            $campos = $this->getClauses($editForm->getData()->getContenido())['campos'];
            $camposExistentes=$em->getRepository('GoproVipacReporteBundle:Campo')->findBy(['sentencia'=>$entity->getId()]);

            foreach($camposExistentes as $campoExistente):
                if(!in_array($campoExistente->getNombre(),$campos)){
                    $em->remove($campoExistente);
                }else{
                    $key = array_search($campoExistente->getNombre(),$campos);
                    if($key!==false){
                        unset($campos[$key]);
                    }
                }
            endforeach;

            foreach($campos as $campo){

                $campoEntity=new Campo();
                $campoEntity->setNombre($campo);
                $campoEntity->setNombremostrar($campo);
                $campoEntity->setSentencia($entity);
                $entity->getCampos()->add($campoEntity);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('gopro_vipac_reporte_sentencia_edit', array('id' => $id)));
        }
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Sentencia entity.
     *
     * @Route("/{id}", name="gopro_vipac_reporte_sentencia_delete")
     * @Secure(roles="ROLE_ADMIN")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('GoproVipacReporteBundle:Sentencia')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se puede encontrar la sentencia SQL.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('sentencia'));
    }

    /**
     * Creates a form to delete a Sentencia entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->get('form.factory')->createNamedBuilder(
            'deleteForm',
            'form',
            null,
            [
                'action'=>$this->generateUrl('gopro_vipac_reporte_sentencia_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
