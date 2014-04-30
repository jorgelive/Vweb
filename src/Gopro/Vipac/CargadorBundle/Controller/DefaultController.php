<?php

namespace Gopro\Vipac\CargadorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use \Doctrine\DBAL\Schema\Table;

class DefaultController extends Controller
{
    /**
     * @Route("/index/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        $conn = $this->get('doctrine.dbal.default_connection');
        $paises = $conn->fetchAll('SELECT * FROM reservas.pais');
        //print_r($array);
        //$sql = "SELECT * FROM reservas.paises WHERE";
        //$stmt = $this->connection->prepare($sql);
        //$stmt->execute();


        //return $bar;
        return array('paises' => $paises);
    }

    /**
     * @Route("/upload")
     * @Template()
     */
    public function uploadAction()
    {
        $conn = $this->get('doctrine.dbal.default_connection');
        $paises = $conn->fetchAll('SELECT * FROM reservas.pais');
        //print_r($array);
        //$sql = "SELECT * FROM reservas.paises WHERE";
        //$stmt = $this->connection->prepare($sql);
        //$stmt->execute();


        //return $bar;
        return array('paises' => $paises);
    }



    /**
     * @Route("/excelreader")
     * @Template()
     */
    public function excelreaderAction()
    {
        extract($this->parseExcel());

        if(isset($tablaSpecs['tipo'])&&in_array($tablaSpecs['tipo'],Array('IU','UI','I','U'))&&isset($valores)&&isset($tablaSpecs)&&isset($columnaSpecs)&&!empty($valores)&&!empty($tablaSpecs)&&!empty($columnaSpecs)){
            $mensajes=$this->dbPreProcesss($tablaSpecs,$columnaSpecs,$valores);
        }elseif(isset($tablaSpecs['tipo'])&&!in_array($tablaSpecs['tipo'],Array('IU','UI','I','U'))){
            $mensajes=array('No se definio correctamente el tipo de proceso');
        }else{
            $mensajes=array('No existe informacion necesaria para el proceso');
        }
        return array('mensajes' => $mensajes);
    }

    private function sanitizeString($str, $what=NULL, $with='')
    {
        if($what === NULL)
        {
            $what[] = "/[\\x00-\\x20]+/";
            $what[] = "/[']+/";
            $what[] = "/[(]+/";
            $what[] = "/[)]+/";
            $what[] = "/[-]+/";
            $with=array();
        }

        foreach ($what as $dummy):
            $with[]='';
        endForeach;

        $proceso = trim(preg_replace($what, $with, $str ));
        return $proceso;
    }


    private function dbPreProcesss($tablaSpecs,$columnaSpecs,$valores){
        $conn = $this->get('doctrine.dbal.default_connection');
        $query[]="SELECT cols.table_name, cols.column_name, cols.position, cons.status, cons.owner";
        $query[]="FROM all_constraints cons, all_cons_columns cols";
        $query[]="WHERE cols.table_name = '".$tablaSpecs['nombre']."' AND cons.constraint_type = 'P'";
        $query[]="AND cons.constraint_name = cols.constraint_name AND cons.owner = cols.owner";
        $query[]="AND cons.owner = '".$tablaSpecs['schema']."' ORDER BY cols.table_name, cols.position";
        //print_r(implode(' ',$query));
        $statement = $conn->query(implode(' ',$query));
        $keysArray = $statement->fetchAll();

        foreach($keysArray as $key):
            $keyInTable[]=$key['COLUMN_NAME'];
        endforeach;
        //$table = new \Doctrine\DBAL\Schema\Table('PRUEBA');
        //$keys=$table->getPrimaryKeyColumns();
        $keysDiff=array_diff($keyInTable,$tablaSpecs['llaves']);

        $existente=array();

        if (empty($keysDiff)){
            foreach ($valores as $rowNumber => $row):
                foreach ($row as $col => $valor):
                    if(isset($columnaSpecs[$col]['nombre'])&&isset($columnaSpecs[$col]['llave'])){
                        if($columnaSpecs[$col]['llave']=='si'){
                            $primaryKeysPH[$rowNumber][]=$columnaSpecs[$col]['nombre'].'= :'.$columnaSpecs[$col]['nombre'].$this->sanitizeString($valor);
                            $primaryKeys[$rowNumber][$columnaSpecs[$col]['nombre'].$this->sanitizeString($valor)]=$valor;
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
        }else{
            $mensajes[]="Las llaves primarias no coiciden con las definidas en la tabla";
        }

        if(!empty($existente)||(empty($existente) && $tablaSpecs['tipo']=='I')){
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

    private function parseExcel($filename='/Volumes/Archivo/prueba.xlsx'){
        $tablaSpecs=array();
        $columnaSpecs=array();
        $valores=array();
        $excelLoader = $this->get('phpexcel');
        $objPHPExcel = $excelLoader->createPHPExcelObject( $filename);
        $total_sheets=$objPHPExcel->getSheetCount();
        $allSheetName=$objPHPExcel->getSheetNames();
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = $this->get('phpexcel')->columnIndexFromString($highestColumn);
        $arrayY=0;
        $specRow=false;
        $specRowType='';
        for ($row = 1; $row <= $highestRow;++$row)
        {
            for ($col = 0; $col <$highestColumnIndex;++$col)
            {
                $value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                if ($col==0 && substr($value,0,1)=="&" && substr($value,3,1)=="&"){
                    $specRow=true;
                    if(substr($value,0,4)=="&ta&"){
                        $specRowType='T';
                        $value=substr($value, 4);
                    }elseif(substr($value,0,4)=="&co&"){
                        $specRowType='C';
                        $value=substr($value, 4);
                    }else{
                        $specRowType='';
                    }

                }elseif($col==0 && substr($value,0,1)!="&"){
                    $specRow=false;
                    $specRowType='';
                }
                if($specRow===true){
                    if($specRowType=='C'){
                        $valorArray=explode(':',$value);
                        if(isset($valorArray[1])){
                            $columnaSpecs[$col][$valorArray[0]]=$valorArray[1];
                            if($valorArray[0]=='nombre'){
                                $tablaSpecs['columnas'][$col]=$valorArray[1];
                            }
                            if($valorArray[0]=='llave'&&$valorArray[1]=='si'&& isset($columnaSpecs[$col]['nombre'])){
                                $tablaSpecs['llaves'][$col]=$columnaSpecs[$col]['nombre'];
                            }
                        }

                    }if($specRowType=='T'){
                        $valorArray=explode(':',$value);
                        if(isset($valorArray[1])){
                            $tablaSpecs[$valorArray[0]]=$valorArray[1];
                        }

                    }
                }else{
                    $valores[$arrayY][$col]=$value;
                }
            }
            $arrayY ++;
        }
        return compact('tablaSpecs','columnaSpecs','valores');
    }

    private function dbProcess($conn,$rowNumber,$tablaSpecs,$wherePH,$whereArray,$actPH,$actArray,$insertPH,$insertArray,$existente){

        /*if(!empty($whereArray)){
            $selectQuery='SELECT * FROM '.$tablaSpecs['schema'].'.'.$tablaSpecs['nombre'].' WHERE '.implode(' AND ', $wherePH);
            $statement = $conn->prepare($selectQuery);
            foreach ($whereArray as $whereKey => $whereValor):
                $statement->bindValue($whereKey,$whereValor);
            endforeach;
            $statement->execute();
            $registro=$statement->fetchAll();
            foreach ($whereArray as $whereKey => $whereValor):
                unset($registro[0][$whereKey]);
            endforeach;
        }*/
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


    /**
     * @Route("/excel")
     * @Template()
     */
    public function excelAction()
    {
        $conn = $this->get('doctrine.dbal.default_connection');
        $paises = $conn->fetchAll('SELECT * FROM reservas.pais');

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("Viapac")
            ->setTitle("Documento Generado")
            ->setDescription("Documento generado para descargar");
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Codigo')
            ->setCellValue('B1', 'Nombre');

        $phpExcelObject->setActiveSheetIndex(0)->fromArray($paises, NULL, 'A2');
        $phpExcelObject->getActiveSheet()->setTitle('Hoja de datos');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=archivo.xlsx');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }
}
