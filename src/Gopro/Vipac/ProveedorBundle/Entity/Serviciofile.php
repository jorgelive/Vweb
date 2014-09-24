<?php

namespace Gopro\Vipac\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Serviciofile
 * @ORM\Table(name="pro_serviciofile")
 * @ORM\Entity
 * @GRID\Source(columns="id, file, numpax")
 */
class Serviciofile
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
     * @ORM\Column(name="nombre", type="string", length=20)
     * @Assert\NotBlank
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="numpax", type="integer")
     * @Assert\NotBlank
     */
    private $numpax;

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
     * @ORM\ManyToOne(targetEntity="Servicio", inversedBy="serviciofiles")
     */
    private $servicio;

    /**
     * @ORM\ManyToOne(targetEntity="Comprobante", inversedBy="serviciofiles")
     */
    private $comprobante;

    public function __construct() {

    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getFile();
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
     * Set file
     *
     * @param string $file
     * @return Serviciofile
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set numpax
     *
     * @param integer $numpax
     * @return Serviciofile
     */
    public function setNumpax($numpax)
    {
        $this->numpax = $numpax;

        return $this;
    }

    /**
     * Get numpax
     *
     * @return integer 
     */
    public function getNumpax()
    {
        return $this->numpax;
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     * @return Serviciofile
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
     * @return Serviciofile
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
     * Set servicio
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Servicio $servicio
     * @return Serviciofile
     */
    public function setServicio(\Gopro\Vipac\ProveedorBundle\Entity\Servicio $servicio = null)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio
     *
     * @return \Gopro\Vipac\ProveedorBundle\Entity\Servicio 
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * Set comprobante
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Comprobante $comprobante
     * @return Serviciofile
     */
    public function setComprobante(\Gopro\Vipac\ProveedorBundle\Entity\Comprobante $comprobante = null)
    {
        $this->comprobante = $comprobante;

        return $this;
    }

    /**
     * Get comprobante
     *
     * @return \Gopro\Vipac\ProveedorBundle\Entity\Comprobante 
     */
    public function getComprobante()
    {
        return $this->comprobante;
    }
}
