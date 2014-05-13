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
}