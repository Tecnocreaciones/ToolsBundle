<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\ORM\Document;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Tecnocreaciones\Bundle\ToolsBundle\Model\Base\BaseModel;

/**
 * Base de documento
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 * @ORM\MappedSuperclass()
 */
abstract class ModelDocument extends BaseModel
{
    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @ORM\Column(type="text",nullable=false)
     */
    protected $path;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $hash;
    
    /**
     * Extension del archivo
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    protected $extension;
    
    /**
     * Mime Type
     * @ORM\Column(name="mime_type",type="string", length=40, nullable=false)
     */
    protected $mimeType;
    
    public $file;
    
    private $temp;
    
    private $old;
    
    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        $uploadPath = __DIR__.'/../../../../../../var/'.$this->getUploadDir();
        if(!is_dir($uploadPath)){
            mkdir($uploadPath, 0777, true);
        }
        return $uploadPath;
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        $dir = 'uploads/';
        return $dir;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        $this->name = $file->getClientOriginalName();
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }
    
    private function generateUniqueID() { 
        return sprintf("%s-%s",$this->strRandom(),$this->strRandom());
    }
    
    private function strRandom($length = 36)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Document
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Document
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return Document
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }
    
    public function getExtension() {
        return $this->extension;
    }

    public function getMimeType() {
        return $this->mimeType;
    }
    
    public function getId() {
        return $this->id;
    }

    /**
     * Set extension
     *
     * @param string $extension
     *
     * @return Document
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     *
     * @return Document
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }
    
    public abstract function getSubDir();
    
    public abstract function isDebug();
        
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = $this->generateUniqueID();
            
            $this->hash = $this->generateUniqueID();
            
            $extension = pathinfo($this->name,PATHINFO_EXTENSION);
            if($extension === null){
                $extension = $this->getFile()->getExtension();
            }
            if(empty($extension)){
                $extension = $this->getFile()->guessExtension();
            }
            $path = "";
            if($this->isDebug() === true){
                $path = 'debug/';
            }else{
                $path = 'prod/';
            }
            $date = new \DateTime();
            $dPath = sprintf("%s/%s/%s/%s/%s/%s",$date->format("Y"),$date->format("m"),$date->format("d"),$date->format("H"),$date->format("i"), substr(md5($date->getTimestamp()),0, 3));
            $path = sprintf("%s%s/%s/%s.%s",$path,$this->getSubDir(),$dPath,$filename,$extension);
            
            $this->path = $path;
            $this->extension = $extension;
            $this->mimeType = $this->getFile()->getMimeType();
        }
    }
    
    /**
     * @ORM\PreRemove()
     */
    public function preRemove() {
        $this->old = clone $this;
    }
    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $old = $this->old;
        $this->old = null;
        if($old){
            $old->old = null;
            $old->removeUpload();
            return;
        }
        if (($file = $this->getAbsolutePath()) && file_exists($file)) {
            @unlink($file);
        }
    }
    
    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }
        
        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $dir = pathinfo($this->path,PATHINFO_DIRNAME);
        $filename = pathinfo($this->path,PATHINFO_FILENAME);
        if($this->extension){
            $filename = $filename.".".$this->extension;
        }
        $this->getFile()->move($this->getUploadRootDir().$dir,$filename);

        // check if we have an old image
        if ($this->temp) {
            // delete the old image
            @unlink($this->getUploadRootDir().'/'.$this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }
    
    /**
     * @param type $className
     * @return BaseDocument
     */
    public function cloneToObject($className) {
        $clone = new $className();
//        $clone = new self();
        $clone->setExtension($this->getExtension());
        $clone->setHash($this->getHash());
        $clone->setMimeType($this->getMimeType());
        $clone->setName($this->getName());
        $clone->setPath($this->getPath());
        return $clone;
    }
    
}