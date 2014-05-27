<?php
namespace Gopro\Vipac\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class BaseController extends Controller
{
    private $mensajes=array();

    public function setMensajes($mensaje)
    {
        if(is_array($mensaje)){
            $this->mensajes=array_merge($this->mensajes,$mensaje);

        }elseif(is_string($mensaje)){
            $this->mensajes[]=$mensaje;
        }
    }

    public function getMensajes()
    {
        return $this->mensajes;
    }

    public function getUserName(){
        $usuario=$this->get('security.context')->getToken()->getUser();
        if(!is_string($usuario)){
            $usuario=$usuario->getUsername();
        }
        return $usuario;
    }
}