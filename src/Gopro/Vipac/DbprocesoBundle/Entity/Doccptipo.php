<?php
namespace Gopro\Vipac\DbprocesoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="dbp_doccptipo")
 * @ORM\HasLifecycleCallbacks
 */
class Doccptipo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=5)
     * @Assert\NotBlank
     */
    private $tipo;

    /**
     * @ORM\Column(type="string", length=2)
     * @Assert\NotBlank
     */
    private $subtipo;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $subtotal;

    /**
     * @ORM\Column(type="boolean")
     */
    private $impuesto1;

    /**
     * @ORM\Column(type="boolean")
     */
    private $impuesto2;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $rubro1;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $rubro2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rubro2porcentaje;

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
     * @return Doccptipo
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
     * Set tipo
     *
     * @param string $tipo
     * @return Doccptipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set subtipo
     *
     * @param string $subtipo
     * @return Doccptipo
     */
    public function setSubtipo($subtipo)
    {
        $this->subtipo = $subtipo;

        return $this;
    }

    /**
     * Get subtipo
     *
     * @return string
     */
    public function getSubtipo()
    {
        return $this->subtipo;
    }

    /**
     * Set subtotal
     *
     * @param string $subtotal
     * @return Doccptipo
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return string 
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set impuesto1
     *
     * @param boolean $impuesto1
     * @return Doccptipo
     */
    public function setImpuesto1($impuesto1)
    {
        $this->impuesto1 = $impuesto1;

        return $this;
    }

    /**
     * Get impuesto1
     *
     * @return boolean 
     */
    public function getImpuesto1()
    {
        return $this->impuesto1;
    }

    /**
     * Set impuesto2
     *
     * @param string $impuesto2
     * @return Doccptipo
     */
    public function setImpuesto2($impuesto2)
    {
        $this->impuesto2 = $impuesto2;

        return $this;
    }

    /**
     * Get impuesto2
     *
     * @return string 
     */
    public function getImpuesto2()
    {
        return $this->impuesto2;
    }

    /**
     * Set rubro1
     *
     * @param string $rubro1
     * @return Doccptipo
     */
    public function setRubro1($rubro1)
    {
        $this->rubro1 = $rubro1;

        return $this;
    }

    /**
     * Get rubro1
     *
     * @return string 
     */
    public function getRubro1()
    {
        return $this->rubro1;
    }

    /**
     * Set rubro2
     *
     * @param string $rubro2
     * @return Doccptipo
     */
    public function setRubro2($rubro2)
    {
        $this->rubro2 = $rubro2;

        return $this;
    }

    /**
     * Get rubro2
     *
     * @return string 
     */
    public function getRubro2()
    {
        return $this->rubro2;
    }

    /**
     * Set rubro2porcentaje
     *
     * @param string $rubro2porcentaje
     * @return Doccptipo
     */
    public function setRubro2porcentaje($rubro2porcentaje)
    {
        $this->rubro2porcentaje = $rubro2porcentaje;

        return $this;
    }

    /**
     * Get rubro2porcentaje
     *
     * @return string
     */
    public function getRubro2porcentaje()
    {
        return $this->rubro2porcentaje;
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     * @return Doccptipo
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
     * @return Doccptipo
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
