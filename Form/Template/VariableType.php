<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Template;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Tecnocreaciones\Bundle\ToolsBundle\Model\ORM\Template\ModelParameter;

/**
 * Formulario de variable
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class VariableType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder                
                ->add('name', null, [
                ])
                ->add('description', null, [
                ])
                ->add('typeVariable', ChoiceType::class, [
                    "placeholder" => "label.choice.empty",
                    "choices" => ModelParameter::getLabelsTypeVariable(),
                    'translation_domain' => 'labels'
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

    /**
     * @return string
     */
    public function getName() 
    {
        return 'form_variable';
    }
}
