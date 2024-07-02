<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Transformer;

use Symfony\Component\Form\FormInterface;
use Limenius\Liform\Transformer\AbstractTransformer;
use Symfony\Component\Form\FormView;

/**
 * Transforma el campo oculto
 *
 * @author Máximo Sojo <maxsojo13@gmail.com>
 */
class HiddenTransformer extends AbstractTransformer
{
    use \Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\CommonFunctionsTrait;
    
    /**
     * {@inheritdoc}
     */
    public function transform(FormInterface $form, array $extensions = [], $widget = null)
    {
        $this->initCommonCustom($form);
        $formView = $this->formView;
        
        $schema = [
            "type" => "hidden",
        ];

        $this->addWidget($form, $schema, false);

        $schema = $this->addData($form, $schema);
        $schema = $this->addCommonSpecs($form, $schema, $extensions, $widget);
        $schema = $this->addCommonCustom($form, $schema);
        
        
        return $schema;
    }
}
