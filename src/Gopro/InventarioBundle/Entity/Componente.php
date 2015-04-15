<?php

namespace Gopro\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Componente
 *
 * @ORM\Table(name="inv_componente")
 * @ORM\Entity
 * @GRID\Source(columns="id, item.id, item.nombre, componentetipo.nombre, componenteestado.nombre, fechacompra, fechafingarantia, componentecaracteristicas.contenido:Group_Concat:Distinct, softwares.nombre:Group_Concat:Distinct", groupBy={"id", "item.nombre"})
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
     * @Grid\Column(field="fechacompra", title="Compra", format="Y-m-d")
     */
    private $fechacompra;

    /**
     * @var \DateTime$fechafingarantia
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Grid\Column(field="fechafingarantia", title="Garantía", format="Y-m-d")
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
     * @Grid\Column(field="item.nombre", title="Item")
     * @Grid\Column(filter="select", visible=false, field="item.id", title="Item ID")
     */
    private $item;

    /**
     * @var \Gopro\InventarioBundle\Entity\Componentetipo
     *
     * @ORM\ManyToOne(targetEntity="Componentetipo", inversedBy="componentes")
     * @ORM\JoinColumn(name="componentetipo_id", referencedColumnName="id", nullable=false)
     * @Grid\Column(filter="select", field="componentetipo.nombre", title="Tipo")
     */
    private $componentetipo;

    /**
     * @var \Gopro\InventarioBundle\Entity\Componenteestado
     *
     * @ORM\ManyToOne(targetEntity="Componenteestado", inversedBy="componentes")
     * @ORM\JoinColumn(name="componenteestado_id", referencedColumnName="id", nullable=false)
     * @Grid\Column(filter="select", field="componenteestado.nombre", title="Estado")
     */
    private $componenteestado;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Componentecaracteristica", mappedBy="componente", cascade={"persist","remove"})
     * @GRID\Column(field="componentecaracteristicas.contenido:Group_Concat:Distinct", title="Caracteristicas", filterable=false)
     */
    private $componentecaracteristicas;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Software",inversedBy="componentes")
     * @ORM\JoinTable(name="inv_componente_software")
     * @GRID\Column(field="softwares.nombre:Group_Concat:Distinct", title="Software", filterable=false)
     */
    private $softwares;

    public function __construct() {
        $this->componentecaracteristicas = new ArrayCollection();
        $this->softwares = new ArrayCollection();
    }

    /**
     * @return string
     */
    function __toString()
    {
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
     * Add componentecaracteristicas
     *
     * @param \Gopro\InventarioBundle\Entity\Componentecaracteristica $componentecaracteristicas
     * @return Componente
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
    public function removeComponentecaracteristica(\Gopro\InventarioBundle\Entity\Componentecaracteristica $componentecaracteristica)
    {
        $this->componentecaracteristicas->removeElement($componentecaracteristica);
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
