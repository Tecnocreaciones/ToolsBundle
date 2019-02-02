<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Tab;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Formulario para subir documentos en la tab de documentos
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DocumentsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder                
                ->add('documents',FileType::class, [
                'label' => ' ',
                'multiple' => true,
            ])
                ->add('comments',TextType::class, [
                'label' => ' ',
                'required' => false,
                'attr' => [
                    "class" => "form-control input-flat",
                    "style" => "width: 20%;height: 30px;display: inline;",
                    "placeholder" => "Comentarios (opcional)",
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
        return "tab_documents";
    }

}
