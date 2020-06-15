<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Event;

use Tecnocreaciones\Bundle\ToolsBundle\Model\Block\BlockInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Evento para agregar bloques a renderizar
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class BlockEvent extends Event
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * @var BlockInterface[]
     */
    protected $blocks = [];

    /**
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
    }

    /**
     * @param BlockInterface $block
     */
    public function addBlock(BlockInterface $block)
    {
        $this->blocks[] = $block;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return mixed
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getSetting($name, $default = null)
    {
        return isset($this->settings[$name]) ? $this->settings[$name] : $default;
    }
}
