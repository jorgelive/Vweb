<?php

namespace Gopro\UserBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
 *
 * @ORM\Table(name="fos_user_user")
 * @ORM\Entity
 */

class User extends BaseUser
{

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \Gopro\UserBundle\Entity\Dependencia
     *
     * @ORM\ManyToOne(targetEntity="Dependencia", inversedBy="users")
     */
    protected $dependencia;

    /**
     * @var \Gopro\UserBundle\Entity\Area
     *
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="users")
     */
    protected $area;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Cuenta", mappedBy="user", cascade={"persist"})
     */
    private $cuentas;

    public function __construct() {

        parent::__construct();

        $this->cuentas = new ArrayCollection();

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
     * Set dependencia
     *
     * @param \Gopro\UserBundle\Entity\Dependencia $dependencia
     * @return User
     */
    public function setDependencia(\Gopro\UserBundle\Entity\Dependencia $dependencia = null)
    {
        $this->dependencia = $dependencia;

        return $this;
    }

    /**
     * Get dependencia
     *
     * @return \Gopro\UserBundle\Entity\Dependencia 
     */
    public function getDependencia()
    {
        return $this->dependencia;
    }


    /**
     * Set area
     *
     * @param \Gopro\UserBundle\Entity\Area $area
     * @return User
     */
    public function setArea(\Gopro\UserBundle\Entity\Area $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \Gopro\UserBundle\Entity\Area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Get area
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->getFirstname().' '.$this->getLastname();
    }

    /**
     * Add cuenta
     *
     * @param \Gopro\UserBundle\Entity\Cuenta $cuentas
     * @return User
     */
    public function addCuenta(\Gopro\UserBundle\Entity\Cuenta $cuentas)
    {
        $this->cuentas[] = $cuentas;

        return $this;
    }

    /**
     * Remove cuenta
     *
     * @param \Gopro\UserBundle\Entity\Cuenta $cuenta
     */
    public function removeCuenta(\Gopro\UserBundle\Entity\Cuenta $cuenta)
    {
        $this->cuentas->removeElement($cuenta);
    }

    /**
     * Get cuentas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCuentas()
    {
        return $this->cuentas;
    }



}
