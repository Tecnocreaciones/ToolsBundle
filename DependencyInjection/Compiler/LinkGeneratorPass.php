<?php

/*
 * This file is part of the TecnoReady Solutions C.A. package.
 * 
 * (c) www.tecnoready.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tecnocreaciones\Bundle\ToolsBundle\Service\LinkGeneratorService;

/**
 * Carga los items agregados como servicios
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class LinkGeneratorPass implements CompilerPassInterface 
{
    public function process(ContainerBuilder $container) {
        if($container->getParameter('tecnocreaciones_tools.service.link_generator.enable') === false){
            return;
        }
        $definition = $container->getDefinition(LinkGeneratorService::class);
        $tags = $container->findTaggedServiceIds('link_generator.item');
        
        foreach ($tags as $id => $attributes) {
            $itemDefinition = $container->getDefinition($id);
            $definition->addMethodCall('addLinkGeneratorItem',array($itemDefinition));
        }
    }
}
