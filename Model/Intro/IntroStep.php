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
 * Pasos de IntroJs
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class IntroStep implements IntroStepInterface 
{
    protected $intro;
    
    /**
     * Elemento donde se va a atachar el intro
     * @var string
     * @ORM\Column(name="element",type="string",nullable=true)
     */
    protected $element;
    
    /**
     * Texto a mostrar en el intro (intro)
     * @var string
     * @ORM\Column(name="content",type="text")
     */
    protected $content;
    
    /**
     * Posicion del intro
     * @var string
     * @ORM\Column(name="position",type="string",length=10)
     */
    protected $position;
    
    /**
     * Orden del paso
     * @var string
     * @ORM\Column(name="order_step",type="integer")
     */
    protected $orderStep;
    
    /**
     * Habilitado
     * @var boolean
     * @ORM\Column(name="enabled",type="boolean")
     */
    protected $enabled = true;
    
    /**
     * Set intro
     *
     * @param IntroInterface $intro
     * @return IntroStep
     */
    public function setIntro(IntroInterface $intro = null)
    {
        $this->intro = $intro;

        return $this;
    }

    /**
     * Get intro
     *
     * @return IntroInterface
     */
    public function getIntro()
    {
        return $this->intro;
    }
    
    public function isEnabled(){
        return $this->enabled;
    }
    
    
    /**
     * Set element
     *
     * @param integer $element
     * @return IntroStep
     */
    public function setElement($element)
    {
        $this->element = $element;

        return $this;
    }

    /**
     * Get element
     *
     * @return integer 
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return IntroStep
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set position
     *
     * @param string $position
     * @return IntroStep
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set order Step
     *
     * @param string $orderStep
     * @return IntroStep
     */
    public function setOrderStep($orderStep)
    {
        $this->orderStep = $orderStep;

        return $this;
    }

    /**
     * Get orderStep
     *
     * @return string 
     */
    public function getOrderStep()
    {
        return $this->orderStep;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return IntroStep
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
    
    public static function getPositions(){
        return [
            self::POSITION_LEFT => 'intro_step.position.left',
            self::POSITION_RIGHT => 'intro_step.position.right',
            self::POSITION_TOP => 'intro_step.position.top',
            self::POSITION_BOTTOM => 'intro_step.position.bottom',
        ];
    }
    
    public function __toString() 
    {
        $_toString = '-';
        if($this->getIntro() !== null){
            $_toString = substr($this->content, 0,20);
        }
        
        return $_toString;
    }
}
