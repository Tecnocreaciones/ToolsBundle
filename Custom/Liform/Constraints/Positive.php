<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Constraints;

/**
 * Validación de numero
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class Positive extends Constraint
{
    public $message = 'This value should be positive.';
}
