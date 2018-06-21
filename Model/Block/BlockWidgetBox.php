<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Block;

use Sonata\BlockBundle\Model\Block;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bloque en grid (Widget)
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 * @ORM\MappedSuperclass()
 */
class BlockWidgetBox extends Block
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     * @ORM\Column(name="name",type="string",length=100)
     */
    protected $name;

    /**
     * @var array
     * @ORM\Column(name="settings",type="json_array")
     */
    protected $settings;

    /**
     * @var boolean
     * @ORM\Column(name="enabled",type="boolean")
     */
    protected $enabled = true;

    /**
     * @var integer
     * @ORM\Column(name="position",type="integer")
     */
    protected $position = 1;

    /**
     * @var \DateTime
     * @ORM\Column(name="createdAt",type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="updatedAt",type="datetime",nullable=true)
     */
    protected $updatedAt;

    /**
     * @var string
     * @ORM\Column(name="type",type="string",length=100)
     */
    protected $type;

    /**
     * @var string
     * @ORM\Column(name="event",type="string",length=140)
     */
    protected $event;
    
    /**
     * @var array
     */
    protected $children;
    
    /**
     * 
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = new \DateTime();
        
        return $this;
    }
    
    /**
     * 
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = new \DateTime();
        
        return $this;
    }
    
    function getEvent() 
    {
        return $this->event;
    }

    function setEvent($event) 
    {
        $this->event = $event;
    }
    
    function getChildren() 
    {
        if(!$this->children){
            $this->children = array();
        }
        return $this->children;
    }

}
