<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Constraints;

use ReflectionClass;

/**
 * Base de validacion
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class Constraint
{
    /**
     * Nombre obligatorio y unico
     * @var string
     */
    public $name;
    public $fullyQualifiedName;
    
    public function __construct()
    {
        $reflectionClass = new ReflectionClass($this);
        $this->name = $reflectionClass->getShortName();
        $this->fullyQualifiedName = $reflectionClass->getName();
    }
}
