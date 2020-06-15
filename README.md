ToolsBundle
===========
Provee servicios basicos y comunes en aplicaciones web, prefijos en tablas, conversor de unidades, generador de secuencia con Doctrine2,
manejador de configuracion guardandola en la base de datos asociados a clave=valor, generador de widgets y definir repositorios como servicios
de forma muy facil, un Role Voter para evaluar expresiones regulares en los roles del usuario.

    tecnocreaciones_tools:
        table_prefix:
            enable: false
            prefix: abc
            prefix_separator: _
            listerner_class: Tecnocreaciones\Bundle\ToolsBundle\EventListener\TablePrefixListerner
        unit_converter:
            enable: false
            service_name: tecnocreaciones_tools.unit_converter
        sequence_generator:
            enable: false
            options:
                additional_masks:
                    - mask1
                    - mask2
        configuration_manager:
            enable: false
            debug: false
            configuration_class: Coramer\Sigtec\CoreBundle\Entity\Configuration
            configuration_group_class: Tecnocreaciones\Bundle\ToolsBundle\Entity\Configuration\BaseGroup
            configuration_manager_class: Tecnocreaciones\Bundle\ToolsBundle\Model\Configuration\ConfigurationManager
            configuration_name_service: tec.configuration
        widget_block_grid:
            enable: false
            debug: false
            widget_block_grid_class: null
            widget_box_manager: tecnocreaciones_tools.service.orm.widget_box_manager
        repository_as_service:
            enable: false
            tag_service: app.repository
        role_pattern_voter:
            enable: false
            role_pattern_voter_class: Tecnocreaciones\Bundle\ToolsBundle\Security\Authorization\Voter\RolePatternVoter
            role_pattern_voter_prefix:
        twig:
            breadcrumb: true
            page_header: true
        extra_form_types:
            enable: false
            autocomplete_entities:
                users:
                    class: AcmeDemoBundle:User
                    role: ROLE_ADMIN
                    property: email

                products:
                    class: AcmeDemoBundle:Product
                    role: ROLE_ADMIN
                    search: contains
        # extra Inspired in ShtumiUsefulBundle
        link_generator:
            enable: true
    sonata_admin:
        dashboard:
                groups:
                    sonata.admin.group.administration:
                        label:           sonata_administration
                        label_catalogue: SonataAdminBundle
                        icon:            '<i class="fa fa-cogs"></i>'
                        items:
                            - sonata.admin.configuration
                            - sonata.admin.configuration_group


    Agrega repositorios como servicios a las clases
    <service id="repository.plant" class="Coramer\Sigtec\CompanyBundle\Repository\PlantRepository">
        <tag name="app.repository" class="Coramer\Sigtec\CompanyBundle\Entity\Plant" />
    </service>


TecnocreacionesToolsBundleExtraFormTypes:
    resource: "@TecnocreacionesToolsBundle/Resources/config/routing/extra_form_types.xml"
    prefix: /extra-form-types


###Configuration Codemirror
Add default parameters to `config.yml`:
``` yaml
twig:
    form:
        resources:
            - 'TecnocreacionesToolsBundle:ExtraFormTypes:code_mirror_widget.html.twig'

assetic:
    bundles:
        - # ... other bundles
        - TecnocreacionesToolsBundle

tecnocreaciones_tools:
    extra_form_types:
        code_mirror:
                parameters:
                    mode: twig
                    lineNumbers: true
                    lineWrapping: true
                    theme: 3024-day
                mode_dirs:
                - @TecnocreacionesToolsBundle/Resources/public/codemirror/js/mode
                themes_dirs:
                - @TecnocreacionesToolsBundle/Resources/public/codemirror/css/theme
```


Install assets:
``` bash
$ ./app/console assets:install web --symlink
```

###Usage
``` php
 $builder->add('content', 'code_mirror', array(
    'required' => true,
    'parameters' => array(
         'lineNumbers' => 'true'
     )
 ));
```


Configurar: link_generator
    Crear una clase que herede de "LinkGeneratorItem" e implementar sus metodos.
    Luego agregarlo como servicio con la tag "link_generator.item"
    app.my_link_generator_item:
        class: Coramer\Sigtec\WebBundle\Service\MyLinkGeneratorItem
        tags:
            - { name: link_generator.item }

    class MyLinkGeneratorItem extends LinkGeneratorItem
    {
        public static function getConfigObjects()
        {
            return [
                ['class' => 'Coramer\Sigtec\WebBundle\Entity\Indicator\Indicator','icon' => 'fa fa-line-chart','route' => 'coramer_indicator_show','labelMethod' => 'getRef'],
                ['class' => 'Coramer\Sigtec\WebBundle\Entity\StrategicView\StrategicView','icon' => null,'route' => 'coramer_strategic_view_show','labelMethod' => 'getRef'],
                ['class' => 'Coramer\Sigtec\WebBundle\Entity\TechnicalClaim\TechnicalClaim','icon' => null,'route' => 'coramer_technical_claim_show','labelMethod' => 'getRef'],
                ['class' => 'Coramer\Sigtec\WebBundle\Entity\TechnicalAssistance\FormalTechnicalAssistance','icon' => null,'route' => 'coramer_formal_technical_assistance_show','labelMethod' => 'getRef'],
                ['class' => 'Coramer\Sigtec\WebBundle\Entity\ReportTechnical\ReportTechnical','icon' => null,'route' => 'coramer_sigtec_backend_company_report_technical_show'],
            ];
        }
    }


Si usas tabs Importar tabs.yml en routing.yml

TecnocreacionesToolsTabs:
    resource: "@TecnocreacionesToolsBundle/Resources/config/routing/tabs.yml"
    prefix: /tabs
