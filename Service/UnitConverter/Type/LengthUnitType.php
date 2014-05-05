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
    const UNIT_INCH = 'inch';
    const UNIT_MICROMETRO = 'micrometro';
    const UNIT_MILLIMETER = 'millimeter';
    const UNIT_CENTIMETER = 'centimeter';
    const UNIT_DECIMETER = 'decimeter';
    const UNIT_METER = 'meter';
    const UNIT_DECAMETER = 'decameter';
    const UNIT_HECTOMETER = 'hectometer';
    const UNIT_KILOMETER = 'kilometer';
    const UNIT_MEGAMETER = 'megameter';
    
    public function getDescription() {
        return 'Units long';
    }

    public static function getType() {
        return 'length';
    }

    public function init() {
        $this->insertUnit(self::UNIT_INCH, _('inch,in'), 0.0254);
        $this->insertUnit(self::UNIT_MICROMETRO, _('micrometro,µm'), 0.000001);
        $this->insertUnit(self::UNIT_MILLIMETER, _('milimeter,mm'), 0.001);
        $this->insertUnit(self::UNIT_CENTIMETER, _('centimeter,cm'), 0.01);
        $this->insertUnit(self::UNIT_DECIMETER, _('decimeter,dm'), 0.1);
        $this->insertUnit(self::UNIT_METER, _('meter,m'), 1);
        $this->insertUnit(self::UNIT_DECAMETER, _('decameter,dm'), 10);
        $this->insertUnit(self::UNIT_HECTOMETER, _('hectometer,hmM'), 100);
        $this->insertUnit(self::UNIT_KILOMETER, _('kilometer,km'), 1000);
        $this->insertUnit(self::UNIT_MEGAMETER, _('megameter,MM'), 1000000);
    }
    
    public function convert($qty, $fromUnit, $toUnit)
    {
        $fromUnitNdx = $this->findUnitValue($fromUnit);
        $toUnitNdx = $this->findUnitValue($toUnit);
        $result = ($qty * $fromUnitNdx) / $toUnitNdx;
        return $result;
    }
}
