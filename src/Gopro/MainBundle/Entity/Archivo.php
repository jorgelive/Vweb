<?php
namespace Gopro\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Gopro\MainBundle\Entity\Repository\ArchivoRepository")
 * @ORM\Table(name="mai_archivo")
 * @ORM\HasLifecycleCallbacks
 */
class Archivo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extension;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $operacion;

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
     * @ORM\ManyToOne(targetEntity="Gopro\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $archivo;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->nombre;
    }

    /**
     * Sets archivo.
     *
     * @param UploadedFile $archivo
     */
    public function setArchivo(UploadedFile $archivo = null)
    {
        $this->archivo = $archivo;
        if (is_file($this->getAbsolutePath())) {
            $this->temp = $this->getAbsolutePath();
        } else {
             $this->extension = 'initial';
        }
    }

    /**
     * Get archivo.
     *
     * @return UploadedFile
     */
    public function getArchivo()
    {
        return $this->archivo;
    }

    private $temp;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getArchivo()) {
            !empty($this->getArchivo()->guessExtension()) ? $this->extension = $this->getArchivo()->guessExtension() : $this->extension = $this->getArchivo()->getClientOriginalExtension();
            $this->nombre = preg_replace('/\.[^.]*$/', '', $this->getArchivo()->getClientOriginalName());
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getArchivo()) {
            return;
        }
        if (isset($this->temp)) {
            unlink($this->temp);
            $this->temp = null;
        }

        $this->getArchivo()->move(
            $this->getUploadRootDir(),
            $this->id.'.'.$this->extension
        );

        $this->setArchivo(null);
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->temp = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (isset($this->temp)) {
            unlink($this->temp);
        }
    }

    public function getAbsolutePath()
    {
        return null === $this->extension
            ? null
            : $this->getUploadRootDir().'/'.$this->id.'.'.$this->extension;
    }

    public function getWebPath()
    {
        return null === $this->extension
            ? null
            : $this->getUploadDir() . '/'.$this->id.'.'.$this->extension;
    }

    public function getThumbPath()
    {
        if($this->extension===null){
            return null;
        }
        if(in_array($this->extension,['jpg','jpeg','png',''])){
            return $this->getUploadDir() . '/thumb/'.$this->id.'.'.$this->extension;

        }else{
            return $this->getUploadDir() . '/bundles/goprovipacmain/img/iconos/'.$this->extension.'.png';
        }
    }

    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'carga/archivos';
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
     * @return Archivo
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
     * Set extension
     *
     * @param string $extension
     * @return Archivo
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set user
     *
     * @param \Gopro\UserBundle\Entity\User $user
     * @return Archivo
     */
    public function setUser(\Gopro\UserBundle\Entity\User $user)
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
     * Set operacion
     *
     * @param string $operacion
     * @return Archivo
     */
    public function setOperacion($operacion)
    {
        $this->operacion = $operacion;

        return $this;
    }

    /**
     * Get operacion
     *
     * @return string 
     */
    public function getOperacion()
    {
        return $this->operacion;
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     * @return Archivo
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
     * @return Archivo
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
}
