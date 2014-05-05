<?php
namespace Gopro\Vipac\DbprocesoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="dbp_doccpclase")
 * @ORM\HasLifecycleCallbacks
 */
class Doccpclase
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Doccp", mappedBy="doccpclase")
     **/
    private $doccps;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $subtotal;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $rubro1;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $rubro2;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $impuesto1;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $impuesto2;

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

    public function __construct() {
        $this->doccps = new ArrayCollection();
    }

}