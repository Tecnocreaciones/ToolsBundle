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
                            ->booleanNode('enable')->defaultFalse()->cannotBeEmpty()->end()
                            ->scalarNode('prefix')->defaultValue('abc')->cannotBeEmpty()->end()
                            ->scalarNode('prefix_separator')->defaultValue('_')->cannotBeEmpty()->end()
                            ->scalarNode('listerner_class')->defaultValue('Tecnocreaciones\Bundle\ToolsBundle\EventListener\TablePrefixListerner')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('unit_converter')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->cannotBeEmpty()->end()
                            ->scalarNode('service_name')->defaultValue('tecnocreaciones_tools.unit_converter')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('sequence_generator')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->cannotBeEmpty()->end()
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
                    ->arrayNode('configuration_manager')
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
                    ->arrayNode('widget_block_grid')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->cannotBeEmpty()->end()
                            ->booleanNode('debug')->defaultFalse()->end()
                            ->scalarNode('widget_block_grid_class')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('widget_box_manager')->defaultValue('tecnocreaciones_tools.service.orm.widget_box_manager')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('install')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->cannotBeEmpty()->end()
                            ->booleanNode('debug')->defaultFalse()->end()
                            ->booleanNode('interactive')->defaultFalse()->end()
                            ->booleanNode('create_admin')->defaultTrue()->end()
                            ->scalarNode('app_name')->defaultValue('App')->cannotBeEmpty()->end()
                            ->arrayNode('credentials')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('username')->defaultValue('admin')->cannotBeEmpty()->end()
                                    ->scalarNode('password')->defaultValue('12345')->cannotBeEmpty()->end()
                                    ->scalarNode('email')->defaultValue('admin@example.local')->cannotBeEmpty()->end()
                                    ->scalarNode('role')->defaultValue('ROLE_SUPER_ADMIN')->cannotBeEmpty()->end()
                                ->end()
                            ->end()
                            ->arrayNode('commands')
                                ->defaultValue(array(
                                    'doctrine:database:create',
                                    'doctrine:schema:create',
                                    'assets:install',
                                    'assetic:dump',
                                    'doctrine:fixtures:load',
                                ))
                                ->prototype('scalar')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('repository_as_service')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->cannotBeEmpty()->end()
                            ->scalarNode('tag_service')->defaultValue('app.repository')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('twig')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('breadcrumb')->defaultFalse()->cannotBeEmpty()->end()
                            ->booleanNode('page_header')->defaultFalse()->cannotBeEmpty()->end()
                            ->scalarNode('breadcrumb_template')->defaultValue('TecnocreacionesToolsBundle:Twig:breadcrumb.html.twig')->cannotBeEmpty()->end()
                            ->scalarNode('page_header_template')->defaultValue('TecnocreacionesToolsBundle:Twig:page_header.html.twig')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                
                    ->arrayNode('extra_form_types')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->cannotBeEmpty()->end()
                            
                            ->arrayNode('autocomplete_entities')
                                ->useAttributeAsKey('id')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('class')
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode('field')
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode('role')
                                            ->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode('search')
                                            ->defaultValue('begins_with')
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode('form')
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->booleanNode('case_insensitive')
                                             ->defaultTrue()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                
                        ->end()
                    ->end()
                
                    ->arrayNode('role_pattern_voter')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->cannotBeEmpty()->end()
                            ->scalarNode('role_pattern_voter_class')->defaultValue('Tecnocreaciones\Bundle\ToolsBundle\Security\Authorization\Voter\RolePatternVoter')->cannotBeEmpty()->end()
                            ->scalarNode('role_pattern_voter_prefix')
                                        ->cannotBeEmpty()
                                        ->isRequired()
                                        ->validate()
                                            ->ifNull()
                                                ->thenInvalid('Invalid role prefix')->end()
                            ->end()
                        ->end()
                    ->end()
                
                    ->arrayNode('intro')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->cannotBeEmpty()->end()
                            ->scalarNode('intro_class')->defaultNull()->end()
                            ->scalarNode('intro_step_class')->defaultNull()->end()
                            ->scalarNode('intro_log_class')->defaultNull()->end()
                            ->booleanNode('admin')->defaultFalse()->cannotBeEmpty()->end()
                            ->scalarNode('intro_admin_class')->defaultValue('Tecnocreaciones\Bundle\ToolsBundle\Admin\Intro\IntroAdmin')->end()
                            ->scalarNode('intro_admin_step_class')->defaultValue('Tecnocreaciones\Bundle\ToolsBundle\Admin\Intro\IntroStepAdmin')->end()
                            ->arrayNode('areas')
                                ->defaultValue(array(
                                    'intro.welcome',
                                ))
                                ->prototype('scalar')
                            ->end()
                        ->end()
                    ->end()
                
                ->end()
        ;
        
        return $treeBuilder;
    }
}
