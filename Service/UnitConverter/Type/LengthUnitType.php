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
 * Unidades de longitud (Metrico)
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class LengthUnitType extends UnitType
{
    const UNIT_INCH = 'pulgada';
    const UNIT_MICROMETRO = 'micrometro';
    const UNIT_MILLIMETER = 'millimetro';
    const UNIT_CENTIMETER = 'centimetro';
    const UNIT_DECIMETER = 'decimetro';
    const UNIT_METER = 'metro';
    const UNIT_DECAMETER = 'decametro';
    const UNIT_HECTOMETER = 'hectometro';
    const UNIT_KILOMETER = 'kilometro';
    const UNIT_MEGAMETER = 'megametro';
    
    public function getDescription() {
        return 'Longitud';
    }

    public static function getType() {
        return 'length_type';
    }

    public function init() {
        $this->insertUnit(self::UNIT_INCH, _('in,'.self::UNIT_INCH), 0.0254);
        $this->insertUnit(self::UNIT_MICROMETRO, _('µm,'.self::UNIT_MICROMETRO), 0.000001);
        $this->insertUnit(self::UNIT_MILLIMETER, _('mm,'.self::UNIT_MILLIMETER), 0.001);
        $this->insertUnit(self::UNIT_CENTIMETER, _('cm,'.self::UNIT_CENTIMETER), 0.01);
        $this->insertUnit(self::UNIT_DECIMETER, _('dm,'.self::UNIT_DECIMETER), 0.1);
        $this->insertUnit(self::UNIT_METER, _('m,'.self::UNIT_METER), 1);
        $this->insertUnit(self::UNIT_DECAMETER, _('dm,'.self::UNIT_DECAMETER), 10);
        $this->insertUnit(self::UNIT_HECTOMETER, _('hmM,'.self::UNIT_HECTOMETER), 100);
        $this->insertUnit(self::UNIT_KILOMETER, _('km,'.self::UNIT_KILOMETER), 1000);
        $this->insertUnit(self::UNIT_MEGAMETER, _('MM,'.self::UNIT_MEGAMETER), 1000000);
    }
    
    public function convert($qty, $fromUnit, $toUnit)
    {
        $fromUnitNdx = $this->findUnitValue($fromUnit);
        $toUnitNdx = $this->findUnitValue($toUnit);
        $result = ($qty * $fromUnitNdx) / $toUnitNdx;
        return $result;
    }
}
