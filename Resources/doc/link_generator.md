# Configuracion del generador de enlaces

## Habilitar la extension
    tecnocreaciones_tools:
        link_generator:
            enable: true
            color: '#5873fe'

### Se debe generar el servicio que tendra la definicion de las entidades a generar los enlaces

      namespace App\Service\Core;

      use Tecnocreaciones\Bundle\ToolsBundle\Model\LinkGenerator\LinkGeneratorItem;

      /**
      * Renderiza objetos como enlaces
      *
      * @author Carlos Mendoza <inhack20@gmail.com>
      */
      class MyLinkGeneratorItem extends LinkGeneratorItem
      {
      public static function getConfigObjects() {
      return [
          ['class' => 'App\Entity\M\Requirement','icon' => 'fas fa-archive','route' => 'm_requirement_show','labelMethod' => 'getRef'],
      ];
      }

      }


### Agregar el servicio a app/config/services.yaml

    # Generador de enlace
    App\Service\Core\MyLinkGeneratorItem:
    tags:
        - { name: link_generator.item }

### Ejemplo de uso:

    {% Genera un enlace 'a' al objeto %}
    {{ path_object(entity) }}

    {% Imprime el enlace del objeto %}
    {{ path_object_url(entity) }}
