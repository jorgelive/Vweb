<?php

namespace Gopro\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Componente
 *
 * @ORM\Table(name="inv_componente")
 * @ORM\Entity
 */
class Componente
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
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="componentes")
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="Componentetipo", inversedBy="componentes")
     */
    private $componentetipo;

    /**
     * @ORM\ManyToOne(targetEntity="Componenteestado", inversedBy="componentes")
     */
    private $componenteestado;


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
     * @return Componente
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
     * @return Componente
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
     * @return Componente
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
     * @return Componente
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
     * Set componentetipo
     *
     * @param \Gopro\InventarioBundle\Entity\Componentetipo $componentetipo
     * @return Componente
     */
    public function setComponentetipo(\Gopro\InventarioBundle\Entity\Componentetipo $componentetipo = null)
    {
        $this->componentetipo = $componentetipo;

        return $this;
    }

    /**
     * Get componentetipo
     *
     * @return \Gopro\InventarioBundle\Entity\Componentetipo 
     */
    public function getComponentetipo()
    {
        return $this->componentetipo;
    }

    /**
     * Set componenteestado
     *
     * @param \Gopro\InventarioBundle\Entity\Componenteestado $componenteestado
     * @return Componente
     */
    public function setComponenteestado(\Gopro\InventarioBundle\Entity\Componenteestado $componenteestado = null)
    {
        $this->componenteestado = $componenteestado;

        return $this;
    }

    /**
     * Get componenteestado
     *
     * @return \Gopro\InventarioBundle\Entity\Componenteestado
     */
    public function getComponenteestado()
    {
        return $this->componenteestado;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getNombre();
    }
}
