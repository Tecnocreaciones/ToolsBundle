<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Extension\Core\Type;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Boton de enviar formulario (dinamico)
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DynamicSubmitType extends SubmitType
{
    const RENDER_IN_FORM_TOP = "form_top";
    const RENDER_IN_FORM_BOTTOM = "form_bottom";
    
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            "text_color" => null,//Color hexadecimal de la letra
            "background_color" => null,//Color hexadecimal del fondo
        ]);
        $resolver->setDefault("render_in","form_top");
        $resolver->setAllowedValues("render_in", ["form_top","form_bottom"]);
    }
}
