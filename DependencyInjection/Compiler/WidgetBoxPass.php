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
use Tecnocreaciones\Bundle\ToolsBundle\Model\Block\DefinitionBlockWidgetBoxInterface;
use RuntimeException;

/**
 * Configura los widgets
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class WidgetBoxPass implements CompilerPassInterface 
{
    public function process(ContainerBuilder $container)
    {
        if($container->getParameter('tecnocreaciones_tools.service.widget_block_grid.enable') === false){
            return;
        }
        $definitionGridWidgetBox = $container->getDefinition('tecnocreaciones_tools.service.grid_widget_box');
        $definitionGridWidgetBox->addMethodCall("setOptions",array($container->getParameter("tecnocreaciones_tools.widget_block_grid.options")));
        $tags = $container->findTaggedServiceIds('tecno.block');
        $widgetIds = [];
        foreach ($tags as $id => $attributes) {
            
            $sonataBlockDefinition = $container->getDefinition($id);
            $class =  str_replace('%', '', $sonataBlockDefinition->getClass());
            if($container->hasParameter($class)){
                $class = $container->getParameter($class);
            }
            $reflectionClass = new ReflectionClass($class);
            if(!$reflectionClass->isSubclassOf(DefinitionBlockWidgetBoxInterface::class)){
                throw new RuntimeException(sprintf("The class '%s' must be inherit from '%s'",$class,DefinitionBlockWidgetBoxInterface::class));
            }
            $definitionGridWidgetBox->addMethodCall('addDefinitionsBlockGrid',array(new Reference($id)));
            $widgetIds[] = $id;
        }
        var_dump($widgetIds);
        die;
        $loaderWidget = $container->findDefinition("sonata.block.loader.service.widgets");
        $loaderWidget->addArgument($widgetIds);
    }
}
