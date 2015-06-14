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
 * Administrador de intro
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class IntroAdmin extends Admin 
{
    /**
     *
     * @var \Tecnocreaciones\Bundle\ToolsBundle\Service\Intro\IntroService
     */
    private $introService;
    
    protected function configureFormFields(FormMapper $formMapper)
    {
        $areas = $this->introService->getAreas();
        $formMapper
            ->add('name')
            ->add('area','choice',array(
                'choices' => $areas,
                'translation_domain' => 'admin'
            ))
            ->add('maxShowLimit')
            ->add('maxCancelLimit')
            ->add('autoStart',null,array(
                'required' => false,
            ))
            ->add('enabled',null,array(
                'required' => false,
            ))
        ;
    }
    
    protected function configureDatagridFilters(DatagridMapper $filter) {
        $areas = $this->introService->getAreas();
        $filter
            ->add('name')
            ->add('area',null,array(),'choice',array(
                'choices' => $areas,
                'translation_domain' => 'admin'
            ))
            ->add('autoStart')
            ->add('enabled')
        ;
    }
    
    protected function configureListFields(ListMapper $list) {
        $areas = $this->introService->getAreas();
        $list
            ->addIdentifier('name')
            ->add('area','choice',array(
                'choices' => $areas,
                'translation_domain' => 'admin'
            ))
            ->add('autoStart',null,array('editable' => true))
            ->add('enabled',null,array('editable' => true))
        ;
    }
    
    protected function configureShowFields(\Sonata\AdminBundle\Show\ShowMapper $show) 
    {
        $areas = $this->introService->getAreas();
        $show
            ->add('name')
            ->add('area','choice',array(
                'choices' => $areas,
                'translation_domain' => 'admin'
            ))
            ->add('maxShowLimit')
            ->add('maxCancelLimit')
            ->add('autoStart')
            ->add('enabled')
        ;
    }
    
    function setIntroService($introService) {
        $this->introService = $introService;
    }
}
