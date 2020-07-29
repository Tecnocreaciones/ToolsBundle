<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform;

use Symfony\Component\Form\FormInterface;

/**
 * Funciones comunes
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
trait CommonFunctionsTrait
{
    private $formView;
    
    protected function initCommonCustom(FormInterface $form){
        $this->formView = $form->createView();
    }
    protected function addCommonCustom(FormInterface $form, array $schema)
    {
        $formView = $this->formView;
        $schema["full_name"] = $formView->vars["full_name"];
//        $schema["full_name"] = $formView->vars["name"];
        $schema = $this->addConstraints($form, $schema);
        $schema = $this->addDateParams($form, $schema);
        $schema = $this->addCommonConfigOptions($form, $schema);
        
        return $schema;
    }
    
    protected function addDateParams(FormInterface $form, array $schema){
        if($form->getConfig()->hasOption("format_from_server")){
            $schema["format_from_server"] = $form->getConfig()->getOption("format_from_server");
            $schema["format_to_server"] = $form->getConfig()->getOption("format_to_server");
        }
        return $schema;
    }
    /**
     * Opciones comunes a configurar en los tipos para no agregar uno por uno
     * @param FormInterface $form
     * @param array $schema
     * @return type
     */
    protected function addCommonConfigOptions(FormInterface $form, array $schema){
        $options = ["mode"];
        foreach ($options as $option) {
            if($form->getConfig()->hasOption($option)){
                $schema[$option] = $form->getConfig()->getOption($option);
            }
        }
        return $schema;
    }
    
    /**
     * Añade las validaciones
     * @param FormInterface $form
     * @param array $schema
     * @return type
     */
    protected function addConstraints(FormInterface $form, array $schema)
    {
        $schema['constraints'] = [];
    	if ($constraints = $form->getConfig()->getOption('constraints')) {
            $schema['constraints'] = $constraints;
        }

        return $schema;
    }
}
