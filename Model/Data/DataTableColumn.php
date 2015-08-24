<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Data;

/**
 * Columna de data table
 *
 * @author inhack20
 */
class DataTableColumn 
{
    private $data;
    private $name;
    private $searchable;
    private $orderable;
    private $search;
    private $value;
    
    public function __construct($column) 
    {
        $this->data = $column['data'];
        $this->name = $column['name'];
        $this->searchable = $column['searchable'];
        $this->orderable = $column['orderable'];
        $this->search = $column['search'];
        $this->value = new ValueDataTable($this->search['value']);
    }
    
    public function getData() {
        return $this->data;
    }

    public function getName() {
        return $this->name;
    }

    public function getSearchable() {
        return $this->searchable;
    }

    public function getOrderable() {
        return $this->orderable;
    }

    public function getSearch() {
        return $this->search;
    }
    
    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setSearchable($searchable) {
        $this->searchable = $searchable;
        return $this;
    }

    public function setOrderable($orderable) {
        $this->orderable = $orderable;
        return $this;
    }

    public function setSearch($search) {
        $this->search = $search;
        return $this;
    }
    
    /**
     * 
     * @return ValueDataTable
     */
    public function getValue() {
        return $this->value;
    }
    
    public function __toString() {
        return (string)$this->getValue();
    }
}
