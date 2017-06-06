<?php
namespace Gopro\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class BaseController extends Controller
{
    private $mensajes;

    private $stack;

    private $cantidadTotal;

    /**
     * @param mixed $mensaje
     * @return boolean
     */
    protected function setMensajes($mensaje)
    {
        if(!is_array($this->mensajes)){$this->mensajes=array();}
        if(is_array($mensaje)){
            $this->mensajes=array_merge($this->mensajes,$mensaje);
            return true;
        }elseif(is_string($mensaje)){

            $this->mensajes[]=$mensaje;
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    protected function getMensajes()
    {
        return $this->mensajes;
    }

    /**
     * @return string
     */
    protected function getUserName(){
        $usuario=$this->get('security.context')->getToken()->getUser();
        if(!is_string($usuario)){
            $usuario=$usuario->getUsername();
        }
        return $usuario;
    }

    /**
     * @param mixed $valor
     * @param mixed $key
     * @param array $vars
     * @return boolean
     */
    protected function setCantidadTotal($valor,$key,$vars)
    {
        if(empty($this->cantidadTotal[$vars[0]])){
            $this->cantidadTotal[$vars[0]]=0;
        }
        if( empty($vars[1]) || $key == $vars[1] ){
            $this->cantidadTotal[$vars[0]] = $this->cantidadTotal[$vars[0]] + $valor;
            return true;
        }

        return false;
    }

    /**
     * @param string $id
     * @return integer
     */
    protected function resetCantidadTotal($id)
    {
        $this->cantidadTotal[$id] = 0;

        return $this;
    }

    /**
     * @param string $id
     * @return integer
     */
    protected function getCantidadTotal($id)
    {
        if(empty($this->cantidadTotal[$id])){
            return 0;
        }

        return $this->cantidadTotal[$id];
    }

    /**
     * @param mixed $id
     * @return array
     */
    protected function getStack($id){
        return $this->stack[$id];
    }

    /**
     * @param mixed $valor
     * @param mixed $key
     * @param mixed $vars
     * @return boolean
     */
    protected function setStack($valor,$key,$vars){
        if(is_string($vars)){
            $this->stack[$vars][]=$valor;
            return true;
        }elseif(is_array($vars)&&count($vars)==2){

            $this->stack[$vars[0]][][$vars[1]]=$valor;
            return true;
        }elseif(is_array($vars)&&count($vars)==3&&$key==$vars[2]){
            $this->stack[$vars[0]][][$vars[1]]=$valor;
            return true;

        }
        return false;

    }

    protected function seekAndStack($contenedor, $nombreStack, $key, $newKeyName = NULL) {
        $i = 0;
        foreach ($contenedor as $k => $v) {
            if ($key == $k){
                if(is_array($v)){
                    foreach($v as $subkey => $subv){
                        if (is_numeric($subkey)){
                            if(!isset($this->stack[$nombreStack.'Aux']) || !in_array($subv,$this->stack[$nombreStack.'Aux'])){
                                $this->stack[$nombreStack][][$newKeyName]=$subv;
                                $i++;
                            }
                            $this->stack[$nombreStack.'Aux']=[$subv];
                        }
                    }
                }else{
                    if(!isset($this->stack[$nombreStack.'Aux']) || !in_array($v,$this->stack[$nombreStack.'Aux'])) {
                        $this->stack[$nombreStack][][$newKeyName] = $v;
                        $i++;
                    }
                    $this->stack[$nombreStack.'Aux']=[$v];
                }
            }elseif (is_array($v)){
                $this->seekAndStack($v, $nombreStack, $key, $newKeyName);
            }
        }
        if($i>1){
            return true;
        }
        return false;
    }


}