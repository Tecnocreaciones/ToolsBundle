<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Constraints;

/**
 * No se permite valor vacio
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class NotNull extends Constraint
{
    public $message = 'This value should not be null.';
}
