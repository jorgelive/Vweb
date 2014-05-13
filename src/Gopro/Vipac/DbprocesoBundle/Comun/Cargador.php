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
    private $existente;

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

    public function getExistente(){
        return $this->existente;
    }

    private function setMensajes($mensaje){
        $this->mensajes[]=$mensaje;
    }

    private function getLlaves(){
        return $this->llaves;
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

    private function setExistente(){
        $this->setLlaves();
        //print_r($this->columnaSpecs);
        $this->keysDiff=array_diff($this->getLlaves(),$this->tablaSpecs['llaves']);
        if(!empty($this->keysDiff)){
            $this->setMensajes('Existe diferencia entre las llaves ingresadas y las existentes, no se permite update e insert con esta condici�n');
        }
        $existente=array();
        $primaryKeys=array();
        $primaryKeysPH=array();
        foreach ($this->valores as $rowNumber => $row):
            foreach ($row as $col => $valor):
                if(isset($this->columnaSpecs[$col]['nombre'])&&isset($this->columnaSpecs[$col]['llave'])&&$this->columnaSpecs[$col]['llave']=='si'){
                    $primaryKeysPH[$rowNumber][]=$this->columnaSpecs[$col]['nombre'].'= :'.$this->columnaSpecs[$col]['nombre'].$this->container->get('gopro_dbproceso_comun_variable')->sanitizeString($valor);
                    $primaryKeys[$rowNumber][$this->container->get('gopro_dbproceso_comun_variable')->sanitizeString($this->columnaSpecs[$col]['nombre'].$valor)]=$valor;

                }
            endforeach;
        endforeach;
        //var_dump($primaryKeys);
        if(empty($primaryKeys)||empty($primaryKeysPH)){
            $this->setMensajes('No existe informacion de las llaves primarias');
            return false;
        }
        foreach ($primaryKeysPH as $row):
            $wherePH[]='('.implode(' AND ', $row).')';
        endforeach;
        $selectQuery='SELECT '.implode(', ',$this->tablaSpecs['columnasProceso']).' FROM '.$this->tablaSpecs['schema'].'.'.$this->tablaSpecs['nombre'].' WHERE '.implode(' OR ', $wherePH);

        $statement = $this->conn->prepare($selectQuery);
        //echo ($selectQuery);
        foreach($primaryKeys as $whereArray):
            foreach ($whereArray as $whereKey => $whereValor):
                $statement->bindValue($whereKey,$whereValor);
            endforeach;
        endforeach;

        $statement->execute();
        $registro=$statement->fetchAll();
        //print_r($tablaSpecs);
        foreach($registro as $linea):
            $identArray=array();
            foreach($this->tablaSpecs['llaves'] as $llave):
                $identArray[]=$linea[$llave];
            endforeach;
            $existente[implode('|',$identArray)]=$linea;
        endforeach;

        $this->existente = $existente;

    }

    public function cargaGenerica(){
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
        $this->setExistente();
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
        if(empty($this->existente)&&$this->tablaSpecs['tipo']!='I'){
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
            $this->dbRowProcess($rowNumber+1,$wherePH,$whereArray,$actPH,$actArray,$insertPH,$insertArray);
        endforeach;

        return true;
    }

    private function dbRowProcess($rowNumber,$wherePH,$whereArray,$actPH,$actArray,$insertPH,$insertArray){

        $busqueda=implode('|',$whereArray);
        if(array_key_exists($busqueda,$this->existente)===true){
            if($this->tablaSpecs['tipo']=='I'){
                $this->setMensajes('La linea '.$rowNumber.' ya existe, estamos en modo solo insertar');
                return false;
            }
            foreach ($whereArray as $whereKey => $whereValor):
                unset($this->existente[$busqueda][$whereKey]);
            endforeach;
            $diferencia=array_diff_assoc($this->existente[$busqueda],$actArray);
            if(empty($diferencia)){
                $this->setMensajes('Nada que actualizar para la linea: '.$rowNumber);
                return true;
            }
            $updateQuery='UPDATE '.$this->tablaSpecs['schema'].'.'.$this->tablaSpecs['nombre'].' SET '.implode(', ',$actPH).' WHERE '.implode(' AND ', $wherePH);//update
            $statement = $this->conn->prepare($updateQuery);
            foreach ($actArray as $actKey => $actValor):
                $statement->bindValue($actKey,$actValor);
            endforeach;
            foreach ($whereArray as $whereKey => $whereValor):
                $statement->bindValue($whereKey,$whereValor);
            endforeach;
            $statement->execute();
            $this->setMensajes('Actualizando para la linea: '.$rowNumber);
            return true;

        }elseif(isset($insertArray)&&!empty($insertArray)){
            if ($this->tablaSpecs['tipo']=='U'){
                $this->setMensajes('La linea '.$rowNumber. ' no existe, estamos en modo solo actualizar');
                return false;
            }

            $addQuery='INSERT INTO '.$this->tablaSpecs['schema'].'.'.$this->tablaSpecs['nombre'].' ('.implode(', ',$this->tablaSpecs['columnas']).') VALUES ('.implode(', ',$insertPH).')';
            //echo $addQuery;
            $statement = $this->conn->prepare($addQuery);
            foreach ($insertArray as $insertKey => $insertValor):
                $statement->bindValue($insertKey,$insertValor);
            endforeach;
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