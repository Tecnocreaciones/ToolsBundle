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
 * Unidades de Textil hilos
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class TextileYarnsUnitType extends UnitType
{
    const UNIT_TEX = 'tex';
    
    const UNIT_DENIER = 'denier';
    
    const UNIT_DECITEX = 'decitex';
    
    public function getDescription() {
        return 'Textil hilos';
    }
    
    function init()
    {
        $this->insertUnit(self::UNIT_TEX, _('tex'), 0);
        $this->insertUnit(self::UNIT_DENIER, _('ns'), 0.11);
        $this->insertUnit(self::UNIT_DECITEX, _('dtex'), 0.9);
    }
    
    public static function getType()
    {
        return 'textile_yarns';
    }
}
