<?php

namespace Gopro\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Item
 *
 * @ORM\Table(name="inv_item")
 * @ORM\Entity(repositoryClass="Gopro\InventarioBundle\Entity\ItemRepository")
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
     *
     * @ORM\Column(name="codigo", type="string", length=100)
     * @Assert\NotBlank
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
     * @ORM\OneToMany(targetEntity="Componente", mappedBy="item", cascade={"persist","remove"})
     */
    private $componentes;

    /**
     * @ORM\ManyToOne(targetEntity="Gopro\UserBundle\Entity\Area")
     */
    private $area;

    /**
     * @ORM\ManyToOne(targetEntity="Gopro\UserBundle\Entity\Dependencia")
     */
    private $dependencia;

    /**
     * @ORM\ManyToOne(targetEntity="Itemtipo", inversedBy="items")
     */
    private $itemtipo;

    /**
     * @ORM\ManyToMany(targetEntity="Gopro\UserBundle\Entity\User")
     * @ORM\JoinTable(name="inv_items_users")
     *
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="Mantenimiento", mappedBy="item", cascade={"persist","remove"})
     */
    private $mantenimientos;

    public function __construct() {
        $this->componentes = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->mantenimientos = new ArrayCollection();
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
        return $this->codigo;
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
     * Add componentes
     *
     * @param \Gopro\InventarioBundle\Entity\Componente $componentes
     * @return Item
     */
    public function addComponente(\Gopro\InventarioBundle\Entity\Componente $componentes)
    {
        $this->componentes[] = $componentes;

        return $this;
    }

    /**
     * Remove componentes
     *
     * @param \Gopro\InventarioBundle\Entity\Componente $componentes
     */
    public function removeComponente(\Gopro\InventarioBundle\Entity\Componente $componentes)
    {
        $this->componentes->removeElement($componentes);
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
     * Set area
     *
     * @param \Gopro\UserBundle\Entity\Area $area
     * @return Item
     */
    public function setArea(\Gopro\UserBundle\Entity\Area $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \Gopro\UserBundle\Entity\Area 
     */
    public function getArea()
    {
        return $this->area;
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
     * Add users
     *
     * @param \Gopro\UserBundle\Entity\User $users
     * @return Item
     */
    public function addUser(\Gopro\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Gopro\UserBundle\Entity\User $users
     */
    public function removeUser(\Gopro\UserBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
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
     * Add mantenimientos
     *
     * @param \Gopro\InventarioBundle\Entity\Mantenimiento $mantenimientos
     * @return Item
     */
    public function addMantenimiento(\Gopro\InventarioBundle\Entity\Mantenimiento $mantenimientos)
    {
        $this->mantenimientos[] = $mantenimientos;

        return $this;
    }

    /**
     * Remove mantenimientos
     *
     * @param \Gopro\InventarioBundle\Entity\Mantenimiento $mantenimientos
     */
    public function removeMantenimiento(\Gopro\InventarioBundle\Entity\Mantenimiento $mantenimientos)
    {
        $this->mantenimientos->removeElement($mantenimientos);
    }

    /**
     * Get mantenimientos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMantenimientos()
    {
        return $this->mantenimientos;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getNombre();
    }
}
