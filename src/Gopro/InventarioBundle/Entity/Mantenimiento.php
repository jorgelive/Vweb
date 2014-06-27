<?php

namespace Gopro\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Mantenimiento
 *
 * @ORM\Table(name="inv_mantenimiento")
 * @ORM\Entity
 */
class Mantenimiento
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
     * @ORM\Column(name="descripcion", type="string", length=255)
     * @Assert\NotBlank
     */
    private $descripcion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
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
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="mantenimientos")
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="Mantenimientotipo", inversedBy="mantenimientos")
     */
    private $mantenimientotipo;

    /**
     * @ORM\ManyToOne(targetEntity="Mantenimientoestado", inversedBy="mantenimientos")
     */
    private $mantenimientoestado;

    /**
     * @ORM\ManyToOne(targetEntity="Gopro\UserBundle\Entity\User")
     */
    private $user;

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
     * @return Mantenimiento
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
     * @return Mantenimiento
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
     * @return Mantenimiento
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
     * @return Mantenimiento
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
     * @return Mantenimiento
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
     * Set mantenimientotipo
     *
     * @param \Gopro\InventarioBundle\Entity\Mantenimientotipo $mantenimientotipo
     * @return Mantenimiento
     */
    public function setMantenimientotipo(\Gopro\InventarioBundle\Entity\Mantenimientotipo $mantenimientotipo = null)
    {
        $this->mantenimientotipo = $mantenimientotipo;

        return $this;
    }

    /**
     * Get mantenimientotipo
     *
     * @return \Gopro\InventarioBundle\Entity\Mantenimientotipo 
     */
    public function getMantenimientotipo()
    {
        return $this->mantenimientotipo;
    }

    /**
     * Set mantenimientoestado
     *
     * @param \Gopro\InventarioBundle\Entity\Mantenimientoestado $mantenimientoestado
     * @return Mantenimiento
     */
    public function setMantenimientoestado(\Gopro\InventarioBundle\Entity\Mantenimientoestado $mantenimientoestado = null)
    {
        $this->mantenimientoestado = $mantenimientoestado;

        return $this;
    }

    /**
     * Get mantenimientoestado
     *
     * @return \Gopro\InventarioBundle\Entity\Mantenimientoestado 
     */
    public function getMantenimientoestado()
    {
        return $this->mantenimientoestado;
    }

    /**
     * Set user
     *
     * @param \Gopro\UserBundle\Entity\User $user
     * @return Mantenimiento
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
}
