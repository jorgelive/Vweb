<?php

namespace Gopro\Vipac\CargadorBundle\Comun;
use \Symfony\Component\DependencyInjection\ContainerAware;


class Archivo extends ContainerAware{

    public function parseExcel($setTablaSpecs,$setColumnaSpecs,$filename='/Volumes/Archivo/prueba.xlsx'){
        if ($setTablaSpecs !==false){
            $tablaSpecs=$setTablaSpecs;
        }else{
            $tablaSpecs=array();
        }

        if ($setColumnaSpecs !==false){
            $columnaSpecs=$setColumnaSpecs;
        }else{
            $columnaSpecs=array();
        }

        $valores=array();
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
                            $columnaSpecs[$col][$valorArray[0]]=$valorArray[1];
                            if($valorArray[0]=='nombre'){
                                $tablaSpecs['columnas'][$col]=$valorArray[1];
                            }
                            if($valorArray[0]=='llave'&&$valorArray[1]=='si'&& isset($columnaSpecs[$col]['nombre'])){
                                $tablaSpecs['llaves'][$col]=$columnaSpecs[$col]['nombre'];
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
                    $valores[$arrayY][$col]=$value;
                }
            }
            $arrayY ++;
        }
        return compact('tablaSpecs','columnaSpecs','valores');
    }

}