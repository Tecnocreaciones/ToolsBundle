<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Extension\Core\Type;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $resolver->setRequired(["format_from_server","format_to_server"]);
    }
}
