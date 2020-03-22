<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform;

use Symfony\Component\Form\FormInterface;

/**
 * Funciones comunes
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
trait CommonFunctionsTrait
{
    /**
     * Añade las validaciones
     * @param FormInterface $form
     * @param array $schema
     * @return type
     */
    protected function addConstraints(FormInterface $form, array $schema)
    {
        $schema['constraints'] = [];
    	if ($constraints = $form->getConfig()->getOption('constraints')) {
            $schema['constraints'] = $constraints;
        }

        return $schema;
    }
}
