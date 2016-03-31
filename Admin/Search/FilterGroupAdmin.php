<?php

/*
 * This file is part of the TecnoReady Solutions C.A. package.
 * 
 * (c) www.tecnoready.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Admin\Search;

use Tecnocreaciones\Bundle\ToolsBundle\Admin\MasterAdmin;

/**
 * Administrador de grupo de filtro
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class FilterGroupAdmin extends MasterAdmin
{
    protected function configureListFields(\Sonata\AdminBundle\Datagrid\ListMapper $list) {
        $list
            ->add("description")
            ;
        parent::configureListFields($list);
    }
}
