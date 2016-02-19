<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\LinkGenerator;

use Tecnocreaciones\Bundle\ToolsBundle\Service\LinkGeneratorService;

/**
 * Interface de generador de link
 * 
 * @author Carlos Mendoza<inhack20@gmail.com>
 */
interface LinkGeneratorItemInterface
{
    public static function getConfigObjects();
    
    public function getIconsDefinition();
    
    public function setLinkGeneratorService(LinkGeneratorService $linkGeneratorService);
}
