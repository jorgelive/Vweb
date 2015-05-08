<?php

namespace Gopro\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Caracteristicatipo
 *
 * @ORM\Table(name="inv_caracteristicatipo")
 * @ORM\Entity
 */
class Caracteristicatipo
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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Caracteristica", mappedBy="caracteristicatipo", cascade={"persist","remove"}, orphanRemoval=true)
     */
    private $caracteristicas;

    public function __construct() {
        $this->caracteristicas = new ArrayCollection();
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
     * @return Caracteristicatipo
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
     * @return Caracteristicatipo
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
     * @return Caracteristicatipo
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
     * Add caracteristica
     *
     * @param \Gopro\InventarioBundle\Entity\Caracteristica $caracteristicas
     * @return Caracteristicatipo
     */
    public function addCaracteristica(\Gopro\InventarioBundle\Entity\Caracteristica $caracteristica)
    {
        $caracteristica->setCaracteristicatipo($this);

        $this->caracteristicas[] = $caracteristica;

        return $this;
    }

    /**
     * Remove caracteristica
     *
     * @param \Gopro\InventarioBundle\Entity\Caracteristica $caracteristica
     */
    public function removeCaracteristica(\Gopro\InventarioBundle\Entity\Caracteristica $caracteristica)
    {
        $this->caracteristicas->removeElement($caracteristica);
    }

    /**
     * Get caracteristicas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCaracteristicas()
    {
        return $this->caracteristicas;
    }
}
