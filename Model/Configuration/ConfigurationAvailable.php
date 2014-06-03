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

/**
 * Configuraciones
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class ConfigurationAvailable
{
    protected $configurations;
            
    function get($key,$default = null)
    {
        if(isset($this->configurations[$key])){
            return $this->configurations[$key]['value'];
        }
        return $default;
    }
    
    function getIdByKey($key)
    {
        if(isset($this->configurations[$key])){
            return $this->configurations[$key]['id'];
        }
        return null;
    }
    
    function hasKey($key)
    {
        if(isset($this->configurations[$key])){
            return true;
        }
        return false;
    }
}
