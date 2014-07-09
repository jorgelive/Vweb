<?php

namespace Gopro\MainBundle\Service;
use \Symfony\Component\DependencyInjection\ContainerAware;


class Variableproceso extends ContainerAware{


    public function sanitizeString($str, $what=NULL, $with='')
    {
        if($what === NULL)
        {
            $what[] = "/[\\x00-\\x20]+/";
            $what[] = "/[']+/";
            $what[] = "/[(]+/";
            $what[] = "/[)]+/";
            $what[] = "/[-]+/";
            $what[] = "/[+]+/";
            $what[] = "/[*]+/";
            $what[] = "/[,]+/";
            $what[] = "/[\/]+/";
            $what[] = "/[\\\\]+/";
            $what[] = "/[?]+/";
            $with=array();
        }

        foreach ($what as $dummy):
            $with[]='';
        endForeach;

        $proceso = trim(preg_replace($what, $with, $str));
        return $proceso;
    }

    public function sanitizeQuery($query, $tipo='select')
    {
        if($tipo=='select'){
            $what[] = "/insert/i";
            $what[] = "/update/i";
        }
        $what[] = "/;/";
        $what[] = '/"/';
        $with=array();

        foreach ($what as $dummy):
            $with[]='';
        endForeach;

        $what[] = "/\s/";
        $with[] = ' ';

        $proceso = trim(preg_replace($what, $with, $query));
        return preg_replace('/\s+/', ' ', $proceso);;
    }

    public function utf($variable,$tipo='to')
    {
        $encodings[]='Windows-1250';
        $encodings[]='UTF-8';
        if($tipo!='to'){
            array_reverse($encodings);
        }
        if(is_string($variable)){
            return iconv($encodings[0], $encodings[1], $variable);
        }elseif(is_array($variable)){
            array_walk_recursive(
                $variable,
                function (&$entry,$key,$encodings) {
                    $entry = iconv($encodings[0], $encodings[1], $entry);
                },
                $encodings
            );
            return $variable;
        }
        return null;
    }

    public function exceldate($variable,$tipo='from')
    {
        if($tipo=='from'){
            return date('Y-m-d', mktime(0,0,0,1,$variable-1,1900));
        }else{
            return unixtojd(strtotime($variable)) - gregoriantojd(1, 1, 1900) + 2;
        }

    }

    public function is_multi_array($array) {
        return (count($array) != count($array, 1));
    }


}