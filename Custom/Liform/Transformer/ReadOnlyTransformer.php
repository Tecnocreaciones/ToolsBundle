<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Transformer;

use Symfony\Component\Form\FormInterface;
use Limenius\Liform\Transformer\AbstractTransformer;
use Symfony\Component\Form\FormView;

/**
 * Transforma el campo de solo lectura
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ReadOnlyTransformer extends AbstractTransformer
{
    use \Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\CommonFunctionsTrait;
    
    /**
     * {@inheritdoc}
     */
    public function transform(FormInterface $form, array $extensions = [], $widget = null)
    {
        $this->initCommonCustom($form);
        $formView = $this->formView;
        
        $schema = [];
//        if ($formView->vars['multiple']) {
//            $schema = $this->transformMultiple($form, $choices);
//        } else {
//            $schema = $this->transformSingle($form, $choices);
//        }

        $this->addWidget($form, $schema, false);
        $schema = $this->addCommonSpecs($form, $schema, $extensions, $widget);
        $schema = $this->addCommonCustom($form, $schema);
        $schema = $this->addControlOptions($form,$formView,$schema);
        
        
        return $schema;
    }
    
    protected function addControlOptions(FormInterface $form,FormView $formView, array $schema)
    {
        $schema["type_content"] = $form->getConfig()->getOption('type_content');
        $schema["data"] = $form->getConfig()->getOption('data');
        $reqParams = $form->getConfig()->getOption('req_params');
        if(is_array($reqParams) && count($reqParams) > 0){
            $schema["req_params"] = $reqParams;
        }
        $schema["remote_path"] = $form->getConfig()->getOption('remote_path');

        return $schema;
    }
}
