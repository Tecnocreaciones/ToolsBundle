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
 * Unidades de capacidad
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class CapacityUnitType extends UnitType
{
    const UNIT_MILILITRO = 'mililitro';
    const UNIT_CENTILITRO = 'centilitro';
    const UNIT_DECILITRO = 'decilitro';
    const UNIT_LITRO = 'litro';
    const UNIT_DECALITRO = 'decalitro';
    const UNIT_HECTOLITRO = 'hectolitro';
    const UNIT_KILOLITRO = 'kilolitro';
    
    public function getDescription() {
        return 'Capacidad';
    }

    public function init() 
    {
        $this->insertUnit(self::UNIT_MILILITRO, 'ml', 10);
        $this->insertUnit(self::UNIT_CENTILITRO, 'cl', 10);
        $this->insertUnit(self::UNIT_DECILITRO, 'dl', 10);
        $this->insertUnit(self::UNIT_LITRO, 'l', 10);
        $this->insertUnit(self::UNIT_DECALITRO, 'dal', 10);
        $this->insertUnit(self::UNIT_HECTOLITRO, 'hl', 10);
        $this->insertUnit(self::UNIT_KILOLITRO, 'kl', 10);
    }

    public static function getType() {
        return 'capacity';
    }

}
