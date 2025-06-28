<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Regex;

/**
 * Expresion regular
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class CustomRegex extends Regex {
    public $sharpPattern;
}
