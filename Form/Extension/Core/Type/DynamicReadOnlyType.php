<?php


namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Extension\Core\Type;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Campo de solo lectura, para mostrar informacion
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DynamicReadOnlyType extends HiddenType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
//        $resolver->setDefault("type_content","text");
        $resolver->setDefined(["type_content"]);
        $resolver->setAllowedValues("type_content", ["image","text","html"]);
        $resolver->setRequired(["data","type_content"]);
    }
    
    public function getBlockPrefix(): string
    {
        return "readonly";
    }
}
