<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Extension\Core\Type;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Campo de solo lectura, para mostrar informacion
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DynamicReadOnlyType extends HiddenType
{

    const TYPE_CONTENT_IMAGE = "image";
    const TYPE_CONTENT_TEXT = "text";
    const TYPE_CONTENT_HTML = "html";
    const TYPE_CONTENT_CARD = "card";
    const TYPE_CONTENT_REDIRECT_TO_URL = "redirect_to_url";
    const TYPE_CONTENT_TITLE = "title";

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            "req_params" => [],
            "remote_path" => null,
            "data" => null,
        ]);
        $resolver->setDefined(["type_content", "req_params"]);
        $resolver->setAllowedValues("type_content", ["image", "text", "html", "card", "redirect_to_url", "title"]);
        $resolver->setAllowedTypes("req_params", "array");
        $resolver->setRequired(["data", "type_content"]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars["type_content"] = $options["type_content"];
        $view->vars["remote_path"] = $options["remote_path"];
        $view->vars["req_params"] = $options["req_params"];
        $view->vars["data"] = $options["data"];
    }

    public function getBlockPrefix(): string
    {
        return "readonly";
    }

}
