ToolsBundle
===========

Servicio convertirdor de unidades:
tecnocreaciones_tools.unit_converter


tecnocreaciones_tools:
    table_prefix:
        use_prefix: false
        prefix: %app.db.prefix%
    sequence_generator:
        options:
            additional_masks:
                - mask1
                - mask2
    configuration:
        enable: true
        configuration_class: Coramer\Sigtec\CoreBundle\Entity\Configuration
        debug: false
    block_grid:
        enable: false
        debug: false
        block_grid_class: null

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
    <call method="setContainer">
        <argument type="service" id="service_container" />
    </call>
    <tag name="app.repository" class="Coramer\Sigtec\CompanyBundle\Entity\Plant" />
</service>


Agrega el voter para evaluar seguridad con herencia

<service id="app.security.access.role_pattern_voter" class="Tecnocreaciones\Bundle\ToolsBundle\Security\Authorization\Voter\RolePatternVoter" public="false">
    <argument type="service" id="security.role_hierarchy" />
    <argument>ROLE_APP_</argument>
    <tag name="security.voter" priority="245" />
</service>



Si usas block_wiget Importar en routing.yml

TecnocreacionesToolsBundle:
    resource: "@TecnocreacionesToolsBundle/Resources/config/routing/block_widget_box.yml"
    prefix: /widget-box

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