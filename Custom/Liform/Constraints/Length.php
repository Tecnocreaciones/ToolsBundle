<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Constraints;

/**
 * Valida la longitud de una cadena
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class Length extends Constraint
{
    public $maxMessage = 'This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.';
    public $minMessage = 'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.';
    public $exactMessage = 'This value should have exactly {{ limit }} character.|This value should have exactly {{ limit }} characters.';
    public $max;
    public $min;
}
