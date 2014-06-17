<?php

namespace Gopro\Vipac\ReporteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Gopro\Vipac\MainBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Gopro\Vipac\ReporteBundle\Entity\Sentencia;
use Gopro\Vipac\ReporteBundle\Entity\Campo;
use Gopro\Vipac\ReporteBundle\Form\SentenciaType;

/**
 * Sentencia controller.
 *
 * @Route("/sentencia")
 */
class SentenciaController extends BaseController
{

    private $valoresBind;
    /**
     * @param string $sql
     *
     * @return array
     */
    private function getCampos($sql){
        $campos=array();
        if(strtoupper(substr($sql,0,6))!='SELECT'){
            return $campos;
        }
        if (preg_match('/SELECT (.*?) FROM /i', $sql, $select)) {
            $campos = explode(",",$select[1]);
            $campos = array_map('trim', $campos);
        }
        return array_unique($campos);
    }

    /**
     * Lists all Sentencia entities.
     *
     * @Route("/", name="sentencia")
     * @Method("GET")
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
     * @Route("/", name="sentencia_create")
     * @Method("POST")
     * @Template("GoproVipacReporteBundle:Sentencia:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Sentencia();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setContenido($this->container->get('gopro_dbproceso_comun_variable')->sanitizeQuery($entity->getContenido()));
            $em = $this->getDoctrine()->getManager();
            $campos = $this->getCampos($form->getData()->getContenido());
            foreach($campos as $campo){
                $campoEntity=new Campo();
                $campoEntity->setNombre($campo);
                $campoEntity->setNombremostrar($campo);
                $campoEntity->setSentencia($entity);
                $entity->getCampos()->add($campoEntity);
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('sentencia_show', array('id' => $entity->getId())));
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
            'action' => $this->generateUrl('sentencia_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Sentencia entity.
     *
     * @Route("/new", name="sentencia_new")
     * @Method("GET")
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
     * @param string $campo
     * @param string $valor
     * @return string
     */
    private function setValoresBind($campo,$valor){
        $this->valoresBind[':v'.substr(sha1($this->container->get('gopro_dbproceso_comun_variable')->sanitizeQuery($campo.$valor)),0,28)]=$valor;
        return ':v'.substr(sha1($this->container->get('gopro_dbproceso_comun_variable')->sanitizeQuery($campo.$valor)),0,28);
    }

    /**
     * Finds and displays a Sentencia entity.
     *
     * @Route("/{id}", name="sentencia_show")
     * @Method({"GET","POST"})
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
        $tipos=array();
        if(!empty($entity->getCampos())){
            foreach ($entity->getCampos() as $campoEntity):
                if(!empty($campoEntity->getTipo())&&!empty($campoEntity->getTipo()->getOperadores())){
                        $campos[$campoEntity->getId()]=$campoEntity->getNombre();
                        $tipos[$campoEntity->getId()][$campoEntity->getTipo()->getId()]=$campoEntity->getTipo()->getNombre();
                        foreach ($campoEntity->getTipo()->getOperadores() as $operadorEntity):
                            $operadores[$campoEntity->getId()][$operadorEntity->getId()]=$operadorEntity->getNombre();
                        endforeach;
                };
            endforeach;
        }

        $parametrosForm = $this->parametrosForm($id,json_encode($campos),json_encode($tipos),json_encode($operadores));

        if (
            $request->getMethod() == 'POST'
            &&!empty($request->request->all()['parametrosForm']['filtro'])
            &&!empty($campos)
        ){
            $operadoresLista = [1=>' = ', 2=>' != ' , 3=>' IN '];
            $filtroSQL=array();
            foreach($request->request->all()['parametrosForm']['filtro'] as $nroFiltro => $filtro):

                if(
                    empty($campos[$filtro['campo']])
                    ||empty($operadores[$filtro['campo']][$filtro['operador']])
                    ||empty($filtro['valor'])
                ){
                    $this->setMensajes('No pueden quedar campos vacios');
                    return array(
                        'entity' => $entity,
                        'parametros_form'  => $parametrosForm->createView(),
                        'delete_form' => $deleteForm->createView(),
                        'mensajes' => $this->getMensajes()
                    );
                }

                if(empty($operadores[$filtro['campo']][$filtro['operador']])){
                    $this->setMensajes('No existe el operador');
                    return array(
                        'entity' => $entity,
                        'parametros_form'  => $parametrosForm->createView(),
                        'delete_form' => $deleteForm->createView(),
                        'mensajes' => $this->getMensajes()
                    );
                }

                $filtroSQLPart=array();
                if(isset($tipos[$filtro['campo']][1])){
                    $filtroSQLPart[0]='UPPER(';
                    $filtroSQLPart[1]=$campos[$filtro['campo']];
                    $filtroSQLPart[2]=')';
                    $filtroSQLPart[3]=$operadoresLista[$filtro['operador']];
                    if($filtro['operador']==3){
                        $valores=explode('|',$filtro['valor']);
                        $valoresIn=array();
                        foreach ($valores as $valor):
                            $valoresIn[]='UPPER('.$this->setValoresBind($campos[$filtro['campo']],$valor).')';
                        endforeach;
                        $filtroSQLPart[4]='(';
                        $filtroSQLPart[5]=implode(',',$valoresIn);
                        $filtroSQLPart[6]=')';
                    }else{
                        $filtroSQLPart[4]='UPPER(';
                        $filtroSQLPart[5]=$this->setValoresBind($campos[$filtro['campo']],$filtro['valor']);
                        $filtroSQLPart[6]=')';
                    }
                }elseif(isset($tipos[$filtro['campo']][2])){
                    $filtroSQLPart[0]='NVL(';
                    $filtroSQLPart[1]=$campos[$filtro['campo']];
                    $filtroSQLPart[2]=',0)';
                    $filtroSQLPart[3]=$operadoresLista[$filtro['operador']];
                    if($filtro['operador']==3){
                        $valores=explode('|',$filtro['valor']);
                        $valoresIn=array();
                        foreach ($valores as $valor):
                            $valoresIn[]=$this->setValoresBind($campos[$filtro['campo']],$valor);
                        endforeach;
                        $filtroSQLPart[4]='(';
                        $filtroSQLPart[5]=implode(',',$valoresIn);
                        $filtroSQLPart[6]=')';
                    }else{
                        $filtroSQLPart[4]='';
                        $filtroSQLPart[5]=$this->setValoresBind($campos[$filtro['campo']],$filtro['valor']);
                        $filtroSQLPart[6]='';
                    }
                }elseif(isset($tipos[$filtro['campo']][3])){
                    $filtroSQLPart[0]='TRUNC(';
                    $filtroSQLPart[1]=$campos[$filtro['campo']];
                    $filtroSQLPart[2]=')';
                    $filtroSQLPart[3]=$operadoresLista[$filtro['operador']];
                    if($filtro['operador']==3){
                        $this->setMensajes('El operador no es valido para fechas');
                        return array(
                            'entity' => $entity,
                            'parametros_form'  => $parametrosForm->createView(),
                            'delete_form' => $deleteForm->createView(),
                            'mensajes' => $this->getMensajes()
                        );
                    }else{
                        $filtroSQLPart[4]='TO_DATE(';
                        $filtroSQLPart[5]=$this->setValoresBind($campos[$filtro['campo']],$filtro['valor']);
                        $filtroSQLPart[6]=",'yyyy-mm-dd')";
                    }
                }
                $filtroSQL[$nroFiltro]=implode('',$filtroSQLPart);
            endforeach;
            //echo (implode(' AND ',$filtroSQL));
        }
        return array(
            'entity' => $entity,
            'parametros_form'  => $parametrosForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @param mixed $id The entity id
     * @param array $campos
     * @param array $tipos
     * @param array $operadores
     * @return \Symfony\Component\Form\Form The form
     */
    private function parametrosForm($id,$campos,$tipos,$operadores)
    {
        return $this->get('form.factory')->createNamedBuilder(
            'parametrosForm',
            'form',
            null,
           [
               'action'=>$this->generateUrl('sentencia_show', ['id' => $id]),
               'method'=>'POST',
               'attr'=>['id'=>'parametrosForm']
           ])
            //$this->createFormBuilder(null,['attr'=>['name'=>'parametrosForm','id'=>'parametrosForm']])
            ->add('campos', 'hidden', array('data' => $campos))
            ->add('tipos', 'hidden', array('data' => $tipos))
            ->add('operadores', 'hidden', array('data' => $operadores))
            ->add('submit', 'submit', array('label' => 'Generar'))
            ->getForm();
    }

    /**
     * Displays a form to edit an existing Sentencia entity.
     *
     * @Route("/{id}/edit", name="sentencia_edit")
     * @Method("GET")
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
            'action' => $this->generateUrl('sentencia_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    /**
     * Edits an existing Sentencia entity.
     *
     * @Route("/{id}", name="sentencia_update")
     * @Method("PUT")
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
            $entity->setContenido($this->container->get('gopro_dbproceso_comun_variable')->sanitizeQuery($entity->getContenido()));
            $campos = $this->getCampos($editForm->getData()->getContenido());
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

            return $this->redirect($this->generateUrl('sentencia_edit', array('id' => $id)));
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
     * @Route("/{id}", name="sentencia_delete")
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
                'action'=>$this->generateUrl('sentencia_delete', ['id' => $id]),
                'method'=>'DELETE',
                'attr'=>['id'=>'deleteForm']
            ])
            ->add('submit', 'submit', array('label' => 'Borrar'))
            ->getForm();
    }
}
