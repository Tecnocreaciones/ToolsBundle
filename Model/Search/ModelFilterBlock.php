<?php

/*
 * This file is part of the TecnoReady Solutions C.A. package.
 * 
 * (c) www.tecnoready.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Search;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bloque de filtros
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 * @ORM\MappedSuperclass()
 */
abstract class ModelFilterBlock extends \Tecnocreaciones\Bundle\ToolsBundle\Model\Base\BaseMaster
{   
    const PARAMETER_STYLE = "style";
    
    /**
     * Area donde se rendizara el bloque
     * @var string
     * @ORM\Column(name="area",type="string",length=100)
     */
    protected $area;
    /**
     * Orden del bloque
     * @var integer
     * @ORM\Column(name="order_block",type="integer")
     */
    protected $orderBlock;
    
    /**
     * Parametros extras
     * @var integer
     * @ORM\Column(name="parameters",type="json_array")
     */
    protected $parameters;

    public function __construct() {
        $this->parameters = [];
    }

    public function getArea() {
        return $this->area;
    }

    public function getOrderBlock() {
        return $this->orderBlock;
    }

    public function setArea($area) {
        $this->area = $area;
        return $this;
    }

    public function setOrderBlock($orderBlock) {
        $this->orderBlock = $orderBlock;
        return $this;
    }
    
    public function getFiltersByGroup(ModelFilterGroup $group)
    {
        $filters = [];
        foreach ($this->filterAddeds as $filterAdded) {
            $filter = $filterAdded->getFilter();
            if($filterAdded->getFilterGroup() !== $group){
                continue;
            }
            if($filterAdded->getModelName() !== null){
                $filter->setModelName($filterAdded->getModelName());
            }
            $filters[] = $filter;
        }
        return $filters;
    }
    
    public function getGroupsFilters() {
        $groups = [];
        foreach ($this->filterAddeds as $filterAdded) {
            $group = $filterAdded->getFilterGroup();
            if(in_array($group, $groups)){
                continue;
            }
            $groups[] = $group;
        }
        return $groups;
    }
    
    /**
     * Get filterAddeds
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFilterAddeds()
    {
        return $this->filterAddeds;
    }
    
    public function getParameters() {
        return $this->parameters;
    }
    
    public function getParameter($key,$default = null) {
        return isset($this->parameters[$key]) ? $this->parameters[$key] : $default;
    }

    public function setParameter($key,$value) {
        $this->parameters[$key] = $value;
        return $this;
    }
}
