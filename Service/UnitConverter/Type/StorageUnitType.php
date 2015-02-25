<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\UnitConverter\Type;

use Tecnocreaciones\Bundle\ToolsBundle\Service\UnitConverter\UnitType;

/**
 * Unidades de almacenamiento
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class StorageUnitType extends UnitType
{
    const UNIT_BIT = 'bit';
    const UNIT_NIBBLE = 'nibble';
    const UNIT_BYTE = 'byte';
    const UNIT_KILOBYTE = 'kilobyte';
    const UNIT_MEGABYTE = 'megabyte';
    const UNIT_GIGABYTE = 'gigabyte';
    const UNIT_TERABYTE = 'terabyte';
    const UNIT_PETABYTE = 'petabyte';
    
    public function getDescription()
    {
        return 'Almacenamiento';
    }

    public function init() 
    {
        $this->insertUnit(self::UNIT_BIT, _('bit,bits'), 0);
        $this->insertUnit(self::UNIT_NIBBLE, _('nibble'), 4);
        $this->insertUnit(self::UNIT_BYTE, _('byte'), 2);
        $this->insertUnit(self::UNIT_KILOBYTE, _('Kb,kiloByte'), 1024);
        $this->insertUnit(self::UNIT_MEGABYTE, _('Mb,megaByte'), 1024);
        $this->insertUnit(self::UNIT_GIGABYTE, _('Gb,gigaByte'), 1024);
        $this->insertUnit(self::UNIT_TERABYTE, _('Tb,teraByte'), 1024);
        $this->insertUnit(self::UNIT_PETABYTE, _('Pb,petaByte'), 1024);
    }

    public static function getType()
    {
        return 'storage';
    }
}
