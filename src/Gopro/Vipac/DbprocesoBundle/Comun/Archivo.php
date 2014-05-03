<?php

namespace Gopro\Vipac\DbprocesoBundle\Comun;
use \Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


class Archivo extends ContainerAware{

    public function parseExcel($setTablaSpecs,$setColumnaSpecs,$filename){

        //$filename='/Volumes/Archivo/prueba.xlsx';


        if ($setTablaSpecs !==false){
            $tablaSpecs=$setTablaSpecs;
        }else{
            $tablaSpecs=array();
        }

        if ($setColumnaSpecs !==false){
            foreach($setColumnaSpecs as $columna):
                if(isset($columna['nombre'])){
                    $validCols[]=$columna['nombre'];

                    if (preg_match("/-/i", $columna['nombre'])) {
                        $nombres=explode('-',$columna['nombre']);

                    }else{
                        $nombres=array($columna['nombre']);
                    }

                    unset($columna['nombre']);
                    foreach($nombres as $nombre):
                        $columnaSpecs[$nombre]=$columna;
                        $columnaSpecs[$nombre]['nombre']=$nombre;
                    endforeach;

                }else{
                    $validCols[]='noProcess';
                }
            endforeach;
        }else{
            $columnaSpecs=array();
        }

        $valores=array();

        $fs = new Filesystem();

        if(empty($filename)||!$fs->exists($filename)){
            return compact('tablaSpecs','columnaSpecs','valores');

        }


        $excelLoader = $this->container->get('phpexcel');
        $objPHPExcel = $excelLoader->createPHPExcelObject( $filename);
        $total_sheets=$objPHPExcel->getSheetCount();
        $allSheetName=$objPHPExcel->getSheetNames();
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = $this->container->get('phpexcel')->columnIndexFromString($highestColumn);
        $arrayY=0;
        $specRow=false;
        $specRowType='';
        for ($row = 1; $row <= $highestRow;++$row)
        {
            $procesandoNombre=false;
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
                    if($specRowType=='C'&&$setColumnaSpecs===false){
                        $valorArray=explode(':',$value);
                        if(isset($valorArray[1])){
                            if($valorArray[0]=='nombre'){
                                $columnaSpecs[$valorArray[1]][$valorArray[0]]=$valorArray[1];
                                $tablaSpecs['columnas'][]=$valorArray[1];
                                $validCols[]=$valorArray[1];
                                $procesandoNombre=true;
                            }elseif($procesandoNombre===true){
                                $validCols[]='noProcess';
                            }elseif($procesandoNombre!==true&&isset($validCols)&&!empty($validCols)&&isset($validCols[$col])&&$validCols[$col]!='noProcess'){
                                $arrayKeys=array_keys($columnaSpecs);
                                $columnaSpecs[$validCols[$col]][$valorArray[0]]=$valorArray[1];
                            }

                            if($valorArray[0]=='llave'&&$valorArray[1]=='si'&&isset($validCols[$col])&&$validCols[$col]!='noProcess'){
                                $tablaSpecs['llaves'][]=$columnaSpecs[$validCols[$col]]['nombre'];
                            }
                        }
                    }
                    if($specRowType=='T'&&$setTablaSpecs===false){
                        $valorArray=explode(':',$value);
                        if(isset($valorArray[1])){
                            $tablaSpecs[$valorArray[0]]=$valorArray[1];
                        }

                    }
                }else{
                    if(isset($validCols)&&!empty($validCols)&&isset($validCols[$col])&&$validCols[$col]!='noProcess'){
                        $columnName=$validCols[$col];
                        if(preg_match("/-/i", $validCols[$col])){
                            $value=explode('-',$value);
                            $columnName=explode('-',$columnName);

                        }else{
                            $value=array($value);
                            $columnName=array($columnName);
                        }
                        foreach($value as $key => $parteValor):
                            if(isset($columnaSpecs[$columnName[$key]]['tipo'])&&$columnaSpecs[$columnName[$key]]['tipo']=='exceldate'){
                                $parteValor = date('d/m/Y', mktime(0,0,0,1,$parteValor-1,1900));
                                //$columnaSpecs[$validCols[$col]]['nombre'];
                            }
                            if(isset($columnaSpecs[$columnName[$key]]['tipo'])&&$columnaSpecs[$columnName[$key]]['tipo']=='file'&& $key==1){

                                $parteValor = str_pad($parteValor,10, 0, STR_PAD_LEFT);
                            }

                            $valores[$arrayY][$columnaSpecs[$columnName[$key]]['nombre']]=$parteValor;

                        endforeach;

                    }
                }
            }
            $arrayY ++;
        }

        return compact('tablaSpecs','columnaSpecs','valores');
    }

}