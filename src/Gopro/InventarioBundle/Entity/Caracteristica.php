<?php

namespace Gopro\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Caracteristica
 *
 * @ORM\Table(name="inv_caracteristica")
 * @ORM\Entity
 */
class Caracteristica
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
     * @ORM\OneToMany(targetEntity="Componentecaracteristica", mappedBy="caracteristica", cascade={"persist"})
     */
    private $componentecaracteristicas;

    public function __construct() {
        $this->componentecaracteristicas = new ArrayCollection();
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
     * @return Caracteristica
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
     * @return Caracteristica
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
     * @return Caracteristica
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
     * Add componentecaracteristicas
     *
     * @param \Gopro\InventarioBundle\Entity\Componentecaracteristica $componentecaracteristicas
     * @return Caracteristica
     */
    public function addComponentecaracteristica(\Gopro\InventarioBundle\Entity\Componentecaracteristica $componentecaracteristicas)
    {
        $this->componentecaracteristicas[] = $componentecaracteristicas;

        return $this;
    }

    /**
     * Remove componentecaracteristicas
     *
     * @param \Gopro\InventarioBundle\Entity\Componentecaracteristica $componentecaracteristicas
     */
    public function removeComponentecaracteristica(\Gopro\InventarioBundle\Entity\Componentecaracteristica $componentecaracteristicas)
    {
        $this->componentecaracteristicas->removeElement($componentecaracteristicas);
    }

    /**
     * Get componentecaracteristicas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComponentecaracteristicas()
    {
        return $this->componentecaracteristicas;
    }
}
