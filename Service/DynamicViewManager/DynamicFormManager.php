<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\DynamicViewManager;

use Tecnocreaciones\Bundle\ToolsBundle\Model\DynamicView\DynamicForm;
use Limenius\Liform\Liform;
use Tecnoready\Common\Model\ShowBuilder\FormWidget;

/**
 * Constructor de vista de formulario dinamico
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DynamicFormManager
{
    /**
     * Serializador de formulario de symfony
     * @var Liform
     */
    private $liform;
    
    /**
     * @var DynamicForm
     */
    private $dynamicForm;
    
    /**
     * Inicializa la construccion del formulario
     * @return DynamicForm
     */
    public function start()
    {
        $this->dynamicForm = new DynamicForm($this->liform);
        return $this->dynamicForm;
    }
    
    /**
     * Retorna la instancia actual del formulario
     * @return DynamicForm
     */
    public function content()
    {
        return $this->dynamicForm;
    }
    
    
//    public function end(){
//        $form  = $this->dynamicForm->getForm();
//        $formSerialize = null;
//        if($form){
//            $formSerialize = $this->liform->transform($form);
//        }
//        $d = [
//            "title" => $this->dynamicForm->getTitle(),
//            "form" => $formSerialize,
//        ];
//        return $d;
//    }
    
    /**
     * Form widget para show dinamico
     * @return FormWidget
     */
    public function getShowFormWidget() {
        $content = $this->dynamicForm->end();
        $formWidget = new FormWidget();
        $formWidget->setContent($content);
        return $formWidget;
    }
    
    /**
     * @required
     * @param Liform $liform
     * @return $this
     */
    public function setLiform(Liform $liform)
    {
        $this->liform = $liform;
        return $this;
    }
}
