<?php

/*
 * This file is part of the Witty Growth C.A. - J406095737 package.
 * 
 * (c) www.mpandco.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Agrega todos los wrapper al servicio de configuracion
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ConfigurationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container) {
        if($container->getParameter('tecnocreaciones_tools.service.configuration_manager.enable') === false){
            return;
        }
        $manager = $container->getDefinition($container->getParameter("tecnocreaciones_tools.configuration_manager.name"));
        $tags = $container->findTaggedServiceIds('configuration.wrapper');
        $serviceContainer = new \Symfony\Component\DependencyInjection\Reference("service_container");
        foreach ($tags as $id => $params) {
            $definition = $container->findDefinition($id);
            $reflection = new \ReflectionClass($definition->getClass());
            if($reflection->isSubclassOf("Symfony\Component\DependencyInjection\ContainerAwareInterface")){
                $definition->addMethodCall("setContainer",[$serviceContainer]);
            }
            $manager->addMethodCall("addWrapper",[$definition]);
        }
    }
}
