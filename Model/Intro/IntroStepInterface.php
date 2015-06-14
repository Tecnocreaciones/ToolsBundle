<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Intro;

/**
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
interface IntroStepInterface 
{
    const POSITION_LEFT = 'left';
    const POSITION_RIGHT = 'right';
    const POSITION_TOP = 'top';
    const POSITION_BOTTOM = 'bottom';

    public function setIntro(IntroInterface $intro = null);
    
    public function getIntro();
    
    public function isEnabled();
    
    public static function getPositions();
}
