<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\Intro;

/**
 * Servicio de introduccion
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class IntroService 
{
    protected $adapters;
    
    protected $areas;
    
    public function __construct() {
        $this->adapters = array();
    }
    
    function addAdapter(Adapter\IntroAdapterInterface $adapter)
    {
        
    }
    
    function getAreas() {
        return $this->areas;
    }

    function setAreas(array $areas) 
    {
        $this->areas = $areas;
    }

}
