<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Tab;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Formulario para agregar notas publicas y privadas
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class NotesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder                
                ->add('publicNote',TextareaType::class, [
                'label' => ' ',
                'required' => false,
                'attr' => [
                    "class" => "form-control",
                    "placeholder" => "Nota publica (opcional)",
                ],
            ])
                ->add('privateNote',TextareaType::class, [
                'label' => ' ',
                'required' => false,
                'attr' => [
                    "class" => "form-control",
                    "placeholder" => "Nota privada (opcional)",
                ],
            ])
                ;
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver 
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'method' => 'POST'
        ));
    }

    public function getBlockPrefix()
    {
        return "tab_notes";
    }
}
