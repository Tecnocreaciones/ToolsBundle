<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform;

/**
 * Convertir validacion a una compatible
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
interface ConstraintsParserInterface
{
    public function parse(\Symfony\Component\Validator\Constraint $constraint);
}
