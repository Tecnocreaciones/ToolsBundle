<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Constraints;

/**
 * Correo electronico
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class Email extends Constraint
{
    public $message = 'This value is not a valid email address.';
}
