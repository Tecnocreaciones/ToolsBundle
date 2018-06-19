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
use InvalidArgumentException;

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
    private $definitionsBlockGridByGroup;

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
        $this->definitionsBlockGridByGroup = array();
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
            $widgetBox->setSetting("widget_id",$widgetBox->getId());
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
        if(isset($this->definitionsBlockGrid[$definitionsBlockGrid->getType()])){
            throw new InvalidArgumentException(sprintf("The definition of widget box '%s' is already added.",$definitionsBlockGrid->getType()));
        }
        $this->definitionsBlockGrid[$definitionsBlockGrid->getType()] = $definitionsBlockGrid;
        if(!isset($this->definitionsBlockGridByGroup[$definitionsBlockGrid->getGroup()])){
            $this->definitionsBlockGridByGroup[$definitionsBlockGrid->getGroup()] = [];
        }
        $this->definitionsBlockGridByGroup[$definitionsBlockGrid->getGroup()][] = $definitionsBlockGrid;
    }  
    
    /**
     * 
     * @param type $type
     * @return DefinitionBlockWidgetBoxInterface
     */
    function getDefinitionBlockGrid($type)
    {
        if(!isset($this->definitionsBlockGrid[$type])){
            throw new InvalidArgumentException(sprintf("The definition of widget box '%s' is not added.",$type));
        }
        return $this->definitionsBlockGrid[$type];
    }
    
    public function getDefinitionBlockGridByGroup($group) {
         if(!isset($this->definitionsBlockGridByGroup[$group])){
            throw new InvalidArgumentException(sprintf("The definition group '%s' is not added.",$group));
        }
        return $this->definitionsBlockGridByGroup[$group];
    }

    /**
     * 
     * @return DefinitionBlockWidgetBoxInterface
     */
    function getDefinitionsBlockGrid() 
    {
        return $this->definitionsBlockGrid;
    }
    
    public function getDefinitionsBlockGridByGroup() {
        return $this->definitionsBlockGridByGroup;
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
    
    /**
     * Añade todos los widgets de un tipo
     * @param type $type
     * @return int
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function addAll($type,$nameFilter = null) {
        $definitionBlockGrid = $this->getDefinitionBlockGrid($type);
        if($definitionBlockGrid->hasPermission() == false){
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }
        $events = $definitionBlockGrid->getParseEvents();
        $names = $definitionBlockGrid->getNames();
        
        $templates = $definitionBlockGrid->getTemplates();
        
        $templatesKeys = array_keys($templates);
        $widgetBoxManager = $this->getWidgetBoxManager();
        $i = 0;
        foreach ($names as $name => $value) {
            if($definitionBlockGrid->hasPermission($name) === false){
                continue;
            }
            if($nameFilter !== null && $name !== $nameFilter){
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
    
    public function counInGroup($group) {
        $total = 0;
        $definitions = $this->getDefinitionBlockGridByGroup($group);
        foreach ($definitions as $definition) {
            $total += $definition->countWidgets();
        }
        return $total;
    }
    
    /**
     * Cuenta cuantos widgets hay nuevos
     * @return int
     */
    public function countNews() {
        $news = 0;
        foreach ($this->getDefinitionsBlockGrid() as $grid) {
            $news += $grid->countNews();
        }
        return $news;
    }
}
