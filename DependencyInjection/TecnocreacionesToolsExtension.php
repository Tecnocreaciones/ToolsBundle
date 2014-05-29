<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

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
            $tablePrefixListerner = new \Symfony\Component\DependencyInjection\Definition($container->getParameter('tecnocreaciones_tools.table_prefix_listerner.class'));
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
    }
}
