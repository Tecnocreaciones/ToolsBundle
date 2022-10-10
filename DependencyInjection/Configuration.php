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
        $treeBuilder = new TreeBuilder('tecnocreaciones_tools');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('tecnocreaciones_tools', 'array');
        }
        
        $rootNode
                ->children()
                    ->arrayNode('table_prefix')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->end()
                            ->booleanNode('table_name_lowercase')->defaultFalse()->end()
                            ->scalarNode('prefix')->defaultValue('abc')->cannotBeEmpty()->end()
                            ->scalarNode('prefix_separator')->defaultValue('_')->cannotBeEmpty()->end()
                            ->scalarNode('on_delete')->defaultNull()->end()
                            ->scalarNode('listerner_class')->defaultValue('Tecnocreaciones\Bundle\ToolsBundle\EventListener\TablePrefixListerner')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('unit_converter')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->end()
                            ->scalarNode('service_name')->defaultValue('tecnocreaciones_tools.unit_converter')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('sequence_generator')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->end()
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
                            ->booleanNode('enable')->defaultFalse()->end()
                            ->booleanNode('debug')->end()
                            ->scalarNode('configuration_manager_class')->defaultValue('Tecnoready\Common\Service\ConfigurationService\ConfigurationManager')->cannotBeEmpty()->end()
                            ->scalarNode('adapter')->defaultValue('configuration.adapter.orm')->cannotBeEmpty()->end()
                            ->scalarNode('cache')->defaultValue('configuration.cache.disk')->cannotBeEmpty()->end()
                            ->scalarNode('configuration_name_service')->defaultValue('app.manager.configuration')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('widget')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->end()
                            ->booleanNode('debug')->defaultFalse()->end()
                            ->scalarNode('widget_class')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('widget_adapter')->defaultValue('tecno.widget.orm.adapter')->cannotBeEmpty()->end()
                            ->scalarNode('base_layout')->defaultValue('::layout.html.twig')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('install')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->end()
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
                            ->booleanNode('enable')->defaultFalse()->end()
                            ->scalarNode('tag_service')->defaultValue('app.repository')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                    ->arrayNode('twig')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('breadcrumb')->defaultFalse()->end()
                            ->scalarNode('main_icon')->defaultNull()->end()
                            ->scalarNode('prefix_icon')->defaultNull()->end()
                            ->booleanNode('page_header')->defaultFalse()->end()
                            ->scalarNode('breadcrumb_template')->defaultValue("%kernel.project_dir%/../vendor/tecnoready/common/Resources/views/Breadcrumb/breadcrumb.twig")->cannotBeEmpty()->end()
//                            ->scalarNode('breadcrumb_template')->defaultValue('/Users/inhack20/www/mpandco/pandco_app_client/vendor/tecnoready/common/Resources/views/Breadcrumb/breadcrumb.twig')->cannotBeEmpty()->end()
                            ->scalarNode('page_header_template')->defaultValue('TecnocreacionesToolsBundle:Twig:page_header.html.twig')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                
                    ->arrayNode('extra_form_types')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->end()
                            
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
                                        ->scalarNode('repository_method')
                                            ->defaultNull()
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
                            ->arrayNode('code_mirror')
                                ->children()
                                  ->scalarNode('codemirror_compressed')->defaultNull()->end()
                                  ->scalarNode('form_type')->defaultValue('Tecnocreaciones\Bundle\ToolsBundle\Form\Type\CodeMirrorType')->end()
                                  ->scalarNode('twig_extension')->defaultValue('Tecnocreaciones\Bundle\ToolsBundle\Twig\Extension\CodeMirrorExtension')->end()
                                  ->arrayNode('parameters')
                                    ->prototype('scalar')->end()
                                  ->end()
                                  ->arrayNode('mode_dirs')->isRequired()
//                                    ->requiresAtLeastOneElement()
                                    ->prototype('scalar')->end()
                                  ->end()
                                  ->arrayNode('themes_dirs')->isRequired()
//                                    ->requiresAtLeastOneElement()
                                     ->prototype('scalar')->end()
                                  ->end()
                                  ->arrayNode('addons')->isRequired()
                                     ->prototype('scalar')->end()
                                  ->end()
                                  ->arrayNode('modes')->isRequired()
                                     ->prototype('scalar')->end()
                                  ->end()
                                ->end()
                            ->end()
                
                        ->end()
                    ->end()
                
                    ->arrayNode('role_pattern_voter')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->end()
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
                            ->booleanNode('enable')->defaultFalse()->end()
                            ->scalarNode('intro_class')->defaultNull()->end()
                            ->scalarNode('intro_step_class')->defaultNull()->end()
                            ->scalarNode('intro_log_class')->defaultNull()->end()
                            ->booleanNode('admin')->defaultFalse()->end()
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
                
                    ->arrayNode('link_generator')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->end()
                            ->scalarNode('color')->defaultValue('#000')->end()
                        ->end()
                    ->end()
                
                    ->arrayNode('liform')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->end()
                        ->end()
                    ->end()
                
                    ->arrayNode('database_spool')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->end()
                            ->scalarNode('entity_class')->defaultNull()->end()
                            ->booleanNode('keep_sent_messages')->defaultTrue()->end()
                            ->scalarNode('email_queue_class')->defaultNull()->end()
                            ->scalarNode('email_template_class')->defaultNull()->end()
                            ->scalarNode('email_component_class')->defaultNull()->end()
                            ->scalarNode('email_repository_manager')->defaultValue("doctrine.orm.default_entity_manager")->end()
                            ->arrayNode('options_mailer')
//                                ->prototype('scalar')
                                    ->children()
                                        ->scalarNode('debug')->end()
                                        ->scalarNode('domain_blacklist')->end()
                                        ->scalarNode('debug_mail')->end()
                                        ->scalarNode('env')->end()
                                        ->scalarNode('from_email')->end()
                                        ->scalarNode('from_name')->end()
                                        ->arrayNode('domain_blacklist')->prototype('scalar')->end()->end()
                                    ->end()
//                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('tabs')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->end()
                            ->scalarNode('template')->defaultValue("TecnocreacionesToolsBundle:Tabs:tabs.html.twig")->end()
                            ->scalarNode('default_icon')->defaultValue("fas fa-archive")->end()
                            ->arrayNode('object_types')
                                ->prototype('scalar')
                            ->end()
                            ->end()
                            ->arrayNode('document_manager')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('template')->defaultValue("TecnocreacionesToolsBundle:Tabs:tab_documents.html.twig")->end()
                                    ->scalarNode('adapter')->defaultValue("tecnoready.document_manager_disk_adapter")->end()
                                    ->scalarNode('title')->defaultValue("tab.documents")->end()
                                    ->scalarNode('icon')->defaultValue("fas fa-folder-open")->end()
                                ->end()
                            ->end()
                            ->arrayNode('history_manager')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('template')->defaultValue("TecnocreacionesToolsBundle:Tabs:tab_histories.html.twig")->end()
                                    ->scalarNode('adapter')->defaultNull()->end()
                                    ->scalarNode('title')->defaultValue("tab.history")->end()
                                    ->scalarNode('icon')->defaultValue("fas fa-history")->end()
                                ->end()
                            ->end()
                            ->arrayNode('note_manager')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('template')->defaultValue("TecnocreacionesToolsBundle:Tabs:tab_notes.html.twig")->end()
                                    ->scalarNode('adapter')->defaultNull()->end()
                                    ->scalarNode('title')->defaultValue("tab.notes")->end()
                                    ->scalarNode('icon')->defaultValue("fas fa-clipboard")->end()
                                ->end()
                            ->end()
                            ->arrayNode('exporter')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('template')->defaultValue("@TecnocreacionesToolsBundle/Resources/views/Exporter/documents.html.twig")->end()
                                    ->scalarNode('template_upload')->defaultValue("@TecnocreacionesToolsBundle/Resources/views/Exporter/documents_upload.html.twig")->end()
                                    ->scalarNode('template_manager_adapter')->defaultNull()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('search')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->end()
                            ->booleanNode('admin')->defaultFalse()->end()
                            ->scalarNode('standard_filters')->defaultValue('TecnocreacionesToolsBundle:Search:standard_filters.html.twig')->end()
                            ->scalarNode('additional_filters')->defaultNull()->end()
                            ->scalarNode('data_manager_service')->defaultNull()->end()
                            ->arrayNode('trans_default_domain')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('labels')->defaultValue('labels')->end()
                                    ->scalarNode('choices')->defaultValue('choices')->end()
                                ->end()
                            ->end()
                            ->scalarNode('template_filters')->defaultValue('TecnocreacionesToolsBundle:Search:template_filters.html.twig')->end()
                            
                            ->arrayNode('class')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('filter_group')->defaultNull()->end()
                                    ->scalarNode('filter_group_admin')->defaultValue('Tecnocreaciones\Bundle\ToolsBundle\Admin\Search\FilterGroupAdmin')->end()
                                    ->scalarNode('filter')->defaultNull()->end()
                                    ->scalarNode('filter_admin')->defaultValue('Tecnocreaciones\Bundle\ToolsBundle\Admin\Search\FilterAdmin')->end()
                                    ->scalarNode('filter_block')->defaultNull()->end()
                                    ->scalarNode('filter_block_admin')->defaultValue('Tecnocreaciones\Bundle\ToolsBundle\Admin\Search\FilterBlockAdmin')->end()
                                    ->scalarNode('filter_added')->defaultNull()->end()
                                    ->scalarNode('filter_added_admin')->defaultValue('Tecnocreaciones\Bundle\ToolsBundle\Admin\Search\FilterAddedAdmin')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('statistic_manager')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enable')->defaultFalse()->end()
                            ->scalarNode('adapter')->defaultNull()->end()
                            ->arrayNode('object_types')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('objectType')->defaultNull()->end()
                                        ->scalarNode('adapter')->defaultNull()->end()
                                        ->arrayNode('objectValids')
                                            ->defaultValue(array())
                                            ->prototype('scalar')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
            ;
        
        return $treeBuilder;
    }
}
