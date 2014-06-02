<?php
namespace Gopro\Vipac\DbprocesoBundle\Comun;
use \Symfony\Component\DependencyInjection\ContainerAware;

class Cargador extends ContainerAware{

    private $mensajes=array();
    private $columnaSpecs;
    private $valores;
    private $proceso;
    private $tipo;

    public function getProceso(){
        return $this->proceso;
    }

    private function setProceso(){
        $this->proceso=$this->container->get('gopro_dbproceso_comun_proceso');
        return $this;
    }

    public function getTipo(){
        return $this->tipo;
    }

    private function setTipo($tipo){
        $this->tipo=$tipo;
        return $this;
    }

    public function setParametros($tablaSpecs,$columnaSpecs,$valores,$conexion){

        if(empty($tablaSpecs)
            ||!is_array($tablaSpecs)
            ||!isset($tablaSpecs['nombre'])
            ||!isset($tablaSpecs['schema'])
            ||!isset($tablaSpecs['llaves'])
            ||!isset($tablaSpecs['columnas'])
            ||!isset($tablaSpecs['columnasProceso'])
        ){
            $this->setMensajes('Las especificaciones de la tabla son inválidas');
            return false;
        }

        if(empty($columnaSpecs)||!is_array($columnaSpecs)){
            $this->setMensajes('Las especificaciones de las columnas son inválidos');
            return false;
        }

        if(empty($valores)||!is_array($valores)){
            $this->setMensajes('No se han ingresado los valores');
            return false;
        }

        if(empty($conexion)||!is_object($conexion)){
            $this->setMensajes('La conexión no es válida');
            return false;
        }
        $this->setProceso();
        $this->getProceso()->setTabla($tablaSpecs['nombre']);
        $this->getProceso()->setSchema($tablaSpecs['schema']);
        $this->getProceso()->setCamposInsert($tablaSpecs['columnas']);
        $this->getProceso()->setCamposSelect($tablaSpecs['columnasProceso']);
        $this->getProceso()->setLlaves($tablaSpecs['llaves']);
        if(!isset($tablaSpecs['tipo'])||!is_string($tablaSpecs['tipo'])||!in_array($tablaSpecs['tipo'],['S','IU','UI','I','U'])){
            $this->setMensajes('El tipo de proceso se establece al valor por defecto');
            $tablaSpecs['tipo']='S';
        }
        $this->setTipo($tablaSpecs['tipo']);
        $this->columnaSpecs=$columnaSpecs;
        $this->valores=$valores;
        $this->getProceso()->setConexion($conexion);
        return true;
    }

    public function getMensajes(){
        return array_merge($this->getProceso()->getMensajes(),$this->mensajes);
    }

    private function setMensajes($mensaje){
        $this->mensajes[]=$mensaje;
        return $this;
    }

    private function getKeysDiff(){
        return $this->keysDiff;
    }

    private function setKeysDiff(){
        $query[]="SELECT cols.table_name, cols.column_name, cols.position, cons.status, cons.owner";
        $query[]="FROM all_constraints cons, all_cons_columns cols";
        $query[]="WHERE cols.table_name = '".$this->getProceso()->getTabla()."' AND cons.constraint_type = 'P'";
        $query[]="AND cons.constraint_name = cols.constraint_name AND cons.owner = cols.owner";
        $query[]="AND cons.owner = '".$this->getProceso()->getSchema()."' ORDER BY cols.table_name, cols.position";
        $statement = $this->getProceso()->getConexion()->query(implode(' ',$query));
        $keysArray = $statement->fetchAll();
        $keyInTable=array();
        foreach($keysArray as $key):
            $keyInTable[]=$key['COLUMN_NAME'];
        endforeach;
        $this->keysDiff=array_diff($keyInTable,$this->getProceso()->getLlaves());
        if(!empty($this->keysDiff)){
            $this->setMensajes('Existe diferencia entre las llaves ingresadas y las existentes, no se permite update e insert con esta condición');
        }
    }

    public function prepararSelect(){
        foreach ($this->valores as $rowNumber => $row):
            foreach ($row as $col => $valor):
                if(isset($this->columnaSpecs[$col]['nombre'])&&isset($this->columnaSpecs[$col]['llave'])&&$this->columnaSpecs[$col]['llave']=='si'){
                    $whereArray[$rowNumber][$this->columnaSpecs[$col]['nombre']]=$valor;
                 }
            endforeach;
        endforeach;
        return $whereArray;
    }

    public function ejecutar(){
        $whereArray=$this->prepararSelect();
        if(!$this->getProceso()->setQueryVariables($whereArray)){
            return false;
        }
        $this->setKeysDiff();
        $this->getProceso()->ejecutarSelectQuery();
        if(empty($this->getKeysDiff())&&$this->getTipo()!='S'){
            $this->setMensajes('Se realizo la busqueda, se ejecutan los procesos de escritura');
            $this->dbProcess();
            return true;
        }elseif($this->getTipo()=='S'){
            $this->setMensajes('Se realizo la busqueda, no se ejecuta ningun proceso extra');
            return true;
        }else{
            $this->setMensajes('Existen diferencias en las llaves, no se hará el proceso');
            return false;
        }
    }

    public function dbProcess(){
        if(empty($this->getProceso()->getExistentesIndizados())&&$this->getTipo()!='I'){
            $this->setMensajes('Solo se permite inserciones con tipo I');
            return false;
        }
        foreach ($this->valores as $rowNumber => $row):
            $whereArray=array();
            $actArray=array();
            $insertArray=array();
            foreach ($row as $col => $valor):
                if(isset($this->columnaSpecs[$col]['nombre'])&&isset($this->columnaSpecs[$col]['llave'])){
                    if($this->columnaSpecs[$col]['llave']=='si'){
                        $whereArray[$this->columnaSpecs[$col]['nombre']]=$valor;
                    }elseif(in_array($this->columnaSpecs[$col]['nombre'],$this->getProceso()->getCamposSelect())){
                        $actArray[$this->columnaSpecs[$col]['nombre']]=$valor;
                    }
                    if(in_array($this->columnaSpecs[$col]['nombre'],$this->getProceso()->getCamposInsert())){
                        $insertArray[$this->columnaSpecs[$col]['nombre']]=$valor;
                    }
                }
            endforeach;
            if(!$this->getProceso()->setQueryVariables($whereArray,'whereUpdate')){
                return false;
            }
            if(!$this->getProceso()->setQueryVariables($actArray,'valoresUpdate')){
                return false;
            }
            if(!$this->getProceso()->setQueryVariables($insertArray,'valoresInsert')){
                return false;
            }
            print_r($this->getTipo());
            $this->dbRowProcess($rowNumber+1);
        endforeach;
        return true;
    }

    private function dbRowProcess($rowNumber){
        $busqueda=implode('|',$this->getProceso()->getWhereUpdateValores());
        if(array_key_exists($busqueda,$this->getProceso()->getExistentesIndizados())===true){//todo mejor comparacion
            if($this->getTipo()=='I'){
                $this->setMensajes('La linea '.$rowNumber.' ya existe, estamos en modo solo insertar');
                return false;
            }
            $diferencia=array_diff_assoc($this->getProceso()->getExistentesIndizados()[$busqueda],$this->getProceso()->getValoresUpdateValores());
            if(empty($diferencia)){
                $this->setMensajes('Nada que actualizar para la linea: '.$rowNumber);
                return true;
            }
            if($this->getProceso()->ejecutarUpdateQuery()){
                $this->setMensajes('Actualizando para la linea: '.$rowNumber);
            }
        }elseif(!empty($insertArray)){
            if ($this->getTipo()=='U'){
                $this->setMensajes('La linea '.$rowNumber. ' no existe, estamos en modo solo actualizar');
                return false;
            }
            try{
                if($this->getProceso()->ejecutarInsertQuery()){
                    $this->setMensajes('Insertando para la linea: '.$rowNumber);
                }
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