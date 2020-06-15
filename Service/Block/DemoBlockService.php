<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\Block;

use Tecnocreaciones\Bundle\ToolsBundle\Model\Block\BaseBlockWidgetBoxService;

/**
 * Bloque de prueba
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DemoBlockService extends BaseBlockWidgetBoxService
{
    const NAME_DEMO = "widget.group.demo.demo";
    
    public function getGroup() {
        return "widget.group.demo";
    }

    public function getNames() {
        return array(
            self::NAME_DEMO => array(
                'rol' => null,
            ),
           
        );
    }

    public function getTemplates()
    {
        return array(
            '@TecnocreacionesTools/widget_demo.html.twig' => 'default',
        );
    }

    public function getType() {
        return 'block.widget.demo';
    }

}
