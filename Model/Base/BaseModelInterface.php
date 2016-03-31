<?php

/*
 * This file is part of the TecnoReady Solutions C.A. package.
 * 
 * (c) www.tecnoready.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Base;

/**
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
interface BaseModelInterface 
{
    function getCreatedAt();

    function getUpdatedAt();

    function getDeletedAt();
    
    public function getCreatedFromIp();

    public function getUpdatedFromIp();
    
    public function getId();

    public function getDeletedFromIp();

    public function setDeletedFromIp($deletedFromIp);
}
