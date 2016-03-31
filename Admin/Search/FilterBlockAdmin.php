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
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Admin de bloque de filtros
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class FilterBlockAdmin extends MasterAdmin
{
    protected function configureShowFields(ShowMapper $show) {
        $show
            ->add("id")
            ->add("area")
            ->add("orderBlock")
            ->add("filters")
            ;
        parent::configureShowFields($show);
    }
    
    protected function configureFormFields(FormMapper $form) {
        $form
            ->add("area")
            ->add("orderBlock")
//            ->add("parameters")
            ->add("filterAddeds")
            ;
        parent::configureFormFields($form);
    }
    
    protected function configureDatagridFilters(DatagridMapper $filter) {
        $filter
            ->add("area")
            ->add("orderBlock")
            ;
        parent::configureDatagridFilters($filter);
    }
    
    protected function configureListFields(ListMapper $list) {
        $list
            ->add("area")
            ->add("orderBlock");
        parent::configureListFields($list);
    }
}
