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

use Symfony\Component\Security\Core\User\UserInterface;

/**
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
interface IntroLogInterface 
{
    /**
     * Log que se guarda cuando se finaliza de ver el log.
     */
    const TYPE_LOG_FINISH = 100;
    /**
     * Log que se genera cuando se cancela un log
     */
    const TYPE_LOG_CANCEL = 200;
    
    public function setIntro(IntroInterface $intro);
    /**
     * @return IntroInterface Description
     */
    public function getIntro();
    
    public function setUser(UserInterface $user);
    
    public function refeshShow();
    
    public function refreshCancel();
    
    public function isRenderable();
}
