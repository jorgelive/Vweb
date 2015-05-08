<?php

namespace Gopro\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Item
 *
 * @ORM\Table(name="inv_item")
 * @ORM\Entity(repositoryClass="Gopro\InventarioBundle\Entity\Repository\ItemRepository")
 * @GRID\Source(columns="id, nombre, itemtipo.nombre, dependencia.nombre")
 */
class Item
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
     * @ORM\Column(name="codigo", type="string", length=100, nullable=true)
     */
    private $codigo;

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
     * @ORM\OneToMany(targetEntity="Componente", mappedBy="item", cascade={"persist","remove"}, orphanRemoval=true)
     */
    private $componentes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Gopro\UserBundle\Entity\Area")
     * @ORM\JoinTable(name="inv_item_area")
     */
    private $areas;

    /**
     * @var \Gopro\UserBundle\Entity\Dependencia
     *
     * @ORM\ManyToOne(targetEntity="Gopro\UserBundle\Entity\Dependencia")
     * @ORM\JoinColumn(name="dependencia_id", referencedColumnName="id", nullable=false)
     * @Grid\Column(filter="select", field="dependencia.nombre", title="Ubicacíón")
     */
    private $dependencia;

    /**
     * @var \Gopro\InventarioBundle\Entity\Itemtipo
     *
     * @ORM\ManyToOne(targetEntity="Itemtipo", inversedBy="items")
     * @ORM\JoinColumn(name="itemtipo_id", referencedColumnName="id", nullable=false)
     * @Grid\Column(filter="select", field="itemtipo.nombre", title="Tipo")
     */
    private $itemtipo;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Gopro\UserBundle\Entity\User")
     * @ORM\JoinTable(name="inv_item_user")
     *
     */
    private $users;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Servicio", mappedBy="item", cascade={"persist","remove"}, orphanRemoval=true)
     */
    private $servicios;

    public function __construct() {
        $this->componentes = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->areas = new ArrayCollection();
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
     * Set codigo
     *
     * @param string $codigo
     * @return Item
     */
    public function setCodigo($codigo)
    {
        if(
            is_object($this->getDependencia())
            && is_object($this->getItemtipo())
            && !empty($this->getId())
            && $codigo == $this->getIniciales($this->getDependencia()->getNombre().' '.$this->getItemtipo()->getNombre()).str_pad($this->getId(), 4, '0', STR_PAD_LEFT)
        ){
            $this->codigo = '';
        }else{
            $this->codigo = $codigo;
        }
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        if(!empty($this->codigo)){
            return $this->codigo;
        }elseif(
            is_object($this->getDependencia())
            && is_object($this->getItemtipo())
            && !empty($this->getId())
        ){
            return $this->getIniciales($this->getDependencia()->getNombre().' '.$this->getItemtipo()->getNombre()).str_pad($this->getId(), 4, '0', STR_PAD_LEFT);
        }else{
            return '';
        }

    }


    public function getIniciales($string) {
        $iniciales='';
        $palabras = preg_split("/[\s,_-]+/", $string);
        foreach ($palabras as $palabra) {
            if(!empty($palabra[0])){
                $iniciales .= strtoupper($palabra[0]);
            }
        }
        return $iniciales;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Item
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
     * @return Item
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
     * @return Item
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
     * Add componente
     *
     * @param \Gopro\InventarioBundle\Entity\Componente $componentes
     * @return Item
     */
    public function addComponente(\Gopro\InventarioBundle\Entity\Componente $componente)
    {
        $componente->setItem($this);

        $this->componentes[] = $componente;

        return $this;
    }

    /**
     * Remove componente
     *
     * @param \Gopro\InventarioBundle\Entity\Componente $componente
     */
    public function removeComponente(\Gopro\InventarioBundle\Entity\Componente $componente)
    {
        $this->componentes->removeElement($componente);
    }

    /**
     * Get componentes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComponentes()
    {
        return $this->componentes;
    }

    /**
     * Add area
     *
     * @param \Gopro\UserBundle\Entity\Area $area
     * @return Item
     */
    public function addArea(\Gopro\UserBundle\Entity\Area $area)
    {
        $this->areas[] = $area;

        return $this;
    }

    /**
     * Remove area
     *
     * @param \Gopro\UserBundle\Entity\Area $area
     */
    public function removeArea(\Gopro\UserBundle\Entity\Area $area)
    {
        $this->areas->removeElement($area);
    }

    /**
     * Get areas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * Set dependencia
     *
     * @param \Gopro\UserBundle\Entity\Dependencia $dependencia
     * @return Item
     */
    public function setDependencia(\Gopro\UserBundle\Entity\Dependencia $dependencia = null)
    {
        $this->dependencia = $dependencia;

        return $this;
    }

    /**
     * Get dependencia
     *
     * @return \Gopro\UserBundle\Entity\Dependencia
     */
    public function getDependencia()
    {
        return $this->dependencia;
    }

    /**
     * Set itemtipo
     *
     * @param \Gopro\InventarioBundle\Entity\Itemtipo $itemtipo
     * @return Item
     */
    public function setItemtipo(\Gopro\InventarioBundle\Entity\Itemtipo $itemtipo = null)
    {
        $this->itemtipo = $itemtipo;

        return $this;
    }

    /**
     * Get itemtipo
     *
     * @return \Gopro\InventarioBundle\Entity\Itemtipo 
     */
    public function getItemtipo()
    {
        return $this->itemtipo;
    }

    /**
     * Add user
     *
     * @param \Gopro\UserBundle\Entity\User $user
     * @return Item
     */
    public function addUser(\Gopro\UserBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \Gopro\UserBundle\Entity\User $user
     */
    public function removeUser(\Gopro\UserBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add servicio
     *
     * @param \Gopro\InventarioBundle\Entity\Servicio $servicio
     * @return Item
     */
    public function addServicio(\Gopro\InventarioBundle\Entity\Servicio $servicio)
    {
        $servicio->setItem($this);

        $this->servicios[] = $servicio;

        return $this;
    }

    /**
     * Remove servicio
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
