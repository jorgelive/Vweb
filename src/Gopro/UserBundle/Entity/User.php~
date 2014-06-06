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
}