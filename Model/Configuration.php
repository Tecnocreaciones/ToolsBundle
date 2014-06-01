<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Modelo de configuracion
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class Configuration 
{
    
    protected $id;
    
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
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     * @Gedmo\Timestampable(on="update")
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
    }

    public function setValue($value) {
        $this->value = $value;
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
    }
    
    /**
     * 
     * @param \DateTime $createdAt
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
    }
    
    /**
     * 
     * @param \DateTime $createdAt
     * @ORM\PrePersist
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }

    abstract function getId();
    
    function setId($id)
    {
        $this->id = $id;
    }
}
