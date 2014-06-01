ToolsBundle
===========


tecnocreaciones_tools:
    table_prefix:
        use_prefix: true
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