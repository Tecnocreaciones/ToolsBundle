<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\ORM\Query;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Query builder para las busquedas
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class SearchQueryBuilder 
{
    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $qb;
    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $criteria;
    private $alias;
    private $orderBy;
    
    function __construct(\Doctrine\ORM\QueryBuilder $qb, \Doctrine\Common\Collections\ArrayCollection $criteria, $alias,$orderBy = null) {
        $this->qb = new ProxyQuery($alias,$qb);
        $this->criteria = $criteria;
        $this->alias = $alias;
        if(is_array($orderBy)){
            $orderBy = new \Doctrine\Common\Collections\ArrayCollection($orderBy);
        }
        $this->orderBy = $orderBy;
        $this->joinsAdded = [];
    }
    
    /**
     * Agrega los joins
     * @param array $joins
     * @return \Tecnocreaciones\Bundle\ToolsBundle\ORM\Query\SearchQueryBuilder
     */
    public function leftJoins(array $joins) {
        $this->qb->leftJoins($joins);
        return $this;
    }
    
    /**
     * Agrega los joins
     * @param array $joins
     * @return \Tecnocreaciones\Bundle\ToolsBundle\ORM\Query\SearchQueryBuilder
     */
    public function innerJoins(array $joins) {
        $this->qb->innerJoins($joins);
        return $this;
    }

    /**
     * @return \Tecnocreaciones\Bundle\ToolsBundle\ORM\Query\SearchQueryBuilder
     */
    public function addFieldDescription() {
        if(($description = $this->criteria->remove('description')) != null){
            $description = $this->normalizeValue($description);
            $this->qb
                ->andWhere($this->qb->expr()->like($this->getAlias().'.description', $this->qb->expr()->literal("%$description%")));
        }
        return $this;
    }
    /**
     * @param array $fields
     * @return \Tecnocreaciones\Bundle\ToolsBundle\ORM\Query\SearchQueryBuilder
     */
    public function addFieldIn(array $fields)
    {
        foreach ($fields as $key => $field){
            $fieldToNormalize = $field;
            if(is_string($key)){
                $fieldToNormalize = $key;
            }
            $normalizeField = $this->normalizeField($this->getAlias(),$fieldToNormalize);
            $stringValue = $this->criteria->remove($field);
            if(is_array($stringValue)){
                $valueField = $stringValue;
            }else{
                $valueField = json_decode(urldecode($stringValue),false);
            }
            if(count($valueField) > 0){
                $this->qb
                    ->andWhere($this->qb->expr()->in($normalizeField, $valueField))
                    ;
                
            }
        }
        return $this;
    }
    /**
     * @param array $fields
     * @return \Tecnocreaciones\Bundle\ToolsBundle\ORM\Query\SearchQueryBuilder
     */
    public function addFieldEquals(array $fields)
    {
        foreach ($fields as $field){
            $normalizeField = $this->normalizeField($this->getAlias(),$field);
            $valueField = $this->criteria->remove($field);
            if($valueField !== null){
                $valueField = $this->normalizeValue($valueField);
                $this->qb->andWhere(sprintf("%s = %s",$normalizeField,$this->qb->expr()->literal($valueField)));
            }
        }
        return $this;
    }
    /**
     * @param array $fields
     * @return \Tecnocreaciones\Bundle\ToolsBundle\ORM\Query\SearchQueryBuilder
     */
    public function addFieldLike(array $fields,$defaultValueField = null,array $options = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            "expr" => "andX",
        ]);
        $resolver->setAllowedValues("expr",["andX","orX"]);
        $options = $resolver->resolve($options);
        
        if($options["expr"] == "andX"){
            $x = $this->qb->expr()->andX();//Se coloco andX para buscar siempre por todos los campos en conjutos
        } else if($options["expr"] == "orX"){
            $x = $this->qb->expr()->orX();//Se coloco andX para buscar siempre por todos los campos en conjutos
        }
        foreach ($fields as $key => $field){
            $fieldValue = $field;
            if(is_string($key)){
                $fieldValue = $key;
            }
            $normalizeField = $this->normalizeField($this->getAlias(),$field);            
            $valueField = $this->criteria->remove($fieldValue);
            if($defaultValueField !== null){
                $valueField = $defaultValueField;
            }
            if($valueField !== null){
                $valueField = $this->normalizeValue($valueField);
                $x->add($this->qb->expr()->like($normalizeField,$this->qb->expr()->literal("%".$valueField."%")));
            }
        }
        if($x->count() > 0){
            $this->qb->andWhere($x);
        }
        return $this;
    }
    /**
     * @param array $fields
     * @return \Tecnocreaciones\Bundle\ToolsBundle\ORM\Query\SearchQueryBuilder
     */
    public function addFieldTextAreaLike(array $fields,$defaultValueField = null)
    {
        $this->addFieldLike($fields,$defaultValueField);
        return $this;
    }
    
    /**
     * Añade un campo para realizar una busqueda plana por un valor
     * @param type $queryField
     * @param array $fields
     */
    public function addQueryField($queryField,array $fields) {
        $valueField = $this->criteria->remove($queryField);
        if($valueField !== null){
            $valueField = $this->normalizeValue($valueField);
            $this->addFieldLike($fields,$valueField,[
                "expr" => "orX",//Como es "query" debe ser un orX para que busque en todos los campos.
            ]);
        }
        return $this;
    }
    /**
     * @param array $fields
     * @return \Tecnocreaciones\Bundle\ToolsBundle\ORM\Query\SearchQueryBuilder
     */
    public function addFieldFromTo(array $fields)
    {
        foreach ($fields as $field){
            $fieldFrom = $field."_from";
            $fieldTo = $field."_to";
            $valueFieldFrom = $this->criteria->remove($fieldFrom);
            $valuefieldTo = $this->criteria->remove($fieldTo);
            $normalizeField = $this->normalizeField($this->getAlias(),$field);
            if($valueFieldFrom != null){
                $this->qb
                    ->andWhere(sprintf("%s >= :%s",$normalizeField,$fieldFrom))
                    ->setParameter($fieldFrom,$valueFieldFrom)
                    ;
            }
            if($valuefieldTo != null){
                $this->qb
                    ->andWhere(sprintf("%s <= :%s",$normalizeField,$fieldTo))
                    ->setParameter($fieldTo,$valuefieldTo)
                    ;
            }
        }
        return $this;
    }
    /**
     * Añade busquedas de fechas desde, hasta
     * @param array $fieldDates
     */
    public function addFieldDateFromTo(array $fieldDates) 
    {
        $formatDate = "Y-m-d";
        $completeDateFrom = " 00:00:00";
        $completeDateTo = " 23:59:59";
        
//        $emConfig = $this->qb->getEntityManager()->getConfiguration();
//        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
//        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
//        $emConfig->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');

        foreach ($fieldDates as $fieldDate) {
            $fieldDateNormalize = $this->normalizeField($this->getAlias(), $fieldDate);
            $fieldDateDayFrom = $this->criteria->remove("day_from_".$fieldDate);
            $fieldDateMonthFrom = $this->criteria->remove("month_from_".$fieldDate);
            $fieldDateYearFrom = $this->criteria->remove("year_from_".$fieldDate);
            
            $fieldDateDayTo = $this->criteria->remove("day_to_".$fieldDate);
            $fieldDateMonthTo = $this->criteria->remove("month_to_".$fieldDate);
            $fieldDateYearTo = $this->criteria->remove("year_to_".$fieldDate);
            
//            if(empty($fieldDateDayFrom) && empty($fieldDateMonthFrom) && empty($fieldDateYearFrom) ){
//                continue;
//            }
            
            $dateTimeFrom = null;
            $dateTimeTo = null;
            if($fieldDateDayFrom !== null && $fieldDateMonthFrom !== null && $fieldDateYearFrom !== null){
                $fieldDateDayFrom = str_pad($fieldDateDayFrom, 2,"0",STR_PAD_LEFT);
                $fieldDateMonthFrom = str_pad($fieldDateMonthFrom, 2,"0",STR_PAD_LEFT);
                $fieldDateValue = sprintf("%s/%s/%s",$fieldDateDayFrom,$fieldDateMonthFrom,$fieldDateYearFrom);
                $dateTimeFrom = \DateTime::createFromFormat("d/m/Y", $fieldDateValue);
            }
            
            if($fieldDateDayTo !== null && $fieldDateMonthTo !== null && $fieldDateYearTo !== null){
                $fieldDateDayTo = str_pad($fieldDateDayTo, 2,"0",STR_PAD_LEFT);
                $fieldDateMonthTo = str_pad($fieldDateMonthTo, 2,"0",STR_PAD_LEFT);
                $fieldDateValue = sprintf("%s/%s/%s",$fieldDateDayTo,$fieldDateMonthTo,$fieldDateYearTo);
                $dateTimeTo = \DateTime::createFromFormat("d/m/Y", $fieldDateValue);
            }
            
//            var_dump($dateTimeFrom);
//            var_dump($dateTimeTo);
//            die;
            $addFieldDateYear = function($condition,$fieldValue) use ($fieldDateNormalize){
                $this->qb->andWhere(sprintf("YEAR(%s) %s %s",$fieldDateNormalize,$condition,$fieldValue));
            };
            $addFieldDateMonth = function($condition,$fieldValue) use ($fieldDateNormalize){
                $this->qb->andWhere(sprintf("MONTH(%s) %s %s",$fieldDateNormalize,$condition,$fieldValue));
            };
            $addFieldDateDay = function($condition,$fieldValue) use ($fieldDateNormalize){
                $this->qb->andWhere(sprintf("DAY(%s) %s %s",$fieldDateNormalize,$condition,$fieldValue));
            };
            
            if($dateTimeFrom !== null && $dateTimeTo !== null){
                //Se debe hacer un desde hasta completo

                $this->qb->andWhere($this->qb->expr()->between($fieldDateNormalize,
                        $this->qb->expr()->literal($dateTimeFrom->format($formatDate.$completeDateFrom)), 
                        $this->qb->expr()->literal($dateTimeTo->format($formatDate.$completeDateTo))));
            }else{
                
                if($dateTimeFrom !== null){
                    //Se debe hacer un desde
                    $this->qb->andWhere($this->qb->expr()->gte(
                            $fieldDateNormalize,$this->qb->expr()->literal($dateTimeFrom->format($formatDate.$completeDateFrom))));
                }else{
                    //Se debe filtar individual desde
                    if($fieldDateYearFrom !== null){
                        $addFieldDateYear(">=",$fieldDateYearFrom);
                    }
                    if($fieldDateMonthFrom !== null){
                        $addFieldDateMonth(">=",$fieldDateMonthFrom);
                    }
                    if($fieldDateDayFrom !== null){
                        $addFieldDateDay(">=",$fieldDateDayFrom);
                    }
                }
                if($dateTimeTo !== null){
                    //Se debe hacer un hasta
                    $this->qb->andWhere(
                            $this->qb->expr()->lte($fieldDateNormalize,
                                    $this->qb->expr()->literal($dateTimeTo->format($formatDate.$completeDateTo))));
                }else{
                    //Se debe filtar individual hasta
                    if($fieldDateYearTo !== null){
                        $addFieldDateYear("<=",$fieldDateYearTo);
                    }
                    if($fieldDateMonthTo !== null){
                        $addFieldDateMonth("<=",$fieldDateMonthTo);
                    }
                    if($fieldDateDayTo !== null){
                        $addFieldDateDay("<=",$fieldDateDayTo);
                    }
                }
            }
//            var_dump($dateTimeFrom);
//            var_dump($dateTimeTo);
        }
//        var_dump($this->qb->getDQL());
//        echo($this->qb->getQuery()->getSQL());
//        die;
        return $this;
    }
    /**
     * @param array $fieldDates
     * @return \Tecnocreaciones\Bundle\ToolsBundle\ORM\Query\SearchQueryBuilder
     */
    public function addFieldDate(array $fieldDates) {
        foreach ($fieldDates as $fieldDate) {
            $dateTime = $this->criteria->remove($fieldDate);
            $fieldDateDay = $this->criteria->remove("day_".$fieldDate);
            $fieldDateMonth = $this->criteria->remove("month_".$fieldDate);
            $fieldDateYear = $this->criteria->remove("year_".$fieldDate);
            if($dateTime === null && empty($fieldDateDay) && empty($fieldDateMonth) && empty($fieldDateYear) ){
                continue;
            }
            if($dateTime instanceof \DateTime){
                $this->qb->andWhere($this->qb->expr()->like($this->getAlias().".".$fieldDate,$this->qb->expr()->literal("%".$dateTime->format("Y-m-d")."%")));
            }else if($fieldDateDay !== null && $fieldDateMonth !== null && $fieldDateYear !== null){
                $fieldDateDay = str_pad($fieldDateDay, 2,"0",STR_PAD_LEFT);
                $fieldDateMonth = str_pad($fieldDateMonth, 2,"0",STR_PAD_LEFT);
                
                $fieldDateValue = sprintf("%s/%s/%s",$fieldDateDay,$fieldDateMonth,$fieldDateYear);
                $dateTime = \DateTime::createFromFormat("d/m/Y", $fieldDateValue);
                $this->qb->andWhere($this->qb->expr()->like($this->getAlias().".".$fieldDate,$this->qb->expr()->literal("%".$dateTime->format("Y-m-d")."%")));
            }else{
                
                if($fieldDateDay !== null){
                    $fieldDateDay = str_pad($fieldDateDay, 2,"0",STR_PAD_LEFT);
                    $this->qb
                        ->andWhere(
                            $this->qb->expr()->like(
                                $this->getAlias().".".$fieldDate,$this->qb->expr()->literal("%-%-".$fieldDateDay)
                                             )
                                );
                }
                if($fieldDateMonth !== null){
                    $fieldDateMonth = str_pad($fieldDateMonth, 2,"0",STR_PAD_LEFT);
                    $this->qb
                        ->andWhere(
                            $this->qb->expr()->like(
                                $this->getAlias().".".$fieldDate,$this->qb->expr()->literal("%-".$fieldDateMonth."-%")
                                             )
                                );
                }
                if($fieldDateYear !== null){
                    $this->qb
                        ->andWhere(
                            $this->qb->expr()->like(
                                $this->getAlias().".".$fieldDate,$this->qb->expr()->literal($fieldDateYear."-%-%")
                                             )
                                );
                }
            }
        }
        return $this;
    }
    /**
     * @return \Tecnocreaciones\Bundle\ToolsBundle\ORM\Query\SearchQueryBuilder
     */
    public function addFieldMaxResults() {
        if(($maxResults = $this->criteria->remove('max_results')) != null && $maxResults > 0){
            $this->qb
                ->setMaxResults($maxResults)
                ;
        }
        return $this;
    }
    
    public function applySorting(array $fields,array $default = [])
    {
        if($this->orderBy === null){
            return;
        }
//        var_dump($fields);
//        var_dump($this->orderBy);
        $orderByCount = $this->orderBy->count();
        foreach ($this->orderBy as $field => $value) {
            if(!in_array($field, $fields)){
                continue;
            }
            $valueFiled = strtoupper($value);
            if($valueFiled === null){
                continue;
            }
            if($valueFiled !== "DESC" && $valueFiled !== "ASC"){
                continue;
            }
            $fieldNormalize = $this->normalizeField($this->getAlias(), $field);
            $this->qb->addOrderBy($fieldNormalize, $valueFiled);
//            var_dump($fieldNormalize);
        }
        if($orderByCount == 0 && count($default) > 0){
            foreach ($default as $field => $order) {
                $fieldNormalize = $this->normalizeField($this->getAlias(), $field);
                $this->qb->addOrderBy($fieldNormalize, $order);
            }
        }
//        echo($this->qb->getDQL());
//        die;
    }
    
    private function getAlias()
    {
        return $this->alias;
    }
    
    private function normalizeField($alias,$field) {
        $fieldResponse = "";
        $fieldExplode = explode("__", $field);
        if(strpos($field,".") !== false){
            //Si el nombre del campo es absoluto se usa.
            $fieldResponse = $field;
        }else if(count($fieldExplode) == 2){
            $fieldResponse = sprintf("%s_%s.%s",$alias,$fieldExplode[0],$fieldExplode[1]);
        }else{
            $fieldResponse = sprintf("%s.".$fieldExplode[0],$alias);
        }
        return $fieldResponse;
    }
    /**
     * Decodifica los valores, como se envia por GET la data puede tener
     * @param type $valueField
     * @return type
     */
    private function normalizeValue($valueField)
    {
        $valueField = urldecode($valueField);
        return $valueField;
    }


    public function __call($name, $args)
    {
        if($name === "expr"){
            return $this->qb->expr();
        }
        call_user_func_array([$this->qb, $name], $args);
        return $this;
    }
    /**
    * 
    * @return \Doctrine\ORM\QueryBuilder
    */
    public function getQb() {
        return $this->qb->getQueryBuilder();
    }
}
