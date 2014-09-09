<?php

namespace Gopro\Vipac\ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Campo
 *
 * @ORM\Table(name="rep_operador")
 * @ORM\Entity
 */
class Operador
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
     * @Assert\NotBlank
     * @ORM\Column(name="nombre", type="string", length=50)
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
     * @ORM\ManyToMany(targetEntity="Tipo", mappedBy="operadores")
     */
    private $tipos;

    public function __construct() {
        $this->tipos = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nombre;
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
     * @return Operador
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
     * @return Operador
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
     * @return Operador
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
     * Add tipo
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Tipo $tipo
     * @return Operador
     */
    public function addTipo(\Gopro\Vipac\ReporteBundle\Entity\Tipo $tipo)
    {
        $tipo->addOperador($this);
        return $this;
    }

    /**
     * Remove tipos
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Tipo $tipos
     */
    public function removeTipo(\Gopro\Vipac\ReporteBundle\Entity\Tipo $tipos)
    {
        $this->tipos->removeElement($tipos);
    }

    /**
     * Get tipos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTipos()
    {
        return $this->tipos;
    }
}
