<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\DynamicView;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Tecnocreaciones\Bundle\ToolsBundle\Form\Extension\Core\Type\DynamicSubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Formulario de prueba dinamico
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DynamicTestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('options', ChoiceType::class, [
                    'label' => "Opciones",
                    "choices" => [
                        "opcion 1" => "a",
                        "opcion 2" => "b",
                    ]
                ])
                ->add("date_at",DateType::class,[
                    "label" => "Fecha"
                ])
                ->add("file_image",FileType::class,[
                    "label" => "Archivo de imagen",
                ])
                ->add("file_image",FileType::class,[
                    "label" => "Archivo de imagen",
                ])
                ->add("check_option",CheckboxType::class,[
                    "label" => "Checkbox",
                ])
                ->add("texto_normal",TextType::class,[
                    "label" => "Texto corto",
                ])
                ->add("texto_largo",TextareaType::class,[
                    "label" => "Texto largo",
                ])
                ->add("submit",DynamicSubmitType::class,[
                    "label" => "Boton submit",
                    "render_in" => DynamicSubmitType::RENDER_IN_FORM_BOTTOM,
                ])
        ;
    }
    
    public function getBlockPrefix()
    {
        return "dynamic_form";
    }
}
