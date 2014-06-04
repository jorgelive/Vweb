<?php

namespace Gopro\ExcelBundle;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Factory for PHPExcel objects, StreamedResponse, and PHPExcel_Writer_IWriter.
 *
 * @package Gopro\ExcelBundle
 */
class Factory
{
    private $phpExcelIO;
    private $phpExcelCell;
    private $phpExcelStyle;

    public function __construct($phpExcelIO = '\PHPExcel_IOFactory',$phpExcelCell = '\PHPExcel_Cell')
    {
        $this->phpExcelIO = $phpExcelIO;
        $this->phpExcelCell = $phpExcelCell;
    }
    /**
     * Creates an empty PHPExcel Object if the filename is empty, otherwise loads the file into the object.
     *
     * @param string $filename
     *
     * @return \PHPExcel
     */
    public function createPHPExcelObject($filename =  null)
    {
        if (null == $filename) {
            $phpExcelObject = new \PHPExcel();

            return $phpExcelObject;
        }

        return call_user_func(array($this->phpExcelIO, 'load'), $filename);
    }

    /**
     * Creates an empty PHPExcel Object if the filename is empty, otherwise loads the file into the object.
     *
     * @param string $column
     *
     * @return \PHPExcel
     */
    public function columnIndexFromString($column =  null)
    {
        return call_user_func(array($this->phpExcelCell, 'columnIndexFromString'), $column);
    }

    /**
     * Creates an empty PHPExcel Object if the filename is empty, otherwise loads the file into the object.
     *
     * @param string $indice
     *
     * @return \PHPExcel
     */
    public function stringFromColumnIndex($indice =  null)
    {
        return call_user_func(array($this->phpExcelCell, 'stringFromColumnIndex'), $indice);
    }

    /**
     * Create a writer given the PHPExcelObject and the type,
     *   the type coul be one of PHPExcel_IOFactory::$_autoResolveClasses
     *
     * @param \PHPExcel $phpExcelObject
     * @param string    $type
     *
     *
     * @return \PHPExcel_Writer_IWriter
     */
    public function createWriter(\PHPExcel $phpExcelObject, $type = 'Excel5')
    {
        return call_user_func(array($this->phpExcelIO, 'createWriter'), $phpExcelObject, $type);
    }

    /**
     * Stream the file as Response.
     *
     * @param \PHPExcel_Writer_IWriter $writer
     * @param int                      $status
     * @param array                    $headers
     *
     * @return StreamedResponse
     */
    public function createStreamedResponse(\PHPExcel_Writer_IWriter $writer, $status = 200, $headers = array())
    {
        return new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            },
            $status,
            $headers
        );
    }
}
