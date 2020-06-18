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
use Tecnoready\Common\Model\Block\WidgetInterface;
use RuntimeException;
use Tecnoready\Common\Service\Block\WidgetManager;

/**
 * Configura los widgets
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class WidgetBoxPass implements CompilerPassInterface 
{
    public function process(ContainerBuilder $container)
    {
        if($container->getParameter('tecnocreaciones_tools.service.widget.enable') === false){
            return;
        }
        $options = $container->getParameter("tecnocreaciones_tools.widget.options");
//        var_dump($options);
//        die;
        $widgetManager = $container->getDefinition(WidgetManager::class);
        $widgetManager->addMethodCall("setOptions",array($options));
        $widgetManager->addArgument(new Reference($options["widget_adapter"]));
        $tags = $container->findTaggedServiceIds('tecno.block');
        $widgetIds = [];
        foreach ($tags as $id => $attributes) {
            
            $sonataBlockDefinition = $container->getDefinition($id);
            $class =  str_replace('%', '', $sonataBlockDefinition->getClass());
            if($container->hasParameter($class)){
                $class = $container->getParameter($class);
            }
            $reflectionClass = new ReflectionClass($class);
            if(!$reflectionClass->isSubclassOf(WidgetInterface::class)){
                throw new RuntimeException(sprintf("The class '%s' must be inherit from '%s'",$class,WidgetInterface::class));
            }
            $widgetManager->addMethodCall('addWidget',array(new Reference($id)));
            $widgetIds[] = $id;
        }
//        var_dump($widgetIds);
//        die;
    }
}
