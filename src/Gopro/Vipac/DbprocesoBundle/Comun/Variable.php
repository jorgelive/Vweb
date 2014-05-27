<?php

namespace Gopro\Vipac\DbprocesoBundle\Comun;
use \Symfony\Component\DependencyInjection\ContainerAware;


class Variable extends ContainerAware{


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

        $proceso = trim(preg_replace($what, $with, $str ));
        return $proceso;
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

    public function is_multi_array($array) {
        return (count($array) != count($array, 1));
    }
}