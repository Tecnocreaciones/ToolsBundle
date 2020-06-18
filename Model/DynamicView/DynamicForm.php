<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\DynamicView;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Form\FormInterface;
use Limenius\Liform\Liform;

/**
 * Vista dinamica
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DynamicForm
{
    /**
     * Titulo de la pagina
     * @var string
     * @JMS\SerializedName("title")
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $title;
    
    /**
     * Formulario a serializar
     * @var FormInterface 
     * @JMS\SerializedName("form")
     * @JMS\Expose
     */
    private $form;
    
    /**
     * @var Liform 
     */
    private $liform;
    
    public function __construct(Liform $liform)
    {
        $this->liform = $liform;
    }

        public function getTitle()
    {
        return $this->title;
    }

    public function getForm(): ?FormInterface
    {
        return $this->form;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setForm(FormInterface $form)
    {
        $this->form = $form;
        return $this;
    }
    
     public function end(){
        $form  = $this->form;
        $formSerialize = null;
        if($form){
            $formSerialize = $this->liform->transform($form);
        }
        $d = [
            "title" => $this->getTitle(),
            "form" => $formSerialize,
        ];
        return $d;
    }
}
