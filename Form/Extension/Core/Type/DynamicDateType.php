<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Extension\Core\Type;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

/**
 * Fecha de enviar formulario (dinamico)
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DynamicDateType extends DateType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            "widget" => "single_text",
        ]);
        $placeholderNormalizer = function (Options $options, $placeholder) {
            return $placeholder;
        };
        
        $resolver->setRequired(["format_from_server","format_to_server"]);
        $resolver->setNormalizer('placeholder', $placeholderNormalizer);
    }
}
