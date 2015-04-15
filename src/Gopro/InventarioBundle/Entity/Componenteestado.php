<?php

namespace Gopro\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Componenteestado
 *
 * @ORM\Table(name="inv_componenteestado")
 * @ORM\Entity
 */
class Componenteestado
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
     * @ORM\OneToMany(targetEntity="Componente", mappedBy="componenteestado", cascade={"persist"})
     */
    private $componentes;

    public function __construct() {
        $this->componentes = new ArrayCollection();
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
     * @return Componenteestado
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
     * @return Componenteestado
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
     * @return Componenteestado
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
     * @return Componenteestado
     */
    public function addComponente(\Gopro\InventarioBundle\Entity\Componente $componentes)
    {
        $this->componentes[] = $componentes;

        return $this;
    }

    /**
     * Remove componentes
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
}
