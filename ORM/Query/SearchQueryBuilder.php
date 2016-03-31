<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\ORM\Query;

/**
 * Description of SearchQueryBuilder
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
    
    function __construct(\Doctrine\ORM\QueryBuilder $qb, \Doctrine\Common\Collections\ArrayCollection $criteria, $alias) {
        $this->qb = $qb;
        $this->criteria = $criteria;
        $this->alias = $alias;
    }

    /**
     * @return \Tecnocreaciones\Bundle\ToolsBundle\ORM\Query\SearchQueryBuilder
     */
    public function addFieldDescription() {
        if(($description = $this->criteria->remove('description')) != null){
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
        foreach ($fields as $field){
            $normalizeField = $this->normalizeField($this->getAlias(),$field);
            $valueField = json_decode($this->criteria->remove($field),false);
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
                $this->qb->andWhere(sprintf("%s = %s",$normalizeField,$this->qb->expr()->literal($valueField)));
            }
        }
        return $this;
    }
    /**
     * @param array $fields
     * @return \Tecnocreaciones\Bundle\ToolsBundle\ORM\Query\SearchQueryBuilder
     */
    public function addFieldLike(array $fields)
    {
        foreach ($fields as $field){
            $normalizeField = $this->normalizeField($this->getAlias(),$field);
            $valueField = $this->criteria->remove($field);
            if($valueField !== null){
                $this->qb->andWhere($this->qb->expr()->like($normalizeField,$this->qb->expr()->literal("%".$valueField."%")));
            }
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
     * @param array $fieldDates
     * @return \Tecnocreaciones\Bundle\ToolsBundle\ORM\Query\SearchQueryBuilder
     */
    public function addFieldDate(array $fieldDates) {
        foreach ($fieldDates as $fieldDate) {
            $fieldDateDay = $this->criteria->remove("day_".$fieldDate);
            $fieldDateMonth = $this->criteria->remove("month_".$fieldDate);
            $fieldDateYear = $this->criteria->remove("year_".$fieldDate);
            if(empty($fieldDateDay) && empty($fieldDateMonth) && empty($fieldDateYear) ){
                continue;
            }
            
            if($fieldDateDay !== null && $fieldDateMonth !== null && $fieldDateYear !== null){
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
    
    private function getAlias()
    {
        return $this->alias;
    }
    
    private function normalizeField($alias,$field) {
        $fieldResponse = "";
        $fieldExplode = explode("__", $field);
        if(count($fieldExplode) == 2){
            $fieldResponse = sprintf("%s_%s.%s",$alias,$fieldExplode[0],$fieldExplode[1]);
        }else{
            $fieldResponse = sprintf("%s.".$fieldExplode[0],$alias);
        }
        return $fieldResponse;
    }
}
