<?php

namespace Gopro\Vipac\ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Campo
 *
 * @ORM\Table(name="rep_operador")
 * @ORM\Entity
 */
class Operador
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="nombre", type="string", length=50)
     */
    private $nombre;

    /**
     * @ORM\ManyToMany(targetEntity="Tipo", mappedBy="operadores")
     */
    private $tipos;

    public function __construct() {
        $this->tipos = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nombre;
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Operador
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set operador
     *
     * @param string $operador
     * @return Operador
     */
    public function setOperador($operador)
    {
        $this->operador = $operador;

        return $this;
    }

    /**
     * Get operador
     *
     * @return string 
     */
    public function getOperador()
    {
        return $this->operador;
    }

    /**
     * Add tipos
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Tipo $tipos
     * @return Operador
     */
    public function addTipo(\Gopro\Vipac\ReporteBundle\Entity\Tipo $tipos)
    {
        $this->tipos[] = $tipos;

        return $this;
    }

    /**
     * Remove tipos
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Tipo $tipos
     */
    public function removeTipo(\Gopro\Vipac\ReporteBundle\Entity\Tipo $tipos)
    {
        $this->tipos->removeElement($tipos);
    }

    /**
     * Get tipos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTipos()
    {
        return $this->tipos;
    }
}
