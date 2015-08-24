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
 * Valor de un datatable
 *
 * @author inhack20
 */
class ValueDataTable 
{
    private $originalValue;
    private $dateFormat = 'd/m/Y';
    private $dateTimeFormat = 'd/m/Y H:i a';


    public function __construct($originalValue) 
    {
        $this->originalValue = $originalValue;
    }

    public function getString()
    {
        return (string)$this->originalValue;
    }
    
    public function getFloat()
    {
        return (float)$this->originalValue;
    }
    
    public function getDate()
    {
        $date = \DateTime::createFromFormat($this->dateFormat, $this->originalValue);
        return $date;
    }
    public function getDateTime()
    {
        $date = \DateTime::createFromFormat($this->dateTimeFormat, $this->originalValue);
        return $date;
    }
    public function __toString() {
        return $this->getString();
    }
}
