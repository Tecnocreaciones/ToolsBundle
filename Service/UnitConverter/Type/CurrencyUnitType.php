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
 * Moneda
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class CurrencyUnitType extends UnitType
{
    const UNIT_BOLIVAR = 'bs';
    const UNIT_M_BOLIVAR = 'mbs';
    const UNIT_MM_BOLIVAR = 'mmbs';
    const UNIT_DOLLAR = 'usd';
    const UNIT_M_DOLLAR = 'musd';
    const UNIT_MM_DOLLAR = 'mmusd';
    
    public function getDescription()
    {
        return 'Moneda';
    }
    
    public function init() 
    {
        $this->insertUnit(self::UNIT_BOLIVAR, 'bolivar', 6.30);
        $this->insertUnit(self::UNIT_M_BOLIVAR, 'mbolivar', 6.30);
        $this->insertUnit(self::UNIT_MM_BOLIVAR, 'mmbolivar', 6.30);
        $this->insertUnit(self::UNIT_DOLLAR, 'dollar', 6.30);
        $this->insertUnit(self::UNIT_M_DOLLAR, 'mdollar', 6.30);
        $this->insertUnit(self::UNIT_MM_DOLLAR, 'mdollar', 6.30);
    }
    
    public static function getType() 
    {
        return 'currency';
    }
}
