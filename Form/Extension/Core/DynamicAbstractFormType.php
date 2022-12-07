<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Extension\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario base para formularios dinamicos
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class DynamicAbstractFormType extends AbstractType {

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefaults(array(
            "steps" => 1,
        ));
    }

}
