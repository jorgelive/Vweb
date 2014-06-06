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


}
