<?php

namespace Gopro\Vipac\DbprocesoBundle\Comun;
use \Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


class Archivo extends ContainerAware{

    private $archivo;
    private $mensajes=array();
    private $setTablaSpecs;
    private $setColumnaSpecs;
    private $validCols;
    private $parsed;
    private $descartarBlanco;

    private $tablaSpecs;
    private $columnaSpecs;
    private $existentesRaw;
    private $existentesIndizados;
    private $existentesIndizadosMulti;
    private $existentesIndizadosKp;
    private $existentesIndizadosMultiKp;
    private $existentesCustomRaw;
    private $existentesCustomIndizados;
    private $existentesCustomIndizadosMulti;
    private $existentesDescartados;
    private $camposCustom;
    private $archivoValido;

//generado
    private $archivoGenerado;
    private $nombre;
    private $tipo;
    private $contenido;
    private $encabezado;
    private $formatoColumna;
    private $anchoColumna;
    private $celdas;

    public function getTablaSpecs(){
        return $this->tablaSpecs;
    }

    public function getColumnaSpecs(){
        return $this->columnaSpecs;
    }

    public function setArchivoValido($archivoValido){
        $this->archivoValido=$archivoValido;
        return $this;
    }

    public function getArchivoValido(){
        return $this->archivoValido;
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

    public function getDescartarBlanco(){
        return $this->descartarBlanco;
    }

    public function setDescartarBlanco($descartarBlanco){
        $this->descartarBlanco=$descartarBlanco;
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

    public function getExistentesIndizadosKp(){
        return $this->existentesIndizadosKp;
    }

    private function setExistentesIndizadosKp($existentesIndizadosKp){
        $this->existentesIndizadosKp=$existentesIndizadosKp;
        return $this;
    }

    public function getExistentesIndizadosMultiKp(){
        return $this->existentesIndizadosMultiKp;
    }

    private function setExistentesIndizadosMultiKp($existentesIndizadosMultiKp){
        $this->existentesIndizadosMultiKp=$existentesIndizadosMultiKp;
        return $this;
    }

    private function setExistentesRaw($existentesRaw){
        $this->existentesRaw=$existentesRaw;
        return $this;
    }

    public function setCamposCustom($campos){
        $this->camposCustom=$campos;
        return $this;
    }

    public function getCamposCustom(){
        return $this->camposCustom;
    }

    public function getExistentesCustomIndizados(){
        return $this->existentesCustomIndizados;
    }

    private function setExistentesCustomIndizados($existentesCustomIndizados){
        $this->existentesCustomIndizados=$existentesCustomIndizados;
        return $this;
    }

    public function getExistentesCustomIndizadosMulti(){
        return $this->existentesCustomIndizadosMulti;
    }

    private function setExistentesCustomIndizadosMulti($existentesCustomIndizadosMulti){
        $this->existentesCustomIndizadosMulti=$existentesCustomIndizadosMulti;
        return $this;
    }

    public function getExistentesCustomRaw(){
        return $this->existentesCustomRaw;
    }

    private function setExistentesCustomRaw($existentesCustomRaw){
        $this->existentesCustomRaw=$existentesCustomRaw;
        return $this;
    }

    public function getExistentesDescartados(){
        return $this->existentesDescartados;
    }

    public function setExistentesDescartados($existentesDescartados){
        $this->existentesDescartados=$existentesDescartados;
        return $this;
    }

    public function setParametrosReader($setTablaSpecs,$setColumnaSpecs){
        if(empty($this->getArchivoValido())){
            $this->setMensajes('El archivo no existe');
            return false;
        }
        $this->setTableSpecs=$setTablaSpecs;
        $this->setColumnaSpecs=$setColumnaSpecs;
        if(!is_null($setTablaSpecs)&&!isset($setTablaSpecs['tipo'])){
            $setTablaSpecs['tipo']='S';
        }
        if (!is_null($setTablaSpecs)){
            $this->tablaSpecs=$setTablaSpecs;
        }else{
            $this->tablaSpecs=array();
        }
        if (!is_null($setColumnaSpecs)){
            foreach($setColumnaSpecs as $columna):
                if(isset($columna['nombre'])){
                    $this->validCols[]=$columna['nombre'];
                    if (preg_match("/-/i", $columna['nombre'])) {
                        $nombres=explode('-',$columna['nombre']);

                    }else{
                        $nombres=array($columna['nombre']);
                    }
                    unset($columna['nombre']);
                    foreach($nombres as $nombre):
                        $this->columnaSpecs[$nombre]=$columna;
                        $this->columnaSpecs[$nombre]['nombre']=$nombre;
                        $this->tablaSpecs['columnas'][]=$nombre;
                        if(!isset($columna['proceso'])||(isset($columna['proceso'])&&$columna['proceso']=='si')){
                            $this->tablaSpecs['columnasProceso'][]=$nombre;
                        }
                        if(isset($columna['llave'])&&$columna['llave']=='si'){
                            $this->tablaSpecs['llaves'][]=$nombre;
                        }
                    endforeach;

                }else{
                    $this->validCols[]='noProcess';
                }
            endforeach;
        }else{
            $this->columnaSpecs=array();
            $this->validCols=array();
        }
        return true;
    }

    public function parseExcel(){

        if($this->parsed=='si'){
            $this->setMensajes('El archivo ya fue procesado anteriormente');
            return true;
        }
        $this->parsed='si';

        $excelLoader = $this->container->get('phpexcel');
        $objPHPExcel = $excelLoader->createPHPExcelObject($this->getArchivoValido()->getAbsolutePath());
        $total_sheets=$objPHPExcel->getSheetCount();
        $allSheetName=$objPHPExcel->getSheetNames();
        $hoja = $objPHPExcel->setActiveSheetIndex(0);
        $highestRow = $hoja->getHighestRow();
        $highestColumn = $hoja->getHighestColumn();
        $highestColumnIndex = $excelLoader->columnIndexFromString($highestColumn);
        $specRow=false;
        $specRowType='';
        $existentesRaw=array();
        $existentesIndizados=array();
        $existentesIndizadosMulti=array();
        $existentesIndizadosKp=array();
        $existentesIndizadosMultiKp=array();
        $fila=0;
        for ($row = 1; $row <= $highestRow;++$row)
        {
            $procesandoNombre=false;
            for ($col = 0; $col <$highestColumnIndex;++$col)
            {
                $value=$hoja->getCellByColumnAndRow($col, $row)->getValue();
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
                    if($specRowType=='C'&&is_null($this->setColumnaSpecs)){
                        $valorArray=explode(':',$value);
                        if(isset($valorArray[1])){
                            if($valorArray[0]=='nombre'){
                                $this->validCols[]=$valorArray[1];
                                if(preg_match("/-/i", $valorArray[1])){
                                    $nombres=explode('-',$valorArray[1]);
                                }else{
                                    $nombres=array($valorArray[1]);
                                }
                                foreach($nombres as $nombre):
                                    $this->columnaSpecs[$nombre]['nombre']=$nombre;
                                    $this->tablaSpecs['columnas'][]=$nombre;
                                    $this->tablaSpecs['columnasProceso'][]=$nombre;
                                endforeach;
                                $procesandoNombre=true;
                            }elseif($procesandoNombre===true){
                                $this->validCols[]='noProcess';
                            }elseif($procesandoNombre!==true&&!empty($this->validCols)&&isset($this->validCols[$col])&&$this->validCols[$col]!='noProcess'){
                                if(preg_match("/-/i", $this->validCols[$col])){
                                    $nombres=explode('-',$this->validCols[$col]);
                                }else{
                                    $nombres=array($this->validCols[$col]);
                                }
                                foreach($nombres as $nombre):
                                    $this->columnaSpecs[$nombre][$valorArray[0]]=$valorArray[1];
                                endforeach;
                            }
                            if($valorArray[0]=='llave'&&$valorArray[1]=='si'&&isset($this->validCols[$col])&&$this->validCols[$col]!='noProcess'){
                                if(preg_match("/-/i", $this->validCols[$col])){
                                    $nombres=explode('-',$this->validCols[$col]);
                                }else{
                                    $nombres=array($this->validCols[$col]);
                                }
                                foreach($nombres as $nombre):
                                    $this->tablaSpecs['llaves'][]=$this->columnaSpecs[$nombre]['nombre'];
                                endforeach;
                            }
                            if($valorArray[0]=='proceso'&&$valorArray[1]=='no'&&isset($this->validCols[$col])&&$this->validCols[$col]!='noProcess'){
                                if(preg_match("/-/i", $this->validCols[$col])){
                                    $nombres=explode('-',$this->validCols[$col]);
                                }else{
                                    $nombres=array($this->validCols[$col]);
                                }
                                foreach($nombres as $nombre):
                                    $encontrado=array_search($this->columnaSpecs[$nombre]['nombre'], $this->tablaSpecs['columnasProceso'],true);
                                    if($encontrado!==false){
                                        unset($this->tablaSpecs['columnasProceso'][$encontrado]);
                                    }
                                endforeach;
                            }
                        }
                    }
                    if($specRowType=='T'&&is_null($this->setTablaSpecs)){
                        $valorArray=explode(':',$value);
                        if(isset($valorArray[1])){
                            $this->tablaSpecs[$valorArray[0]]=$valorArray[1];
                        }
                    }
                }else{
                    if(!empty($this->validCols)&&isset($this->validCols[$col])&&$this->validCols[$col]!='noProcess'){
                        $columnName=$this->validCols[$col];
                        if(preg_match("/-/i", $this->validCols[$col])){
                            $value=explode('-',$value);
                            $columnName=explode('-',$columnName);
                        }else{
                            $value=array($value);
                            $columnName=array($columnName);
                        }
                        foreach($value as $key => $parteValor):
                            if(isset($this->columnaSpecs[$columnName[$key]]['tipo'])&&$this->columnaSpecs[$columnName[$key]]['tipo']=='exceldate'){
                                $parteValor = $this->container->get('gopro_dbproceso_comun_variable')->exceldate($parteValor);
                            }
                            if(isset($this->columnaSpecs[$columnName[$key]]['tipo'])&&$this->columnaSpecs[$columnName[$key]]['tipo']=='file'&& $key==1){
                                $parteValor = str_pad($parteValor,10, 0, STR_PAD_LEFT);
                            }
                            if(trim($parteValor)!=''||empty($this->getDescartarBlanco())){
                                $existentesRaw[$fila][$this->columnaSpecs[$columnName[$key]]['nombre']]=$parteValor;
                            }
                        endforeach;
                    }else{
                        if(trim($value)!=''||empty($this->getDescartarBlanco())){
                            $existentesDescartados[$fila][]=$value;
                        }
                    }
                }
            }
            $fila ++;

        }

        if(empty($existentesRaw)){
            $this->setMensajes('No hay valores que procesar');
            return false;
        }

        foreach ($existentesRaw as $nroLinea=>$valor):
            $indice=array();
            $llavesSave=array();
            foreach($this->tablaSpecs['llaves'] as $llave):
                $indice[]=$valor[$llave];
                $llavesSave[$llave]=$valor[$llave];
                unset($valor[$llave]);
            endforeach;
            $existentesIndizados[implode('|',$indice)]=$valor;
            $existentesIndizadosMulti[implode('|',$indice)][]=$valor;
            $existentesIndizadosKp[implode('|',$indice)]=array_merge($llavesSave,$valor);
            $existentesIndizadosMultiKp[implode('|',$indice)][]=array_merge($llavesSave,$valor);
            if(!empty($this->getCamposCustom())){
                $i=0;
                foreach($this->getCamposCustom() as $llaveCustom):
                    if(isset($valor[$llaveCustom])){
                        $existentesCustomIndizadosMulti[implode('|',$indice)][$i][$llaveCustom]=$valor[$llaveCustom];
                        $existentesCustomIndizados[implode('|',$indice)][$llaveCustom]=$valor[$llaveCustom];

                    }
                    $i++;
                endforeach;
            }
            if(!empty($this->getCamposCustom())){
                foreach($this->getCamposCustom() as $llaveCustom):
                    if(isset($valor[$llaveCustom])){
                        $existentesCustomRaw[$nroLinea][$llaveCustom]=$valor[$llaveCustom];
                    }
                endforeach;
            }
        endforeach;

        $this->setExistentesRaw($existentesRaw);

        $this->setExistentesIndizados($existentesIndizados);
        $this->setExistentesIndizadosMulti($existentesIndizadosMulti);
        $this->setExistentesIndizadosKp($existentesIndizadosKp);
        $this->setExistentesIndizadosMultiKp($existentesIndizadosMultiKp);
        if(!empty($existentesCustomIndizados)){
            $this->setExistentesCustomIndizados($existentesCustomIndizados);
        }

        if(!empty($existentesCustomIndizadosMulti)){
            $this->setExistentesCustomIndizadosMulti($existentesCustomIndizadosMulti);
        }
        if(!empty($existentesCustomRaw)){
            $this->setExistentesCustomRaw($existentesCustomRaw);
        }
        if(!empty($existentesDescartados)){
            $this->setExistentesDescartados($existentesDescartados);
        }
        return true;
    }



    public function setParametrosWriter($nombre,$encabezado=null,$contenido){
        $this->setNombre($nombre);
        if($encabezado!=null){
            $this->setEncabezado($encabezado);
        }

        $this->setContenido($contenido);
        $this->setTipo();
    }

    public function setEncabezado($encabezado){
        $this->encabezado=$encabezado;
        return $this;
    }

    public function getEncabezado(){
        return $this->encabezado;
    }

    public function setContenido($contenido){
        $this->contenido=$contenido;
        return $this;
    }

    public function getContenido(){
        return $this->contenido;
    }

    public function setTipo($tipo='xlsx'){
        $this->tipo=$tipo;
        return $this;
    }

    public function getTipo(){
        return $this->tipo;
    }

    public function setNombre($nombre='archivoGenerado'){
        $this->nombre=$nombre;
        return $this;
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function getArchivoGenerado(){
        return $this->archivoGenerado;
    }

    public function setFormatoColumna($formatoColumna){
        $this->formatoColumna=$formatoColumna;
        return $this;
    }

    public function getFormatoColumna(){
        return $this->formatoColumna;
    }

    public function setAnchoColumna($anchoColumna){
        $this->anchoColumna=$anchoColumna;
        return $this;
    }

    public function getAnchoColumna(){
        return $this->anchoColumna;
    }

    public function setCeldas($celdas){
        $this->celdas=$celdas;
        return $this;
    }

    public function getCeldas(){
        return $this->celdas;
    }

    public function setArchivoGenerado(){
        $excelWriter = $this->container->get('phpexcel');
        if(!empty($this->getArchivoValido())){
            $phpExcelObject = $excelWriter->createPHPExcelObject($this->getArchivoValido()->getAbsolutePath());
        }else{
            $phpExcelObject = $excelWriter->createPHPExcelObject();
        }
        $phpExcelObject->getProperties()->setCreator("Viapac")
            ->setTitle("Documento Generado")
            ->setDescription("Documento generado para descargar");
        $hoja=$phpExcelObject->setActiveSheetIndex(0);
        $filaBase=1;
        if(!empty($this->getEncabezado())){
            foreach($this->getEncabezado() as $key=>$encabezado):
                $columna = $excelWriter->stringFromColumnIndex($key);
                $hoja->setCellValue($columna.'1', $encabezado);
            endforeach;
            $filaBase=2;
        }
        $hoja->fromArray($this->getContenido(), NULL, 'A'.$filaBase);

        if(!empty($this->getFormatoColumna())&&$this->container->get('gopro_dbproceso_comun_variable')->is_multi_array($this->getFormatoColumna())){
            $highestRow = $hoja->getHighestRow();
            foreach($this->getFormatoColumna() as $formato => $columnas):
                foreach($columnas as $columna):
                    if (strpos($columna, ':') !== false){
                        $columna=explode(':',$columna,2);
                        if(is_numeric($columna[0])&&(is_numeric($columna[1])||empty($columna[1]))){
                            if(empty($columna[1])){
                                $columna[1]=$excelWriter->columnIndexFromString($hoja->getHighestColumn());
                            }
                            foreach(range($columna[0], $columna[1]) as $columnaProceso) {
                                $columnaString=$excelWriter->stringFromColumnIndex($columnaProceso);
                                $hoja->getStyle($columna.$filaBase.':'.$columna.$highestRow)
                                    ->getNumberFormat()
                                    ->setFormatCode($columnaString);
                            }
                        }

                    }else{
                        if(is_numeric($columna)){
                            $columna=$excelWriter->stringFromColumnIndex($columna);
                        }
                        $hoja->getStyle($columna.$filaBase.':'.$columna.$highestRow)
                            ->getNumberFormat()
                            ->setFormatCode($formato);
                    }
                endforeach;
            endforeach;
        }

        if(!empty($this->getAnchoColumna())&&is_array($this->getAnchoColumna())){
            foreach($this->getAnchoColumna() as $columna => $ancho):
                if (strpos($columna, ':') !== false){
                    $columna=explode(':',$columna,2);
                    if(is_numeric($columna[0])&&(is_numeric($columna[1])||empty($columna[1]))){
                        if(empty($columna[1])){
                            $columna[1]=$excelWriter->columnIndexFromString($hoja->getHighestColumn());
                        }
                        foreach(range($columna[0], $columna[1]) as $columnaProceso) {
                            $columnaString=$excelWriter->stringFromColumnIndex($columnaProceso);
                            if(is_numeric($ancho)){
                                $hoja->getColumnDimension($columnaString)->setWidth($ancho);
                            }elseif($ancho=='auto'){
                                $hoja->getColumnDimension($columnaString)->setAutoSize(true);
                            }
                        }
                    }
                }else{
                    if(is_numeric($columna)){
                        $columna=$excelWriter->stringFromColumnIndex($columna);
                    }
                    if(is_numeric($ancho)){
                        $hoja->getColumnDimension($columna)->setWidth($ancho);
                    }elseif($ancho=='auto'){
                        $hoja->getColumnDimension($columna)->setAutoSize(true);
                    }
                }
            endforeach;
        }

        if(!empty($this->getCeldas())&&$this->container->get('gopro_dbproceso_comun_variable')->is_multi_array($this->getCeldas())){
            if(!empty($this->getCeldas()['texto'])){
                foreach($this->getCeldas()['texto'] as $celda => $valor):
                        $hoja->setCellValueExplicit($celda,$valor,'s');
                endforeach;
            }
        }

        $phpExcelObject->getActiveSheet()->setTitle('Hoja de datos');
        $phpExcelObject->setActiveSheetIndex(0);
        $writer = $this->container->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
        $response = $this->container->get('phpexcel')->createStreamedResponse($writer);
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename='.$this->container->get('gopro_dbproceso_comun_variable')->sanitizeString($this->getNombre().'.'.$this->getTipo()));
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $this->archivoGenerado=$response;
        return $this;
    }

    public function validarArchivo($repositorio,$id,$funcionArchivo){
        $ejecutar=false;
        if($id!==null){
            $archivoAlmacenado=$repositorio->find($id);
        }

        if(!empty($archivoAlmacenado)&&$archivoAlmacenado->getOperacion()==$funcionArchivo){
            $this->setArchivoValido($archivoAlmacenado);
            $ejecutar=true;
        }
        $fs = new Filesystem();

        if(!is_object($this->getArchivoValido())||empty($this->getArchivoValido()->getAbsolutePath())||!$fs->exists($this->getArchivoValido()->getAbsolutePath())){
            $this->setMensajes('El archivo no existe');
            return false;
        }
        if($ejecutar===true){
            $this->archivoValido=$archivoAlmacenado;
        }
        return $ejecutar;
    }
}