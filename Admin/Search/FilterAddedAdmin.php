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
 * Aministrador de filtro añadido
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class FilterAddedAdmin extends MasterAdmin
{
    protected function configureShowFields(ShowMapper $show) {
        $show
            ->add("id")
            ->add("label")
            ->add("modelName")
            ->add("orderFilter")
            ->add("filter")
            ->add("filterBlock")
            ->add("filterGroup")
            ;
        parent::configureShowFields($show);
    }
    
    protected function configureFormFields(FormMapper $form) {
        $form
            ->add("label")
            ->add("modelName")
            ->add("orderFilter")
            ->add("filter")
            ->add("filterBlock")
            ->add("filterGroup")
            ;
        parent::configureFormFields($form);
    }
    
    protected function configureDatagridFilters(DatagridMapper $filter) {
        $filter
            ->add("ref")
            ->add("label")
            ->add("modelName")
            ->add("orderFilter")
            ->add("filter")
            ->add("filterBlock")
            ->add("filterGroup")
            ;
        parent::configureDatagridFilters($filter);
    }
    
    protected function configureListFields(ListMapper $list) {
        $list
            ->addIdentifier("ref")
            ->add("filter")
            ->add("label")
            ->add("modelName")
            ->add("orderFilter")
            ->add("filterBlock")
            ->add("filterGroup")
            ;
        parent::configureListFields($list);
    }
    
    public function createQuery($context = 'list') {
        $query = parent::createQuery($context);
        $query
                ->addSelect("o_f")
                ->addSelect("o_fb")
                ->addSelect("o_fg")
                ->innerJoin("o.filter","o_f")
                ->innerJoin("o.filterBlock","o_fb")
                ->leftJoin("o.filterGroup","o_fg")
                ;
        return $query;
    }
}
