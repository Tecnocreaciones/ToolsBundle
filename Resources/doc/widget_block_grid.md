# Configuracion de widgets

- Se debe tener instalado jQuery
- sonata-project/block-bundle (https://sonata-project.org/bundles/block/master/doc/reference/installation.html)

## Habilitar la extension
    tecnocreaciones_tools:
        widget_block_grid:
                enable: true
                widget_block_grid_class: App\Entity\M\Core\BlockWidgetBox

### Se debe crear la entidad que sera el widget

        namespace Demo\Bundle\Entity;

        use Tecnocreaciones\Bundle\ToolsBundle\Model\Block\BlockWidgetBox as Model;
        use Doctrine\ORM\Mapping as ORM;

        /**
         * Widget
         * @ORM\Entity()
         * @ORM\Table(name="block_widget_box")
         */
        class BlockWidgetBox extends Model {
            /**
             * Usuario dueño del widget
             *
             * @var \App\Entity\M\User
             * @ORM\ManyToOne(targetEntity="App\Entity\M\User")
             */
            private $user;

            function getUser() {
                return $this->user;
            }

            function setUser(\App\Entity\M\User $user) {
                $this->user = $user;
            }
        }


### Agregar rutas a app/config/routing.yml

        TecnocreacionesToolsBundleWidgetBox:
            resource: "@TecnocreacionesToolsBundle/Resources/config/routing/block_widget_box.yml"
            prefix: /widget-box

1. Agregar zona donde se renderizaran los widgets

        {{ widgets_render_area("dashboard") }}

2.  Agregar estilos y javascript

        {{ widgets_render_assets() }}

3. Inicializar grid

        {{ widgets_init_grid() }}

### Actualizar base de datos y assets

        app/console doctrine:schema:update --dump-sql --force
        app/console assets:install web  --symlink
        app/console assetic:dump

### Agregar primer widget

1. Se crea la clase del widget:

        <?php

        namespace Pandco\Bundle\AppBundle\Service\Block;

        use Tecnocreaciones\Bundle\ToolsBundle\Model\Block\BaseBlockWidgetBoxService;

        /**
         * Bloque para mostrar resumenes de sistema (mensajes pendientes por enviar, cola de correo, cola de procesos)
         * sonata.block.widget.system
         * @author Carlos Mendoza <inhack20@gmail.com>
         */
        class SystemBlockService extends BaseBlockWidgetBoxService
        {

            const NAME_SUMMARY = "app.block.system.summary";
            const NAME_SUMMARY_SMS = "app.block.system.sms";

            public function getDescription() {
                return 'sonata.block.app.system_desc';
            }

            public function getEvents() {
                return array(
                    'dashboard'
                );
            }

            public function getNames() {
                return array(
                    self::NAME_SUMMARY => array(
                        //'rol' => 'ROLE_APP_WIDGET_SYSTEM_SUMMARY',
                    ),
                    self::NAME_SUMMARY_SMS => array(
                        //'rol' => 'ROLE_APP_WIDGET_SYSTEM_SUMMARY_SMS',
                    ),
                );
            }

            public function getTemplates() {
                return array(
                    'PandcoAppBundle:Block:system_block.html.twig' => 'default',
                );
            }

            public function getType() {
                return 'sonata.block.widget.system';
            }

2. Se define como servicio:

        sonata.block.widget.system:
            class: Pandco\Bundle\AppBundle\Service\Block\SystemBlockService
            arguments:
                - sonata.block.widget.system
                - '@templating'
            tags:
                - { name: sonata.block }
            calls:
                - [ setContainer, [@service_container]  ]
3. Crea el template "PandcoAppBundle:Block:system_block.html.twig" definido.

        {% extends settings.blockBase %}
        {% block content %}
            <table class="simple-table responsive-table responsive-table-on" style="width: 100%">
                <thead>            
                    <tr>
                        <th scope="col" class="header" style="width: 20%">Referencia</th>
                        <th scope="col" style="width: 65%">Empresa</th>
                        <th scope="col" style="width: 15%">Estatus</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        {% endblock content %}

Agrega las traducciones en "widgets.es.yml" y cada idioma corespondiente.

    sonata.block.event.widgets.dashboard: 'Ventana principal'
    app.block.system.summary: 'Resumen de colas'
    app.block.system.sms: 'Mensajes en cola'

    Carge los css
    {% stylesheets

        '@TecnocreacionesToolsBundle/Resources/public/ducksboard-gridster.js/dist/jquery.gridster.css'
        '@TecnocreacionesToolsBundle/Resources/public/ducksboard-gridster.js/dist/debug.css'
        '@TecnocreacionesToolsBundle/Resources/public/widget_box/widget_box.css'

        filter='uglifycss' filter='cssrewrite'
        output='compiled/stylesheets_index_sigtec.min.css'
    %}
         <link rel="stylesheet" href="{{ asset_url }}" />
    {% endstylesheets %}

    Carga los javascripts
    {% javascripts

        '@TecnocreacionesToolsBundle/Resources/public/ducksboard-gridster.js/dist/jquery.gridster.min.js'
        '@TecnocreacionesToolsBundle/Resources/public/widget_box/widget_box.js'

        filter='?uglifyjs2'
        output='compiled/javascript_index_sigtec.min.js'
    %}
        <script src="{{ asset_url }}"></script>
    {% endjavascripts %}


    E inicializar el grid
    $(function(){ //DOM Ready

      var gridster = $(".gridster > ul").gridster({
          widget_base_dimensions: [80, 60],
          widget_margins: [5,5],
          min_cols: 12,
          draggable: {
              stop: function(e, ui, $widget) {
                console.log(gridster.serialize($widget));
              }
          }
        }).data('gridster');
        $('.widget-box').on('close.ace.widget', function(e) {
            //this = the widget-box
            var widgetBox = $(this);
            gridster.remove_widget( widgetBox.parent(),function(a){
                console.log('eliminado..');
                {#console.log(a);
                console.log(widgetBox);#}
            });
       });
       $('.widget-box').on('reload.ace.widget', function(e) {
            //this = the widget-box
       });
    });
