<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Extension\Core\Type;

use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Boton para usar en la serializacion porque da error por el parametro faltante "required"
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DynamicButtonType extends ButtonType{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('required', false);
    }
}
