<?php

namespace Gopro\Vipac\ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @Assert\NotBlank
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var text
     * @Assert\NotBlank
     * @ORM\Column(name="contenido", type="text")
     */
    private $contenido;

    /**
     * @var datetime $creado
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $creado;

    /**
     * @var datetime $modificado
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $modificado;

    /**
     * @ORM\ManyToMany(targetEntity="Gopro\UserBundle\Entity\Area")
     * @ORM\JoinTable(name="rep_sentencias_areas")
     *
     */
    protected $areas;

    /**
     * @ORM\OneToMany(targetEntity="Campo", mappedBy="sentencia", cascade={"persist","remove"})
     */
    private $campos;

    /**
     * @ORM\OneToMany(targetEntity="Parametro", mappedBy="sentencia", cascade={"persist","remove"})
     */
    private $parametros;

    public function __construct() {
        $this->campos = new ArrayCollection();
        $this->areas = new ArrayCollection();
        $this->parametros = new ArrayCollection();
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
     * Set creado
     *
     * @param \DateTime $creado
     * @return Sentencia
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
     * @return Sentencia
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
     * Add areas
     *
     * @param \Gopro\UserBundle\Entity\Area $areas
     * @return Sentencia
     */
    public function addArea(\Gopro\UserBundle\Entity\Area $areas)
    {
        $this->areas[] = $areas;

        return $this;
    }

    /**
     * Remove areas
     *
     * @param \Gopro\UserBundle\Entity\Area $areas
     */
    public function removeArea(\Gopro\UserBundle\Entity\Area $areas)
    {
        $this->areas->removeElement($areas);
    }

    /**
     * Get areas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAreas()
    {
        return $this->areas;
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

    /**
     * Add parametros
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Parametro $parametros
     * @return Sentencia
     */
    public function addParametro(\Gopro\Vipac\ReporteBundle\Entity\Parametro $parametros)
    {
        $this->parametros[] = $parametros;

        return $this;
    }

    /**
     * Remove parametros
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Parametro $parametros
     */
    public function removeParametro(\Gopro\Vipac\ReporteBundle\Entity\Parametro $parametros)
    {
        $this->parametros->removeElement($parametros);
    }

    /**
     * Get parametros
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParametros()
    {
        return $this->parametros;
    }
}
