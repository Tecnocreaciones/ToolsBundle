<?php

/*
 * This file is part of the Witty Growth C.A. - J406095737 package.
 * 
 * (c) www.mpandco.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Search;

/**
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
interface ModelFilterInterface {
    public static function getTypes();
    
    public static function getMacroTemplate();
}
