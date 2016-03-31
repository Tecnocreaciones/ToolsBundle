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

/**
 * Modelo de filtro estandares
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ModelFilter extends \Tecnocreaciones\Bundle\ToolsBundle\Model\Base\BaseModelMaster
{
    const TYPE_INPUT= "input";
    const TYPE_INPUT_FROM_TO = "input_from_to";
    const TYPE_YES_NO = "yesNo";
    const TYPE_DATE = "date";
    const TYPE_SELECT = "select";
    const TYPE_TODO = "todo";
    const TYPE_YEAR = "year";
    
    const DATATYPE_STRING = "string";
    const DATATYPE_NUMBER = "number";
    const DATATYPE_FLOAT = "float";
    const DATATYPE_PERCENTAGE = "percentage";
}
