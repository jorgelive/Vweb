<?php
namespace Gopro\Vipac\DbprocesoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="dbp_docsaptipo")
 * @ORM\HasLifecycleCallbacks
 */
class Docsaptipo
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=3)
     * @Assert\NotBlank
     */
    private $tiposunat;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=3)
     * @Assert\NotBlank
     */
    private $tiposap;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $exoneradoigv;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $iscporcentaje;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $codigoretencion;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10)
     */
    private $cuenta;

    /**
     * @var string
     *
     * @ORM\Column(type="integer")
     */
    private $tiposervicio;

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
     *
     * @return Docsaptipo
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
     * Set tiposunat
     *
     * @param string $tiposunat
     *
     * @return Docsaptipo
     */
    public function setTiposunat($tiposunat)
    {
        $this->tiposunat = $tiposunat;

        return $this;
    }

    /**
     * Get tiposunat
     *
     * @return string
     */
    public function getTiposunat()
    {
        return $this->tiposunat;
    }

    /**
     * Set tiposap
     *
     * @param string $tiposap
     *
     * @return Docsaptipo
     */
    public function setTiposap($tiposap)
    {
        $this->tiposap = $tiposap;

        return $this;
    }

    /**
     * Get tiposap
     *
     * @return string
     */
    public function getTiposap()
    {
        return $this->tiposap;
    }

    /**
     * Set exoneradoigv
     *
     * @param boolean $exoneradoigv
     *
     * @return Docsaptipo
     */
    public function setExoneradoigv($exoneradoigv)
    {
        $this->exoneradoigv = $exoneradoigv;

        return $this;
    }

    /**
     * Get exoneradoigv
     *
     * @return boolean
     */
    public function getExoneradoigv()
    {
        return $this->exoneradoigv;
    }

    /**
     * Set iscporcentaje
     *
     * @param float $iscporcentaje
     *
     * @return Docsaptipo
     */
    public function setIscporcentaje($iscporcentaje)
    {
        $this->iscporcentaje = $iscporcentaje;

        return $this;
    }

    /**
     * Get iscporcentaje
     *
     * @return float
     */
    public function getIscporcentaje()
    {
        return $this->iscporcentaje;
    }

    /**
     * Set codigoretencion
     *
     * @param string $codigoretencion
     *
     * @return Docsaptipo
     */
    public function setCodigoretencion($codigoretencion)
    {
        $this->codigoretencion = $codigoretencion;

        return $this;
    }

    /**
     * Get codigoretencion
     *
     * @return string
     */
    public function getCodigoretencion()
    {
        return $this->codigoretencion;
    }

    /**
     * Set cuenta
     *
     * @param string $cuenta
     *
     * @return Docsaptipo
     */
    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;

        return $this;
    }

    /**
     * Get cuenta
     *
     * @return string
     */
    public function getCuenta()
    {
        return $this->cuenta;
    }

    /**
     * Set tiposervicio
     *
     * @param integer $tiposervicio
     *
     * @return Docsaptipo
     */
    public function setTiposervicio($tiposervicio)
    {
        $this->tiposervicio = $tiposervicio;

        return $this;
    }

    /**
     * Get tiposervicio
     *
     * @return integer
     */
    public function getTiposervicio()
    {
        return $this->tiposervicio;
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     *
     * @return Docsaptipo
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
     *
     * @return Docsaptipo
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
}
