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
 * Unidades de peso
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class WeightUnitType extends UnitType
{
    const UNIT_MILIGRAMO = 'miligramo';
    const UNIT_CENTIGRAMO = 'centigramo';
    const UNIT_DECIGRAMO = 'decigramo';
    const UNIT_GRAMO = 'gramo';
    const UNIT_DECAGRAMO = 'decagramo';
    const UNIT_HECTOGRAMO = 'hectogramo';
    const UNIT_KILOGRAMO = 'kilogramo';
    const UNIT_TONELADA = 'tonelada';
    
    public function getDescription() {
        return 'Weight units';
    }

    public function init() {
        $this->insertUnit(self::UNIT_MILIGRAMO, 'mg', 0);
        $this->insertUnit(self::UNIT_CENTIGRAMO, 'cg', 10);
        $this->insertUnit(self::UNIT_DECIGRAMO, 'dg', 10);
        $this->insertUnit(self::UNIT_GRAMO, 'g', 10);
        $this->insertUnit(self::UNIT_DECAGRAMO, 'dag', 10);
        $this->insertUnit(self::UNIT_HECTOGRAMO, 'hg', 10);
        $this->insertUnit(self::UNIT_KILOGRAMO, 'kg', 10);
        $this->insertUnit(self::UNIT_TONELADA, 't', 1000);
    }

    public static function getType() {
        return 'weight';
    }
}
