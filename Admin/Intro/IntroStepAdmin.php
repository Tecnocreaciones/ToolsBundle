<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Admin\Intro;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Administrador de intro paso
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class IntroStepAdmin extends Admin
{
    protected function configureFormFields(FormMapper $form) 
    {
        $positions = \Tecnocreaciones\Bundle\ToolsBundle\Model\Intro\IntroStep::getPositions();
        $form
            ->add('intro')
            ->add('element')
            ->add('content')
            ->add('position','choice',array(
                'choices' => $positions,
                'translation_domain' => 'admin'
            ))
            ->add('orderStep')
            ->add('enabled',null,array(
                'required' => false,
            ))
            ;
    }
    
    protected function configureDatagridFilters(DatagridMapper $filter) 
    {
        $positions = \Tecnocreaciones\Bundle\ToolsBundle\Model\Intro\IntroStep::getPositions();
        $filter
            ->add('intro')
            ->add('element')
            ->add('content')
            ->add('position',null,array(),'choice',array(
                'choices' => $positions,
                'translation_domain' => 'admin'
            ))
            ->add('orderStep')
            ->add('enabled')
            ;
    }
    protected function configureListFields(ListMapper $list) 
    {
        $list
            ->addIdentifier('id')
            ->add('intro')
            ->add('element')
            ->add('content')
            ->add('orderStep')
            ->add('enabled',null,array('editable' => true))
            ;
    }
    
    protected function configureShowFields(\Sonata\AdminBundle\Show\ShowMapper $show) 
    {
        $positions = \Tecnocreaciones\Bundle\ToolsBundle\Model\Intro\IntroStep::getPositions();
        $show
            ->add('intro')
            ->add('element')
            ->add('content')
            ->add('position','choice',array(
                'choices' => $positions,
                'translation_domain' => 'admin'
            ))
            ->add('orderStep')
            ->add('enabled')
            ;
    }
    
}
