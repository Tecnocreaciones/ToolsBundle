<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Exporter\Model;

use Tecnocreaciones\Bundle\ToolsBundle\Model\Exporter\ModelDocument;
use DOMDocument;
use SimpleXMLElement;

/**
 * Modelo de xml
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ModelXML extends ModelTXT
{
   public function getFormat() {
        return "xml";
    }
    
    /**
     * Formatea el xml a formato pretty manteniendo los saltos de linea
     * @param SimpleXMLElement $simpleXMLElement
     * @return type
     */
    function formatXml(SimpleXMLElement $simpleXMLElement)
    {
        $xmlDocument = new DOMDocument('1.0');
        $xmlDocument->preserveWhiteSpace = false;
        $xmlDocument->formatOutput = true;
        $xmlDocument->loadXML($simpleXMLElement->asXML());

        return $xmlDocument->saveXML();
    }
}
