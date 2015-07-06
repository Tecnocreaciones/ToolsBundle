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

use Doctrine\ORM\Mapping as ORM;

/**
 * Modelo de log para cada vista de instro
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 * @ORM\MappedSuperclass()
 */
abstract class IntroLog implements IntroLogInterface
{
    /**
     * Veces visto
     * @var integer
     * @ORM\Column(name="count_show",type="integer")
     */
    protected $countShow = 0;
    
    /**
     * Veces cancelado
     * @var integer
     * @ORM\Column(name="count_cancel",type="integer")
     */
    protected $countCancel = 0;
    
    public function autoLog($typeLog)
    {
        $method = $this->resolveLogRefresh($typeLog);
        $this->$method();
    }

    public function refeshShow()
    {
        $this->countShow++;
    }
    public function refreshCancel()
    {
        $this->countCancel++;
    }
    
    public function resolveLogRefresh($typeLog) {
        $logMethodMap = self::getLogMethodMap();
        if(!isset($logMethodMap[$typeLog])){
            throw new \RuntimeException(sprintf("The type log (%s) is not valid, valid are %s",$typeLog,  implode(',', $logMethodMap)));
        }
        return $logMethodMap[$typeLog];
    }
    
    /**
     * Verifica que se deba renderizar el intro de acuerdo al log
     * @return boolean
     */
    public function isRenderable()
    {
        $intro = $this->getIntro();
        $result = true;
        if($this->countCancel >= $intro->getMaxCancelLimit()){
            $result = false;
        }
        if($this->countShow >= $intro->getMaxShowLimit()){
            $result = false;
        }
        return $result;
    }
    
    public static function getLogMethodMap()
    {
        return array(
            self::TYPE_LOG_FINISH => 'refeshShow',
            self::TYPE_LOG_CANCEL => 'refreshCancel',
        );
    }
}
