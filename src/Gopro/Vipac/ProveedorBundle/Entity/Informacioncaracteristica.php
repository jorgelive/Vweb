<?php

namespace Gopro\Vipac\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Informacioncaracteristica
 *
 * @ORM\Table(name="pro_informacioncaracteristica")
 * @ORM\Entity
 */
class Informacioncaracteristica
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
     * @ORM\ManyToOne(targetEntity="Informacion", inversedBy="informacioncaracteristicas")
     * @ORM\JoinColumn(name="informacion_id", referencedColumnName="id", nullable=false)
     */
    private $informacion;

    /**
     * @ORM\ManyToOne(targetEntity="Caracteristica", inversedBy="informacioncaracteristicas")
     * @ORM\JoinColumn(name="caracteristica_id", referencedColumnName="id", nullable=false)
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
     * @return Informacioncaracteristica
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
     * @return Informacioncaracteristica
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
     * @return Informacioncaracteristica
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
     * Set informacion
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Informacion $informacion
     * @return Informacioncaracteristica
     */
    public function setInformacion(\Gopro\Vipac\ProveedorBundle\Entity\Informacion $informacion = null)
    {
        $this->informacion = $informacion;

        return $this;
    }

    /**
     * Get informacion
     *
     * @return \Gopro\Vipac\ProveedorBundle\Entity\Informacion
     */
    public function getInformacion()
    {
        return $this->informacion;
    }

    /**
     * Set caracteristica
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Caracteristica $caracteristica
     * @return Informacioncaracteristica
     */
    public function setCaracteristica(\Gopro\Vipac\ProveedorBundle\Entity\Caracteristica $caracteristica = null)
    {
        $this->caracteristica = $caracteristica;

        return $this;
    }

    /**
     * Get caracteristica
     *
     * @return \Gopro\Vipac\ProveedorBundle\Entity\Caracteristica
     */
    public function getCaracteristica()
    {
        return $this->caracteristica;
    }

}
