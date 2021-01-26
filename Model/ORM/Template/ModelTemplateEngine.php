<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\ORM\Template;

use Doctrine\ORM\Mapping as ORM;
use Tecnoready\Common\Model\Template\TemplateInterface;

/**
 * Base para motor de plantilla con doctrine
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class ModelTemplateEngine implements TemplateInterface
{
    /**
     * Id
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=36)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id;
    
    /**
     * Motor de plantilla (engine)
     * @var string
     * @ORM\Column(type="string",length=30,nullable=false)
     */
    protected $typeTemplate;
    
    /**
     * Contenido del template
     * @var string 
     * @ORM\Column(type="text",nullable=false)
     */
    protected $content;
    
    /**
     * Nombre de la plantilla
     * @var string
     * @ORM\Column(name="name",type="string",length=30)
     */
    protected $name;
    
    public function getId()
    {
        return $this->id;
    }

    public function getTypeTemplate()
    {
        return $this->typeTemplate;
    }

    public function getContent()
    {
        return $this->content;
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

    public function setTypeTemplate($typeTemplate)
    {
        $this->typeTemplate = $typeTemplate;
        return $this;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function __toString()
    {
        $r = "-";
        if($this->id){
            $r = sprintf("(%s) %s", $this->id, $this->name);
        }
        return $r;
    }


}
