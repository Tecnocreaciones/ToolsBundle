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
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Agrega todos los wrapper al servicio de configuracion
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ConfigurationPass implements CompilerPassInterface {
    
    public function process(ContainerBuilder $container) {
        if ($container->getParameter('tecnocreaciones_tools.service.configuration_manager.enable') === false) {
            return;
        }
        $config = $container->getParameter("tecnocreaciones_tools.configuration_manager.configuration");

        $configurationClass = \Tecnocreaciones\Bundle\ToolsBundle\Entity\Configuration\Configuration::class;
        $configurationManagerClass = $config['configuration_manager_class'];
        $configurationManagerNameService = $config['configuration_name_service'];
        $reflectionConfigurationClass = new ReflectionClass($configurationClass);
        if ($reflectionConfigurationClass->isSubclassOf('Tecnoready\Common\Model\Configuration\BaseEntity\DoctrineORMConfiguration') === false) {
            throw new LogicException(
            'The "' . $reflectionConfigurationClass->getName() . '" must inherit from Tecnoready\\Common\\Model\\Configuration\\BaseEntity\\DoctrineORMConfiguration'
            );
        }
        if (isset($config['debug'])) {
            $debug = $config['debug'];
        } else {
            $debug = $container->getParameter('kernel.debug');
        }
        $doctrine2Adapter = new Definition("Tecnocreaciones\Bundle\ToolsBundle\Service\ConfigurationService\Adapter\DoctrineORMAdapter");
        $doctrine2Adapter->addArgument(new Reference('doctrine.orm.entity_manager'));
        $container->setDefinition("configuration.adapter.doctrine_orm", $doctrine2Adapter);
        
        $configurationManager = new Definition($configurationManagerClass, [
            $container->getDefinition("configuration.adapter.doctrine_orm"), [
                "cache_dir" => $container->getParameter('kernel.cache_dir'),
                "add_default_wrapper" => true,
                "debug" => $debug,
            ]
        ]);

        $container->setDefinition($configurationManagerNameService, $configurationManager);
        $container->setParameter('tecnocreaciones_tools.configuration_manager.name', $configurationManagerNameService);

        $extensionToolsDefinition = new Definition('Tecnocreaciones\Bundle\ToolsBundle\Twig\Extension\GlobalConfExtension');
        $extensionToolsDefinition
                ->addMethodCall('setContainer', array(new Reference('service_container')))
                ->addTag('twig.extension')
        ;
        $container->setDefinition('tecnocreaciones_tools.global_config_extension', $extensionToolsDefinition);

        $manager = $container->getDefinition($container->getParameter("tecnocreaciones_tools.configuration_manager.name"));
        $tags = $container->findTaggedServiceIds('configuration.wrapper');
        $serviceContainer = new \Symfony\Component\DependencyInjection\Reference("service_container");
        foreach ($tags as $id => $params) {
            $definition = $container->findDefinition($id);
            $reflection = new \ReflectionClass($definition->getClass());
            if ($reflection->isSubclassOf("Symfony\Component\DependencyInjection\ContainerAwareInterface")) {
                $definition->addMethodCall("setContainer", [$serviceContainer]);
            }
            $manager->addMethodCall("addWrapper", [$definition]);
        }
    }

}
