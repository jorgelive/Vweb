<?php

namespace Gopro\Vipac\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Servicio
 *
 * @ORM\Table(name="pro_servicio")
 * @ORM\Entity
 * @GRID\Source(columns="id, nombre, monto, fecha, hora")
 */
class Servicio
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
     * @ORM\Column(name="nombre", type="string", length=50)
     * @Assert\NotBlank
     */
    private $nombre;

    /**
     * @var decimal
     *
     * @ORM\Column(name="monto", type="decimal", scale=2)
     */
    private $monto;

    /**
     * @var string
     *
     * @ORM\Column(name="fecha", type="datetime")
     * @Assert\NotBlank
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="hora", type="string", length=4)
     * @Assert\NotBlank
     */
    private $hora;

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
     * @ORM\ManyToOne(targetEntity="Gopro\MaestroBundle\Entity\Moneda")
     * @ORM\JoinColumn(name="moneda_id", referencedColumnName="id", nullable=false)
     */
    private $moneda;

    /**
     * @ORM\OneToMany(targetEntity="Serviciofile", mappedBy="servicio", cascade={"persist","remove"})
     */
    private $serviciofiles;


    public function __construct() {
        $this->serviciofiles = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getNombre();
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
     * @return Servicio
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
     * Set monto
     *
     * @param string $monto
     * @return Servicio
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Servicio
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set hora
     *
     * @param string $hora
     * @return Servicio
     */
    public function setHora($hora)
    {
        $this->hora = $hora;

        return $this;
    }

    /**
     * Get hora
     *
     * @return string 
     */
    public function getHora()
    {
        return $this->hora;
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     * @return Servicio
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
     * @return Servicio
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
     * Set moneda
     *
     * @param \Gopro\MaestroBundle\Entity\Moneda $moneda
     * @return Servicio
     */
    public function setMoneda(\Gopro\MaestroBundle\Entity\Moneda $moneda)
    {
        $this->moneda = $moneda;

        return $this;
    }

    /**
     * Get moneda
     *
     * @return \Gopro\MaestroBundle\Entity\Moneda 
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * Add serviciofiles
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Serviciofile $serviciofiles
     * @return Servicio
     */
    public function addServiciofile(\Gopro\Vipac\ProveedorBundle\Entity\Serviciofile $serviciofiles)
    {
        $this->serviciofiles[] = $serviciofiles;
        $serviciofiles->setServicio($this);
        return $this;
    }

    /**
     * Remove serviciofiles
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Serviciofile $serviciofiles
     */
    public function removeServiciofile(\Gopro\Vipac\ProveedorBundle\Entity\Serviciofile $serviciofiles)
    {
        $this->serviciofiles->removeElement($serviciofiles);
    }

    /**
     * Get serviciofiles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getServiciofiles()
    {
        return $this->serviciofiles;
    }
}
