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
 * Manejador de la data que envia data tables
 *
 * @author inhack20
 */
class DataTableData 
{
    protected $draw;
    protected $columns;
    
    protected $order;
    protected $start;
    protected $length;
    
    public function __construct(\Symfony\Component\HttpFoundation\Request $request) 
    {
        $this->draw = $request->get("draw",0);
        $this->order = $request->get("order");
        $this->start = $request->get("start");
        $this->length = $request->get("length");
        $this->search = $request->get("search");
        $this->buildColumns($request->get("columns",[]));
        
    }
    
    /**
     * Construye una columna
     * @param array $columns
     */
    private function buildColumns(array $columns)
    {
        $this->columns = [];
        foreach ($columns as $key => $column)
        {
            $column = new DataTableColumn($column);
            $this->columns[$key] = $column;
            if($column->getName() != ""){
                $this->columns[$column->getName()] = $column;
            }
            if($column->getData() != ""){
                $this->columns[$column->getData()] = $column;
            }
        }
    }
    /**
     * Retorna una columna por el indice
     * @param type $index
     * @return DataTableColumn
     * @throws Exception
     */
    public function getColumnByIndex($index)
    {
        if(!isset($this->columns[$index])){
            throw new Exception(sprintf("Column index %s is not valid",$index));
        }
        return $this->columns[$index];
    }
    /**
     * Retorna la columna por el nombre
     * @param type $name
     * @return DataTableColumn
     * @throws Exception
     */
    public function getColumn($name)
    {
        if(!isset($this->columns[$name])){
            throw new Exception(sprintf("Column name %s is not valid",$name));
        }
        return $this->columns[$name];
    }
    public function getDraw() {
        return $this->draw;
    }

    public function getOrder() {
        return $this->order;
    }

    public function getStart() {
        return $this->start;
    }

    public function getLength() {
        return $this->length;
    }


}
