<?php

/*
 * This file is part of the TecnoReady Solutions C.A. package.
 * 
 * (c) www.tecnoready.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Twig\Extension;

use Tecnocreaciones\Bundle\ToolsBundle\Service\LinkGeneratorService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extension de link generador
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class LinkGeneratorExtension extends AbstractExtension
{
    /**
     *
     * @var LinkGeneratorService
     */
    private $linkGeneratorService;
    
    public function getFunctions() {
        return [
            new TwigFunction('path_object', array($this, 'pathObject'), array('is_safe' => array('html'))),
            new TwigFunction('path_object_url', array($this, 'pathObjectUrl'), array('is_safe' => array('html'))),
        ];
    }
    
    /**
     * Genera un link completo para mostrar el objeto
     * 
     * @param type $entity
     * @param type $type
     * @return type
     */
    function pathObject($entity, $type = LinkGeneratorService::TYPE_LINK_DEFAULT, array $parameters = array()) {
        if($entity === null){
            return "";
        }
        return $this->linkGeneratorService->generate($entity, $type, $parameters);
    }

    /**
     * Genera solo la url de el objeto
     * 
     * @param type $entity
     * @param type $type
     * @return type
     */
    function pathObjectUrl($entity, $type = LinkGeneratorService::TYPE_LINK_DEFAULT, array $parameters = array()) {
        if($entity === null){
            return "";
        }
        return $this->linkGeneratorService->generateOnlyUrl($entity, $type, $parameters);
    }
    
    public function getName() {
        return "link_generator_extension";
    }
    
    public function setLinkGeneratorService(LinkGeneratorService $linkGeneratorService) {
        $this->linkGeneratorService = $linkGeneratorService;
        return $this;
    }
}
