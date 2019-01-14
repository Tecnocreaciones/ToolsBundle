<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Exporter\Model;

use Tecnocreaciones\Bundle\ToolsBundle\Model\Exporter\ModelDocument;

/**
 * Modelo de txt
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ModelTXT extends ModelDocument
{
    public function getFormat() {
        return "txt";
    }

    public function write(array $parameters = []) {
        $fname = tempnam(null, $this->getName().".".$this->getFormat());
        extract($parameters);
        $fh = fopen($fname, "a");
            include $this->getFilePathContent();
        fclose($fh);
        $pathFileOut = $this->getDocumentPath($parameters);
        rename($fname, $pathFileOut);
        
        return $pathFileOut;
    }
}
