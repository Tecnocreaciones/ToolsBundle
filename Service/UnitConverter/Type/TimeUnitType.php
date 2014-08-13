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
    const UNIT_SECONDS = 'seconds';
    const UNIT_MINUTES = 'minutes';
    const UNIT_HOURS = 'hours';
    const UNIT_DAYS = 'days';
    const UNIT_WEEKS = 'weeks';
    const UNIT_MONTHS = 'months';
    const UNIT_YEARS = 'years';


    public function getDescription() {
        return 'Tiempo';
    }

    public static function getType() {
        return 'time';
    }

    public function init() {
        $this->insertUnit(self::UNIT_SECONDS, _('seconds,second,sec,secs,s'), 0);
        $this->insertUnit(self::UNIT_MINUTES, _('minutes,minute,min,mins,m'), 60);
        $this->insertUnit(self::UNIT_HOURS, _('hours,hour,hr,hrs,h'), 60);
        $this->insertUnit(self::UNIT_DAYS, _('days,day,d'), 24);
        $this->insertUnit(self::UNIT_WEEKS, _('weeks,week,w'), 7);
        $this->insertUnit(self::UNIT_MONTHS, _('months,month,mth,M'), 30);
        $this->insertUnit(self::UNIT_YEARS, _('years,year,yrs,y'), 365);
    }
}
