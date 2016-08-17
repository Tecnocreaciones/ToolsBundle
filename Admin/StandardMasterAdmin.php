<?php

/*
 * This file is part of the Witty Growth C.A. - J406095737 package.
 * 
 * (c) www.mpandco.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Modelo estandar de maestros que solo contiene la descripcion
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class StandardMasterAdmin extends MasterAdmin
{
    protected function configureShowFields(ShowMapper $show) 
    {
        $show
            ->add("id")
            ->add("description")
            ;
        parent::configureShowFields($show);
    }
    
    protected function configureListFields(ListMapper $list) {
        $list
            ->addIdentifier("description")
            ;
        parent::configureListFields($list);
    }
    
    protected function configureFormFields(FormMapper $form) {
        $form
            ->add("description")
            ;
        parent::configureFormFields($form);
    }
    
    protected function configureDatagridFilters(DatagridMapper $filter) {
        $filter
            ->add("description")
            ;
        parent::configureDatagridFilters($filter);
    }
}
