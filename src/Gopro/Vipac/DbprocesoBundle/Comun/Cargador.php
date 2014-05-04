<?php
namespace Gopro\Vipac\DbprocesoBundle\Comun;
use \Symfony\Component\DependencyInjection\ContainerAware;

class Cargador extends ContainerAware{



    private $mensajes;

    private $tablaSpecs;

    private $columnaSpecs;

    private $valores;

    public function setTablaSpecs($tablaSpecs){
        $this->tablaSpecs=$tablaSpecs;
    }

    public function setColumnaSpecs($columnaSpecs){
        $this->columnaSpecs=$columnaSpecs;
    }

    public function setValores($valores){
        $this->valores=$valores;
    }

    public function getMensajes(){
        return $this->mensajes;
    }

    private function setMensajes($mensaje){
        $this->mensajes[]=$mensaje;
    }



    public function cargadorGenerico($tablaSpecs,$columnaSpecs,$valores){

        if(isset($tablaSpecs['tipo'])&&in_array($tablaSpecs['tipo'],Array('IU','UI','I','U'))&&isset($valores)&&isset($tablaSpecs)&&isset($columnaSpecs)&&!empty($valores)&&!empty($tablaSpecs)&&!empty($columnaSpecs)){
            $conn = $this->container->get('doctrine.dbal.default_connection');
            $existente=$this->generalQuery($conn,$tablaSpecs,$columnaSpecs,$valores,true);
            $mensajes=$this->dbPreProcess($conn,$tablaSpecs,$columnaSpecs,$valores,$existente);
        }elseif(isset($tablaSpecs['tipo'])&&!in_array($tablaSpecs['tipo'],Array('IU','UI','I','U'))){
            $mensajes=array('No se definio correctamente el tipo de proceso');
        }else{
            $mensajes=array('No existe informacion necesaria para el proceso');
        }
        return $mensajes;
    }

    public function obtenerLlaves($conn,$tablaSpecs){
        $query[]="SELECT cols.table_name, cols.column_name, cols.position, cons.status, cons.owner";
        $query[]="FROM all_constraints cons, all_cons_columns cols";
        $query[]="WHERE cols.table_name = '".$tablaSpecs['nombre']."' AND cons.constraint_type = 'P'";
        $query[]="AND cons.constraint_name = cols.constraint_name AND cons.owner = cols.owner";
        $query[]="AND cons.owner = '".$tablaSpecs['schema']."' ORDER BY cols.table_name, cols.position";
        //print_r(implode(' ',$query));
        $statement = $conn->query(implode(' ',$query));
        $keysArray = $statement->fetchAll();
        $keyInTable=array();
        foreach($keysArray as $key):
            $keyInTable[]=$key['COLUMN_NAME'];
        endforeach;
        return $keyInTable;

    }

    public function generalQuery($conn,$tablaSpecs,$columnaSpecs,$valores,$estricto){

        $keysDiff=array_diff($this->obtenerLlaves($conn,$tablaSpecs),$tablaSpecs['llaves']);
        $existente=array();
        if(!empty($keysDiff)&&$estricto===true){
            return array();

        }

        foreach ($valores as $rowNumber => $row):
            foreach ($row as $col => $valor):
                if(isset($columnaSpecs[$col]['nombre'])&&isset($columnaSpecs[$col]['llave'])){
                    if($columnaSpecs[$col]['llave']=='si'){
                        $primaryKeysPH[$rowNumber][]=$columnaSpecs[$col]['nombre'].'= :'.$columnaSpecs[$col]['nombre'].$this->container->get('gopro_dbproceso_comun_variable')->sanitizeString($valor);
                        $primaryKeys[$rowNumber][$columnaSpecs[$col]['nombre'].$this->container->get('gopro_dbproceso_comun_variable')->sanitizeString($valor)]=$valor;
                    }
                }
            endforeach;
        endforeach;

        if(!empty($primaryKeys)&&!empty($primaryKeysPH)){

            foreach ($primaryKeysPH as $row):
                $wherePH[]='('.implode(' AND ', $row).')';
            endforeach;
            $selectQuery='SELECT '.implode(', ',$tablaSpecs['columnas']).' FROM '.$tablaSpecs['schema'].'.'.$tablaSpecs['nombre'].' WHERE '.implode(' OR ', $wherePH);

            $statement = $conn->prepare($selectQuery);
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
                foreach($tablaSpecs['llaves'] as $llave):
                    $identArray[]=$linea[$llave];
                endforeach;
                $existente[implode('|',$identArray)]=$linea;
            endforeach;
        }

        return $existente;

    }



    public function dbPreProcess($conn,$tablaSpecs,$columnaSpecs,$valores,$existente){
         if(!empty($existente)||(empty($existente)&&$tablaSpecs['tipo']=='I')){
            foreach ($valores as $rowNumber => $row):
                $whereArray=array();
                $wherePH=array();
                $actArray=array();
                $actPH=array();
                $insertPH=array();
                $insertArray=array();
                foreach ($row as $col => $valor):
                    if(isset($columnaSpecs[$col]['nombre'])&&isset($columnaSpecs[$col]['llave'])){
                        if($columnaSpecs[$col]['llave']=='si' && empty($keysDiff)){
                            $wherePH[]=$columnaSpecs[$col]['nombre'].'= :'.$columnaSpecs[$col]['nombre'];
                            $whereArray[$columnaSpecs[$col]['nombre']]=$valor;
                        }else{
                            $actPH[]=$columnaSpecs[$col]['nombre'].'= :'.$columnaSpecs[$col]['nombre'];
                            $actArray[$columnaSpecs[$col]['nombre']]=$valor;
                        }
                        $insertPH[]=':'.$columnaSpecs[$col]['nombre'];
                        $insertArray[$columnaSpecs[$col]['nombre']]=$valor;
                    }
                endforeach;
                $mensajes[]=$this->dbProcess($conn,$rowNumber+1,$tablaSpecs,$wherePH,$whereArray,$actPH,$actArray,$insertPH,$insertArray,$existente);
            endforeach;
        }else{
            $mensajes[]="Solo se permite inserciones con tipo 'I'";
        }
        return $mensajes;


    }

    private function dbProcess($conn,$rowNumber,$tablaSpecs,$wherePH,$whereArray,$actPH,$actArray,$insertPH,$insertArray,$existente){

        $busqueda=implode('|',$whereArray);
        if(array_key_exists($busqueda,$existente)===true){
            if($tablaSpecs['tipo']=='U'||$tablaSpecs['tipo']=='UI'||$tablaSpecs['tipo']=='IU'){
                foreach ($whereArray as $whereKey => $whereValor):
                    unset($existente[$busqueda][$whereKey]);
                endforeach;
                $diferencia=array_diff_assoc($existente[$busqueda],$actArray);
                if(!empty($diferencia)){
                    $updateQuery='UPDATE '.$tablaSpecs['schema'].'.'.$tablaSpecs['nombre'].' SET '.implode(', ',$actPH).' WHERE '.implode(' AND ', $wherePH);//update
                    $statement = $conn->prepare($updateQuery);
                    foreach ($actArray as $actKey => $actValor):
                        $statement->bindValue($actKey,$actValor);
                    endforeach;
                    foreach ($whereArray as $whereKey => $whereValor):
                        $statement->bindValue($whereKey,$whereValor);
                    endforeach;
                    $statement->execute();
                    $mensaje= 'Actualizando para la linea: '.$rowNumber;
                }else{
                    $mensaje= 'Nada que actualizar para la linea: '.$rowNumber;
                }
            }else{
                $mensaje= 'La linea '.$rowNumber. ' ya existe, estamos en modo solo insertar';
            }
        }elseif(isset($insertArray)&&!empty($insertArray)){
            if($tablaSpecs['tipo']=='I'||$tablaSpecs['tipo']=='UI'||$tablaSpecs['tipo']=='IU'){
                $addQuery='INSERT INTO '.$tablaSpecs['schema'].'.'.$tablaSpecs['nombre'].' ('.implode(', ',$tablaSpecs['columnas']).') VALUES ('.implode(', ',$insertPH).')';
                //echo $addQuery;
                $statement = $conn->prepare($addQuery);
                foreach ($insertArray as $insertKey => $insertValor):
                    $statement->bindValue($insertKey,$insertValor);
                endforeach;
                try{
                    $statement->execute();
                    $mensaje= 'Agregando para la linea: '.$rowNumber;
                }catch(\Exception $e){
                    preg_match('/ORA-00001/', $e->getMessage(), $coincidencias, PREG_OFFSET_CAPTURE);
                    if(!empty($coincidencias)){
                        $mensaje= 'No se agrego la linea: '.$rowNumber.' (El registro ya existe)';
                    }else{
                        $mensaje= 'No se agrego la linea: '.$rowNumber.' ('.$e->getMessage().')';
                    }
                }

            }else{
                $mensaje= 'La linea '.$rowNumber. ' no existe, estamos en modo solo actualizar';
            }
        }else{
            return ('No se actualizo nada, los parametros son errados');
        }
        return $mensaje;
    }

}