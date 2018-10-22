<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Exporter\Model;

use Tecnocreaciones\Bundle\ToolsBundle\Model\Exporter\ModelDocument;

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
}
