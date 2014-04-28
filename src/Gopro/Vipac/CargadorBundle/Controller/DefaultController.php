<?php

namespace Gopro\Vipac\CargadorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
        $conn = $this->get('doctrine.dbal.default_connection');

        $filename ='/Volumes/Archivo/prueba.xlsx';
        $excelLoader = $this->get('phpexcel');
        $objPHPExcel = $excelLoader->createPHPExcelObject( $filename);

        $total_sheets=$objPHPExcel->getSheetCount();

        $allSheetName=$objPHPExcel->getSheetNames();
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = $this->get('phpexcel')->columnIndexFromString($highestColumn);
        $valores=array();
        $columnaSpecs=array();
        $tablaSpecs=array();
        $arrayY=0;
        $specRow=false;
        $specRowType='';
        for ($row = 1; $row <= $highestRow;++$row)
        {
            $whereArray=array();
            $wherePH=array();
            $actArray=array();
            $actPH=array();
            $insertPH=array();
            $insertArray=array();
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
                        }

                    }if($specRowType=='T'){
                        $valorArray=explode(':',$value);
                        if(isset($valorArray[1])){
                            $tablaSpecs[$valorArray[0]]=$valorArray[1];
                        }

                    }


                }else{
                    $valores[$arrayY][$col]=$value; //era $row-1
                    if(isset($columnaSpecs[$col]['nombre'])&&isset($columnaSpecs[$col]['tipo'])&&isset($columnaSpecs[$col]['llave'])){
                        //if($columnaSpecs[$col]['tipo']=='cadena'){$comilla="'";}else{$comilla='';}
                        if($columnaSpecs[$col]['llave']=='si'){
                            $wherePH[]=$columnaSpecs[$col]['nombre'].'= :'.$columnaSpecs[$col]['nombre'];
                            $whereArray[$columnaSpecs[$col]['nombre']]=$value;
                        }else{
                            $actPH[]=$columnaSpecs[$col]['nombre'].'= :'.$columnaSpecs[$col]['nombre'];
                            $actArray[$columnaSpecs[$col]['nombre']]=$value;
                        }

                        $insertPH[]=':'.$columnaSpecs[$col]['nombre'];
                        $insertArray[$columnaSpecs[$col]['nombre']]=$value;
                    }


                }
            }
            if($specRow===true){
                //$arrayYSpecs ++;
            }else{
                $selectQuery='SELECT * FROM '.$tablaSpecs['schema'].'.'.$tablaSpecs['nombre'].' WHERE '.implode(' AND ', $wherePH);
                //echo $selectQuery;
                $statement = $conn->prepare($selectQuery);
                foreach ($whereArray as $whereKey => $whereValor):
                    $statement->bindValue($whereKey,$whereValor);
                endforeach;
                $statement->execute();
                $registro=$statement->fetchAll();
                if(isset($registro)&&!empty($registro[0])&&isset($whereArray)&&!empty($whereArray)){


                    foreach ($whereArray as $whereKey => $whereValor):
                       unset($registro[0][$whereKey]);
                    endforeach;
                    $diferencia=array_diff_assoc($registro[0],$actArray);
                    if(!empty($diferencia)){
                    //print_r($actArray);
                        $updateQuery='UPDATE '.$tablaSpecs['schema'].'.'.$tablaSpecs['nombre'].' SET '.implode(', ',$actPH).' WHERE '.implode(' AND ', $wherePH);//update
                        //echo $updateQuery;
                        $statement = $conn->prepare($updateQuery);
                        foreach ($actArray as $actKey => $actValor):
                            $statement->bindValue($actKey,$actValor);
                        endforeach;
                        foreach ($whereArray as $whereKey => $whereValor):
                            $statement->bindValue($whereKey,$whereValor);
                        endforeach;
                        $statement->execute();
                        $mensaje[]= 'Actualizando para la linea: '.$row;
                    }else{
                        $mensaje[]= 'Nada que actualizar para la linea: '.$row;
                    }
                    //var_dump($registro);
                }else{
                    $addQuery='INSERT INTO '.$tablaSpecs['schema'].'.'.$tablaSpecs['nombre'].' ('.implode(', ',$tablaSpecs['columnas']).') VALUES ('.implode(', ',$insertPH).')';
                    //echo $addQuery;
                    $statement = $conn->prepare($addQuery);
                    foreach ($insertArray as $insertKey => $insertValor):
                        $statement->bindValue($insertKey,$insertValor);
                    endforeach;
                    $statement->execute();
                    $mensaje[]= 'Agregando para la linea: '.$row;
                    //var_dump($registro);
                }
                $arrayY ++;
            }

        }
        return array('mensajes' => $mensaje);
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
