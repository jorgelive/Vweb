<?php

namespace Gopro\Vipac\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Informaciontipo
 *
 * @ORM\Table(name="pro_informaciontipo")
 * @ORM\Entity
 */
class Informaciontipo
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
     * @ORM\OneToMany(targetEntity="Informacion", mappedBy="informaciontipo", cascade={"persist"})
     */
    private $informaciones;

    public function __construct() {
        $this->informaciones = new ArrayCollection();
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
     * @return Informaciontipo
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
     * @return Informaciontipo
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
     * @return Informaciontipo
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
     * Add informaciones
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Informacion $informaciones
     * @return Informaciontipo
     */
    public function addInformacione(\Gopro\Vipac\ProveedorBundle\Entity\Informacion $informaciones)
    {
        $this->informaciones[] = $informaciones;

        return $this;
    }

    /**
     * Remove informaciones
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Informacion $informaciones
     */
    public function removeInformacione(\Gopro\Vipac\ProveedorBundle\Entity\Informacion $informaciones)
    {
        $this->informaciones->removeElement($informaciones);
    }

    /**
     * Get informaciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInformaciones()
    {
        return $this->informaciones;
    }
}
