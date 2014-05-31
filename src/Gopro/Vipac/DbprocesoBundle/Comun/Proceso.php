<?php
namespace Gopro\Vipac\DbprocesoBundle\Comun;
use \Symfony\Component\DependencyInjection\ContainerAware;

class Proceso extends ContainerAware{

    private $conexion;
    private $tabla;
    private $schema;
    private $mensajes=array();
    private $existentesRaw;
    private $existentesIndizados;
    private $existentesIndizadosMulti;
    private $whereSelectValores;
    private $whereSelectPh;
    private $camposSelect;
    private $camposCustom;
    private $existentesCustomRaw;
    private $existentesCustomIndizados;
    private $existentesCustomIndizadosMulti;
    private $llaves;

    //valores temporales por fila
    private $whereUpdateValores;
    private $whereUpdatePh;
    private $valoresUpdateValores;
    private $valoresUpdatePh;
    private $valoresInsertValores;
    private $valoresInsertPh;
    private $camposInsert;

    public function setTabla($tabla){
        $this->tabla=$tabla;
        return $this;
    }

    public function getTabla(){
        return $this->tabla;
    }

    public function setSchema($schema){
        $this->schema=$schema;
        return $this;
    }

    public function getSchema(){
        return $this->schema;
    }

    public function setConexion($conexion){
        $this->conexion=$conexion;
        return $this;
    }

    public function getConexion(){
        return $this->conexion;
    }

    public function getWhereSelectValores(){
        return $this->whereSelectValores;
    }

    public function setWhereSelectValores($whereSelectValores){
        $this->whereSelectValores=$whereSelectValores;
        return $this;
    }

    public function getWhereSelectPh(){
        return $this->whereSelectPh;
    }

    public  function setWhereSelectPh($whereSelectPh){
        $this->whereSelectPh=$whereSelectPh;
        return $this;
    }

    public function getCamposSelect(){
        return $this->camposSelect;
    }

    public function setCamposSelect($camposSelect){
        $this->camposSelect=$camposSelect;
        return $this;
    }

    public function getWhereUpdateValores(){
        return $this->whereUpdateValores;
    }

    public function setWhereUpdateValores($whereUpdateValores){
        $this->whereUpdateValores=$whereUpdateValores;
        return $this;
    }

    public function getWhereUpdatePh(){
        return $this->whereUpdatePh;
    }

    public function setWhereUpdatePh($whereUpdatePh){
        $this->whereUpdatePh=$whereUpdatePh;
        return $this;
    }

    public function getValoresUpdateValores(){
        return $this->valoresUpdateValores;
    }

    public function setValoresUpdateValores($valoresUpdateValores){
        $this->valoresUpdateValores=$valoresUpdateValores;
        return $this;
    }

    public function getValoresUpdatePh(){
        return $this->valoresUpdatePh;
    }

    public function setValoresUpdatePh($valoresUpdatePh){
        $this->valoresUpdatePh=$valoresUpdatePh;
        return $this;
    }

    public function getValoresInsertValores(){
        return $this->valoresInsertValores;
    }

    public function setValoresInsertValores($valoresInsertValores){
        $this->valoresInsertValores=$valoresInsertValores;
        return $this;
    }

    public function getValoresInsertPh(){
        return $this->valoresInsertPh;
    }

    public function setValoresInsertPh($valoresInsertPh){
        $this->valoresInsertPh=$valoresInsertPh;
        return $this;
    }

    public function getCamposInsert(){
        return $this->camposInsert;
    }

    public function setCamposInsert($camposInsert){
        $this->camposInsert=$camposInsert;
        return $this;
    }

    public function getMensajes(){
        return $this->mensajes;
    }

    private function setMensajes($mensaje){
        $this->mensajes[]=$mensaje;
        return $this;
    }

    public function getExistentesRaw(){
        return $this->existentesRaw;
    }

    private function setExistentesRaw($existentesRaw){
        $this->existentesRaw=$existentesRaw;
        return $this;
    }

    public function getExistentesIndizados(){
        return $this->existentesIndizados;
    }

    private function setExistentesIndizados($existentesIndizados){
        $this->existentesIndizados=$existentesIndizados;
        return $this;
    }

    public function getExistentesIndizadosMulti(){
        return $this->existentesIndizadosMulti;
    }

    private function setExistentesIndizadosMulti($existentesIndizadosMulti){
        $this->existentesIndizadosMulti=$existentesIndizadosMulti;
        return $this;
    }

    public function getLlaves(){
        return $this->llaves;
    }

    public function setLlaves($llaves){
        $this->llaves=$llaves;
        return $this;
    }

    public function getCamposCustom(){
        return $this->camposCustom;
    }

    public function setCamposCustom($camposCustom){
        $this->camposCustom=$camposCustom;
        return $this;
    }

    public function getExistentesCustomRaw(){
        return $this->existentesCustomRaw;
    }

    public function setExistentesCustomRaw($existentesCustomRaw){
        $this->existentesCustomRaw=$existentesCustomRaw;
        return $this;
    }

    public function getExistentesCustomIndizados(){
        return $this->existentesCustomIndizados;
    }

    public function setExistentesCustomIndizados($existentesCustomIndizados){
        $this->existentesCustomIndizados=$existentesCustomIndizados;
        return $this;
    }

    public function getExistentesCustomIndizadosMulti(){
        return $this->existentesCustomIndizados;
    }

    public function setExistentesCustomIndizadosMulti($existentesCustomIndizadosMulti){
        $this->existentesCustomIndizadosMulti=$existentesCustomIndizadosMulti;
        return $this;
    }

    private function getSerializedPhString($phArray){
        if(empty($phArray) ||!is_array($phArray)){
            $this->setMensajes('No hay información para crear las condiciones de busqueda');
            return false;
        }
        if(!$this->container->get('gopro_dbproceso_comun_variable')->is_multi_array($phArray)){
            return implode(' AND ', $phArray);
        }
        $wherePH=array();
        foreach ($phArray as $row):
            if(is_array($row)){
                $wherePH[]='('.implode(' AND ', $row).')';
            }
        endforeach;
        return implode(' OR ', $wherePH);
    }

    private function bindValues($statement,$values){
        if(empty($values) ||!is_array($values)){
            $this->setMensajes('No hay valores para asignar');
            return false;
        }
        foreach($values as $whereKey=>$whereValor):
            if(is_array($whereValor)){
                foreach ($whereValor as $whereSubKey => $whereSubValor):
                    $statement->bindValue($whereSubKey,$whereSubValor);
                endforeach;
            }else{
                $statement->bindValue($whereKey,$whereValor);
            }
        endforeach;
        return $statement;
    }

    public function setQueryVariables($informacion,$tipo='whereSelect'){
        if(empty($informacion)){
            $this->setMensajes('No existe informacion para definir las condiciones');
            return false;
        }

        foreach($informacion as $key => $valor):
            if(is_array($valor)&&($tipo!='valoresUpdate'||$tipo!='valoresInsert'||$tipo!='camposInsert'|| $tipo=='camposselect')){
                foreach($valor as $subKey => $subValor):
                    $procesoPh[$key][]=$subKey.'= :'.'v'.substr(sha1($this->container->get('gopro_dbproceso_comun_variable')->sanitizeString($subKey.$subValor)),0,28);
                    $procesoValor[$key]['v'.substr(sha1($this->container->get('gopro_dbproceso_comun_variable')->sanitizeString($subKey.$subValor)),0,28)]=$subValor;
                    $selectKeys[]=$subKey;
                endforeach;
            }elseif(is_array($valor)&&($tipo=='valoresUpdate'||$tipo=='valoresInsert'||$tipo!='camposInsert' || $tipo=='camposselect')){
                $this->setMensajes('El valor ingresado para "insert" o "act" no puede ser procesado');
                return false;
            }else{
                if($tipo=='camposInsert' || $tipo=='camposselect'){
                    $procesoValor=$valor;
                }elseif($tipo=='valoresUpdate' || $tipo=='whereUpdate'){ //todo quitar el whereupdate para el generico
                    $procesoPh[]=$key.'= :'.$key;
                    $procesoValor[$key]=$valor;
                }elseif($tipo=='valoresInsert'){
                    $procesoPh[]=':'.$key;
                    $procesoValor[$key]=$valor;
                }else{
                    $procesoPh[]=$key.'= :'.'v'.substr(sha1($this->container->get('gopro_dbproceso_comun_variable')->sanitizeString($key.$valor)),0,28);
                    $procesoValor['v'.substr(sha1($this->container->get('gopro_dbproceso_comun_variable')->sanitizeString($key.$valor)),0,28)]=$valor;
                    $selectKeys[]=$key;
                }
            }
        endforeach;
        if(!isset($procesoPh)||!isset($procesoValor)){
            $this->setMensajes('No existe informacion para definir las condiciones');
            return false;
        }
        if($tipo=='whereSelect'){
            $this->setWhereSelectPh($procesoPh);
            $this->setWhereSelectValores($procesoValor);
            $this->setLlaves(array_unique($selectKeys));
        }elseif($tipo=='whereUpdate'){
            $this->setWhereUpdatePh($procesoPh);
            $this->setWhereUpdateValores($procesoValor);
        }elseif($tipo=='valoresUpdate'){
            $this->setValoresUpdatePh($procesoPh);
            $this->setValoresUpdateValores($procesoValor);
        }elseif($tipo=='valoresInsert'){
            $this->setValoresInsertPh($procesoPh);
            $this->setValoresInsertValores($procesoValor);
        }elseif($tipo=='camposInsert'){
            $this->setCamposInsert($procesoValor);
        }elseif($tipo=='camposSelect'){
            $this->setCamposSelect($procesoValor);
        }
        return true;
    }

    public function ejecutarSelectQuery(){
        if(
            !is_object($this->getConexion())
            ||empty($this->getWhereSelectPh())
            ||empty($this->getWhereSelectValores())
            ||empty($this->getCamposSelect())
        ){
            $this->setMensajes('No existen los parametros para el select');
            return false;
        }
        $selectQuery='SELECT '.implode(', ',$this->getCamposSelect()).' FROM '.$this->getSchema().'.'.$this->getTabla().' WHERE '.$this->getSerializedPhString($this->getWhereSelectPh());

        $statement = $this->getConexion()->prepare($selectQuery);
        $statement=$this->bindValues($statement,$this->getWhereSelectValores());
        if(!$statement->execute()){
            return false;
        }
        $existentesRaw=$statement->fetchAll();
        $this->setExistentesRaw($existentesRaw);
        foreach($this->getExistentesRaw() as $nroLinea => $linea):
            $indexedArray=array();
            foreach($this->getLlaves() as $llave):
                if(isset($linea[$llave])){
                    $indexedArray[]=$linea[$llave];
                    unset($linea[$llave]);
                }
            endforeach;
            $existentesIndizados[implode('|',$indexedArray)]=$linea;
            $existentesIndizadosMulti[implode('|',$indexedArray)][]=$linea;
            if(!empty($this->getCamposCustom())){
                $i=0;
                foreach($this->getCamposCustom() as $campo):
                    if(isset($linea[$campo])){
                        $existentesCustomIndizadosMulti[implode('|',$indexedArray)][$i][$campo]=$linea[$campo];
                        $existentesCustomIndizados[implode('|',$indexedArray)][$campo]=$linea[$campo];
                    }
                $i++;
                endforeach;
            }
            if(!empty($this->getCamposCustom())){
                foreach($this->getCamposCustom() as $campo):
                    if(isset($linea[$campo])){
                        $existentesCustomRaw[$nroLinea][$campo]=$linea[$campo];
                    }
                endforeach;
            }

        endforeach;
        $this->setExistentesIndizados($existentesIndizados);
        $this->setExistentesIndizadosMulti($existentesIndizadosMulti);
        if(isset($existentesCustomRaw)){
            $this->setExistentesCustomRaw($existentesCustomRaw);
        }
        if(isset($existentesCustomIndizados)){
            $this->setExistentesCustomIndizados($existentesCustomIndizados);
        }
        if(isset($existentesCustomIndizadosMulti)){
            $this->setExistentesCustomIndizadosMulti($existentesCustomIndizadosMulti);
        }
        return true;
    }

    public function ejecutarUpdateQuery(){
        if(
            !is_object($this->getConexion())
            ||empty($this->getValoresUpdatePh())
            ||empty($this->getValoresUpdateValores())
            ||empty($this->getWhereUpdatePh())
            ||empty($this->getWhereUpdateValores())
        ){
            $this->setMensajes('No existen los parametros de actualización');
            return false;
        }
        $updateQuery='UPDATE '.$this->getSchema().'.'.$this->getTabla().' SET '.implode(', ',$this->getValoresUpdatePh()).' WHERE '.$this->getSerializedPhString($this->getWhereUpdatePh());//update
        $statement = $this->getConexion()->prepare($updateQuery);
        $statement=$this->bindValues($statement,array_merge($this->getValoresUpdateValores(),$this->getWhereUpdateValores()));
        return $statement->execute();
    }

    public function ejecutarInsertQuery(){
        if(
            !is_object($this->getConexion())
            ||empty($this->getValoresInsertPh())
            ||empty($this->getValoresInsertValores())
            ||empty($this->getCamposInsert())
        ){
            $this->setMensajes('No existen los parametros para insersión');
            return false;
        }
        $addQuery='INSERT INTO '.$this->getSchema().'.'.$this->getTabla().' ('.implode(', ',$this->getCamposInsert()).') VALUES ('.implode(', ',$this->getValoresInsertPh()).')';
        $statement = $this->getConexion()->prepare($addQuery);
        $statement=$this->bindValues($statement,$this->getValoresInsertValores());
        return $statement->execute();
    }
}