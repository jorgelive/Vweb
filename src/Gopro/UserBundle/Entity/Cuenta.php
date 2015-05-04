<?php

namespace Gopro\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * cuenta
 *
 * @ORM\Table(name="use_cuenta")
 * @ORM\Entity
 * @GRID\Source(columns="id, cuentatipo.nombre, nombre")
 */
class Cuenta
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Grid\Column(visible=false, field="id", title="ID")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     * @Assert\NotBlank
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=100)
     * @Assert\NotBlank
     */
    private $password;

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
     * @var \Gopro\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="cuentas")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * @Grid\Column(filter="select", field="user.username", title="Usuario")
     */
    private $user;

    /**
     * @var \Gopro\UserBundle\Entity\Cuentatipo
     *
     * @ORM\ManyToOne(targetEntity="Cuentatipo", inversedBy="cuentas")
     * @ORM\JoinColumn(name="cuentatipo_id", referencedColumnName="id", nullable=false)
     * @Grid\Column(filter="select", field="cuentatipo.nombre", title="Tipo de Cuenta")
     */
    private $cuentatipo;

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getNombre();
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
     * @return Cuenta
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
     * Set password
     *
     * @param string $password
     * @return Cuenta
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     * @return Cuenta
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
     * @return Cuenta
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
     * Set user
     *
     * @param \Gopro\UserBundle\Entity\User $user
     * @return Cuenta
     */
    public function setUser(\Gopro\UserBundle\Entity\User $user = null)
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

    /**
     * Set cuentatipo
     *
     * @param \Gopro\UserBundle\Entity\cuentatipo $cuentatipo
     * @return Cuenta
     */
    public function setCuentatipo(\Gopro\UserBundle\Entity\Cuentatipo $cuentatipo = null)
    {
        $this->cuentatipo = $cuentatipo;

        return $this;
    }

    /**
     * Get cuentatipo
     *
     * @return \Gopro\UserBundle\Entity\cuentatipo
     */
    public function getCuentatipo()
    {
        return $this->cuentatipo;
    }

}
