<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Block\Manager;

/**
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
interface BlockWidgetBoxManagerInterface
{
    function save(\Tecnocreaciones\Bundle\ToolsBundle\Model\Block\BlockWidgetBox $blockWidgetBox);
    
    function remove(\Tecnocreaciones\Bundle\ToolsBundle\Model\Block\BlockWidgetBox $blockWidgetBox);
    
    function createNew();
    
    function find($id);
    
    function findByIds(array $ids);
    
    function findAllPublishedByEvent($event);
    
    /**
     * 
     * @param array $parameters
     * @return \Tecnocreaciones\Bundle\ToolsBundle\Model\Block\BlockWidgetBox Description
     */
    function buildBlockWidget(array $parameters = array());
}
