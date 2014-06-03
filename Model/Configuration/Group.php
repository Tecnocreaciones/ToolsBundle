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

/**
 * Grupo de configuracion
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 * @ORM\MappedSuperclass
 */
abstract class Group 
{
    /**
     * Nombre del grupo
     * 
     * @var string
     * @ORM\Column(name="name", type="string",length=200)
     */
    protected $name;
    
    /**
     * Descripcion del grupo
     * 
     * @var string
     * @ORM\Column(name="description", type="string",length=200)
     */
    protected $description;
    
    /**
     * 
     * @var boolean
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    protected $active = true;
    
    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setName($name) {
        $this->name = $name;
        
        return $this;
    }

    public function setDescription($description) {
        $this->description = $description;
        
        return $this;
    }
    
    public function getActive() {
        return $this->active;
    }

    public function setActive($active) {
        $this->active = $active;
        
        return $this;
    }
}
