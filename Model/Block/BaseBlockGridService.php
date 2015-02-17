<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Block;

/**
 * Base de un bloque en un widget box
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
abstract class BaseBlockGridService extends BaseBlockService 
{
    public function setDefaultSettings(\Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'url'      => false,
            'title'    => 'Titulo',
            'name'    => 'Nombre',
            'template' => 'TecnocreacionesToolsBundle:WidgetBox:block_widget_box.html.twig',
            'positionX' => 1,
            'positionY' => 1,
            'sizeX' => 4,
            'sizeY' => 4,
            'fullscreenWidget' => true,
            'reloadWidget' => true,
            'collapseWidget' => true,
            'closeWidget' => true,
            'isCollapsed' => false,
        ));
    }
}
