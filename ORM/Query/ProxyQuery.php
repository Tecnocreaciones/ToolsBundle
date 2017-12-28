<?php

/*
 * This file is part of the Witty Growth C.A. - J406095737 package.
 * 
 * (c) www.mpandco.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\ORM\Query;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Func;

/**
 * Proxy para el query de search query builder
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ProxyQuery 
{
    private $alias;
    
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;
    
    /**
     * Todos los left joins
     * @var array
     */
    private $leftJoins;
    /**
     * Todos los inner joins
     * @var array
     */
    private $innerJoins;
    /**
     * Join que ya se han añadido
     * @var array 
     */
    private $joinsAdded;
    
    public function __construct($alias, QueryBuilder $queryBuilder) {
        $this->alias = $alias;
        $this->queryBuilder = $queryBuilder;
        $this->joinsAdded = [];
    }
    
    public function leftJoins(array $joins) {
        $this->addToJoin("leftJoins", $joins);
        return $this;
    }
    
    public function innerJoins(array $joins) {
        $this->addToJoin("innerJoins", $joins);
        return $this;
    }
    
    private function addToJoin($property,array $joins) {
        $this->$property = [];
        foreach ($joins as $key => $join) {
            $jKey = sprintf("%s_%s",  $this->alias,$key);
            if(in_array($join, $this->$property)){
                throw new \InvalidArgumentException(sprintf("The join %s is already added.",$join));
            }
            if(is_array($join)){
                foreach ($join as $k => &$v) {
                    $v = sprintf("%s_%s",  $this->alias,$v);
                }
            }
            $this->$property[$jKey] = $join;
        }
        return $this;
    }

    public function __call($name, $args)
    {
        foreach ($args as $arg) {
            if($arg instanceof Func){
                $this->resolveJoin($arg->getName());
            }else if(is_string($arg)){
                $this->resolveJoin($arg);
            }
        }
        if($name === "expr"){
            return $this->queryBuilder->expr();
        }
        if($name === "innerJoin" && count($args) == 2){
            
            if($this->hasJoinAdded($args[1])){
                return;
            }else {
                $this->joinsAdded[] = $args[1];
            }
        }
            
        return call_user_func_array([$this->queryBuilder, $name], $args);
    }
    
    private function resolveJoin($expName) {
        $explodeName = explode(" ", $expName);
        $resolves = [];
        foreach ($explodeName as $name) {
            $namePoint = explode(".",$name);
            if(count($namePoint) == 2){
                $resolves[] = $namePoint[0]; 
//                        var_dump($namePoint[0]);
            }
        }
//        var_dump($resolves);
        foreach ($resolves as $join) {
            $this->resolveJoins($join);
        }
//        var_dump($this->leftJoins);
    }
    
    private function resolveJoins($join) {
        
        $this->buildJoin($join);
    }


    private function buildJoin($join){
        if(in_array($join, $this->joinsAdded)){
            return;
        }
        if(isset($this->leftJoins[$join])){
            $property = "leftJoins";
        }else if(isset($this->innerJoins[$join])){
            $property = "innerJoins";
        }else {
            //No manejada el join
            return;
        }
//        var_dump($join." : $property");
//        var_dump($this->$property[$join]);
        if(isset($this->$property[$join])){
            $found = $this->$property[$join];
            
            if(is_array($found)){
                $this->buildJoin(array_values($found)[0]);
                $valueJoin = array_keys($found)[0];
            }else {
                $valueJoin = $found;
            }
//            var_dump("agregando ".$property." ".$join." ".$valueJoin);
            $this->joinsAdded[] = $join;
            if($property === "leftJoins"){
                $this->queryBuilder->leftJoin($valueJoin, $join);
            }else if($property === "innerJoins"){
                $this->queryBuilder->innerJoin($valueJoin, $join);
            }
        }
        
//        var_dump($found);
    }
    
    private function hasJoinAdded($join) {
        return in_array($join, $this->joinsAdded);
    }

    public function __get($name)
    {
        return $this->queryBuilder->$name;
    }
    
    public function getQueryBuilder() {
        return $this->queryBuilder;
    }
}