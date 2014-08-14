<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Configuration;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Modelo de configuracion
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks()
 */
abstract class Configuration 
{
    /**
     * Indice de configuracion
     * 
     * @var string
     * @ORM\Column(name="keyIndex", type="string",length=200)
     */
    protected $key;
    
    /**
     * Valor de configuracion
     * 
     * @var string
     * @ORM\Column(name="value", type="string",length=200)
     */
    protected $value;
    
    /**
     * Descripcion de la configuracion
     * 
     * @var string
     * @ORM\Column(name="description", type="string",length=200)
     */
    protected $description;
    
    /**
     * Grupo de la configuracion
     * 
     * @var \Tecnocreaciones\Bundle\ToolsBundle\Entity\Configuration\BaseGroup
     * @ORM\ManyToOne(targetEntity="Tecnocreaciones\Bundle\ToolsBundle\Entity\Configuration\BaseGroup")
     */
    protected $group;
    
    /**
     * Valor de configuracion
     * 
     * @var boolean
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active = true;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime",nullable=true)
     */
    protected $updatedAt;
    
    public function getKey() {
        return $this->key;
    }

    public function getValue() {
        return $this->value;
    }

    public function setKey($key) {
        $this->key = $key;
        
        return $this;
    }

    public function setValue($value) {
        $this->value = $value;
        
        return $this;
    }
    
    public function getActive() {
        return $this->active;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setActive($active) {
        $this->active = $active;
        
        return $this;
    }
    
    /**
     * 
     * @param \DateTime $createdAt
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
        
        return $this;
    }
    
    /**
     * 
     * @param \DateTime $createdAt
     * @ORM\PreUpdate
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
        
        return $this;
    }

    abstract function getId();
    
    function setId($id)
    {
        $this->id = $id;
    }
    
    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }
    
    public function getGroup() {
        return $this->group;
    }

    public function setGroup(\Tecnocreaciones\Bundle\ToolsBundle\Entity\Configuration\BaseGroup $group)
    {
        $this->group = $group;
        return $this;
    }
}
