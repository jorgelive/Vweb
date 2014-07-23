<?php

namespace Gopro\Vipac\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Adjuntotipo
 *
 * @ORM\Table(name="pro_adjuntotipo")
 * @ORM\Entity
 */
class Adjuntotipo
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
     * @ORM\OneToMany(targetEntity="Informacionadjunto", mappedBy="adjuntotipo", cascade={"persist"})
     */
    private $informacionadjuntos;

    /**
     * @ORM\ManyToMany(targetEntity="Informaciontipo")
     * @ORM\JoinTable(name="pro_informacionadjuntos_informaciontipos")
     */
    private $informaciontipos;

    public function __construct() {
        $this->informacionadjuntos = new ArrayCollection();
        $this->informaciontipos = new ArrayCollection();
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
     * @return Adjuntotipo
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
     * @return Adjuntotipo
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
     * @return Adjuntotipo
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
     * Add informacionadjuntos
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Informacionadjunto $informacionadjuntos
     * @return Adjuntotipo
     */
    public function addInformacionadjunto(\Gopro\Vipac\ProveedorBundle\Entity\Informacionadjunto $informacionadjuntos)
    {
        $this->informacionadjuntos[] = $informacionadjuntos;

        return $this;
    }

    /**
     * Remove informacionadjuntos
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Informacionadjunto $informacionadjuntos
     */
    public function removeInformacionadjunto(\Gopro\Vipac\ProveedorBundle\Entity\Informacionadjunto $informacionadjuntos)
    {
        $this->informacionadjuntos->removeElement($informacionadjuntos);
    }

    /**
     * Get informacionadjuntos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInformacionadjuntos()
    {
        return $this->informacionadjuntos;
    }

    /**
     * Add informaciontipos
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Informaciontipo $informaciontipos
     * @return Adjuntotipo
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
