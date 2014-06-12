<?php

namespace Gopro\Vipac\ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sentencia
 *
 * @ORM\Table(name="rep_sentencia")
 * @ORM\Entity
 */
class Sentencia
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
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var text
     *
     * @ORM\Column(name="contenido", type="text")
     */
    private $contenido;

    /**
     * @ORM\OneToMany(targetEntity="Campo", mappedBy="sentencia", cascade={"persist"})
     */
    private $campos;

    public function __construct() {
        $this->campos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Sentencia
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return Sentencia
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set contenido
     *
     * @param string $contenido
     * @return Sentencia
     */
    public function setContenido($contenido)
    {
        $this->contenido = $contenido;

        return $this;
    }

    /**
     * Get contenido
     *
     * @return string 
     */
    public function getContenido()
    {
        return $this->contenido;
    }

    /**
     * Add campos
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Campo $campos
     * @return Sentencia
     */
    public function addCampo(\Gopro\Vipac\ReporteBundle\Entity\Campo $campos)
    {
        $this->campos[] = $campos;

        return $this;
    }

    /**
     * Remove campos
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Campo $campos
     */
    public function removeCampo(\Gopro\Vipac\ReporteBundle\Entity\Campo $campos)
    {
        $this->campos->removeElement($campos);
    }

    /**
     * Get campos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCampos()
    {
        return $this->campos;
    }

}
