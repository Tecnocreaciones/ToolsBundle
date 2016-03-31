<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * Modelo base para los maestros
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 * @ORM\MappedSuperclass()
 */
abstract class BaseModelMaster extends BaseModel 
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    protected $active = true;
    
    function getActive() {
        return $this->active;
    }

    function setActive($active) {
        $this->active = $active;
        
        return $this;
    }
}
