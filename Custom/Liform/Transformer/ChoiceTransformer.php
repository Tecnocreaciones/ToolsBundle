<?php

/*
 * This file is part of the Limenius\Liform package.
 *
 * (c) Limenius <https://github.com/Limenius/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Transformer;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;
use Limenius\Liform\Transformer\AbstractTransformer;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
class ChoiceTransformer extends AbstractTransformer
{
    use \Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\CommonFunctionsTrait;
    
    /**
     * {@inheritdoc}
     */
    public function transform(FormInterface $form, array $extensions = [], $widget = null)
    {
        $formView = $form->createView();

        $choices = [];
        foreach ($formView->vars['choices'] as $choiceView) {
            if ($choiceView instanceof ChoiceGroupView) {
                foreach ($choiceView->choices as $choiceItem) {
                    $choices[] = [
                        "id" => $choiceItem->value,
                        "label" => $this->translator->trans($choiceItem->label),
//                        "disabled" => $this->isDisabled($choiceItem->attr)
                    ];
                }
            } else {
                $choices[] = [
                    "id" => $choiceView->value,
                    "label" => $this->translator->trans($choiceView->label),
//                    "disabled" => $this->isDisabled($choiceView->attr)
                ];
            }
        }

        if ($formView->vars['multiple']) {
            $schema = $this->transformMultiple($form, $choices);
        } else {
            $schema = $this->transformSingle($form, $choices);
        }

        $this->addWidget($form, $schema, false);
        $schema = $this->addCommonSpecs($form, $schema, $extensions, $widget);
        $schema = $this->addHelp($form, $schema);
        $schema = $this->addConstraints($form, $schema);

        return $schema;
    }

    private function transformSingle(FormInterface $form, $choices)
    {
        $formView = $form->createView();

        $schema = [
            'choices' => $choices,
            'type' => 'string',
        ];

        if ($formView->vars['expanded']) {
            $schema['widget'] = 'choice-expanded';
        }

        return $schema;
    }

    private function transformMultiple(FormInterface $form, $choices)
    {
        $formView = $form->createView();

        $schema = [
            'items' => [
                'type' => 'string',
                'choices' => $choices,
                'minItems' => $this->isRequired($form) ? 1 : 0,
            ],
            'uniqueItems' => true,
            'type' => 'array',
        ];

        if ($formView->vars['expanded']) {
            $schema['widget'] = 'choice-multiple-expanded';
        }

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
     * Añadir data
     *  
     * @author Máximo Sojo <maxsojo13@gmail.com>
     * @param  $attr
     */
    protected function addData($attr)
    {
        $data = null;
        if ($attr && isset($attr["data"])) {
            $data = $attr["data"];
        }
        
        return $data;
    }

    /**
     * isDisabled
     *  
     * @author Máximo Sojo <maxsojo13@gmail.com>
     * @param  $attr
     * @return boolean
     */
    protected function isDisabled($attr)
    {
        $disabled = null;
        if ($attr && isset($attr["disabled"])) {
            $disabled = $attr["disabled"];
        }
        
        return $disabled;
    }
}
