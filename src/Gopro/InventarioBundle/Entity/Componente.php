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
     * @var \DateTime $fechacompra
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechacompra;

    /**
     * @var \DateTime$fechafingarantia
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechafingarantia;

    /**
     * @var \DateTime $fechafingarantia
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechabaja;

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
     * @var \Gopro\InventarioBundle\Entity\Item
     *
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="componentes")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", nullable=false)
     */
    private $item;

    /**
     * @var \Gopro\InventarioBundle\Entity\Componentetipo
     *
     * @ORM\ManyToOne(targetEntity="Componentetipo", inversedBy="componentes")
     * @ORM\JoinColumn(name="componentetipo_id", referencedColumnName="id", nullable=false)
     */
    private $componentetipo;

    /**
     * @var \Gopro\InventarioBundle\Entity\Componenteestado
     *
     * @ORM\ManyToOne(targetEntity="Componenteestado", inversedBy="componentes")
     * @ORM\JoinColumn(name="componenteestado_id", referencedColumnName="id", nullable=false)
     */
    private $componenteestado;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Caracteristica", mappedBy="componente", cascade={"persist","remove"}, orphanRemoval=true)
     */
    private $caracteristicas;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Software",inversedBy="componentes")
     * @ORM\JoinTable(name="inv_componente_software")
     */
    private $softwares;

    public function __construct() {
        $this->caracteristicas = new ArrayCollection();
        $this->softwares = new ArrayCollection();
    }

    /**
     * @return string
     */
    function __toString()
    {
        if(empty($this->getItem()) || empty($this->getComponentetipo())){
            return null;
        }
        return $this->getItem()->getNombre().' - '.$this->getComponentetipo()->getNombre();
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
     * Set fechacompra
     *
     * @param \DateTime $fechacompra
     * @return Componente
     */
    public function setFechacompra($fechacompra)
    {
        $this->fechacompra = $fechacompra;

        return $this;
    }

    /**
     * Get fechacompra
     *
     * @return \DateTime 
     */
    public function getFechacompra()
    {
        return $this->fechacompra;
    }

    /**
     * Set fechafingarantia
     *
     * @param \DateTime $fechafingarantia
     * @return Componente
     */
    public function setFechafingarantia($fechafingarantia)
    {
        $this->fechafingarantia = $fechafingarantia;

        return $this;
    }

    /**
     * Get fechafingarantia
     *
     * @return \DateTime 
     */
    public function getFechafingarantia()
    {
        return $this->fechafingarantia;
    }


    /**
     * Set fechabaja
     *
     * @param \DateTime $fechabaja
     * @return Componente
     */
    public function setFechabaja($fechabaja)
    {
        $this->fechabaja = $fechabaja;

        return $this;
    }

    /**
     * Get fechabaja
     *
     * @return \DateTime
     */
    public function getFechabaja()
    {
        return $this->fechabaja;
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
     * Add caracteristica
     *
     * @param \Gopro\InventarioBundle\Entity\Caracteristica $caracteristica
     * @return Componente
     */
    public function addCaracteristica(\Gopro\InventarioBundle\Entity\Caracteristica $caracteristica)
    {
        $caracteristica->setComponente($this);

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

    /**
     * Add softwares
     *
     * @param \Gopro\InventarioBundle\Entity\Software $softwares
     * @return Componente
     */
    public function addSoftware(\Gopro\InventarioBundle\Entity\Software $softwares)
    {
        $this->softwares[] = $softwares;

        return $this;
    }

    /**
     * Remove softwares
     *
     * @param \Gopro\InventarioBundle\Entity\Software $software
     */
    public function removeSoftware(\Gopro\InventarioBundle\Entity\Software $software)
    {
        $this->softwares->removeElement($software);
    }

    /**
     * Get softwares
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSoftwares()
    {
        return $this->softwares;
    }

}
