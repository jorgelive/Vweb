<?php
namespace Gopro\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class BaseController extends Controller
{
    private $mensajes;

    private $stack;

    private $suma;

    /**
     * @param mixed $mensaje
     * @return boolean
     */
    protected function setMensajes($mensaje)
    {
        if (!is_array($this->mensajes)) {
            $this->mensajes = array();
        }
        if (is_array($mensaje)) {
            $this->mensajes = array_merge($this->mensajes, $mensaje);
            return true;
        } elseif (is_string($mensaje)) {

            $this->mensajes[] = $mensaje;
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
    protected function getUserName()
    {
        $usuario = $this->get('security.context')->getToken()->getUser();
        if (!is_string($usuario)) {
            $usuario = $usuario->getUsername();
        }
        return $usuario;
    }

    /**
     * @param mixed $valor
     * @param mixed $key
     * @param array $vars
     * @return \Gopro\MainBundle\Controller\BaseController
     */
    protected function setSumaForWalk($valor, $key, $vars)
    {

        if (empty($vars[1]) || $key == $vars[1]) {
            $this->setSuma($vars[0], $valor);
        }

        return $this;
    }

    /**
     * @param string $id
     * @param number $monto
     * @return \Gopro\MainBundle\Controller\BaseController
     */
    protected function setSuma($id, $monto)
    {
        if(!isset($this->suma[$id])){
            $this->suma[$id] = $monto;
        }else{
            $this->suma[$id] = $this->suma[$id] + $monto;
        }

        return $this;
    }


    /**
     * @param string $id
     * @return \Gopro\MainBundle\Controller\BaseController
     */
    protected function resetSuma($id)
    {
        $this->suma[$id] = 0;

        return $this;
    }

    /**
     * @param string $id
     * @return integer
     */
    protected function getSuma($id)
    {
        if (empty($this->suma[$id])) {
            return 0;
        }

        return $this->suma[$id];
    }

    /**
     * @param mixed $id
     * @return array
     */
    protected function getStack($id)
    {
        if(isset($this->stack[$id])){
            return $this->stack[$id];
        }else{
            return array();
        }

    }

    /**
     * @param string $nombreStack
     * @param mixed $valor
     * @param string $nombreIndice
     * @return \Gopro\MainBundle\Controller\BaseController
     */
    protected function setStack($nombreStack, $valor, $nombreIndice = null)
    {
        if (empty($nombreIndice)) {
            $this->stack[$nombreStack][] = $valor;
        } else {
            $this->stack[$nombreStack][][$nombreIndice] = $valor;
        }
        return $this;
    }

    /**
     * @param string $id
     * @return \Gopro\MainBundle\Controller\BaseController
     */
    protected function resetStack($id)
    {
        $this->stack[$id] = array();

        return $this;
    }

    /**
     * @param mixed $valor
     * @param mixed $key
     * @param mixed $vars
     * @return boolean
     */
    protected function setStackForWalk($valor, $key, $vars)
    {
        if (is_string($vars)) {
            $this->setStack($vars, $valor);
            return true;
        } elseif (is_array($vars) && (count($vars) == 2 || (count($vars) == 3 && $key == $vars[2]))) {
            $this->setStack($vars[0], $valor, $vars[1]);
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param array $contenedor
     * @param mixed $nombreStacks
     * @param mixed $keys
     * @param mixed $newKeyNames
     * @param mixed $formaters
     * @param boolean $resetStacks
     * @return boolean
     */
    protected function seekAndStack($contenedor, $nombreStacks, $keys, $newKeyNames = NULL, $formaters = NULL, $resetStacks = true)
    {
        if(!is_array($nombreStacks)){
            $nombreStacks = array($nombreStacks);
        }
        if(!is_array($keys)){
            $keys = array($keys);
        }

        if(!empty($newKeyNames) && !is_array($newKeyNames)){
            $newKeyNames = array($newKeyNames);
        }

        if(!empty($formaters) && !is_array($formaters)){
            $formaters = array($formaters);
        }

        if(count($nombreStacks) != count($keys)
            || (!empty($newKeyNames) && count($newKeyNames) != count($nombreStacks))
            || (!empty($formaters) && count($formaters) != count($nombreStacks))
        ){
            return false;
        }

        $i = 0;

        foreach ($contenedor as $k => $v) {

            foreach ($nombreStacks as $nroStack => $nombreStack){
                if ($i = 0 && ($resetStacks === true || !isset($this->stack[$nombreStack]))){
                    $this->resetStack($nombreStack . 'Aux');
                    $this->resetStack($nombreStack);
                }

                if(!is_callable($formaters[$nroStack])){
                    $formaters[$nroStack] = function($value){
                        return $value;
                    };
                }

                if ($keys[$nroStack] === $k) {
                    if (is_array($v)) {
                        foreach ($v as $subkey => $subv) {
                            //se descarta la infoirmacion de la llave
                            if (!is_array($subv)) {
                                if ($subv !== null && (!isset($this->stack[$nombreStack . 'Aux']) || !in_array($subv, $this->stack[$nombreStack . 'Aux']))) {
                                    $this->setStack($nombreStack, $formaters[$nroStack]($subv), $newKeyNames[$nroStack]);
                                    $this->setStack($nombreStack . 'Aux', $formaters[$nroStack]($subv));
                                    $i++;
                                }
                            }
                        }
                    } else {
                        if ($v !== null && (!isset($this->stack[$nombreStack . 'Aux']) || !in_array($formaters[$nroStack]($v), $this->stack[$nombreStack . 'Aux']))) {
                            $this->setStack($nombreStack, $formaters[$nroStack]($v), $newKeyNames[$nroStack]);
                            $this->setStack($nombreStack . 'Aux', $formaters[$nroStack]($v));
                            $i++;
                        }
                    }
                } elseif (is_array($v)) {
                    $this->seekAndStack($v, $nombreStacks, $keys, $newKeyNames, $formaters);
                }
            }
        }

        if ($i > 1) {
            return true;
        }
        return false;
    }

}