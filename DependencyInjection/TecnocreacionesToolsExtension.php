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
        
        if($config['table_prefix']['enable'] === true )
        {
            $tablePrefix = $config['table_prefix']['prefix'].$config['table_prefix']['prefix_separator'];
            $tablePrefixListerner = new Definition($config['table_prefix']['listerner_class']);
            $tablePrefixListerner
                    ->addArgument($tablePrefix)
                    ->addTag('doctrine.event_subscriber')
                    ;
            $container->setDefinition('tecnocreaciones_tools.table_prefix_subscriber', $tablePrefixListerner);
        }
        
        if($config['sequence_generator']['enable'] === true){
           $loader->load('services/sequence_generator.xml');
           
           if(isset($config['sequence_generator']['options'])){
               $options = $config['sequence_generator']['options'];
               $sequenceGenerator = $container->getDefinition('tecnocreaciones_tools.sequence_generator');
               $sequenceGenerator->addArgument($options);
           }
        }
        
        if($config['unit_converter']['enable'] === true)
        {
            $loader->load('services/unit_converter.xml');
            $container->setParameter('tecnocreaciones_tools.unit_converter.service_name',$config['unit_converter']['service_name']);
        }
        
        if($config['configuration_manager']['enable'] === true){
            $loaderYml->load('services/configuration_manager.yml');
            
            if($config['configuration_manager']['configuration_class'] === null){
                throw new InvalidArgumentException(
                    'The "configuration_class" option must be set in tecnocreaciones_tools.configuration_manager'
                );
            }
            
            $configurationClass = $config['configuration_manager']['configuration_class'];
            $configurationManagerClass = $config['configuration_manager']['configuration_manager_class'];
            $configurationManagerNameService = $config['configuration_manager']['configuration_name_service'];
            $configurationGroupClass = $config['configuration_manager']['configuration_group_class'];
            $reflectionConfigurationClass = new ReflectionClass($configurationClass);
            if($reflectionConfigurationClass->isSubclassOf('Tecnocreaciones\Bundle\ToolsBundle\Model\Configuration\Configuration') === false){
                throw new LogicException(
                    'The "'.$reflectionConfigurationClass->getName().'" must inherit from Tecnocreaciones\\Bundle\\ToolsBundle\\Model\\Configuration\\Configuration'
                );
            }
            if(isset($config['configuration_manager']['debug'])){
                $debug = $config['configuration_manager']['debug'];
            }else{
                $debug = $container->getParameter('kernel.debug');
            }
            $configurationService = new Definition($container->getParameter('tecnocreaciones_tools.configuration_service.class'));
            $configurationService->addArgument(array(
                'configuration_class' => $configurationClass,
                'cache_dir' => $container->getParameter('kernel.cache_dir'),
                'debug' => $debug,
            ));
            $configurationService->addMethodCall('setContainer',array(new Reference('service_container')));
            $container->setDefinition('tecnocreaciones_tools.configuration_service', $configurationService);
            
            
            
            $reflectionConfigurationManagerClass = new ReflectionClass($configurationManagerClass);
            if($reflectionConfigurationManagerClass->isSubclassOf('Tecnocreaciones\Bundle\ToolsBundle\Model\Configuration\ConfigurationManager') === false){
                throw new LogicException(
                    'The "'.$reflectionConfigurationManagerClass->getName().'" must inherit from Tecnocreaciones\\Bundle\\ToolsBundle\\Model\\Configuration\\ConfigurationManager'
                );
            }
            $configurationManager = new Definition($configurationManagerClass);
            $configurationManager->addMethodCall('setContainer',array(new Reference('service_container')));
            $container->setDefinition($configurationManagerNameService, $configurationManager);
            
            $container->setParameter('tecnocreaciones_tools.configuration_service.name', $configurationManagerNameService);
            
            $extensionToolsDefinition = new Definition('Tecnocreaciones\Bundle\ToolsBundle\Twig\Extension\GlobalConfExtension');
            $extensionToolsDefinition
                    ->addMethodCall('setContainer',array(new Reference('service_container')))
                    ->addTag('twig.extension')
                    ;
            $container->setDefinition('tecnocreaciones_tools.global_config_extension', $extensionToolsDefinition);
                    
            $container->setParameter('tecnocreaciones_tools.configuration_class.class', $configurationClass);
            $container->setParameter('tecnocreaciones_tools.configuration_group_class.class', $configurationGroupClass);
        }
        
        if($config['widget_block_grid']['enable'] === true)
        {
           $loader->load('services/widget_block_grid.xml');
            
           $blockGridConfig = $config['widget_block_grid']; 
           $blockGridClass = $blockGridConfig['widget_block_grid_class'];
           $widgetBoxManager = $blockGridConfig['widget_box_manager'];
           
           if(empty($blockGridClass)){
                throw new LogicException(
                    'The "tecnocreaciones_tools.widget_block_grid.widget_block_grid_class" in config.yml must defined'
                );
           }

           $reflectionBlockWidgetBox = new ReflectionClass($blockGridClass);
           
           if($blockGridClass != 'Tecnocreaciones\Bundle\ToolsBundle\Model\Block\BlockWidgetBox' && $reflectionBlockWidgetBox->isSubclassOf('Tecnocreaciones\Bundle\ToolsBundle\Model\Block\BlockWidgetBox') === false){
                throw new LogicException(
                    'The "'.$reflectionBlockWidgetBox->getName().'" must inherit from Tecnocreaciones\\Bundle\\ToolsBundle\\Model\\Block\\BlockWidgetBox'
                );
            }
            $widgetBoxManagerDefinition = $container->getDefinition($widgetBoxManager);
            $reflectionWidgetBoxManager = new ReflectionClass($widgetBoxManagerDefinition->getClass());
            
            if($reflectionWidgetBoxManager->isSubclassOf('Tecnocreaciones\Bundle\ToolsBundle\Model\Block\Manager\BlockWidgetBoxManager') === false){
                throw new LogicException(
                    'The "'.$reflectionWidgetBoxManager->getName().'" must inherit from Tecnocreaciones\\Bundle\\ToolsBundle\\Model\\Block\\Manager\\BlockWidgetBoxManager'
                );
            }
            
            $container->setParameter('tecnocreaciones_tools.widget_block_grid.widget_block_grid_class', $blockGridClass);
            $container->setParameter('tecnocreaciones_tools.widget_block_grid.debug', $blockGridConfig['debug']);
            $container->setParameter('tecnocreaciones_tools.widget_block_grid.enable', $blockGridConfig['enable']);
            $container->setParameter('tecnocreaciones_tools.widget_block_grid.widget_box_manager', $widgetBoxManager);
        }
        
//        if($config['install']['enable'] === true){
        //Comandos para instalar la aplicacion
            $container->setParameter('tecnocreaciones_tools.credentials.username', $config['install']['credentials']['username']);
            $container->setParameter('tecnocreaciones_tools.credentials.password', $config['install']['credentials']['password']);
            $container->setParameter('tecnocreaciones_tools.credentials.email', $config['install']['credentials']['email']);
            $container->setParameter('tecnocreaciones_tools.credentials.role', $config['install']['credentials']['role']);
            $container->setParameter('tecnocreaciones_tools.credentials.interactive', $config['install']['interactive']);
            $container->setParameter('tecnocreaciones_tools.commands', $config['install']['commands']);
            $container->setParameter('tecnocreaciones_tools.create_admin', $config['install']['create_admin']);
            $container->setParameter('tecnocreaciones_tools.app_name', $config['install']['app_name']);
//            var_dump($config['install']);
//            die;
//        }
        
        if($config['repository_as_service']['enable'] === true)
        {
            $loaderYml->load('services/repository_as_service.yml');
            $container->setParameter('tecnocreaciones_tools.repository_as_service.tag_service', $config['repository_as_service']['tag_service']);
        }
        
        if($config['role_pattern_voter']['enable'] === true)
        {
            $loader->load('services/role_pattern_voter.xml');
            $container->setParameter('tecnocreaciones_tools.role_pattern_voter.voter_class', $config['role_pattern_voter']['role_pattern_voter_class']);
            $container->setParameter('tecnocreaciones_tools.role_pattern_voter.voter_prefix', $config['role_pattern_voter']['role_pattern_voter_prefix']);
        }
        
        if($config['twig'] != ''){
            if($config['twig']['breadcrumb'] === true || $config['twig']['page_header'] === true){

                $container->setParameter('tecnocreaciones_tools.twig.breadcrumb.template', $config['twig']['breadcrumb_template']);
                $container->setParameter('tecnocreaciones_tools.twig.page_header.template', $config['twig']['page_header_template']);
            }
        }
        
        
        if($config['extra_form_types']['enable'] === true)
        {
            $codeMirror = $config['extra_form_types']['code_mirror'];
//            var_dump($config['extra_form_types']['code_mirror']);
//            die;
            $loader->load('services/extra_form_types.xml');
            $container->setParameter('tecnocreaciones.extra_form_types.autocomplete_entities', $config['extra_form_types']['autocomplete_entities']);
            
            $container->setParameter('code_mirror.codemirror_compressed', $codeMirror['codemirror_compressed']);
            $container->setParameter('code_mirror.form_type', $codeMirror['form_type']);
            $container->setParameter('code_mirror.parameters', $codeMirror['parameters']);
            $container->setParameter('code_mirror.twig.extension', $codeMirror['twig_extension']);
            $container->setParameter('code_mirror.mode_dirs', $codeMirror['mode_dirs']);
            $container->setParameter('code_mirror.themes_dirs', $codeMirror['themes_dirs']);
            
            $loaderYml->load('services/code_mirror.yml');
        }
//        var_dump($config);
//        die;
        if($config['intro']['enable'] === true)
        {
            $loaderYml->load('services/intro.yml');
            if($config['intro']['admin'] === true){
                $loaderYml->load('admin/intro.yml');
            }
            $container->setParameter('tecnocreaciones_tools.intro.intro_class', $config['intro']['intro_class']);
            $container->setParameter('tecnocreaciones_tools.intro.intro_step_class', $config['intro']['intro_step_class']);
            $container->setParameter('tecnocreaciones_tools.intro.intro_log_class', $config['intro']['intro_log_class']);
            $container->setParameter('tecnocreaciones_tools.intro.intro_admin_class', $config['intro']['intro_admin_class']);
            $container->setParameter('tecnocreaciones_tools.intro.intro_admin_step_class', $config['intro']['intro_admin_step_class']);
            $container->setParameter('tecnocreaciones_tools.intro.areas', $config['intro']['areas']);
            $container->setParameter('tecnocreaciones_tools.intro.config', $config['intro']);
        }
        
        if($config['intro']['enable'] === true 
                || ($config['twig'] != '' && ($config['twig']['breadcrumb'] === true || $config['twig']['page_header'] === true))
            ){
            
            $extensionToolsDefinition = new Definition('Tecnocreaciones\Bundle\ToolsBundle\Twig\Extension\TemplateUtilsExtension');
                    $extensionToolsDefinition
                            ->addMethodCall('setContainer',array(new Reference('service_container')))
                            ->addTag('twig.extension')
                            ;
            $container->setDefinition('tecnocreaciones_tools.template_utils_extension', $extensionToolsDefinition);
        }
        
        $container->setParameter('tecnocreaciones_tools.service.table_prefix.enable', $config['table_prefix']['enable']);
        $container->setParameter('tecnocreaciones_tools.service.sequence_generator.enable', $config['sequence_generator']['enable']);
        $container->setParameter('tecnocreaciones_tools.service.unit_converter.enable', $config['unit_converter']['enable']);
        $container->setParameter('tecnocreaciones_tools.service.configuration_manager.enable', $config['configuration_manager']['enable']);
        $container->setParameter('tecnocreaciones_tools.service.widget_block_grid.enable', $config['widget_block_grid']['enable']);
        $container->setParameter('tecnocreaciones_tools.service.repository_as_service.enable', $config['repository_as_service']['enable']);
        
        $container->setParameter('tecnocreaciones_tools.twig.breadcrumb.enable', $config['twig']['breadcrumb']);
        $container->setParameter('tecnocreaciones_tools.twig.page_header.enable', $config['twig']['page_header']);
        $container->setParameter('tecnocreaciones_tools.twig.extra_form_types.enable', $config['extra_form_types']['enable']);
    }
}
