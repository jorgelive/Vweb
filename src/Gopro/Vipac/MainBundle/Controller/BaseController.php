<?php
namespace Gopro\Vipac\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class BaseController extends Controller
{
    private $mensajes;

    private $stack;

    private $montoTotal;

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
     * @param mixed $condicion
     * @return boolean
     */
    protected function setMontoTotal($valor,$key,$condicion)
    {
        if(empty($this->montoTotal)){$this->montoTotal=0;}
        if($condicion===null||$key==$condicion){
            $this->montoTotal=$this->montoTotal+$valor;
            return true;
        }
        return false;
    }

    /**
     * @return integer
     */
    protected function resetMontoTotal()
    {
        $this->montoTotal=0;
        return $this->montoTotal;
    }

    /**
     * @return integer
     */
    protected function getMontoTotal()
    {
        return $this->montoTotal;
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
        }
        return false;

    }
}