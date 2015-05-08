<?php

namespace Gopro\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Serviciotipo
 *
 * @ORM\Table(name="inv_serviciotipo")
 * @ORM\Entity
 */
class Serviciotipo
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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Servicio", mappedBy="serviciotipo", cascade={"persist","remove"}, orphanRemoval=true)
     */
    private $servicios;

    public function __construct() {
        $this->servicios = new ArrayCollection();
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
     * @return Serviciotipo
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
     * Add servicio
     *
     * @param \Gopro\InventarioBundle\Entity\Servicio $servicio
     * @return Serviciotipo
     */
    public function addServicio(\Gopro\InventarioBundle\Entity\Servicio $servicio)
    {
        $servicio->setServiciotipo($this);

        $this->servicios[] = $servicio;

        return $this;
    }

    /**
     * Remove servicios
     *
     * @param \Gopro\InventarioBundle\Entity\Servicio $servicio
     */
    public function removeServicio(\Gopro\InventarioBundle\Entity\Servicio $servicio)
    {
        $this->servicios->removeElement($servicio);
    }

    /**
     * Get servicios
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getServicios()
    {
        return $this->servicios;
    }
}
