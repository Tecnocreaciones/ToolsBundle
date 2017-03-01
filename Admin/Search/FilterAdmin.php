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
 * Admin de filtro
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class FilterAdmin extends MasterAdmin
{
    protected function configureShowFields(ShowMapper $show) {
        $show
            ->add("id")
            ->add("filterGroup")
            ->add("typeFilter")
            ->add("label")
            ->add("modelName")
            ->add("rol")
//            ->add("parameters")
            ;
        parent::configureShowFields($show);
    }
    
    protected function configureFormFields(FormMapper $form) {
         $form
            ->add("filterGroup")
            ->add("typeFilter")
            ->add("label")
            ->add("modelName")
            ->add("rol")
//            ->add("parameters")
            ;
        parent::configureFormFields($form);
    }
    
    protected function configureDatagridFilters(DatagridMapper $filter) {
        $filter
            ->add("filterGroup")
            ->add("typeFilter", null,[])
            ->add("label")
            ->add("modelName");
        parent::configureDatagridFilters($filter);
    }
    
    protected function configureListFields(ListMapper $list) {
        if($this->hasProperty("ref")){
            $list->addIdentifier("ref");
            $list
            ->add("label");
        }else{
            $list
            ->addIdentifier("label");
        }
            $list
            ->add("filterGroup")
            ->add("typeFilter")
            ->add("modelName");
        parent::configureListFields($list);
    }
}
