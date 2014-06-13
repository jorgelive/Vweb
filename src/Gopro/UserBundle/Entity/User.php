<?php

namespace Gopro\UserBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="fos_user_user")
 * @ORM\Entity
 */

class User extends BaseUser
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Dependencia", inversedBy="users")
     */
    protected $dependencia;

    /**
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="users")
     */
    protected $area;


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



}
