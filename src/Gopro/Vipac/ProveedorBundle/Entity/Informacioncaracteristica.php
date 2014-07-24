<?php

namespace Gopro\Vipac\ProveedorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Informacioncaracteristica
 *
 * @ORM\Table(name="pro_informacioncaracteristica")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Informacioncaracteristica
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="texto", type="string", length=255, nullable=true)
     */
    private $texto;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero", type="integer", nullable=true)
     */
    private $numero;

    /**
     * @var date
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var boolean
     *
     * @ORM\Column(name="booleano", type="boolean", nullable=true)
     */
    private $booleano;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $archivocargado;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $archivo;

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
     * @ORM\ManyToOne(targetEntity="Informacion", inversedBy="informacioncaracteristicas")
     * @ORM\JoinColumn(name="informacion_id", referencedColumnName="id", nullable=false)
     */
    private $informacion;

    /**
     * @ORM\ManyToOne(targetEntity="Caracteristica", inversedBy="informacioncaracteristicas")
     * @ORM\JoinColumn(name="caracteristica_id", referencedColumnName="id", nullable=false)
     */
    private $caracteristica;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getContenido();
    }

    public function getContenido(){
        if(!empty($this->getTexto())){
            return $this->getTexto();
        }elseif(!empty($this->getTexto())){
            return $this->getNumero();
        }
    }

    /**
     * Sets archivocargado.
     *
     * @param UploadedFile $archivocargado
     */
    public function setArchivocargado(UploadedFile $archivocargado=null)
    {
        $this->archivocargado=$archivocargado;
        if(is_file($this->getAbsolutePath())) {
            $this->temp=$this->getAbsolutePath();
        }else{
            $this->archivo=null;
        }
    }

    /**
     * Get archivocargado.
     *
     * @return UploadedFile
     */
    public function getArchivocargado()
    {
        return $this->archivocargado;
    }

    private $temp;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if(null !== $this->getArchivocargado()){
            $this->archivo = $this->getArchivocargado()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getArchivocargado()) {
            return;
        }
        if (isset($this->temp)) {
            unlink($this->temp);
            $this->temp = null;
        }

        $this->getArchivocargado()->move(
            $this->getUploadRootDir(),
            $this->id.'.'.$this->getArchivocargado()->guessExtension()
        );

        $this->setArchivocargado(null);
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
        return null === $this->archivo
            ? null
            : $this->getUploadRootDir().'/'.$this->id.'.'.$this->archivo;
    }

    public function getWebPath()
    {
        return null === $this->archivo
            ? null
            : $this->getUploadDir().'/'.$this->id.'.'.$this->archivo;
    }

    public function getThumbPath()
    {
        if($this->archivo===null){
            return null;
        }
        if(in_array($this->archivo,['jpg','jpeg','png',''])){
            return $this->getUploadDir() . '/thumb/'.$this->id.'.'.$this->archivo;

        }else{
            return $this->getUploadDir() . '/bundles/goprovipacmain/img/iconos/'.$this->archivo.'.png';
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
     * Set texto
     *
     * @param string $texto
     * @return Informacioncaracteristica
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;

        return $this;
    }

    /**
     * Get texto
     *
     * @return string 
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     * @return Informacioncaracteristica
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Informacioncaracteristica
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set booleano
     *
     * @param boolean $booleano
     * @return Informacioncaracteristica
     */
    public function setBooleano($booleano)
    {
        $this->booleano = $booleano;

        return $this;
    }

    /**
     * Get booleano
     *
     * @return boolean 
     */
    public function getBooleano()
    {
        return $this->booleano;
    }

    /**
     * Set archivo
     *
     * @param string $archivo
     * @return Informacioncaracteristica
     */
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;

        return $this;
    }

    /**
     * Get archivo
     *
     * @return string 
     */
    public function getArchivo()
    {
        return $this->archivo;
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     * @return Informacioncaracteristica
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
     * @return Informacioncaracteristica
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

    /**
     * Set informacion
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Informacion $informacion
     * @return Informacioncaracteristica
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
     * Set caracteristica
     *
     * @param \Gopro\Vipac\ProveedorBundle\Entity\Caracteristica $caracteristica
     * @return Informacioncaracteristica
     */
    public function setCaracteristica(\Gopro\Vipac\ProveedorBundle\Entity\Caracteristica $caracteristica)
    {
        $this->caracteristica = $caracteristica;

        return $this;
    }

    /**
     * Get caracteristica
     *
     * @return \Gopro\Vipac\ProveedorBundle\Entity\Caracteristica 
     */
    public function getCaracteristica()
    {
        return $this->caracteristica;
    }
}
