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
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Tecnocreaciones\Bundle\ToolsBundle\Service\Block\Event\MainSummaryBlockEvent;

/**
 * Base de un bloque en un widget box
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
abstract class BaseBlockWidgetBoxService extends AbstractBlockService implements DefinitionBlockWidgetBoxInterface
{
    protected $cachePermission = null;
    
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    
    public function execute(BlockContextInterface $blockContext, Response $response = null) {
        // merge settings
        $settings = $blockContext->getSettings();
        
        return $this->renderResponse($blockContext->getTemplate(),array(
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings,
        ),$response);
    }
    
    public function setDefaultSettings(OptionsResolverInterface $resolver) {
        $this->configureSettings($resolver);
    }
    
    /**
     * Eventos que escucha el widget para renderizarse
     */
    protected abstract function getEvents();
    
    public function getParseEvents() {
        $events = [];
        foreach ($this->getEvents() as $event) {
            $events[] = MainSummaryBlockEvent::EVENT_BASE.$event;
        }
        return $events;
    }

    
    public function configureSettings(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
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
        return 'widgets';
    }
    
    public function countWidgets() {
        $count = 0;
        foreach ($this->getNames() as $name => $values) {
            if($this->hasPermission($name)){
                $count++;
            }
        }
        return $count;
    }
    
    public function hasPermission($name = null) 
    {
        if($this->cachePermission !== null){
            return $this->cachePermission;
        }
        $isGranted = true;
        if($name != null){
            $names = $this->getNames();
            if(isset($names[$name]['rol'])){
                $isGranted = $this->isGranted($names[$name]['rol']);
                $this->cachePermission = $isGranted;
            }
        }
        return $isGranted;
    }
}
