<?php

namespace Gopro\Vipac\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Caracteristica
 *
 * @ORM\Table(name="pro_caracteristica")
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
     * @ORM\OneToMany(targetEntity="Informacioncaracteristica", mappedBy="caracteristica", cascade={"persist"})
     */
    private $informacioncaracteristicas;

    /**
     * @ORM\ManyToMany(targetEntity="Informaciontipo")
     * @ORM\JoinTable(name="pro_caracteristicas_informaciontipos")
     */
    private $informaciontipos;

    public function __construct() {
        $this->informacioncaracteristicas = new ArrayCollection();
        $this->informaciontipos = new ArrayCollection();
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
     * Add informacioncaracteristicas
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Informacioncaracteristica $informacioncaracteristicas
     * @return Caracteristica
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

    /**
     * Add informaciontipos
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Informaciontipo $informaciontipos
     * @return Caracteristica
     */
    public function addInformaciontipo(\Gopro\Vipac\ProveedorBundle\Entity\Informaciontipo $informaciontipos)
    {
        $this->informaciontipos[] = $informaciontipos;

        return $this;
    }

    /**
     * Remove informaciontipos
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Informaciontipo $informaciontipos
     */
    public function removeInformaciontipo(\Gopro\Vipac\ProveedorBundle\Entity\Informaciontipo $informaciontipos)
    {
        $this->informaciontipos->removeElement($informaciontipos);
    }

    /**
     * Get informaciontipos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInformaciontipos()
    {
        return $this->informaciontipos;
    }
}
