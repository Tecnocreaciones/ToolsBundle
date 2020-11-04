<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Transformer;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Serializa el copy checkbox
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class CopyCheckboxTransformer extends BooleanTransformer
{
    public function transform(\Symfony\Component\Form\FormInterface $form, array $extensions = array(), $widget = null)
    {
        $schema = parent::transform($form, $extensions, $widget);
        $formView = $this->formView;
        
        $schema = $this->addControlOptions($form,$formView,$schema);
        $schema["widget"] = "checkbox";
        return $schema;
    }
    
    protected function addControlOptions(FormInterface $form,FormView $formView, array $schema)
    {
        $schema["copy_from"] = $form->getConfig()->getOption('copy_from');

        return $schema;
    }
}
