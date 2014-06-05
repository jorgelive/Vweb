<?php

namespace Gopro\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * User
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
}