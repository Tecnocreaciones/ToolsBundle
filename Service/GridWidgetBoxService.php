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

use Sonata\BlockBundle\Event\BlockEvent;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tecnocreaciones\Bundle\ToolsBundle\Model\Block\BlockWidgetBox;
use Tecnocreaciones\Bundle\ToolsBundle\Model\Block\DefinitionBlockWidgetBoxInterface;

/**
 * Servicio para construir un grid ordenado (tecnocreaciones_tools.service.grid_widget_box)
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class GridWidgetBoxService implements ContainerAwareInterface
{
    /**
     * Limite de columas en en grid
     * @var type 
     */
    private $limitCol = 3;
    private $currentRow = 1;
    private $quantityBlock = 1;
    
    private $blocks;
    
    private $definitionsBlockGrid;

    /**
     *
     * @var BlockEvent
     */
    private $event;
    
    /**
     *
     * @var ContainerAwareInterface
     */
    private $container;


    public function __construct() 
    {
        $this->blocks = array();
        $this->definitionsBlockGrid = array();
    }
    
    public function addBlock(BlockWidgetBox $block) 
    {
        if($block->getSetting('positionX') === null){
            $block->setSetting('positionX',$this->currentRow);
        }
        if($block->getSetting('positionY') === null){
            $block->setSetting('positionY',$this->quantityBlock);
        }
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

    function setEvent(BlockEvent &$event) 
    {
        $this->event = $event;
    }
    
    /**
     * Añades todos los widgets disponibles del usuario por el evento
     * @param BlockEvent $event
     * @param type $eventName
     */
    public function addAllPublishedByEvent(BlockEvent &$event, $eventName)
    {
        $this->setEvent($event);
        $widgetsBox = $this->getWidgetBoxManager()->findAllPublishedByEvent($eventName);
        foreach ($widgetsBox as $widgetBox) {
            $widgetBox->setSetting('name',$widgetBox->getName());
            $this->addBlock($widgetBox);
        }
    }
    /**
     * Cuenta los widgets publicados por un evento
     * @param type $eventName
     * @return type
     */
    public function countPublishedByEvent($eventName)
    {
        $widgetsBox = $this->getWidgetBoxManager()->findAllPublishedByEvent($eventName);
        return count($widgetsBox);
    }
    
    function addDefinitionsBlockGrid(DefinitionBlockWidgetBoxInterface $definitionsBlockGrid) 
    {
        $this->definitionsBlockGrid[$definitionsBlockGrid->getType()] = $definitionsBlockGrid;
        
    }  
    
    /**
     * 
     * @param type $type
     * @return DefinitionBlockWidgetBoxInterface
     */
    function getDefinitionBlockGrid($type)
    {
        if(isset($this->definitionsBlockGrid[$type])){
            
        }
        return $this->definitionsBlockGrid[$type];
    }


    /**
     * 
     * @return DefinitionBlockWidgetBoxInterface
     */
    function getDefinitionsBlockGrid() 
    {
        return $this->definitionsBlockGrid;
    }
        
    /**
     * 
     * @return \Tecnocreaciones\Bundle\ToolsBundle\Model\Block\Manager\BlockWidgetBoxManagerInterface
     */
    private function getWidgetBoxManager()
    {
        return $this->container->get($this->container->getParameter('tecnocreaciones_tools.widget_block_grid.widget_box_manager'));
    }
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
    
    public function addAll($type) {
        $definitionBlockGrid = $this->getDefinitionBlockGrid($type);
        if($definitionBlockGrid->hasPermission() == false){
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }
        $events = $definitionBlockGrid->getEvents();
        $names = array();
        $names = $definitionBlockGrid->getNames();
        
        $templates = $definitionBlockGrid->getTemplates();
        
        $templatesKeys = array_keys($templates);
        $widgetBoxManager = $this->getWidgetBoxManager();
        $i = 0;
        foreach ($names as $name => $value) {
            if($definitionBlockGrid->hasPermission($name) === false){
                continue;
            }
            $blockWidgetBox = $widgetBoxManager->buildBlockWidget();
            $blockWidgetBox->setType($type);
            $blockWidgetBox->setName($name);
            $blockWidgetBox->setSetting('template',$templatesKeys[0]);
            $blockWidgetBox->setEvent($events[0]);
            $blockWidgetBox->setCreatedAt(new \DateTime());
            $blockWidgetBox->setEnabled(true);
            $widgetBoxManager->save($blockWidgetBox);
            $i++;
        }
        return $i;
    }
}
