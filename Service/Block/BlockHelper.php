<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\Block;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tecnocreaciones\Bundle\ToolsBundle\Event\BlockEvent;

/**
 * Ayudante para renderizar los bloques en el grid
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class BlockHelper extends Helper
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    
    public function getName()
    {
        return "tecno_wrid_block";
    }
    
    /**
     * @param string $name
     * @param array  $options
     *
     * @return string
     */
    public function renderEvent($name, array $options = [])
    {
        $eventName = sprintf('tecno.block.event.%s', $name);

        /** @var BlockEvent $event */
        $event = $this->eventDispatcher->dispatch($eventName, new BlockEvent($options));

        $content = '';

        foreach ($event->getBlocks() as $block) {
            $content .= $this->render($block);
        }

        return $content;
    }
    
    
    /**
     * @required
     * @param EventDispatcherInterface $eventDispatcher
     * @return $this
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        return $this;
    }
}
