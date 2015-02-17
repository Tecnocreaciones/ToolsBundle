<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Service;

/**
 * Servicio para construir un grid ordenado
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class GridWidgetBoxService 
{
    /**
     * Limite de columas en en grid
     * @var type 
     */
    private $limitCol = 3;
    private $currentRow = 1;
    private $quantityBlock = 1;
    
    private $blocks;
    
    /**
     *
     * @var \Sonata\BlockBundle\Event\BlockEvent
     */
    private $event;
    
    public function __construct() 
    {
        $this->blocks = array();
    }
    
    public function addBlock(\Tecnocreaciones\Bundle\ToolsBundle\Model\Block\BlockGrid $block) 
    {
        $block->setSetting('positionX',$this->currentRow);
        $block->setSetting('positionY',$this->quantityBlock);
        $this->blocks[] = $block;
        $this->event->addBlock($block);
        
        $this->quantityBlock++;
        if((($this->limitCol + 1) / $this->quantityBlock) == 1){
            $this->currentRow++;
            $this->quantityBlock = 1;
        }
    }
            
    function getLimitCol() {
        return $this->limitCol;
    }

    function getEvent() {
        return $this->event;
    }

    function setLimitCol($limitCol) {
        $this->limitCol = $limitCol;
    }

    function setEvent(\Sonata\BlockBundle\Event\BlockEvent &$event) 
    {
        $this->event = $event;
    }


}
