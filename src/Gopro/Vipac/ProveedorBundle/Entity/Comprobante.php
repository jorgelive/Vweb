<?php

namespace Gopro\Vipac\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Comprobante
 *
 * @ORM\Table(name="pro_comprobante")
 * @ORM\Entity
 * @GRID\Source(columns="id, serie, numero")
 */
class Comprobante
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
     * @var integer
     * @Assert\NotBlank
     * @ORM\Column(name="nombre", type="integer", length=5)
     */
    private $serie;

    /**
     * @var integer
     * @Assert\NotBlank
     * @ORM\Column(name="descripcion", type="integer", length=10)
     */
    private $numero;

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
     * @ORM\OneToMany(targetEntity="Serviciofile", mappedBy="comprobante", cascade={"persist"})
     */
    private $serviciofiles;

    public function __construct() {
        $this->serviciofiles = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getSerie().'-'.$this->getNumero();
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
     * Set serie
     *
     * @param integer $serie
     * @return Comprobante
     */
    public function setSerie($serie)
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get serie
     *
     * @return integer 
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     * @return Comprobante
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     * @return Comprobante
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
     * @return Comprobante
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
     * Add serviciofiles
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Serviciofile $serviciofiles
     * @return Comprobante
     */
    public function addServiciofile(\Gopro\Vipac\ProveedorBundle\Entity\Serviciofile $serviciofiles)
    {
        $this->serviciofiles[] = $serviciofiles;

        return $this;
    }

    /**
     * Remove serviciofiles
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Serviciofile $serviciofiles
     */
    public function removeServiciofile(\Gopro\Vipac\ProveedorBundle\Entity\Serviciofile $serviciofiles)
    {
        $this->serviciofiles->removeElement($serviciofiles);
    }

    /**
     * Get serviciofiles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getServiciofiles()
    {
        return $this->serviciofiles;
    }
}
