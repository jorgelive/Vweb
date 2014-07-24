<?php

namespace Gopro\Vipac\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Informacion
 *
 * @ORM\Table(name="pro_informacion")
 * @ORM\Entity
 */
class Informacion
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
     * @ORM\Column(name="descripcion", type="integer")
     */
    private $ruc;

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
     * @ORM\ManyToOne(targetEntity="Informaciontipo", inversedBy="informaciones" )
     * @ORM\JoinColumn(name="informaciontipo_id", referencedColumnName="id", nullable=false)
     */
    private $informaciontipo;

    /**
     * @ORM\OneToMany(targetEntity="Informacioncaracteristica", mappedBy="informacion", cascade={"persist","remove"})
     */
    private $informacioncaracteristicas;

    public function __construct() {
        $this->informacioncaracteristicas = new ArrayCollection();
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
     * @return Informacion
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
     * Set ruc
     *
     * @param integer $ruc
     * @return Informacion
     */
    public function setRuc($ruc)
    {
        $this->ruc = $ruc;

        return $this;
    }

    /**
     * Get ruc
     *
     * @return integer 
     */
    public function getRuc()
    {
        return $this->ruc;
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     * @return Informacion
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
     * @return Informacion
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
     * Set informaciontipo
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Informaciontipo $informaciontipo
     * @return Informacion
     */
    public function setInformaciontipo(\Gopro\Vipac\ProveedorBundle\Entity\Informaciontipo $informaciontipo)
    {
        $this->informaciontipo = $informaciontipo;

        return $this;
    }

    /**
     * Get informaciontipo
     *
     * @return \Gopro\Vipac\ProveedorBundle\Entity\Informaciontipo 
     */
    public function getInformaciontipo()
    {
        return $this->informaciontipo;
    }

    /**
     * Add informacioncaracteristicas
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Informacioncaracteristica $informacioncaracteristicas
     * @return Informacion
     */
    public function addInformacioncaracteristica(\Gopro\Vipac\ProveedorBundle\Entity\Informacioncaracteristica $informacioncaracteristicas)
    {
        $this->informacioncaracteristicas[] = $informacioncaracteristicas;

        return $this;
    }

    /**
     * Remove informacioncaracteristicas
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Informacioncaracteristica $informacioncaracteristicas
     */
    public function removeInformacioncaracteristica(\Gopro\Vipac\ProveedorBundle\Entity\Informacioncaracteristica $informacioncaracteristicas)
    {
        $this->informacioncaracteristicas->removeElement($informacioncaracteristicas);
    }

    /**
     * Get informacioncaracteristicas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInformacioncaracteristicas()
    {
        return $this->informacioncaracteristicas;
    }
}
