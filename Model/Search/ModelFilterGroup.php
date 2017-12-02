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

use Doctrine\ORM\Mapping as ORM;

/**
 * Modelo de grupo de filtro
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 * @ORM\MappedSuperclass()
 */
abstract class ModelFilterGroup extends \Tecnocreaciones\Bundle\ToolsBundle\Model\Base\BaseMaster
{
    /**
     * Orden del grupo dentro del area
     * @var integer
     * @ORM\Column(name="order_group",type="integer")
     */
    protected $orderGroup = 0;
    
    public function getOrderGroup() {
        return $this->orderGroup;
    }

    public function setOrderGroup($orderGroup) {
        $this->orderGroup = $orderGroup;
        return $this;
    }
}