<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Extension\Core\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;

/**
 * Checkbox que permite copiar valores de un campo a otro
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DynamicCopyCheckboxType extends AbstractType
{
    public function buildView(\Symfony\Component\Form\FormView $view, \Symfony\Component\Form\FormInterface $form, array $options)
    {
        $view->vars["copy_from"] = $options["copy_from"];
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired(["copy_from"]);
        $resolver->setAllowedTypes("copy_from","array");
        /**
         * Se copiara mediante el nombre de los campos, se debe indicar campo origen y campo destino
         * Ejemplo:
         * array("rcvPlus_postpago[typeIdentity]" => "rcvPlus_postpago[owner_rif_choice]")
         */
    }
    
    public function getBlockPrefix()
    {
        return "copy_checkbox";
    }
    
    public function getParent()
    {
        return CheckboxType::class;
    }
}
