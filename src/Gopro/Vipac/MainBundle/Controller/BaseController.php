<?php
namespace Gopro\Vipac\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class BaseController extends Controller
{
    private $mensajes=array();

    private $montoTotal=0;

    protected function setMensajes($mensaje)
    {
        if(is_array($mensaje)){
            $this->mensajes=array_merge($this->mensajes,$mensaje);

        }elseif(is_string($mensaje)){
            $this->mensajes[]=$mensaje;
        }
    }

    protected function getMensajes()
    {
        return $this->mensajes;
    }

    protected function getUserName(){
        $usuario=$this->get('security.context')->getToken()->getUser();
        if(!is_string($usuario)){
            $usuario=$usuario->getUsername();
        }
        return $usuario;
    }

    protected function setMonto($valor,$key,$condicion)
    {
        if($condicion===null||$key==$condicion){
            $this->montoTotal=$this->montoTotal+$valor;
        }
    }

    protected function resetMonto()
    {
        $this->montoTotal=0;
    }

    protected function getMonto()
    {
        return $this->montoTotal;
    }
}