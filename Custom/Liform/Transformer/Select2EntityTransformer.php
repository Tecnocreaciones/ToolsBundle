<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Transformer;

use Symfony\Component\Form\FormInterface;
use Limenius\Liform\Transformer\AbstractTransformer;
use Symfony\Component\Form\FormView;


/**
 * Transformer del select2 entity con ajax
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class Select2EntityTransformer extends AbstractTransformer
{
    use \Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\CommonFunctionsTrait;
    
    /**
     * {@inheritdoc}
     */
    public function transform(FormInterface $form, array $extensions = [], $widget = null)
    {
        $this->initCommonCustom($form);
        $formView = $this->formView;
        $choices = [];
        
        $schema = [];
//        if ($formView->vars['multiple']) {
//            $schema = $this->transformMultiple($form, $choices);
//        } else {
//            $schema = $this->transformSingle($form, $choices);
//        }

        $this->addWidget($form, $schema, false);
        $schema = $this->addCommonSpecs($form, $schema, $extensions, $widget);
        $schema = $this->addHelp($form, $schema);
        $schema = $this->addCommonCustom($form, $schema);
        $schema = $this->addEmptyData($form,$formView,$schema);
        $schema = $this->addSelect2($form, $formView,$schema);
        
        
        return $schema;
    }
    
    /**
     * Añadir help
     *  
     * @author Máximo Sojo <maxsojo13@gmail.com>
     * @param  FormInterface $form
     * @param  array         $schema
     */
    protected function addHelp(FormInterface $form, array $schema)
    {
        $translationDomain = $form->getConfig()->getOption('translation_domain');
        if ($attr = $form->getConfig()->getOption('attr')) {
            if (isset($attr['help'])) {
                $schema['attr']['help'] = $this->translator->trans($attr['help'], [], $translationDomain);
            }
        }

        return $schema;
    }
    
    /**
     * Añadir las opciones del select2
     * @param FormInterface $form
     * @param array $schema
     * @return type
     */
    protected function addSelect2(FormInterface $form,FormView $formView, array $schema)
    {
        $translationDomain = $form->getConfig()->getOption('translation_domain');
//        if ($attr = $form->getConfig()->getOption('attr')) {
//        }
        if (isset($formView->vars['attr']['data-req_params'])) {
            $schema["req_params"] = @json_decode($formView->vars['attr']['data-req_params'],true);
        }
//        var_dump($attr);
//        die;
        $schema["remote_path"] = $formView->vars["remote_path"];
        $schema["minimum_input_length"] = $form->getConfig()->getOption('minimum_input_length');

        return $schema;
    }
    
    /**
     * @param FormInterface $form
     * @param array         $schema
     *
     * @return array
     */
    protected function addEmptyData(FormInterface $form,FormView $formView, array $schema)
    {
     	if (($emptyData = $form->getConfig()->getOption('empty_data')) && !empty($formView->vars["value"])) {
            $schema['empty_data'] = [
                "id" => $formView->vars["value"],
                "label" => (string)$emptyData,
            ];
        }

        return $schema;
    }
}
