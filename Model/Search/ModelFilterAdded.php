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
 * Filtro añadido a bloque
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 * @ORM\MappedSuperclass()
 */
abstract class ModelFilterAdded extends \Tecnocreaciones\Bundle\ToolsBundle\Model\Base\BaseModelMaster
{
    /**
     * Orden del filtro dentro del grupo
     * @var integer
     * @ORM\Column(name="order_filter",type="integer")
     */
    protected $orderFilter = 0;

    public function getOrderFilter() {
        return $this->orderFilter;
    }

    public function setOrderFilter($orderFilter) {
        $this->orderFilter = $orderFilter;
        return $this;
    }
    
    public function getFilter() {
        return $this->filter;
    }

    public function setFilter($filter) {
        $this->filter = $filter;
        return $this;
    }
    
    public function getFilterBlock() {
        return $this->filterBlock;
    }

    public function setFilterBlock($filterBlock) {
        $this->filterBlock = $filterBlock;
        return $this;
    }
    
    public function setFilterGroup(ModelFilterGroup $filterGroup) {
        $this->filterGroup = $filterGroup;
        return $this;
    }
    
    public function __toString() {
        return $this->getFilter()?(string)$this->getFilter():"-";
    }
}
