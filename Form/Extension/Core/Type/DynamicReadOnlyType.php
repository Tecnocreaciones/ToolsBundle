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
        $resolver->setDefaults([
            "req_params" => [],
            "remote_path" => null,
            "data" => null,
        ]);
        $resolver->setDefined(["type_content","req_params"]);
        $resolver->setAllowedValues("type_content", ["image","text","html","card","redirect_to_url","title"]);
        $resolver->setAllowedTypes("req_params", "array");
        $resolver->setRequired(["data","type_content"]);
    }
    
    public function getBlockPrefix(): string
    {
        return "readonly";
    }
}
