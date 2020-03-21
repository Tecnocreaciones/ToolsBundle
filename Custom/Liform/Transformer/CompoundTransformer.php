<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Transformer;

use Limenius\Liform\FormUtil;
use Limenius\Liform\ResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Limenius\Liform\Transformer\CompoundTransformer as AbstractCompoundTransformer;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
class CompoundTransformer extends AbstractCompoundTransformer
{
    /**
     * {@inheritdoc}
     */
    public function transform(FormInterface $form, array $extensions = [], $widget = null)
    {
        $schema = parent::transform($form, $extensions, $widget);
        $properties = $schema["properties"];

        foreach ($form->all() as $name => $field) {
            $transformerData = $this->resolver->resolve($field);
            $properties[$name]["required"] = $transformerData['transformer']->isRequired($field);
            $properties[$name]["disabled"] = $this->isDisabled($field);
            unset($properties[$name]["propertyOrder"]);
        }

        $schema["action"] = $form->getConfig()->getOption('action');
        $schema["method"] = $form->getConfig()->getOption('method');
        $schema["properties"] = $properties;


        return $schema;
    }

    /**
     * @param FormInterface $form
     *
     * @return boolean
     */
    protected function isDisabled(FormInterface $form)
    {
        return $form->getConfig()->getOption('disabled');
    }
}