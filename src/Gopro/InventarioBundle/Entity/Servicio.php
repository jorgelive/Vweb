<?php

namespace Gopro\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;


/**
 * Servicio
 *
 * @ORM\Table(name="inv_servicio")
 * @ORM\Entity
 * @GRID\Source(columns="id, fecha, descripcion, item.nombre, serviciotipo.nombre, servicioestado.nombre")
 */
class Servicio
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @GRID\Column(visible=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     * @Assert\NotBlank
     * @Grid\Column(title="Descripción")
     */
    private $descripcion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     * @Grid\Column(format="Y-m-d", title="Fecha")
     */
    private $fecha;

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
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="servicios")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", nullable=false)
     * @Grid\Column(field="item.nombre", title="Item")
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="Serviciotipo", inversedBy="servicios")
     * @ORM\JoinColumn(name="serviciotipo_id", referencedColumnName="id", nullable=false)
     * @Grid\Column(filter="select", field="serviciotipo.nombre", title="Tipo")
     */
    private $serviciotipo;

    /**
     * @ORM\ManyToOne(targetEntity="Servicioestado", inversedBy="servicios")
     * @ORM\JoinColumn(name="servicioestado_id", referencedColumnName="id", nullable=false)
     * @Grid\Column(filter="select", field="servicioestado.nombre", title="Estado")
     */
    private $servicioestado;

    /**
     * @ORM\ManyToOne(targetEntity="Gopro\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Servicioaccion", mappedBy="servicio", cascade={"persist","remove"})
     */
    private $servicioacciones;

    public function __construct() {
        $this->servicioacciones = new ArrayCollection();
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getDescripcion();
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return Servicio
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
     * Set item
     *
     * @param \Gopro\InventarioBundle\Entity\Item $item
     * @return Servicio
     */
    public function setItem(\Gopro\InventarioBundle\Entity\Item $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return \Gopro\InventarioBundle\Entity\Item 
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set serviciotipo
     *
     * @param \Gopro\InventarioBundle\Entity\Serviciotipo $serviciotipo
     * @return Servicio
     */
    public function setServiciotipo(\Gopro\InventarioBundle\Entity\Serviciotipo $serviciotipo = null)
    {
        $this->serviciotipo = $serviciotipo;

        return $this;
    }

    /**
     * Get serviciotipo
     *
     * @return \Gopro\InventarioBundle\Entity\Serviciotipo
     */
    public function getServiciotipo()
    {
        return $this->serviciotipo;
    }

    /**
     * Set servicioestado
     *
     * @param \Gopro\InventarioBundle\Entity\Servicioestado $servicioestado
     * @return Servicio
     */
    public function setServicioestado(\Gopro\InventarioBundle\Entity\Servicioestado $servicioestado = null)
    {
        $this->servicioestado = $servicioestado;

        return $this;
    }

    /**
     * Get servicioestado
     *
     * @return \Gopro\InventarioBundle\Entity\Servicioestado
     */
    public function getServicioestado()
    {
        return $this->servicioestado;
    }

    /**
     * Set user
     *
     * @param \Gopro\UserBundle\Entity\User $user
     * @return Servicio
     */
    public function setUser(\Gopro\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Gopro\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add servicioacciones
     *
     * @param \Gopro\InventarioBundle\Entity\Servicioaccion $servicioacciones
     * @return Servicio
     */
    public function addServicioaccion(\Gopro\InventarioBundle\Entity\Servicioaccion $servicioacciones)
    {
        $this->servicioacciones[] = $servicioacciones;

        return $this;
    }

    /**
     * Remove servicioacciones
     *
     * @param \Gopro\InventarioBundle\Entity\Servicioaccion $servicioaccion
     */
    public function removeServicioaccion(\Gopro\InventarioBundle\Entity\Servicioaccion $servicioaccion)
    {
        $this->servicioacciones->removeElement($servicioaccion);
    }

    /**
     * Get servicioacciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServicioacciones()
    {
        return $this->servicioacciones;
    }
}
