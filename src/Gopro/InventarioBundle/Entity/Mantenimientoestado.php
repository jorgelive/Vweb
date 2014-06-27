<?php

namespace Gopro\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Mantenimientoestado
 *
 * @ORM\Table(name="inv_mantenimientoestado")
 * @ORM\Entity
 */
class Mantenimientoestado
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
     * @Assert\NotBlank
     */
    private $nombre;


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
     * @ORM\OneToMany(targetEntity="Mantenimiento", mappedBy="mantenimientoestado", cascade={"persist"})
     */
    private $mantenimientos;

    public function __construct() {
        $this->mantenimientos = new ArrayCollection();
    }

    /**
     * @return string
     */
    function __toString()
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
     * @return Mantenimientoestado
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
     * Set creado
     *
     * @param \DateTime $creado
     * @return Mantenimientoestado
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
     * @return Mantenimientoestado
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
     * Add mantenimientos
     *
     * @param \Gopro\InventarioBundle\Entity\Mantenimiento $mantenimientos
     * @return Mantenimientoestado
     */
    public function addMantenimiento(\Gopro\InventarioBundle\Entity\Mantenimiento $mantenimientos)
    {
        $this->mantenimientos[] = $mantenimientos;

        return $this;
    }

    /**
     * Remove mantenimientos
     *
     * @param \Gopro\InventarioBundle\Entity\Mantenimiento $mantenimientos
     */
    public function removeMantenimiento(\Gopro\InventarioBundle\Entity\Mantenimiento $mantenimientos)
    {
        $this->mantenimientos->removeElement($mantenimientos);
    }

    /**
     * Get mantenimientos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMantenimientos()
    {
        return $this->mantenimientos;
    }
}
