<?php
namespace Gopro\Vipac\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Gopro\Vipac\ProveedorBundle\Entity\Repository\InformacionadjuntoRepository")
 * @ORM\Table(name="pro_informacionadjunto")
 * @ORM\HasLifecycleCallbacks
 */
class Informacionadjunto
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extension;

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

    /**
     * @ORM\ManyToOne(targetEntity="Adjuntotipo")
     * @ORM\JoinColumn(name="adjuntotipo_id", referencedColumnName="id", nullable=false)
     */
    private $adjuntotipo;

    /**
     * @ORM\ManyToOne(targetEntity="Informacion")
     * @ORM\JoinColumn(name="informacion_id", referencedColumnName="id", nullable=false)
     */
    private $informacion;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $archivo;

    public function __toString()
    {
        return $this->getAdjuntotipo()->getNombre();
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
            $this->extension = $this->getArchivo()->guessExtension();
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
            $this->id.'.'.$this->getArchivo()->guessExtension()
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
        return __DIR__ . '/../../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'carga/informacionadjunto';
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
     * Set adjuntotipo
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Adjuntotipo $adjuntotipo
     * @return Archivo
     */
    public function setAdjuntotipo(\Gopro\Vipac\ProveedorBundle\Entity\Adjuntotipo $adjuntotipo)
    {
        $this->adjuntotipo = $adjuntotipo;

        return $this;
    }

    /**
     * Get adjuntotipo
     *
     * @return \Gopro\Vipac\ProveedorBundle\Entity\Adjuntotipo
     */
    public function getAdjuntotipo()
    {
        return $this->adjuntotipo;
    }

    /**
     * Set informacion
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Informacion $informacion
     * @return Archivo
     */
    public function setInformacion(\Gopro\Vipac\ProveedorBundle\Entity\Informacion $informacion)
    {
        $this->informacion = $informacion;

        return $this;
    }

    /**
     * Get informacion
     *
     * @return \Gopro\Vipac\ProveedorBundle\Entity\Informacion
     */
    public function getInformacion()
    {
        return $this->informacion;
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
