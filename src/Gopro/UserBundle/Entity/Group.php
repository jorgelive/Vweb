<?php

namespace Gopro\UserBundle\Entity;

use Sonata\UserBundle\Entity\BaseGroup as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 *
 * @ORM\Table(name="fos_user_group")
 * @ORM\Entity
 */
class Group extends BaseGroup
{

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

}