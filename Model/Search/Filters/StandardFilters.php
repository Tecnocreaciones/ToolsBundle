<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Search\Filters;

/**
 * Filtros estandares
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class StandardFilters implements GroupFilterInterface 
{
    const TYPE_INPUT= "input";
    const TYPE_INPUT_FROM_TO = "inputFromTo";
    const TYPE_YES_NO = "yesNo";
    const TYPE_DATE = "date";
    const TYPE_SELECT = "select";
    const TYPE_TODO = "todo";
    const TYPE_YEAR = "year";
    
    public static function getMacroTemplate() 
    {
        return "TecnocreacionesToolsBundle:Search:standard_filters.html.twig";
    }

    public static function getName() 
    {
        return "standard";
    }

    public static function getTypes() {
        return [
            self::TYPE_INPUT => 'choice.filter.input',
            self::TYPE_INPUT_FROM_TO => 'choice.filter.inputFromTo',
            self::TYPE_YES_NO => 'choice.filter.yesNo',
            self::TYPE_DATE => 'choice.filter.date',
            self::TYPE_SELECT => 'choice.filter.select',
            self::TYPE_TODO => 'choice.filter.todo',
            self::TYPE_YEAR => 'choice.filter.year',
        ];
    }
}
