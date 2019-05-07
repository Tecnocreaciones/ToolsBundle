<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Tab;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Formulario para generar documento
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ExporterType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();
        $builder
                ->add('name', ChoiceType::class, [
                    'label' => ' ',
                    "choices" => $data,
//                    "placeholder" => "documents.generated.model.placeholder",
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
        return "documents_generated";
    }

}
