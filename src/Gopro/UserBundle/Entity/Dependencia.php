<?php

namespace Gopro\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Dependencia
 *
 * @ORM\Table(name="gp_dependencia")
 * @ORM\Entity
 */

class Dependencia
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $direccion;

    /**
     * @ORM\ManyToOne(targetEntity="Organizacion", inversedBy="dependencias")
     */
    protected $organizacion;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="dependencia")
     */
    protected $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * @return Dependencia
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
     * Set email
     *
     * @param string $email
     * @return Dependencia
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     * @return Dependencia
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string 
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set organizacion
     *
     * @param \Gopro\UserBundle\Entity\Organizacion $organizacion
     * @return Dependencia
     */
    public function setOrganizacion(\Gopro\UserBundle\Entity\Organizacion $organizacion = null)
    {
        $this->organizacion = $organizacion;

        return $this;
    }

    /**
     * Get organizacion
     *
     * @return \Gopro\UserBundle\Entity\Organizacion 
     */
    public function getOrganizacion()
    {
        return $this->organizacion;
    }

    /**
     * Add users
     *
     * @param \Gopro\UserBundle\Entity\User $users
     * @return Dependencia
     */
    public function addUser(\Gopro\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Gopro\UserBundle\Entity\User $users
     */
    public function removeUser(\Gopro\UserBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    function __toString()
    {
        return $this->getNombre();
    }
}
