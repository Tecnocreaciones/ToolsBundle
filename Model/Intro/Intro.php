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
 * Ayudas con IntroJs
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 * @ORM\MappedSuperclass()
 */
abstract class Intro implements IntroInterface
{
    /**
     * Nombre del intro
     * @var string
     * @ORM\Column(name="name",type="string")
     */
    protected $name;
    
    /**
     * Opciones (skipLabel,nextLabel,prevLabel,doneLabel)
     * @var array
     * @ORM\Column(name="options",type="json_array")
     */
    protected $options;
    
    /**
     * Auto iniciar ayuda
     * @var boolean
     * @ORM\Column(name="auto_start",type="boolean")
     */
    protected $autoStart;
    
    /**
     * Area
     * @var integer
     * @ORM\Column(name="area",type="integer")
     */
    protected $area;
    
    /**
     * Limite maximo para mostrar intro al finalizar
     * @var integer
     * @ORM\Column(name="max_show_limit",type="integer")
     */
    protected $maxShowLimit = 3;
    
    /**
     * Limite maximo para cancelar intro antes de no mostrar mas
     * @var integer
     * @ORM\Column(name="max_cancel_limit",type="integer")
     */
    protected $maxCancelLimit = 3;
    
    /**
     *
     * @var type 
     */
    protected $steps;
    
    /**
     * Habilitado
     * @var boolean
     * @ORM\Column(name="enabled",type="boolean")
     */
    protected $enabled;
    
    public function __construct() {
        $this->options = array();
        $this->steps = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function isEnabled()
    {
        return $this->enabled;
    }
    
    function getSteps() {
        return $this->steps;
    }

    function addStep(\Tecnocreaciones\Bundle\ToolsBundle\Model\Intro\IntroStepInterface $step) {
        $this->steps[] = $step;
    }
    
    /**
     * Remove steps
     *
     * @param \Coramer\Sigtec\WebBundle\Entity\Core\IntroStep $steps
     */
    public function removeStep(\Coramer\Sigtec\WebBundle\Entity\Core\IntroStep $steps)
    {
        $this->steps->removeElement($steps);
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return Intro
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set options
     *
     * @param array $options
     * @return Intro
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options
     *
     * @return array 
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set autoStart
     *
     * @param boolean $autoStart
     * @return Intro
     */
    public function setAutoStart($autoStart)
    {
        $this->autoStart = $autoStart;

        return $this;
    }

    /**
     * Get autoStart
     *
     * @return boolean 
     */
    public function getAutoStart()
    {
        return $this->autoStart;
    }

    /**
     * Set area
     *
     * @param integer $area
     * @return Intro
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return integer 
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set maxShowLimit
     *
     * @param integer $maxShowLimit
     * @return Intro
     */
    public function setMaxShowLimit($maxShowLimit)
    {
        $this->maxShowLimit = $maxShowLimit;

        return $this;
    }

    /**
     * Get maxShowLimit
     *
     * @return integer 
     */
    public function getMaxShowLimit()
    {
        return $this->maxShowLimit;
    }

    /**
     * Set maxCancelLimit
     *
     * @param integer $maxCancelLimit
     * @return Intro
     */
    public function setMaxCancelLimit($maxCancelLimit)
    {
        $this->maxCancelLimit = $maxCancelLimit;

        return $this;
    }

    /**
     * Get maxCancelLimit
     *
     * @return integer 
     */
    public function getMaxCancelLimit()
    {
        return $this->maxCancelLimit;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Intro
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

        
    public function __toString() 
    {
        return $this->getName()?:'-';
    }
}
