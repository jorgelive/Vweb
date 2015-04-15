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
     * @var \DateTime $creado
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $creado;

    /**
     * @var \DateTime $modificado
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $modificado;

    /**
     * @ORM\ManyToOne(targetEntity="Sentencia", inversedBy="campos")
     * @ORM\JoinColumn(name="sentencia_id", referencedColumnName="id", nullable=false)
     */
    private $sentencia;

    /**
     * @ORM\ManyToOne(targetEntity="Tipo", inversedBy="campos")
     * @ORM\JoinColumn(name="tipo_id", referencedColumnName="id", nullable=true)
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
     * Set creado
     *
     * @param \DateTime $creado
     * @return Campo
     */
    public function setCreado($creado)
    {
        $this->creado = $creado;

        return $this;
    }

    /**
     * Get creado
     *
     * @return \DateTime
     */
    public function getCreado()
    {
        return $this->creado;
    }

    /**
     * Set modificado
     *
     * @param \DateTime $modificado
     * @return Campo
     */
    public function setModificado($modificado)
    {
        $this->modificado = $modificado;

        return $this;
    }

    /**
     * Get modificado
     *
     * @return \DateTime
     */
    public function getModificado()
    {
        return $this->modificado;
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
