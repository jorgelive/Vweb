<?php

namespace Gopro\Vipac\ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Sentencia
 *
 * @ORM\Table(name="rep_tipo")
 * @ORM\Entity
 */
class Tipo
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
     * @ORM\Column(name="nombre", type="string", length=100)
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
     * @ORM\OneToMany(targetEntity="Campo", mappedBy="tipo", cascade={"persist"})
     */
    private $campos;

    /**
     * @ORM\ManyToMany(targetEntity="Operador", inversedBy="tipos")
     * @ORM\JoinTable(name="rep_tipos_operadores")
     */
    private $operadores;

    public function __construct() {
        $this->operadores = new ArrayCollection();
        $this->campos = new ArrayCollection();
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
     * @return Tipo
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
     * @return Tipo
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
     * @return Tipo
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
     * Add campos
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Campo $campos
     * @return Tipo
     */
    public function addCampo(\Gopro\Vipac\ReporteBundle\Entity\Campo $campos)
    {
        $this->campos[] = $campos;

        return $this;
    }

    /**
     * Remove campos
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Campo $campos
     */
    public function removeCampo(\Gopro\Vipac\ReporteBundle\Entity\Campo $campos)
    {
        $this->campos->removeElement($campos);
    }

    /**
     * Get campos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCampos()
    {
        return $this->campos;
    }

    /**
     * Add operadores
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Operador $operadores
     * @return Tipo
     */
    public function addOperador(\Gopro\Vipac\ReporteBundle\Entity\Operador $operadores)
    {
        $this->operadores[] = $operadores;

        return $this;
    }

    /**
     * Remove operadores
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Operador $operadores
     */
    public function removeOperador(\Gopro\Vipac\ReporteBundle\Entity\Operador $operadores)
    {
        $this->operadores->removeElement($operadores);
    }

    /**
     * Get operadores
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOperadores()
    {
        return $this->operadores;
    }
}
