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
 * Definicion de Intro
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
interface IntroInterface {
    
    public function addStep(IntroStepInterface $step);
    
    public function getSteps();
    
    public function isEnabled();
    
    function getName();
    function setName($name);
    
    function getArea();
    function setArea($area);
    
    function getMaxShowLimit();
    function getMaxCancelLimit();
}
