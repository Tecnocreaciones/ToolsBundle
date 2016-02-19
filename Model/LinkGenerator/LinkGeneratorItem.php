<?php

/*
 * This file is part of the TecnoReady Solutions C.A. package.
 * 
 * (c) www.tecnoready.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\LinkGenerator;

use Tecnocreaciones\Bundle\ToolsBundle\Service\LinkGeneratorService;

/**
 * Base de item de generador de link
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class LinkGeneratorItem implements LinkGeneratorItemInterface 
{
    /**
     *
     * @var LinkGeneratorService 
     */
    protected $linkGeneratorService;
    
    public function setLinkGeneratorService(LinkGeneratorService $linkGeneratorService) {
        $this->linkGeneratorService = $linkGeneratorService;
        return $this;
    }
}
