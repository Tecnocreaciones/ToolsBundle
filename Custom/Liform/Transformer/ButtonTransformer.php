<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Transformer;

use Symfony\Component\Form\FormInterface;
use Limenius\Liform\Transformer\StringTransformer as AbstractStringTransformer;

/**
 * Transforma un boton
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ButtonTransformer extends AbstractStringTransformer
{
    /**
     * {@inheritdoc}
     */
    public function transform(FormInterface $form, array $extensions = [], $widget = null)
    {
        $schema = ['type' => 'string'];
        $schema = $this->addCommonSpecs($form, $schema, $extensions, $widget);
        $schema["render_in"] = $form->getConfig()->getOption('render_in');
        return $schema;
    }
}
