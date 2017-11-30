<?php

namespace Gopro\InventarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Caracteristica
 *
 * @ORM\Table(name="inv_caracteristica")
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
     * @ORM\ManyToOne(targetEntity="Componente", inversedBy="caracteristicas")
     * @ORM\JoinColumn(name="componente_id", referencedColumnName="id", nullable=false)
     */
    private $componente;

    /**
     * @var \Gopro\InventarioBundle\Entity\Caracteristicatipo
     *
     * @ORM\ManyToOne(targetEntity="Caracteristicatipo", inversedBy="caracteristicas")
     * @ORM\JoinColumn(name="caracteristicatipo_id", referencedColumnName="id", nullable=false)
     */
    private $caracteristicatipo;

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
     * @return Caracteristica
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
     * Set componente
     *
     * @param \Gopro\InventarioBundle\Entity\Componente $componente
     * @return Caracteristica
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
     * Set caracteristicatipo
     *
     * @param \Gopro\InventarioBundle\Entity\Caracteristicatipo $caracteristicatipo
     * @return Caracteristica
     */
    public function setCaracteristicatipo(\Gopro\InventarioBundle\Entity\Caracteristicatipo $caracteristicatipo = null)
    {
        $this->caracteristicatipo = $caracteristicatipo;

        return $this;
    }

    /**
     * Get caracteristicatipo
     *
     * @return \Gopro\InventarioBundle\Entity\Caracteristicatipo
     */
    public function getCaracteristicatipo()
    {
        return $this->caracteristicatipo;
    }

}
