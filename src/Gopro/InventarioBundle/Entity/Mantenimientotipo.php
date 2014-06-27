<?php

namespace Gopro\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Mantenimientotipo
 *
 * @ORM\Table(name="inv_mantenimientotipo")
 * @ORM\Entity
 */
class Mantenimientotipo
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
     * @ORM\OneToMany(targetEntity="Mantenimiento", mappedBy="mantenimientotipo", cascade={"persist"})
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
     * @return Mantenimientotipo
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
     * Add mantenimientos
     *
     * @param \Gopro\InventarioBundle\Entity\Mantenimiento $mantenimientos
     * @return Mantenimientotipo
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
