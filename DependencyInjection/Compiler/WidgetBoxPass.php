<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\DependencyInjection\Compiler;

use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Description of WidgetBoxPass
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class WidgetBoxPass implements CompilerPassInterface 
{
    public function process(ContainerBuilder $container)
    {
        $definitionGridWidgetBox = $container->getDefinition('tecnocreaciones_tools.service.grid_widget_box');
        $tags = $container->findTaggedServiceIds('sonata.block');
        foreach ($tags as $id => $attributes) {
            
            $sonataBlockDefinition = $container->getDefinition($id);
            $class =  str_replace('%', '', $sonataBlockDefinition->getClass());
            if($container->hasParameter($class)){
                $class = $container->getParameter($class);
            }
            $reflectionClass = new ReflectionClass($class);
            if($reflectionClass->isSubclassOf('Tecnocreaciones\Bundle\ToolsBundle\Model\Block\DefinitionBlockWidgetBoxInterface')){
                $definitionGridWidgetBox->addMethodCall('addDefinitionsBlockGrid',array(new Reference($id)));
            }
        }
    }
}
