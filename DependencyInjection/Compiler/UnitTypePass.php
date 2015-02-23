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

/**
 * Description of UnitTypePass
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class UnitTypePass implements \Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    public function process(\Symfony\Component\DependencyInjection\ContainerBuilder $container) {
        $unitConverterDefinition = new \Symfony\Component\DependencyInjection\Definition($container->getParameter('tecnocreaciones_tools.unit_converter.class'));
        $cache_dir = $container->getParameter('kernel.cache_dir') . DIRECTORY_SEPARATOR .'tecnocreaciones_tools';
        $optionsUnitConverter = array('cache_dir' => $cache_dir);
        $unitConverterDefinition->addArgument($optionsUnitConverter);
        
        $tags = $container->findTaggedServiceIds('tecnocreaciones_tools.unit_converter.unit');
        foreach ($tags as $id => $attributes) {
            $unitConverterDefinition->addMethodCall('addUnit',array(new \Symfony\Component\DependencyInjection\Reference($id)));
        }
        
        $serviceName = $container->getParameter('tecnocreaciones_tools.unit_converter.service_name');
        $container->setDefinition($serviceName,$unitConverterDefinition);
    }
}
