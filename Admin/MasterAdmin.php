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

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Base de maestros
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class MasterAdmin extends Admin
{
    private $reflectionClass;
    
    protected function configureShowFields(ShowMapper $show) 
    {
        $show
            ->add('createdAt')
            ->add('updatedAt')
            ->add('enabled')
            ;
    }
    
    protected function configureListFields(ListMapper $list) 
    {
        $list
//            ->add('createdAt')
            ->add('enabled',null,array('editable' => true))
            ->add('_action', 'show', array(
                'template' => 'TecnocreacionesToolsBundle:SonataAdmin/CRUD:list__action.html.twig',
                'actions' => array(
                    'show' => array(
                        'template' => 'TecnocreacionesToolsBundle:SonataAdmin/CRUD:list__action_show.html.twig'
                    ),
                    'edit' => array(
                        'template' => 'TecnocreacionesToolsBundle:SonataAdmin/CRUD:list__action_edit.html.twig'
                    ),
                    'history' => array(
                        'template' => 'TecnocreacionesToolsBundle:SonataAdmin/CRUD:list__action_history.html.twig'
                    ),
                    'delete' => array(
                        'template' => 'TecnocreacionesToolsBundle:SonataAdmin/CRUD:list__action_delete.html.twig'
                    ),
//                    'move' => array(
//                        'template' => 'PicossSonataExtraAdminBundle:CRUD:list__action_sort.html.twig',
//                        'hide_label' => false, // Hide button text, default to true
//                    )
                )
            ))
            ;
    }
    
    protected function configureFormFields(FormMapper $form) 
    {
        $form
            ->add('enabled',null,array(
                "required" => false,
            ))
            ;
    }
    
    protected function configureDatagridFilters(DatagridMapper $filter) 
    {
        $filter
            ->add('enabled')
            ;
    }
    
    protected function getChoiceEmptyValue()
    {
        return 'choice.empty_value';
    }
    
    protected function hasProperty($property)
    {
        if(!$this->reflectionClass){
            $this->reflectionClass = new \ReflectionClass($this->getClass());
            
        }
        return $this->reflectionClass->hasProperty($property);
    }
}
