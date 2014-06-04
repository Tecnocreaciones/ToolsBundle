<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\DependencyInjection;

use InvalidArgumentException;
use LogicException;
use ReflectionClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TecnocreacionesToolsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $locator = new FileLocator(__DIR__.'/../Resources/config');
        $loader = new Loader\XmlFileLoader($container, $locator);
        $loader->load('services.xml');
        
        $loaderYml = new Loader\YamlFileLoader($container, $locator);
        $loaderYml->load('services.yml');
        
        if($config['table_prefix']['use_prefix']){
            $tablePrefix = $config['table_prefix']['prefix'].$config['table_prefix']['prefix_separator'];
            $tablePrefixListerner = new Definition($container->getParameter('tecnocreaciones_tools.table_prefix_listerner.class'));
            $tablePrefixListerner
                    ->addArgument($tablePrefix)
                    ->addTag('doctrine.event_subscriber')
                    ;
            $container->setDefinition('tecnocreaciones_tools.table_prefix_subscriber', $tablePrefixListerner);
        }
        if($config['sequence_generator']){
           if($config['sequence_generator']['options']){
               $options = $config['sequence_generator']['options'];
               $sequenceGenerator = $container->getDefinition('tecnocreaciones_tools.sequence_generator');
               $sequenceGenerator->addArgument($options);
           }
        }
        if($config['configuration']['enable'] === true){
            if($config['configuration']['configuration_class'] === null){
                throw new InvalidArgumentException(
                    'The "configuration_class" option must be set in tecnocreaciones_tools.configuration'
                );
            }
            
            $configurationClass = $config['configuration']['configuration_class'];
            $configurationGroupClass = $config['configuration']['configuration_group_class'];
            $reflectionConfigurationClass = new ReflectionClass($configurationClass);
            if($reflectionConfigurationClass->isSubclassOf('Tecnocreaciones\Bundle\ToolsBundle\Model\Configuration\Configuration') === false){
                throw new LogicException(
                    'The "'.$reflectionConfigurationClass->getName().'" must inherit from Tecnocreaciones\\Bundle\\ToolsBundle\\Model\\Configuration\\Configuration'
                );
            }
            if(isset($config['configuration']['debug'])){
                $debug = $config['configuration']['debug'];
            }else{
                $debug = $container->getParameter('kernel.debug');
            }
            $configurationManager = new Definition($container->getParameter('tecnocreaciones_tools.configuration_service.class'));
            $configurationManager->addArgument(array(
                'configuration_class' => $configurationClass,
                'cache_dir' => $container->getParameter('kernel.cache_dir'),
                'debug' => $debug,
            ));
            $configurationManager->addMethodCall('setContainer',array(new Reference('service_container')));
            $container->setDefinition('tecnocreaciones_tools.configuration_service', $configurationManager);
            
            $extensionToolsDefinition = new Definition('Tecnocreaciones\Bundle\ToolsBundle\Twig\Extension\GlobalConfExtension');
            $extensionToolsDefinition
                    ->addMethodCall('setContainer',array(new Reference('service_container')))
                    ->addTag('twig.extension')
                    ;
            $container->setDefinition('tecnocreaciones_tools.global_config_extension', $extensionToolsDefinition);
                    
            $container->setParameter('tecnocreaciones_tools.configuration_class.class', $configurationClass);
            $container->setParameter('tecnocreaciones_tools.configuration_group_class.class', $configurationGroupClass);
            
            $loaderYml->load('admin.yml');
        }
        
    }
}
