<?php
namespace Gopro\Vipac\DbprocesoBundle\Comun;
use \Symfony\Component\DependencyInjection\ContainerAware;

class Proceso extends ContainerAware{

    private $conexion;
    private $tabla;
    private $schema;
    private $mensajes=array();
    private $existenteRaw;
    private $existenteIndex;
    private $whereSelectValores;
    private $whereSelectPh;
    private $camposSelect;

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
    }

    public function getTabla(){
        return $this->tabla;
    }

    public function setSchema($schema){
        $this->schema=$schema;
    }

    public function getSchema(){
        return $this->schema;
    }

    public function setConexion($conexion){
        $this->conexion=$conexion;
    }

    public function getConexion(){
        return $this->conexion;
    }

    public function getWhereSelectValores(){
        return $this->whereSelectValores;
    }

    public function setWhereSelectValores($where){
        $this->whereSelectValores=$where;
    }

    public function getWhereSelectPh(){
        return $this->whereSelectPh;
    }

    public  function setWhereSelectPh($wherePh){
        $this->whereSelectPh=$wherePh;
    }

    public function getCamposSelect(){
        return $this->camposSelect;
    }

    public function setCamposSelect($campos){
        $this->camposSelect=$campos;
    }

    public function getWhereUpdateValores(){
        return $this->whereUpdateValores;
    }

    public function setWhereUpdateValores($where){
        $this->whereUpdateValores=$where;
    }

    public function getWhereUpdatePh(){
        return $this->whereUpdatePh;
    }

    public function setWhereUpdatePh($wherePh){
        $this->whereUpdatePh=$wherePh;
    }

    public function getValoresUpdateValores(){
        return $this->valoresUpdateValores;
    }

    public function setValoresUpdateValores($act){
        $this->valoresUpdateValores=$act;
    }

    public function getValoresUpdatePh(){
        return $this->valoresUpdatePh;
    }

    public function setValoresUpdatePh($actPh){
        $this->valoresUpdatePh=$actPh;
    }

    public function getValoresInsertValores(){
        return $this->valoresInsertValores;
    }

    public function setValoresInsertValores($insert){
        $this->valoresInsertValores=$insert;
    }

    public function getValoresInsertPh(){
        return $this->valoresInsertPh;
    }

    public function setValoresInsertPh($insertPh){
        $this->valoresInsertPh=$insertPh;
    }

    public function getCamposInsert(){
        return $this->camposInsert;
    }

    public function setCamposInsert($campos){
        $this->camposInsert=$campos;
    }

    public function getMensajes(){
        return $this->mensajes;
    }

    private function setMensajes($mensaje){
        $this->mensajes[]=$mensaje;
    }

    private function setExistenteRaw($datos){
        $this->existenteRaw=$datos;
    }

    public function getExistenteRaw(){
        return $this->existenteRaw;
    }

    public function getExistenteIndex(){
        return $this->existenteIndex;
    }

    private function setExistenteIndex($datos){
        $this->existenteIndex=$datos;
    }

    public function getLlaves(){
        return $this->llaves;
    }

    public function setLlaves($llaves){
        $this->llaves=$llaves;
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
                endforeach;
            }elseif(is_array($valor)&&($tipo=='valoresUpdate'||$tipo=='valoresInsert'||$tipo!='camposInsert' || $tipo=='camposselect')){
                $this->setMensajes('El valor ingresado para "insert" o "act" no puede ser procesado');
                return false;
            }else{
                if($tipo=='camposInsert' || $tipo=='camposselect'){
                    $procesoValor=$valor;
                }elseif($tipo=='valoresUpdate'){
                    $procesoPh[]=$key.'= :'.$key;
                    $procesoValor[$key]=$valor;
                }elseif($tipo=='valoresInsert'){
                    $procesoPh[]=':'.$key;
                    $procesoValor[$key]=$valor;
                }else{
                    $procesoPh[]=$key.'= :'.'v'.substr(sha1($this->container->get('gopro_dbproceso_comun_variable')->sanitizeString($key.$valor)),0,28);
                    $procesoValor['v'.substr(sha1($this->container->get('gopro_dbproceso_comun_variable')->sanitizeString($key.$valor)),0,28)]=$valor;
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
        if(empty($this->getWhereSelectPh())||empty($this->getWhereSelectValores())||empty($this->getCamposSelect())){
            $this->setMensajes('No existen los parametros para el select');
            return false;
        }
        $selectQuery='SELECT '.implode(', ',$this->getCamposSelect()).' FROM '.$this->getSchema().'.'.$this->getTabla().' WHERE '.$this->getSerializedPhString($this->getWhereSelectPh());
        $statement = $this->getConexion()->prepare($selectQuery);
        $statement=$this->bindValues($statement,$this->getWhereSelectValores());
        if(!$statement->execute()){
            return false;
        }
        $registros=$statement->fetchAll();
        $this->setExistenteRaw($registros);
        foreach($this->getExistenteRaw() as $linea):
            $identArray=array();
            foreach($this->getLlaves() as $llave):
                $identArray[]=$linea[$llave];
                unset($linea[$llave]);
            endforeach;
            $existente[implode('|',$identArray)]=$linea;
        endforeach;
        $this->setExistenteIndex($existente);
        return true;
    }

    public function ejecutarUpdateQuery(){
        if(empty($this->getValoresUpdatePh())||empty($this->getWhereUpdatePh())||empty($this->getValoresUpdateValores())||empty($this->getWhereUpdateValores())){
            $this->setMensajes('No existen los parametros de actualización');
            return false;
        }
        $updateQuery='UPDATE '.$this->getSchema().'.'.$this->getTabla().' SET '.implode(', ',$this->getValoresUpdatePh()).' WHERE '.$this->getSerializedPhString($this->getWhereUpdatePh());//update
        $statement = $this->getConexion()->prepare($updateQuery);
        $statement=$this->bindValues($statement,array_merge($this->getValoresUpdateValores(),$this->getWhereUpdateValores()));
        return $statement->execute();
    }

    public function ejecutarInsertQuery(){
        if(empty($this->getValoresInsertPh())||empty($this->getValoresInsertValores())||empty($this->getCamposInsert())){
            $this->setMensajes('No existen los parametros para insersión');
            return false;
        }
        $addQuery='INSERT INTO '.$this->getSchema().'.'.$this->getTabla().' ('.implode(', ',$this->getCamposInsert()).') VALUES ('.implode(', ',$this->getValoresInsertPh()).')';
        $statement = $this->getConexion()->prepare($addQuery);
        $statement=$this->bindValues($statement,$this->getValoresInsertValores());
        return $statement->execute();
    }
}