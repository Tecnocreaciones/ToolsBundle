<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\ORM\Template;

use Doctrine\ORM\Mapping as ORM;

/**
 * Base de parametro
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class ModelVariable
{
    const TYPE_INT = "INT";
    const TYPE_FLOAT = "FLOAT";
    const TYPE_OBJECT = "OBJECT";
    const TYPE_STRING = "STRING";
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * Descripcion del contenido de la variable
     * @var string
     * @ORM\Column(name="description",type="text")
     */
    protected $description;
    
    /**
     * Tipo de contenido de la variable (self::TYPE_*)
     * @var string
     * @ORM\Column(type="string",length=10,nullable=false)
     */
    protected $typeVariable;
    
    /**
     * Nombre de la variable
     * @var string
     * @ORM\Column(name="name",type="string",length=30)
     */
    protected $name;
    
    public function getDescription()
    {
        return $this->description;
    }

    public function getTypeVariable()
    {
        return $this->typeVariable;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function setTypeVariable($typeVariable)
    {
        $this->typeVariable = $typeVariable;
        return $this;
    }
    
    public function getTypeVariableLabel(){
        $type = $this->getTypeVariable();
        return $type === null ? : array_search($type,self::getLabelsTypeVariable());
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
        
    public static function getLabelsTypeVariable() 
    {
        return array(
            "label.parameter.type.int" => self::TYPE_INT,
            "label.parameter.type.float" => self::TYPE_FLOAT,
            "label.parameter.type.object" => self::TYPE_OBJECT,
            "label.parameter.type.string" => self::TYPE_STRING,
        );
    }
    
    public function __toString()
    {
        $r = "-";
        if($this->name){
            $r = sprintf("(%s) %s", $this->name, $this->description);
        }
        return $r;
    }
}
