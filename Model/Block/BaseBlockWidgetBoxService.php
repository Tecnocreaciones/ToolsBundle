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

use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tecnocreaciones\Bundle\ToolsBundle\Model\Block\DefinitionBlockWidgetBoxInterface;

/**
 * Base de un bloque en un widget box
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
abstract class BaseBlockWidgetBoxService extends BaseBlockService implements DefinitionBlockWidgetBoxInterface
{
    public function execute(BlockContextInterface $blockContext, Response $response = null) {
        // merge settings
        $settings = $blockContext->getSettings();
        
        return $this->renderResponse($blockContext->getTemplate(),array(
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings,
        ),$response);
    }
    
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'url'      => false,
            'title'    => 'Titulo',
            'name'    => 'Nombre',
            'template' => 'TecnocreacionesToolsBundle:WidgetBox:block_widget_box.html.twig',
            'blockBase' => 'TecnocreacionesToolsBundle:WidgetBox:block_widget_box.html.twig',
            'positionX' => 1,
            'positionY' => 1,
            'sizeX' => 4,
            'sizeY' => 4,
            'oldSizeY' => 4,
            'fullscreenWidget' => true,
            'reloadWidget' => true,
            'collapseWidget' => true,
            'closeWidget' => true,
            'isCollapsed' => false,
        ));
    }
    
    public function getTranslationDomain() {
        return 'widgetBox';
    }
    
    public function hasPermission() {
        return true;
    }
}
