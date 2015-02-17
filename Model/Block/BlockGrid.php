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

/**
 * Description of BlockGrid
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class BlockGrid extends Block
{
    /**
     * Posicion en la Columna
     * @var type 
     */
    protected $positionY = 1;
    /**
     * Posicion en la fila
     * @var type 
     */
    protected $positionX = 1;
    
    /**
     * Tamaño en X
     * @var integer
     */
    protected $sizeX = 4;
    /**
     * Tamaño en Y
     * @var integer
     */
    protected $sizeY = 4;
            
    
}
