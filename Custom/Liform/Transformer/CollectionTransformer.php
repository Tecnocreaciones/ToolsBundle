<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Transformer;

use Limenius\Liform\Exception\TransformerException;
use Limenius\Liform\ResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Limenius\Liform\Transformer\ArrayTransformer;

/**
 * @author Máximo Sojo <maxsojo13@gmail.com>
 */
class CollectionTransformer extends ArrayTransformer
{
    use \Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\CommonFunctionsTrait;

    /**
     * {@inheritdoc}
     */
    public function transform(FormInterface $form, array $extensions = [], $widget = null): array
    {
        $this->initCommonCustom($form);

        $children = [];
        foreach ($form->all() as $name => $field) {
            $transformerData = $this->resolver->resolve($field);
            $transformedChild = $transformerData['transformer']->transform($field, $extensions, $transformerData['widget']);
            $children[] = $transformedChild;

            if ($transformerData['transformer']->isRequired($field)) {
                $required[] = $field->getName();
            }
        }

        if (empty($children)) {
            $entryType = $form->getConfig()->getAttribute('prototype');

            if (!$entryType) {
                throw new TransformerException('Liform cannot infer the json-schema representation of a an empty Collection or array-like type without the option "allow_add" (to check the proptotype). Evaluating "'.$form->getName().'"');
            }

            $transformerData = $this->resolver->resolve($entryType);
            $children[] = $transformerData['transformer']->transform($entryType, $extensions, $transformerData['widget']);
            $children[0]['title'] = 'prototype';
        }
        
        $schema = [
            'type' => 'collection',
            'title' => $form->getConfig()->getOption('label'),
            'items' => $children,
        ];

        $schema = $this->addCommonSpecs($form, $schema, $extensions, $widget);

        return $schema;
    }
}
