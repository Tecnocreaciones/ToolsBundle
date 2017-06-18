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

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Base de maestros
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class MasterAdmin extends AbstractAdmin
{
    const FORMAT_DATE_TIME = 'Y-m-d h:i:s a';
    private $reflectionClass;
    
    protected function configureShowFields(ShowMapper $show) 
    {
        $fieldDate = ['type' => 'datetime','parameters' => ['format' => self::FORMAT_DATE_TIME]];
        $fields = array(
            'description' => ['type' => null],
            'createdAt' => $fieldDate,
            'updatedAt' => $fieldDate,
            'deletedAt' => $fieldDate,
            'enabled');
        foreach ($fields as $key => $value) {
            if($this->hasProperty($key) && !$show->has($key)){
                $type = $value['type'];
                $parameters = [];
                if(isset($value['parameters'])){
                    $parameters = $value['parameters'];
                }
                $show->add($key,$type,$parameters);
            }
        }
    }
    
    protected function configureListFields(ListMapper $list) 
    {
        if($this->hasProperty("enabled") && !$list->has("enabled")){
            
            $list->add('enabled',null,array('editable' => true));
        }
        $list
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
        if($this->hasProperty("enabled")){
        $form
            ->add('enabled',null,array(
                "required" => false,
            ))
            ;
        }
    }
    
    protected function configureDatagridFilters(DatagridMapper $filter) 
    {
        if($this->hasProperty("enabled")){
            $filter
                ->add('enabled')
                ;
        }
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
