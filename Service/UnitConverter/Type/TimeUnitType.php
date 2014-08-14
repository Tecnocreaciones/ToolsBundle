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
 * Unidades de tiempos
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class TimeUnitType extends UnitType
{
    const UNIT_SECONDS = 'segundos';
    const UNIT_MINUTES = 'minutos';
    const UNIT_HOURS = 'horas';
    const UNIT_DAYS = 'dias';
    const UNIT_WEEKS = 'semanas';
    const UNIT_MONTHS = 'mes';
    const UNIT_YEARS = 'años';


    public function getDescription() {
        return 'Tiempo';
    }

    public static function getType() {
        return 'time';
    }

    public function init() {
        $this->insertUnit(self::UNIT_SECONDS, _(self::UNIT_SECONDS.',second,sec,secs,s'), 0);
        $this->insertUnit(self::UNIT_MINUTES, _(self::UNIT_MINUTES.',minute,min,mins,m'), 60);
        $this->insertUnit(self::UNIT_HOURS, _(self::UNIT_HOURS.',hour,hr,hrs,h'), 60);
        $this->insertUnit(self::UNIT_DAYS, _(self::UNIT_DAYS.',day,d'), 24);
        $this->insertUnit(self::UNIT_WEEKS, _(self::UNIT_WEEKS.',week,w'), 7);
        $this->insertUnit(self::UNIT_MONTHS, _(self::UNIT_MONTHS.',month,mth,M'), 30);
        $this->insertUnit(self::UNIT_YEARS, _(self::UNIT_YEARS.',year,yrs,y'), 365);
    }
}
