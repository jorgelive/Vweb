<?php

namespace Gopro\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Componentespec
 *
 * @ORM\Table(name="inv_componentecaracteristica")
 * @ORM\Entity
 */
class Componentecaracteristica
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
     * @ORM\Column(name="contenido", type="string", length=100)
     * @Assert\NotBlank
     */
    private $contenido;

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
     * @ORM\ManyToOne(targetEntity="Componente", inversedBy="componentecaracteristicas")
     */
    private $componente;

    /**
     * @ORM\ManyToOne(targetEntity="Caracteristica", inversedBy="componentecaracteristicas")
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
