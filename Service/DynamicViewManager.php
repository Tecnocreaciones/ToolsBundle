<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service;

use Tecnocreaciones\Bundle\ToolsBundle\Service\DynamicViewManager\DynamicFormManager;

/**
 * Manejador de vista dinamica
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DynamicViewManager
{
    /**
     * 
     * @var ShowBuilderManager
     */
//    private $showBuilderManager;
    
    /**
     * @var DynamicFormManager
     */
    private $dynamicFormManager;

    /**
     * @return DynamicFormManager
     */
    public function dynamicForm()
    {
        return $this->dynamicFormManager;
    }
    
    /**
     * @required
     * @param DynamicFormManager $dynamicFormManager
     * @return $this
     */
    public function setDynamicFormManager(DynamicFormManager $dynamicFormManager)
    {
        $this->dynamicFormManager = $dynamicFormManager;
        return $this;
    }
}
