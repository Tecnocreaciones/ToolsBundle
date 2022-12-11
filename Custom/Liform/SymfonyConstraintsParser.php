<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Positive;
use Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Constraints as Constraints;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    
    /**
     * Traductor
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    private $translator;


    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }
    
    public function parse($constraint)
    {
        $parsed = null;
        $info = $this->getMappedInfo();
        $originClass = get_class($constraint);
        if(array_key_exists($originClass, $info)){
            $mappedInfo = $info[$originClass];
            $reflection = new \ReflectionClass($mappedInfo["mapped"]);
            $parsed = $reflection->newInstanceArgs();
            $this->mapProperties($constraint, $parsed,$mappedInfo["properties"]);
            $this->transProperties($mappedInfo,$parsed);
        }
        
        if($parsed === null){
            @trigger_error(sprintf("No se pudo parsear la validacion '%s'", get_class($constraint)), \E_USER_DEPRECATED);
//            throw new RuntimeException(sprintf("No se pudo parsear la validacion '%s'", get_class($constraint)));
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
     * Traduce las propiedades
     * @param array $mappedInfo
     * @param \Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Constraints\Constraint $destination
     */
    private function transProperties(array $mappedInfo,Constraints\Constraint $destination)
    {
        $domain = "validators";
        if(isset($mappedInfo["trans_properties"])){
            foreach ($mappedInfo["trans_properties"] as $propertyPath) {
                $value = $this->propertyAccessor->getValue($destination, $propertyPath);
                $this->propertyAccessor->setValue($destination, $propertyPath, $this->trans($value,[],$domain));
            }
        }
        if(isset($mappedInfo["trans_callback"])){
            $self = $this;
            $trans = function($id, array $parameters = [], $domain = "validators", $locale = null) use ($self){
                return $self->trans($id, $parameters, $domain, $locale);
            };
            call_user_func_array($mappedInfo["trans_callback"],[$destination, $trans]);
        }
    }
    
    /**
     * Acceso directo a traduccion
     * @param type $id
     * @param array $parameters
     * @param type $domain
     * @param type $locale
     * @return type
     */
    private function trans($id, array $parameters = [], $domain = "validators", $locale = null)
    {
        return $this->translator->trans($id,$parameters,$domain,$locale);
    }


    /**
     * Retorna la informacion de las validaciones a mapear
     * @return array
     */
    private function getMappedInfo()
    {
        return [
            NotBlank::class => [
                "mapped" => Constraints\NotBlank::class,
                "properties" => ["message"],
                "trans_properties" => ["message"],
            ],
            NotNull::class => [
                "mapped" => Constraints\NotNull::class,
                "properties" => ["message"],
                "trans_properties" => ["message"],
            ],
            Email::class => [
                "mapped" => Constraints\Email::class,
                "properties" => ["message"],
                "trans_properties" => ["message"],
            ],
            //Ignoramos esta validación porque en c# no hace match la expresión regular como en php, cambia el formato
//            Regex::class => [
//                "mapped" => Constraints\Regex::class,
//                "properties" => ["message","pattern"],
//                "trans_properties" => ["message"],
//            ],
            Positive::class => [
                "mapped" => Constraints\Positive::class,
                "properties" => ["message"],
                "trans_properties" => ["message"],
            ],
            Length::class => [
                "mapped" => Constraints\Length::class,
                "properties" => ["maxMessage","minMessage","exactMessage","max","min"],
                "trans_callback" => function(Constraints\Constraint $constraint,$trans){
                    if(!empty($constraint->min)){
                        $constraint->minMessage = $trans($constraint->minMessage,["{{ limit }}" => $constraint->min]);
                    }
                    if(!empty($constraint->max)){
                        $constraint->maxMessage = $trans($constraint->maxMessage,["{{ limit }}" => $constraint->max]);
                    }
                    if(!empty($constraint->min) && !empty($constraint->max)){
                        $constraint->exactMessage = $trans($constraint->exactMessage,["{{ limit }}" => $constraint->min]);
                    }
                },
            ],
        ];
    }
    
    /**
     * @required
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @return $this
     */
    public function setTranslator(\Symfony\Contracts\Translation\TranslatorInterface $translator)
    {
        $this->translator = $translator;
        return $this;
    }
}
