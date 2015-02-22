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
            'icon' => '<i class="fa fa-sort"></i>',
            'isMaximizable' => true,
            'isReloadble' => true,
            'isCollapsible' => true,
            'isClosable' => true,
            'isCollapsed' => false,//Esta minimizada
            'isLoadedData' => true,//Esta cargada la data
            'isTransparent' => false,//Transparente
            'isColorable' => true,//Se puede cambiar el color del wiget
            'widgetColor' => null,//Color del widget
        ));
    }
    
    public function getTranslationDomain() {
        return 'widgetBox';
    }
    
    public function hasPermission($name = null) 
    {
        $isGranted = true;
        if($name != null){
            $names = $this->getNames();
            if(isset($names[$name]['rol'])){
                $isGranted = $this->isGranted($names[$name]['rol']);
            }
        }
        return $isGranted;
    }
}
