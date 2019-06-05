<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Email;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Tecnoready\Common\Model\Email\ORM\ModelComponent;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\OptionsResolver\Options;

/**
 * Formulario de componente de correo
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ComponentBodyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('body', CKEditorType::class, [
                    "config" => [
                        "toolbar" => "full",
                    ],
                    'label' => false,
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired("data_class");
        $resolver->setDefaults(array(
            "empty_data" => function (Options $options){
                $class = $options['data_class'];
                $obj = new $class();
                $obj->setTypeComponent(ModelComponent::TYPE_COMPONENT_BODY);
                return $obj;
            }
        ));
    }

    public function getBlockPrefix()
    {
        return "component";
    }

}
