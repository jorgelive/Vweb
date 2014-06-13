<?php

namespace Gopro\Vipac\ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Campo
 *
 * @ORM\Table(name="rep_campo")
 * @ORM\Entity
 */
class Campo
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
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="nombremostrar", type="string", length=100)
     */
    private $nombremostrar;

    /**
     * @var string
     *
     * @ORM\Column(name="predeterminado", type="string", length=100, nullable=true)
     */
    private $predeterminado;

    /**
     * @ORM\ManyToOne(targetEntity="Sentencia", inversedBy="campos")
     */
    private $sentencia;

    /**
     * @ORM\ManyToOne(targetEntity="Tipo", inversedBy="campos")
     */
    private $tipo;

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
     * @return Campo
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
     * Set nombremostrar
     *
     * @param string $nombremostrar
     * @return Campo
     */
    public function setNombremostrar($nombremostrar)
    {
        $this->nombremostrar = $nombremostrar;

        return $this;
    }

    /**
     * Get nombremostrar
     *
     * @return string 
     */
    public function getNombremostrar()
    {
        return $this->nombremostrar;
    }

    /**
     * Set predeterminado
     *
     * @param string $predeterminado
     * @return Campo
     */
    public function setPredeterminado($predeterminado)
    {
        $this->predeterminado = $predeterminado;

        return $this;
    }

    /**
     * Get predeterminado
     *
     * @return string 
     */
    public function getPredeterminado()
    {
        return $this->predeterminado;
    }

    /**
     * Set sentencia
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Sentencia $sentencia
     * @return Campo
     */
    public function setSentencia(\Gopro\Vipac\ReporteBundle\Entity\Sentencia $sentencia = null)
    {
        $this->sentencia = $sentencia;

        return $this;
    }

    /**
     * Get sentencia
     *
     * @return \Gopro\Vipac\ReporteBundle\Entity\Sentencia 
     */
    public function getSentencia()
    {
        return $this->sentencia;
    }

    /**
     * Set tipo
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Tipo $tipo
     * @return Campo
     */
    public function setTipo(\Gopro\Vipac\ReporteBundle\Entity\Tipo $tipo = null)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return \Gopro\Vipac\ReporteBundle\Entity\Tipo 
     */
    public function getTipo()
    {
        return $this->tipo;
    }
}
