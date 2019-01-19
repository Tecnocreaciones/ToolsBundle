<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Tab;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

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
            ]);
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
