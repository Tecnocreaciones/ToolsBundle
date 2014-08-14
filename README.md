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