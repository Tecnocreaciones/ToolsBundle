<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\Block\Event;

use Sonata\BlockBundle\Event\BlockEvent;

/**
 * Agrega para renderizar el bloques principal de resumen (block.main.summary)
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class MainSummaryBlockEvent 
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    
    const EVENT_BASE = "sonata.block.event.widgets.";

        /**
     * @param  BlockEvent
     *
     * @return BlockInterface
     */
    public function onBlock(BlockEvent $event)
    {
        $target = $event->getSetting("target");
        $eventName = self::EVENT_BASE.$target;
        $gridWidgetBox = $this->getGridWidgetBoxService();
        
        $gridWidgetBox->addAllPublishedByEvent($event,$eventName);
    }
    
    /**
     * 
     * @return \Tecnocreaciones\Bundle\ToolsBundle\Service\GridWidgetBoxService
     */
    private function getGridWidgetBoxService()
    {
        return $this->container->get('tecnocreaciones_tools.service.grid_widget_box');
    }
}
