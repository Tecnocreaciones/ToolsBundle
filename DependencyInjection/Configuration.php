<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('tecnocreaciones_tools');
        
        $rootNode
                ->children()
                    ->arrayNode('table_prefix')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('use_prefix')->defaultFalse()->cannotBeEmpty()->end()
                            ->scalarNode('prefix')->defaultValue('abc')->cannotBeEmpty()->end()
                            ->scalarNode('prefix_separator')->defaultValue('_')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('sequence_generator')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('options')
                                ->children()
                                    ->arrayNode('additional_masks')
                                        ->prototype('scalar')->end()
                                ->end()
                        ->end()
                    ->end()
                ->end()
        ;
        
        return $treeBuilder;
    }
}
