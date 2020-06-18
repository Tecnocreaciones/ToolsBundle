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
        
        $loaderYml = new Loader\YamlFileLoader($container, $locator);
        $loaderYml->load("services.yaml");
        if($config['table_prefix']['enable'] === true )
        {
            $tablePrefix = $config['table_prefix']['prefix'].$config['table_prefix']['prefix_separator'];
            $tableNameLowercase = $config['table_prefix']['table_name_lowercase'];
            $tablePrefixListerner = new Definition($config['table_prefix']['listerner_class']);
            $tablePrefixListerner
                    ->addArgument($tablePrefix)
                    ->addArgument($tableNameLowercase)
                    ->addTag('doctrine.event_subscriber',["priority" => 10000])
                    ;
            $tablePrefixListerner->addMethodCall("setConfig",array($config['table_prefix']));
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
            $configurationClass = \Tecnocreaciones\Bundle\ToolsBundle\Entity\Configuration\Configuration::class;
            $container->setParameter('tecnocreaciones_tools.configuration_class.class', $configurationClass);
            $loaderYml->load('services/configuration_manager.yml');
            $container->setParameter('tecnocreaciones_tools.configuration_manager.configuration', $config['configuration_manager']);
        }
        
        if($config['widget_block_grid']['enable'] === true)
        {
           $loaderYml->load('services/widget_block_grid.yml');
            
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
                $loaderYml->load('services/breadcrumb.yml');
//                var_dump($config['twig']['breadcrumb_template']);
//                die;
//                $path = realpath($config['twig']['breadcrumb_template']);
                $path = $config['twig']['breadcrumb_template'];
                $container->setParameter('tecnocreaciones_tools.twig.breadcrumb.options',[
                    "twig_breadcrumb_template" => $path,
                    "main_icon" => $config['twig']['main_icon'],
                    "prefix_icon" => $config['twig']['prefix_icon'],
                ]);
                $container->setParameter('tecnocreaciones_tools.twig.page_header.template', $config['twig']['page_header_template']);
            }
        }
        
        
        if(isset($config['extra_form_types']) && $config['extra_form_types']['enable'] === true)
        {
            $loader->load('services/extra_form_types.xml');
            $container->setParameter('tecnocreaciones.extra_form_types.autocomplete_entities', $config['extra_form_types']['autocomplete_entities']);
            
            if(isset($config['extra_form_types']['code_mirror'])){
                $codeMirror = $config['extra_form_types']['code_mirror'];
                
                $container->setParameter('code_mirror.codemirror_compressed', $codeMirror['codemirror_compressed']);
                $container->setParameter('code_mirror.form_type', $codeMirror['form_type']);
                $container->setParameter('code_mirror.parameters', $codeMirror['parameters']);
                $container->setParameter('code_mirror.twig.extension', $codeMirror['twig_extension']);
                $container->setParameter('code_mirror', $codeMirror);

                $loaderYml->load('services/code_mirror.yml');
            }
        }
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
        
        $extensionToolsDefinition = new Definition('Tecnocreaciones\Bundle\ToolsBundle\Twig\Extension\UtilsExtension');
                $extensionToolsDefinition
                        ->addMethodCall('setContainer',array(new Reference('service_container')))
                        ->addMethodCall('setConfig',array($config))
                        ->addTag('twig.extension')
                        ->setAutowired(true)
                        ;
        $container->setDefinition('tecnocreaciones_tools.utils_extension', $extensionToolsDefinition);
        
        if($config['link_generator']['enable'] === true)
        {
            $loaderYml->load('services/link_generator.yml');
        }
        if($config['search']['enable'] === true)
        {
            $container->setParameter('tecnocreaciones_tools.search.config', $config['search']);
            $container->setParameter('tecnocreaciones_tools.search.config.class.filter_group', $config['search']['class']['filter_group']);
            $container->setParameter('tecnocreaciones_tools.search.config.class.filter_group_admin', $config['search']['class']['filter_group_admin']);
            $container->setParameter('tecnocreaciones_tools.search.config.class.filter', $config['search']['class']['filter']);
            $container->setParameter('tecnocreaciones_tools.search.config.class.filter_admin', $config['search']['class']['filter_admin']);
            $container->setParameter('tecnocreaciones_tools.search.config.class.filter_block', $config['search']['class']['filter_block']);
            $container->setParameter('tecnocreaciones_tools.search.config.class.filter_block_admin', $config['search']['class']['filter_block_admin']);
            $container->setParameter('tecnocreaciones_tools.search.config.class.filter_added', $config['search']['class']['filter_added']);
            $container->setParameter('tecnocreaciones_tools.search.config.class.filter_added_admin', $config['search']['class']['filter_added_admin']);
            
            $loaderYml->load('services/search.yml');
            if($config['search']['admin'] === true){
                $loaderYml->load('admin/search.yml');
            }
        }
        
        if($config['database_spool']['enable'] === true){
           $loaderYml->load('services/database_spool.yml');
           $twigSwiftMailerDefinition = $container->getDefinition("Tecnoready\Common\Service\Email\TwigSwiftMailer");
           $optionsMailer = $config['database_spool']["options_mailer"];
           $twigSwiftMailerDefinition->replaceArgument(3, $optionsMailer);
           $container->setParameter("tecnoready.swiftmailer_db.spool.entity_class", $config['database_spool']["entity_class"]);
           $container->setParameter("tecnoready.swiftmailer_db.spool.keep_sent_messages", $config['database_spool']["keep_sent_messages"]);
           $container->setParameter("tecnoready.swiftmailer_db.spool.keep_sent_messages", $config['database_spool']["keep_sent_messages"]);
           $container->setParameter("tecnoready.swiftmailer_db.spool.email_queue_class", $config['database_spool']["email_queue_class"]);
           $container->setParameter("tecnoready.swiftmailer_db.spool.email_template_class", $config['database_spool']["email_template_class"]);
           $container->setParameter("tecnoready.swiftmailer_db.email_component_class", $config['database_spool']["email_component_class"]);
           $container->setParameter("tecnoready.swiftmailer_db.email_repository_manager", $config['database_spool']["email_repository_manager"]);
        }
        
        $container->setParameter('tecnocreaciones_tools.service.tabs.enable',$config['tabs']['enable']);
        if($config['tabs']['enable'] === true){
            $loaderYml->load('services/tabs.yml');
            unset($config['tabs']["enable"]);
            $container->setParameter('tecnocreaciones_tools.service.tabs',$config['tabs']);
        }
        
        if($config['liform']['enable'] === true){
            $loaderYml->load('services/liform.yml');
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
        
        $container->setParameter('tecnocreaciones_tools.service.link_generator.enable', $config['link_generator']['enable']);
        $container->setParameter('tecnocreaciones_tools.service.link_generator.color', $config['link_generator']['color']);        
        $container->setParameter('tecnocreaciones_tools.service.search.enable', $config['search']['enable']);
        $container->setParameter('tecnocreaciones_tools.service.database_spool.enable', $config['database_spool']['enable']);

        if($config['statistic_manager']['enable'] === true){
            $loaderYml->load('services/statistic.yml');
            $container->setParameter('tecnocreaciones_tools.service.statistic',$config['statistic_manager']);
        }
        $container->setParameter('tecnocreaciones_tools.service.statistic.enable',$config['statistic_manager']['enable']);        
    }
}
