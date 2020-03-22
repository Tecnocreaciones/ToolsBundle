<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Constraints as Constraints;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Convierte las validaciones de symfony en validaciones estandar
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class SymfonyConstraintsParser implements ConstraintsParserInterface
{
    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor 
     */
    private $propertyAccessor;
    
    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }
    
    public function parse($constraint)
    {
        $parsed = null;
        $info = $this->getMappedClass();
        $originClass = get_class($constraint);
        if(array_key_exists($originClass, $info)){
            $mappedInfo = $info[$originClass];
            $reflection = new \ReflectionClass($mappedInfo["mapped"]);
            $parsed = $reflection->newInstanceArgs();
            $this->mapProperties($constraint, $parsed,$mappedInfo["properties"]);
        }
        
        if($parsed === null){
            throw new RuntimeException(sprintf("No se pudo parsear la validacion '%s'", get_class($constraint)));
        }
        return $parsed;
    }
    
    /**
     * Mapea los valores de una clase a otra
     * @param type $origin
     * @param type $destination
     * @param array $properties
     */
    private function mapProperties($origin,$destination,array $properties)
    {
        foreach ($properties as $propertyPath) {
            $value = $this->propertyAccessor->getValue($origin, $propertyPath);
            $this->propertyAccessor->setValue($destination, $propertyPath, $value);
        }
    }
    
    /**
     * Retorna la informacion de las validaciones a mapear
     * @return array
     */
    private function getMappedClass()
    {
        return [
            NotBlank::class => [
                "mapped" => Constraints\NotBlank::class,
                "properties" => ["message"]
            ],
            Length::class => [
                "mapped" => Constraints\Length::class,
                "properties" => ["maxMessage","minMessage","exactMessage","max","min"]
            ],
        ];
    }
}
