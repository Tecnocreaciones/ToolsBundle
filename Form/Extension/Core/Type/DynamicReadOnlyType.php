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

    /**
     * Tipo de contenido imagen (url, base64)
     */
    const TYPE_CONTENT_IMAGE = "image";
    const TYPE_CONTENT_ICON = "icon";
    /**
     * Tipo de contenido texto plano en entry de solo lectura
     */
    const TYPE_CONTENT_TEXT = "text";
    /**
     * Tipo de contenido html en label
     */
    const TYPE_CONTENT_HTML = "html";
    const TYPE_CONTENT_CARD = "card";
    /**
     * Tipo de contenido redirección a url (para abrir el navegador)
     */
    const TYPE_CONTENT_REDIRECT_TO_URL = "redirect_to_url";
    /**
     * Tipo de contenido titulo (Separador de formularios)
     */
    const TYPE_CONTENT_TITLE = "title";
    
    /**
     * Tipo de contenido label que al tocarlo carga una URI dentro de la app
     */
    const TYPE_CONTENT_URL = "url";

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            "req_params" => [],
            "remote_path" => null,
            "data" => null,
            "placeholder" => null,
        ]);
        $resolver->setDefined(["type_content", "req_params"]);
        $resolver->setAllowedValues("type_content", [
            self::TYPE_CONTENT_IMAGE, 
            self::TYPE_CONTENT_TEXT,
            self::TYPE_CONTENT_HTML, 
            self::TYPE_CONTENT_CARD, 
            self::TYPE_CONTENT_REDIRECT_TO_URL, 
            self::TYPE_CONTENT_TITLE,
            self::TYPE_CONTENT_URL
        ]);
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
