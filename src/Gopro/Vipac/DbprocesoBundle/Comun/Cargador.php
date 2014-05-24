<?php
namespace Gopro\Vipac\DbprocesoBundle\Comun;
use \Symfony\Component\DependencyInjection\ContainerAware;

class Cargador extends ContainerAware{

    private $mensajes=array();
    private $tablaSpecs;
    private $columnaSpecs;
    private $valores;
    private $conn;
    private $llaves;
    private $keysDiff;
    private $existenteRaw;
    private $existenteIndex;
    private $whereSelectValores;
    private $whereSelectPh;
    //valores temporales por fila
    private $whereUpdateValores;
    private $whereUpdatePh;
    private $actUpdateValores;
    private $actUpdatePh;
    private $insertValores;
    private $insertPh;

    public function getWhereSelectValores(){
        return $this->whereSelectValores;
    }

    private function setWhereSelectValores($where){
        $this->whereSelectValores=$where;
    }

    public function getWhereSelectPh(){
        return $this->whereSelectPh;
    }

    private function setWhereSelectPh($wherePh){
        $this->whereSelectPh=$wherePh;
    }

    private function getWhereUpdateValores(){
        return $this->whereUpdateValores;
    }

    private function setWhereUpdateValores($where){
        $this->whereUpdateValores=$where;
    }

    public function getWhereUpdatePh(){
        return $this->whereUpdatePh;
    }

    private function setWhereUpdatePh($wherePh){
        $this->whereUpdatePh=$wherePh;
    }

    public function getActUpdateValores(){
        return $this->actUpdateValores;
    }

    private function setActUpdateValores($act){
        $this->actUpdateValores=$act;
    }

    public function getActUpdatePh(){
        return $this->actUpdatePh;
    }

    private function setActUpdatePh($actPh){
        $this->actUpdatePh=$actPh;
    }

    public function getInsertValores(){
        return $this->insertValores;
    }

    private function setInsertValores($insert){
        $this->insertValores=$insert;
    }

    public function getInsertPh(){
        return $this->insertPh;
    }

    private function setInsertPh($insertPh){
        $this->insertPh=$insertPh;
    }

    public function setParametros($tablaSpecs,$columnaSpecs,$valores,$conn){
        $this->tablaSpecs=$tablaSpecs;
        $this->columnaSpecs=$columnaSpecs;
        $this->valores=$valores;
        $this->conn=$conn;
        $this->mensajes=array();
    }

    public function getMensajes(){
        return $this->mensajes;
    }

    private function setMensajes($mensaje){
        $this->mensajes[]=$mensaje;
    }

    private function getLlaves(){
        return $this->llaves;
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


    private function setLlaves(){
        $query[]="SELECT cols.table_name, cols.column_name, cols.position, cons.status, cons.owner";
        $query[]="FROM all_constraints cons, all_cons_columns cols";
        $query[]="WHERE cols.table_name = '".$this->tablaSpecs['nombre']."' AND cons.constraint_type = 'P'";
        $query[]="AND cons.constraint_name = cols.constraint_name AND cons.owner = cols.owner";
        $query[]="AND cons.owner = '".$this->tablaSpecs['schema']."' ORDER BY cols.table_name, cols.position";
        //print_r(implode(' ',$query));
        $statement = $this->conn->query(implode(' ',$query));
        $keysArray = $statement->fetchAll();
        $keyInTable=array();
        foreach($keysArray as $key):
            $keyInTable[]=$key['COLUMN_NAME'];
        endforeach;
        $this->llaves=$keyInTable;
    }

    public function is_multi_array($array) {
        return (count($array) != count($array, 1));
    }

    private function getWherePhString($phArray){
        if(empty($phArray) ||!is_array($phArray)){
            $this->setMensajes('No hay información para crear las condiciones de busqueda');
            return false;
        }
        if(!$this->is_multi_array($phArray)){
            return implode(' AND ', $phArray);
        }
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

    public function setWhereSelect($whereArray){

        if(empty($whereArray)){
            $this->setMensajes('No existe informacion para definir las condiciones');
            return false;
        }

        foreach($whereArray as $key => $valor):
            if(is_array($valor)){
                foreach($valor as $subKey => $subValor):
                    //echo $valor;
                    $wherePh[$key][]=$subKey.'= :'.'v'.substr(sha1($this->container->get('gopro_dbproceso_comun_variable')->sanitizeString($subKey.$subValor)),0,28);
                    $whereValor[$key]['v'.substr(sha1($this->container->get('gopro_dbproceso_comun_variable')->sanitizeString($subKey.$subValor)),0,28)]=$subValor;

                endforeach;
            }else{
                $wherePh[]=$key.'= :'.'v'.substr(sha1($this->container->get('gopro_dbproceso_comun_variable')->sanitizeString($key.$valor)),0,28);
                $whereValor['v'.substr(sha1($this->container->get('gopro_dbproceso_comun_variable')->sanitizeString($key.$valor)),0,28)]=$valor;

            }
        endforeach;
        if(!isset($wherePh)||!isset($whereValor)){
            $this->setMensajes('No existe informacion para definir las condiciones');
            return false;
        }

        $this->setWhereSelectPh($wherePh);
        $this->setWhereSelectValores($whereValor);
        return true;
    }

    private function setExistentes(){
        $this->setLlaves();

        $this->keysDiff=array_diff($this->getLlaves(),$this->tablaSpecs['llaves']);
        if(!empty($this->keysDiff)){
            $this->setMensajes('Existe diferencia entre las llaves ingresadas y las existentes, no se permite update e insert con esta condici�n');
        }
        $existente=array();
        foreach ($this->valores as $rowNumber => $row):
            foreach ($row as $col => $valor):
                if(isset($this->columnaSpecs[$col]['nombre'])&&isset($this->columnaSpecs[$col]['llave'])&&$this->columnaSpecs[$col]['llave']=='si'){
                    $whereArray[$rowNumber][$this->columnaSpecs[$col]['nombre']]=$valor;
                 }
            endforeach;
        endforeach;

        if(!$this->setWhereSelect($whereArray)){
            return false;
        }

        if(!$this->ejecutarSelectQuery()){
            return false;
        }
        $registro=$this->ejecutarSelectQuery();
        $this->setExistenteRaw($registro);

        foreach($this->getExistenteRaw() as $linea):
            $identArray=array();
            foreach($this->tablaSpecs['llaves'] as $llave):
                $identArray[]=$linea[$llave];
            endforeach;
            $existente[implode('|',$identArray)]=$linea;
        endforeach;

        $this->setExistenteIndex($existente);
    }

    public function ejecutarSelectQuery(){
        $selectQuery='SELECT '.implode(', ',$this->tablaSpecs['columnasProceso']).' FROM '.$this->tablaSpecs['schema'].'.'.$this->tablaSpecs['nombre'].' WHERE '.$this->getWherePhString($this->getWhereSelectPh());
        $statement = $this->conn->prepare($selectQuery);
        $statement=$this->bindValues($statement,$this->getWhereSelectValores());
        $statement->execute();
        $registros=$statement->fetchAll();
        return $registros;

    }

    public function ejecutar(){
        $procesar=true;
        if(!isset($this->tablaSpecs['tipo'])||!in_array($this->tablaSpecs['tipo'],Array('S','IU','UI','I','U'))){
            $this->setMensajes('El tipo de proceso no esta establecido o no es correcto');
            $procesar=false;
        }

        if(empty($this->valores)){
            $this->setMensajes('No existen valores para procesar');
            $procesar=false;
        }

        if(empty($this->tablaSpecs)){
            $this->setMensajes('Las especificaciones de la tabla no existen');
            $procesar=false;
        }

        if(empty($this->tablaSpecs)){
            $this->setMensajes('Las especificaciones de las columnas no existen');
            $procesar=false;
        }

        if($procesar===false){
            return false;
        }
        $this->setExistentes();
        if(empty($this->keysDiff)&&$this->tablaSpecs['tipo']!='S'){
            $this->setMensajes('Se realizo la busqueda, se ejecutan los procesos de escritura');
            $this->dbProcess();
            return true;
        }elseif($this->tablaSpecs['tipo']=='S'){
            $this->setMensajes('Se realizo la busqueda, no se ejecuta ningun proceso extra');
            return true;
        }else{
            $this->setMensajes('Existen diferencias en las llaves, no se hara el proceso');
            return false;
        }
    }

    public function dbProcess(){
        if(empty($this->getExistenteIndex())&&$this->tablaSpecs['tipo']!='I'){
            $this->setMensajes('Solo se permite inserciones con tipo I');
            return false;
        }

        foreach ($this->valores as $rowNumber => $row):
            $whereArray=array();
            $wherePH=array();
            $actArray=array();
            $actPH=array();
            $insertPH=array();
            $insertArray=array();
            foreach ($row as $col => $valor):
                if(isset($this->columnaSpecs[$col]['nombre'])&&isset($this->columnaSpecs[$col]['llave'])){
                    if($this->columnaSpecs[$col]['llave']=='si'){
                        $wherePH[]=$this->columnaSpecs[$col]['nombre'].'= :'.$this->columnaSpecs[$col]['nombre'];
                        $whereArray[$this->columnaSpecs[$col]['nombre']]=$valor;
                    }elseif(in_array($this->columnaSpecs[$col]['nombre'],$this->tablaSpecs['columnasProceso'])){
                        $actPH[]=$this->columnaSpecs[$col]['nombre'].'= :'.$this->columnaSpecs[$col]['nombre'];
                        $actArray[$this->columnaSpecs[$col]['nombre']]=$valor;
                    }
                    if(in_array($this->columnaSpecs[$col]['nombre'],$this->tablaSpecs['columnasProceso'])){
                        $insertPH[]=':'.$this->columnaSpecs[$col]['nombre'];
                        $insertArray[$this->columnaSpecs[$col]['nombre']]=$valor;
                    }
                }
            endforeach;
            $this->setWhereUpdateValores($whereArray);
            $this->setWhereUpdatePh($wherePH);
            $this->setActUpdateValores($actArray);
            $this->setActUpdatePh($actPH);
            $this->setInsertValores($insertArray);
            $this->setInsertPh($insertPH);
            $this->dbRowProcess($rowNumber+1);
        endforeach;

        return true;
    }

    private function dbRowProcess($rowNumber){

        $busqueda=implode('|',$this->getWhereUpdateValores());
        if(array_key_exists($busqueda,$this->getExistenteIndex())===true){
            if($this->tablaSpecs['tipo']=='I'){
                $this->setMensajes('La linea '.$rowNumber.' ya existe, estamos en modo solo insertar');
                return false;
            }
            foreach ($this->getWhereUpdateValores() as $whereKey => $whereValor):
                unset($this->getExistenteIndex()[$busqueda][$whereKey]);
            endforeach;
            $diferencia=array_diff_assoc($this->getExistenteIndex()[$busqueda],$this->getActUpdateValores());
            if(empty($diferencia)){
                $this->setMensajes('Nada que actualizar para la linea: '.$rowNumber);
                return true;
            }

            $updateQuery='UPDATE '.$this->tablaSpecs['schema'].'.'.$this->tablaSpecs['nombre'].' SET '.implode(', ',$this->getActUpdatePh()).' WHERE '.$this->getWherePhString($this->getWhereUpdatePh());//update
            $statement = $this->conn->prepare($updateQuery);
            $statement=$this->bindValues($statement,array_merge($this->getActUpdateValores(),$this->getWhereUpdateValores()));
            $statement->execute();
            $this->setMensajes('Actualizando para la linea: '.$rowNumber);
            return true;

        }elseif(isset($insertArray)&&!empty($insertArray)){
            if ($this->tablaSpecs['tipo']=='U'){
                $this->setMensajes('La linea '.$rowNumber. ' no existe, estamos en modo solo actualizar');
                return false;
            }

            $addQuery='INSERT INTO '.$this->tablaSpecs['schema'].'.'.$this->tablaSpecs['nombre'].' ('.implode(', ',$this->tablaSpecs['columnas']).') VALUES ('.implode(', ',$this->getInsertPh()).')';
            //echo $addQuery;
            $statement = $this->conn->prepare($addQuery);
            $statement=$this->bindValues($statement,$this->getInsertValores());
            try{
                $statement->execute();
                $this->setMensajes('Agregando para la linea: '.$rowNumber);
            }catch(\Exception $e){
                preg_match('/ORA-00001/', $e->getMessage(), $coincidencias, PREG_OFFSET_CAPTURE);
                if(!empty($coincidencias)){
                    $this->setMensajes('No se agrego la linea: '.$rowNumber.' (El registro ya existe)');
                }else{
                    $this->setMensajes('No se agrego la linea: '.$rowNumber.' ('.$e->getMessage().')');
                }
                return false;
            }
            return true;

        }else{
            $this->setMensajes('No se actualizo nada, los parametros son errados');
            return false;
        }

    }

}