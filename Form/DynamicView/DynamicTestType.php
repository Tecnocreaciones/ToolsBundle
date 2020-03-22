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
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Formulario de prueba dinamico
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DynamicTestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraints = [
            new NotBlank(),
            new Length(['min' => 3]),
        ];
        $builder
                ->add('select_options', ChoiceType::class, [
                    'label' => "Opciones",
                    "choices" => [
                        "opcion 1" => "a",
                        "opcion 2" => "b",
                    ],
                    'constraints' => $constraints,
                ])
                ->add("date_at",DateType::class,[
                    "label" => "Fecha",
                    'constraints' => $constraints,
                ])
                ->add("file_image",FileType::class,[
                    "label" => "Archivo de imagen",
                    'constraints' => $constraints,
                ])
                ->add("file_image",FileType::class,[
                    "label" => "Archivo de imagen",
                    'constraints' => $constraints,
                ])
                ->add("check_option",CheckboxType::class,[
                    "label" => "Checkbox",
                    'constraints' => $constraints,
                ])
                ->add("texto_normal",TextType::class,[
                    "label" => "Texto corto",
                    'constraints' => $constraints,
                ])
                ->add("texto_largo",TextareaType::class,[
                    "label" => "Texto largo",
                    'constraints' => $constraints,
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
