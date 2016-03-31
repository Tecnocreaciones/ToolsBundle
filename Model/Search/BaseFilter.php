<?php

/*
 * This file is part of the Witty Growth C.A. - J406095737 package.
 * 
 * (c) www.mpandco.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Search;

use Doctrine\ORM\Mapping as ORM;

/**
 * Base de filtro
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 * @ORM\MappedSuperclass()
 */
abstract class BaseFilter extends ModelFilter 
{   
    /**
     * Tipo de filtro (self::TYPE_*)
     * @var integer
     * @ORM\Column(name="type_filter",type="string",length=100)
     */
    protected $typeFilter;
    /**
     * Etiqueta
     * @var string
     * @ORM\Column(name="label",type="string",length=200)
     */
    protected $label;
    /**
     * Nombre del modelo
     * @var string
     * @ORM\Column(name="model_name",type="string",length=200,nullable=true)
     */
    protected $modelName;
    /**
     * Parametros adicionales
     * @var array
     * @ORM\Column(name="parameters",type="array")
     */
    protected $parameters;
    
    public function __construct() {
        $this->parameters = [];
    }
    public function setParameter($key,$value) {
        $this->parameters[$key] = $value;
        return $this;
    }
    public function getParameter($key) {
        return $this->parameters[$key];
    }

    /**
     * Set typeFilter
     *
     * @param string $typeFilter
     * @return Filter
     */
    public function setTypeFilter($typeFilter)
    {
        $this->typeFilter = $typeFilter;

        return $this;
    }

    /**
     * Get typeFilter
     *
     * @return string 
     */
    public function getTypeFilter()
    {
        return $this->typeFilter;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return Filter
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set modelName
     *
     * @param string $modelName
     * @return Filter
     */
    public function setModelName($modelName)
    {
        $this->modelName = $modelName;

        return $this;
    }

    /**
     * Get modelName
     *
     * @return string 
     */
    public function getModelName()
    {
        return $this->modelName;
    }

    /**
     * Set parameters
     *
     * @param array $parameters
     * @return Filter
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return array 
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get filterGroup
     */
    public function getFilterGroup()
    {
        return $this->filterGroup;
    }
    
    public function __toString() {
        return $this->getLabel()?:"-";
    }
}
