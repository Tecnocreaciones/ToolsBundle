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
class ConfigurationPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if ($container->getParameter('tecnocreaciones_tools.service.tabs.enable') === true) {
            $tabs = $container->getParameter("tecnocreaciones_tools.service.tabs");
            $adapterDefinition = $container->findDefinition($tabs["document_manager"]["adapter"]);
            $definitionDocumentManager = $container->findDefinition("tecnoready.document_manager");
            $definitionDocumentManager->addArgument($adapterDefinition);

            $idHistoryManagerAdapter = $tabs["history_manager"]["adapter"];
            if($container->hasDefinition($idHistoryManagerAdapter)){
                $adapterDefinition = $container->findDefinition($idHistoryManagerAdapter);
                $historyManagerDefinition = $container->findDefinition("tecnoready.history_manager");
                $historyManagerDefinition->addArgument($adapterDefinition);
            }

            $idNoteManagerAdapter = $tabs["note_manager"]["adapter"];
            if($container->hasDefinition($idNoteManagerAdapter)){
                $adapterDefinition = $container->findDefinition($idNoteManagerAdapter);
                $noteManagerDefinition = $container->findDefinition("tecnoready.note_manager");
                $noteManagerDefinition->addArgument($adapterDefinition);
            }

            //Exportador
            $exporter = $container->getDefinition("app.service.exporter");
            $chaines = $container->findTaggedServiceIds("exporter.chain");
            $models = $container->findTaggedServiceIds("exporter.chain.model");
            foreach ($models as $id => $model) {
                $idChain = $model[0]["chain"];
                if (!isset($chaines[$idChain])) {
                    throw new \InvalidArgumentException(sprintf("The exporter chain '%s' is not exists.", $idChain));
                }
                $chain = $container->getDefinition($idChain);
                $chain->addMethodCall("add", [$container->getDefinition($id)]);
            }
            foreach ($chaines as $id => $chain) {
                $exporter->addMethodCall("addChainModel", [$container->getDefinition($id)]);
            }
        }


        if ($container->getParameter('tecnocreaciones_tools.service.configuration_manager.enable') === false) {
            return;
        }

        $config = $container->getParameter("tecnocreaciones_tools.configuration_manager.configuration");

        $configurationClass = $container->getParameter("tecnocreaciones_tools.configuration_class.class");
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

        $configurationManager = new Definition($configurationManagerClass, [
            new Reference($config['adapter']), new Reference($config['cache']), [
                "add_default_wrapper" => true,
                "debug" => $debug,
            ]
        ]);
        $configurationManager->setPublic(true);
        $tags = $container->findTaggedServiceIds('configuration.transformer');
        foreach ($tags as $id => $params) {
            $definition = $container->findDefinition($id);
            $configurationManager->addMethodCall("addTransformer", [$definition]);
        }
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
