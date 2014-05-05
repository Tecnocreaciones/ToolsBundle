<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle;

/**
 * Description of ToolsEvents
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
final class ToolsEvents
{
     /**
     * The PRE_PURGER event occurs when a ORMPurger was start a purgue.
     *
     * @var string
     */
    const PRE_PURGER = 'doctrine.purger.pre';
}
