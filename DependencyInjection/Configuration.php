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
                                    ->scalarNode('temporary_mask')->defaultValue('TEMP')->cannotBeEmpty()->end()
                                    ->arrayNode('additional_masks')
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('configuration')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->cannotBeEmpty()->end()
                            ->booleanNode('debug')->end()
                            ->scalarNode('configuration_class')->defaultValue('Tecnocreaciones\Bundle\ToolsBundle\Entity\Configuration\Configuration')->cannotBeEmpty()->end()
                            ->scalarNode('configuration_group_class')->defaultValue('Tecnocreaciones\Bundle\ToolsBundle\Entity\Configuration\BaseGroup')->cannotBeEmpty()->end()
                            ->scalarNode('configuration_manager_class')->defaultValue('Tecnocreaciones\Bundle\ToolsBundle\Model\Configuration\ConfigurationManager')->cannotBeEmpty()->end()
                            ->scalarNode('configuration_name_service')->defaultValue('tec.configuration')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('block_grid')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->cannotBeEmpty()->end()
                            ->booleanNode('debug')->defaultFalse()->end()
                            ->scalarNode('block_grid_class')->defaultNull()->cannotBeEmpty()->end()
                            ->scalarNode('widget_box_manager')->defaultValue('tecnocreaciones_tools.service.orm.widget_box_manager')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
        ;
        
        return $treeBuilder;
    }
}
