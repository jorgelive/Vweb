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
     * @Secure(roles="ROLE_USER")
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
     * @Secure(roles="ROLE_USER")
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
        $camposPorNombre=array();
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
        $camposArray=array();

        foreach($camposSQL as $campoSQL):
            if(empty($camposPorNombre[$campoSQL])){
                throw $this->createNotFoundException('No existe el encabezado: '.$campoSQL.'.');
            }
            $key=array_search($campoSQL,$campos);
            if(!empty($key)){
                $camposDropdown[]=array('key'=>$key,'valor'=>$camposMostrar[$key]);
                if(isset($tipos[$key][4])){
                    $camposArray[]="to_char(".$campoSQL.",'hh24:mi')";
                }elseif(isset($tipos[$key][3])){
                    $camposArray[]="to_char(".$campoSQL.",'yyyy-mm-dd')";
                }else{
                    $camposArray[]=$campoSQL;
                }
            }else{
                $camposArray[]=$campoSQL;
            }
        endforeach;

        $encabezadoArray=$camposSQL;

        $limite=100;
        $destino=null;
        if(!empty($request->request->all()['parametrosForm']['limite'])){
            $limite=$request->request->all()['parametrosForm']['limite'];
        }

        if(!empty($request->request->all()['parametrosForm']['destino'])){
            $destino=$request->request->all()['parametrosForm']['destino'];
        }

        $qb=$em->createQueryBuilder();

        $condiciones[]=$qb->expr()->orx('p.user = :user');
        $condiciones[]=$qb->expr()->orx('p.publico = :publico');

        $parametrosGuardados = $qb->addSelect('p.id, p.nombre, p.contenido')
            ->from('GoproVipacReporteBundle:Parametro', 'p')
            ->orderBy('p.id', 'ASC')
            ->where(join(' OR ', $condiciones))
            ->andWhere($qb->expr()->eq('p.sentencia', ':sentencia'))
            ->setParameter('user', $this->getUser())
            ->setParameter('publico', 1)
            ->setParameter('sentencia', $entity)
            ->getQuery()
            ->getArrayResult();

        foreach($parametrosGuardados as $key => $parametroGuardado):
            $parametrosGuardados[$key]['borrarRoute']=$this->generateUrl('gopro_vipac_reporte_parametro_delete',['id'=>$parametroGuardado['id']]);
        endforeach;

        $parametrosGuardadosOpciones=['sentenciaid'=>$id,'agregarurl'=>$this->generateUrl('gopro_vipac_reporte_parametro_create')];
        $parametrosForm = $this->parametrosForm($id,json_encode($camposDropdown),json_encode($tipos),json_encode($operadores),json_encode(['ASC'=>'Ascendente','DESC'=>'Descendente']),json_encode(['COL'=>'Columna','CON'=>'Conteo','AGR'=>'Agregamiento']),json_encode($parametrosGuardados),json_encode($parametrosGuardadosOpciones),$destino,$limite);

        if (
            $request->getMethod() == 'POST'
            &&!empty($campos)
        ){

            $gruposGrupoString = '';
            $gruposSelectArray=array();
            $gruposGrupoArray=array();
            $gruposEncabezado=array();

            $existeAGR=false;
            $existeCOL=false;

            if(!empty($request->request->all()['parametrosForm']['grupo'])){
                $i=1;
                foreach($request->request->all()['parametrosForm']['grupo'] as $grupo):
                    if(
                        empty($campos[$grupo['campo']])
                        ||empty($grupo['grupo'])
                    ){
                        $this->setMensajes('El agrupamiento de la fila '.$i.' no es válido.');
                    }else{
                        $grupoAplicado[]=['campo'=>$grupo['campo'],'grupo'=>$grupo['grupo']];
                        if($grupo['grupo']=='AGR'||$grupo['grupo']=='CON'){
                            if($grupo['grupo']=='CON'){
                                $gruposSelectArray[]='count('.$campos[$grupo['campo']].')';
                                $ordenGrupo[$grupo['campo']]='count('.$campos[$grupo['campo']].')';
                            }elseif(isset($tipos[$grupo['campo']][4])){
                                $gruposSelectArray[]="rtrim(xmlagg(xmlelement(s,to_char(".$campos[$grupo['campo']].",'hh24:mi'),', ').extract('//text()') order by ".$campos[$grupo['campo']].").getClobVal(),', ') C_".$campos[$grupo['campo']];
                                $ordenGrupo[$grupo['campo']]="rtrim(xmlagg(xmlelement(s,to_char(".$campos[$grupo['campo']].",'hh24:mi'),', ').extract('//text()') order by ".$campos[$grupo['campo']].").getClobVal(),', ')";
                            }elseif(isset($tipos[$grupo['campo']][3])){
                                $gruposSelectArray[]="rtrim(xmlagg(xmlelement(s,to_char(".$campos[$grupo['campo']].",'yyyy-mm-dd'),', ').extract('//text()') order by ".$campos[$grupo['campo']].").getClobVal(),', ') C_".$campos[$grupo['campo']];
                                $ordenGrupo[$grupo['campo']]="rtrim(xmlagg(xmlelement(s,to_char(".$campos[$grupo['campo']].",'yyyy-mm-dd'),', ').extract('//text()') order by ".$campos[$grupo['campo']].").getClobVal(),', ')";
                            }elseif(isset($tipos[$grupo['campo']][2])){
                                $gruposSelectArray[]='sum('.$campos[$grupo['campo']].')';
                                $ordenGrupo[$grupo['campo']]='sum('.$campos[$grupo['campo']].')';
                            }else{
                                $gruposSelectArray[]="rtrim(xmlagg(xmlelement(s,".$campos[$grupo['campo']].",', ').extract('//text()') order by ".$campos[$grupo['campo']].").getClobVal(),', ') C_".$campos[$grupo['campo']];
                                $ordenGrupo[$grupo['campo']]="rtrim(xmlagg(xmlelement(s,".$campos[$grupo['campo']].",', ').extract('//text()') order by ".$campos[$grupo['campo']].").getClobVal(),', ')";
                            }
                            $existeAGR=true;
                        }else{
                            if(isset($tipos[$grupo['campo']][4])){
                                $gruposSelectArray[]="to_char(".$campos[$grupo['campo']].",'hh24:mi')";
                            }elseif(isset($tipos[$grupo['campo']][3])){
                                $gruposSelectArray[]="to_char(".$campos[$grupo['campo']].",'yyyy-mm-dd')";
                            }else{
                                $gruposSelectArray[]=$campos[$grupo['campo']];
                            }
                            $ordenGrupo[$grupo['campo']]= $campos[$grupo['campo']];
                            $gruposGrupoArray[]=$campos[$grupo['campo']];
                            $existeCOL=true;
                        }
                        $gruposEncabezado[]=$campos[$grupo['campo']];
                    }
                    $i++;
                endforeach;
            }

            if($existeAGR===true&&$existeCOL===true){
                if(!empty($gruposGrupoArray)){
                    $gruposGrupoString = ' GROUP BY '.implode(', ',$gruposGrupoArray);
                }

                if(!empty($gruposSelectArray)){
                    $camposArray = $gruposSelectArray;
                }
                $encabezadoArray=$gruposEncabezado;
            }elseif($existeAGR===true||$existeCOL===true){
                if(!empty($gruposSelectArray)){
                    $camposArray = $gruposSelectArray;
                    $encabezadoArray=$gruposEncabezado;
                }
            }

            $ordenesString = '';
            $ordenesArray=array();

            if(!empty($request->request->all()['parametrosForm']['orden'])){
                $i=1;
                foreach($request->request->all()['parametrosForm']['orden'] as $nroOrden => $orden):
                    if(
                        empty($campos[$orden['campo']])
                        ||empty($orden['orden'])
                    ){
                        $this->setMensajes('El orden de la fila '. $i .' no es válido.');
                    }else{
                        $ordenAplicado[]=['campo'=>$orden['campo'],'orden'=>$orden['orden']];
                        $ordenesArrayPart=array();
                        if(!empty($ordenGrupo[$orden['campo']])){
                            $ordenesArrayPart[0]=$ordenGrupo[$orden['campo']];
                        }elseif(empty($gruposGrupoString)){
                            $ordenesArrayPart[0]=$campos[$orden['campo']];
                        }else{
                            $this->setMensajes('No se ordenará por '. $camposMostrar[$orden['campo']] .', en modo agrupamiento solo se puede ordenar por las filas mostradas.');
                        }
                        if(isset($ordenesArrayPart[0])){
                            $ordenesArrayPart[1]=$orden['orden'];
                            $ordenesArray[$nroOrden]=implode(' ',$ordenesArrayPart);
                        }
                    }
                    $i++;
                endforeach;
            }

            if(!empty($ordenesArray)){
                $ordenesString = ' ORDER BY '.implode(', ',$ordenesArray);
            }

            $filtrosString = '';
            $filtroArray=array();

            if(!empty($request->request->all()['parametrosForm']['filtro'])){
                $operadoresLista = [
                    1=>' = ',
                    2=>' != ',
                    3=>' IN ',
                    4=>' LIKE ',
                    5=>' LIKE ',
                    6=>' LIKE ',
                    7=>' >= ',
                    8=>' <= ',
                    9=>' != '
                ];
                $i=1;

                foreach($request->request->all()['parametrosForm']['filtro'] as $nroFiltro => $filtro):
                    if(
                        (
                            empty($campos[$filtro['campo']])
                            || empty($operadores[$filtro['campo']][$filtro['operador']])
                            || (empty($filtro['valor']) && !is_numeric($filtro['valor']))
                        ) && $filtro['operador'] != 9
                    ){
                        $this->setMensajes('El filtro de la fila '.$i.' no es válido.');
                    }else{
                        $filtroAplicado[]=['campo'=>$filtro['campo'],'operador'=>$filtro['operador'],'valor'=>$filtro['valor']];
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
                            if($filtro['operador']==9){
                                $filtroArrayPart[0]='NVL(';
                                $filtroArrayPart[1]=$campos[$filtro['campo']];
                                $filtroArrayPart[2]=",' ')";
                            }else{
                                $filtroArrayPart[0]='UPPER(';
                                $filtroArrayPart[1]=$campos[$filtro['campo']];
                                $filtroArrayPart[2]=')';
                            }
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
                            }elseif($filtro['operador']==9){
                                $filtroArrayPart[4]='';
                                $filtroArrayPart[5]="' '";
                                $filtroArrayPart[6]='';
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
                            if($filtro['operador']==9){
                                $filtroArrayPart[0]='';
                                $filtroArrayPart[1]=$campos[$filtro['campo']];
                                $filtroArrayPart[2]='';
                                $filtroArrayPart[3]='';
                            }else{
                                $filtroArrayPart[0]='NVL(';
                                $filtroArrayPart[1]=$campos[$filtro['campo']];
                                $filtroArrayPart[2]=',0)';
                                $filtroArrayPart[3]=$operadoresLista[$filtro['operador']];
                            }
                            if($filtro['operador']==3){
                                $valores=explode('|',$filtro['valor']);
                                $valoresIn=array();
                                foreach ($valores as $valor):
                                    $valoresIn[]=$this->setValoresBind($campos[$filtro['campo']],$valor);
                                endforeach;
                                $filtroArrayPart[4]='(';
                                $filtroArrayPart[5]=implode(',',$valoresIn);
                                $filtroArrayPart[6]=')';
                            }elseif($filtro['operador']==9){
                                $filtroArrayPart[4]=' ';
                                $filtroArrayPart[5]='IS NOT NULL';
                                $filtroArrayPart[6]='';
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
                            if($filtro['operador']==9){
                                $filtroArrayPart[0]='';
                                $filtroArrayPart[1]=$campos[$filtro['campo']];
                                $filtroArrayPart[2]='';
                                $filtroArrayPart[3]='';
                            }else{
                                $filtroArrayPart[0]='TRUNC(';
                                $filtroArrayPart[1]=$campos[$filtro['campo']];
                                $filtroArrayPart[2]=')';
                                $filtroArrayPart[3]=$operadoresLista[$filtro['operador']];
                            }
                            if($filtro['operador']==3||$filtro['operador']==4||$filtro['operador']==5||$filtro['operador']==6){
                                $this->setMensajes('El operador no es valido para fechas.');
                                return array(
                                    'entity' => $entity,
                                    'parametros_form'  => $parametrosForm->createView(),
                                    'delete_form' => $deleteForm->createView(),
                                    'mensajes' => $this->getMensajes()
                                );
                            }elseif($filtro['operador']==9){
                                $filtroArrayPart[4]=' ';
                                $filtroArrayPart[5]='IS NOT NULL';
                                $filtroArrayPart[6]='';
                            }else{
                                $filtroArrayPart[4]='TO_DATE(';
                                $filtroArrayPart[5]=$this->setValoresBind($campos[$filtro['campo']],$filtro['valor']);
                                $filtroArrayPart[6]=",'yyyy-mm-dd')";
                            }
                        }elseif(isset($tipos[$filtro['campo']][4])){
                            if($filtro['operador']==9){
                                $filtroArrayPart[0]='';
                                $filtroArrayPart[1]=$campos[$filtro['campo']];
                                $filtroArrayPart[2]='';
                                $filtroArrayPart[3]='';
                            }else{
                                $filtroArrayPart[0]='to_char(';
                                $filtroArrayPart[1]=$campos[$filtro['campo']];
                                $filtroArrayPart[2]=",'hh24')";
                                $filtroArrayPart[3]=$operadoresLista[$filtro['operador']];
                            }
                            if($filtro['operador']==2||$filtro['operador']==3||$filtro['operador']==4||$filtro['operador']==5||$filtro['operador']==6){
                                $this->setMensajes('El operador no es valido para horas.');
                                return array(
                                    'entity' => $entity,
                                    'parametros_form'  => $parametrosForm->createView(),
                                    'delete_form' => $deleteForm->createView(),
                                    'mensajes' => $this->getMensajes()
                                );
                            }elseif($filtro['operador']==9){
                                $filtroArrayPart[4]=' ';
                                $filtroArrayPart[5]='IS NOT NULL';
                                $filtroArrayPart[6]='';
                            }else{
                                if(strlen($filtro['valor'])==1){
                                    $filtro['valor']='0'.$filtro['valor'];
                                }
                                $filtroArrayPart[4]='';
                                $filtroArrayPart[5]=$this->setValoresBind($campos[$filtro['campo']],$filtro['valor']);
                                $filtroArrayPart[6]='';
                            }
                        }
                        $filtroArray[$nroFiltro]=implode('',$filtroArrayPart);
                    }
                    $i++;
                endforeach;
            }

            if(!empty($filtroArray)){
                if(empty($this->getClauses($entity->getContenido())['condiciones'])){
                    $palabraWhere=' WHERE ';
                }else{
                    $palabraWhere=' AND ';
                }
                $filtrosString = $palabraWhere.implode(' AND ',$filtroArray);
            }

            $limiteSQL=$this->setValoresBind('limite',$limite);

            $selectQuery = 'select * from ( select '.implode(', ',$camposArray).' FROM '.$this->getClauses($entity->getContenido())['tabla'].$this->getClauses($entity->getContenido())['condiciones'].$filtrosString.$gruposGrupoString.$ordenesString.' ) WHERE ROWNUM <= '.$limiteSQL;

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

            $encabezados=array();

            foreach($encabezadoArray as $campoEncabezado):
                $encabezados[]=$camposPorNombre[$campoEncabezado];
            endforeach;

            if($destino=='archivo'){
                $archivoGenerado=$this->get('gopro_main_archivoexcel');
                return $archivoGenerado
                    ->setArchivo()
                    ->setParametrosWriter('Reporte_'.(new \DateTime())->format('Y-m-d H:i:s'),$resultados,$encabezados)
                    ->getArchivo();
            }else{
                if(!empty($filtroAplicado)){
                    $parametrosAplicados['filtro']=$filtroAplicado;
                }
                if(!empty($ordenAplicado)){
                    $parametrosAplicados['orden']=$ordenAplicado;
                }
                if(!empty($grupoAplicado)){
                    $parametrosAplicados['grupo']=$grupoAplicado;
                }
                if(!empty($parametrosAplicados)){
                    $parametrosForm->add('parametrosaplicados', 'hidden', array('data' => json_encode($parametrosAplicados)));
                }

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
     * @param string $campos
     * @param string $tipos
     * @param string $operadores
     * @param string $ordenes
     * @param string $grupos
     * @param string $parametrosGuardados
     * @param string $parametrosGuardadosOpciones
     * @param string $destino
     * @param int $limite
     * @return \Symfony\Component\Form\Form The form
     */
    private function parametrosForm($id,$campos,$tipos,$operadores,$ordenes,$grupos,$parametrosGuardados,$parametrosGuardadosOpciones,$destino=null,$limite=null)
    {
        if(empty($destino)){
            $destino='pantalla';
        }
        $destinoChOp = array('pantalla'=>'Pantalla','archivo'=>'Archivo');
        $destinoCh=array('choices'=>$destinoChOp,'multiple'=>false,'expanded'=>true,'data'=>$destino);
        $limiteChOp = array(500=>'500', 1000=>'1000', 5000=>'5000', 10000=>'10000', 50000=>'50000');
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
            ->add('campos', 'hidden', array('data' => $campos))
            ->add('tipos', 'hidden', array('data' => $tipos))
            ->add('operadores', 'hidden', array('data' => $operadores))
            ->add('ordenes', 'hidden', array('data' => $ordenes))
            ->add('grupos', 'hidden', array('data' => $grupos))
            ->add('parametrosguardados', 'hidden', array('data' => $parametrosGuardados))
            ->add('parametrosguardadosopciones', 'hidden', array('data' => $parametrosGuardadosOpciones))
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
                $campoEntity->setNombremostrar(ucwords(strtolower($campo)));
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

        return $this->redirect($this->generateUrl('gopro_vipac_reporte_sentencia'));
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
