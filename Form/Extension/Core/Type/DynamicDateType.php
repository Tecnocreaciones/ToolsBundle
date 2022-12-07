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
            "format" => "yyyy-MM-dd",//Formato de fecha en php recibida, debe coincidir el mismo formato con "format_to_server" en c#
            "format_from_server" => "yyyy-MM-dd HH:mm:ss",//c# formato desde servidor a local telefono
            "format_to_server" => "yyyy-MM-dd",//c# formato de telefono a recibir al servidor
        ]);
        $placeholderNormalizer = function (Options $options, $placeholder) {
            return $placeholder;
        };
        
        $resolver->setRequired(["format_from_server","format_to_server"]);
        $resolver->setNormalizer('placeholder', $placeholderNormalizer);
    }
}
