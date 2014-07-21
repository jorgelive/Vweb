<?php

namespace Gopro\Vipac\ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Campo
 *
 * @ORM\Table(name="rep_parametro")
 * @ORM\Entity
 */
class Parametro
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
     * @var string
     * @Assert\NotBlank
     * @ORM\Column(name="contenido", type="text")
     */
    private $contenido;

    /**
     * @var string
     *
     * @ORM\Column(name="publico", type="boolean", nullable=false)
     */
    private $publico;

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
     * @ORM\ManyToOne(targetEntity="Sentencia", inversedBy="parametros")
     * @ORM\JoinColumn(name="sentencia_id", referencedColumnName="id", nullable=false)
     */
    private $sentencia;

    /**
     * @ORM\ManyToOne(targetEntity="Gopro\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

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
     * @return Parametro
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
     * Set contenido
     *
     * @param string $contenido
     * @return Parametro
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
     * Set publico
     *
     * @param boolean $publico
     * @return Parametro
     */
    public function setPublico($publico)
    {
        $this->publico = $publico;

        return $this;
    }

    /**
     * Get publico
     *
     * @return boolean 
     */
    public function getPublico()
    {
        return $this->publico;
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     * @return Parametro
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
     * @return Parametro
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
     * Set sentencia
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Sentencia $sentencia
     * @return Parametro
     */
    public function setSentencia(\Gopro\Vipac\ReporteBundle\Entity\Sentencia $sentencia)
    {
        $this->sentencia = $sentencia;

        return $this;
    }

    /**
     * Get sentencia
     *
     * @return \Gopro\Vipac\ReporteBundle\Entity\Sentencia 
     */
    public function getSentencia()
    {
        return $this->sentencia;
    }

    /**
     * Set user
     *
     * @param \Gopro\UserBundle\Entity\User $user
     * @return Parametro
     */
    public function setUser(\Gopro\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Gopro\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
