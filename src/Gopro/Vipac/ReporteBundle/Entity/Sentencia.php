<?php

namespace Gopro\Vipac\ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Sentencia
 *
 * @ORM\Table(name="rep_sentencia")
 * @ORM\Entity
 */
class Sentencia
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
     * @Assert\NotBlank
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @ORM\Column(name="contenido", type="text")
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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Gopro\UserBundle\Entity\Group")
     * @ORM\JoinTable(name="rep_sentencia_group")
     *
     */
    protected $groups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Campo", mappedBy="sentencia", cascade={"persist","remove"}, orphanRemoval=true)
     */
    private $campos;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Parametro", mappedBy="sentencia", cascade={"persist","remove"}, orphanRemoval=true)
     */
    private $parametros;

    public function __construct() {
        $this->campos = new ArrayCollection();
        $this->areas = new ArrayCollection();
        $this->parametros = new ArrayCollection();
    }

    /**
     * @return string
     */
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
     * @return Sentencia
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return Sentencia
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set contenido
     *
     * @param string $contenido
     * @return Sentencia
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
     * @return Sentencia
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
     * @return Sentencia
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
     * Add group
     *
     * @param \Gopro\UserBundle\Entity\Group $group
     * @return Sentencia
     */
    public function addGroup(\Gopro\UserBundle\Entity\Group $group)
    {

        $this->groups[] = $group;

        return $this;
    }

    /**
     * Remove group
     *
     * @param \Gopro\UserBundle\Entity\Group $group
     */
    public function removeGroup(\Gopro\UserBundle\Entity\Group $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add campo
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Campo $campo
     * @return Sentencia
     */
    public function addCampo(\Gopro\Vipac\ReporteBundle\Entity\Campo $campo)
    {
        $campo->setSentencia($this);

        $this->campos[] = $campo;

        return $this;
    }

    /**
     * Remove campo
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Campo $campo
     */
    public function removeCampo(\Gopro\Vipac\ReporteBundle\Entity\Campo $campo)
    {
        $this->campos->removeElement($campo);
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
     * Add parametros
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Parametro $parametros
     * @return Sentencia
     */
    public function addParametro(\Gopro\Vipac\ReporteBundle\Entity\Parametro $parametros)
    {
        $this->parametros[] = $parametros;

        return $this;
    }

    /**
     * Remove parametro
     *
     * @param \Gopro\Vipac\ReporteBundle\Entity\Parametro $parametros
     */
    public function removeParametro(\Gopro\Vipac\ReporteBundle\Entity\Parametro $parametro)
    {
        $this->parametros->removeElement($parametro);
    }

    /**
     * Get parametros
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParametros()
    {
        return $this->parametros;
    }
}
