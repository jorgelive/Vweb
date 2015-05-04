<?php

namespace Gopro\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Componentecaracteristica
 *
 * @ORM\Table(name="inv_componentecaracteristica")
 * @ORM\Entity
 * @GRID\Source(columns="id, componente.item.id, componente.item.nombre, componente.componentetipo.nombre, caracteristica.nombre, contenido")
 */
class Componentecaracteristica
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Grid\Column(visible=false, field="id", title="ID")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="contenido", type="string", length=100)
     * @Assert\NotBlank
     */
    private $contenido;

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
     * @var \Gopro\InventarioBundle\Entity\Componente
     *
     * @ORM\ManyToOne(targetEntity="Componente", inversedBy="componentecaracteristicas")
     * @ORM\JoinColumn(name="componente_id", referencedColumnName="id", nullable=false)
     * @Grid\Column(filter="select", visible=false, field="componente.item.id", title="Item ID")
     * @Grid\Column(filter="select", field="componente.item.nombre", title="Item")
     * @Grid\Column(filter="select", field="componente.componentetipo.nombre", title="Componente")
     */
    private $componente;

    /**
     * @var \Gopro\InventarioBundle\Entity\Caracteristica
     *
     * @ORM\ManyToOne(targetEntity="Caracteristica", inversedBy="componentecaracteristicas")
     * @ORM\JoinColumn(name="caracteristica_id", referencedColumnName="id", nullable=false)
     * @Grid\Column(filter="select", field="caracteristica.nombre", title="Caracteristica")
     */
    private $caracteristica;

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getContenido();
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
     * Set contenido
     *
     * @param string $contenido
     * @return Componentecaracteristica
     */
    public function setContenido($contenido)
    {
        $this->contenido = $contenido;

        return $this;
    }

    /**
     * Get contenido
     *
     * @return string 
     */
    public function getContenido()
    {
        return $this->contenido;
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     * @return Componentecaracteristica
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
     * @return Componentecaracteristica
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
     * Set componente
     *
     * @param \Gopro\InventarioBundle\Entity\Componente $componente
     * @return Componentecaracteristica
     */
    public function setComponente(\Gopro\InventarioBundle\Entity\Componente $componente = null)
    {
        $this->componente = $componente;

        return $this;
    }

    /**
     * Get componente
     *
     * @return \Gopro\InventarioBundle\Entity\Componente 
     */
    public function getComponente()
    {
        return $this->componente;
    }

    /**
     * Set caracteristica
     *
     * @param \Gopro\InventarioBundle\Entity\Caracteristica $caracteristica
     * @return Componentecaracteristica
     */
    public function setCaracteristica(\Gopro\InventarioBundle\Entity\Caracteristica $caracteristica = null)
    {
        $this->caracteristica = $caracteristica;

        return $this;
    }

    /**
     * Get caracteristica
     *
     * @return \Gopro\InventarioBundle\Entity\Caracteristica 
     */
    public function getCaracteristica()
    {
        return $this->caracteristica;
    }

}
