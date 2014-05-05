<?php
namespace Gopro\Vipac\DbprocesoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="dbp_doccp",uniqueConstraints={
 *     @ORM\UniqueConstraint(name="search_idx", columns={"proveedor", "doctipo", "doc"})})
 * @ORM\HasLifecycleCallbacks
 */
class Doccp
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Doccpfile", mappedBy="doccp")
     **/
    private $files;

    /**
     * @ORM\OneToOne(targetEntity="Doccp")
     **/
    private $asociado;

    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="Doccpclase")
     * @Assert\NotBlank
     */
    private $doccpclase;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank
     */
    private $proveedor;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank
     */
    private $doctipo;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank
     */
    private $doc;

    /**
     * @ORM\Column(type="decimal")
     * @Assert\NotBlank
     */
    private $monto;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank
     */
    private $referencia;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @ORM\ManyToOne(targetEntity="Doccpproceso")
     */
    private $doccpproceso;

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
        $this->files = new ArrayCollection();
    }

}