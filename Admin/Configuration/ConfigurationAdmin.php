<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Admin\Configuration;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Administracion del sistema
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class ConfigurationAdmin extends Admin implements \Symfony\Component\DependencyInjection\ContainerAwareInterface
{
    /**
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    
    /**
     *
     * @var \Tecnocreaciones\Bundle\ToolsBundle\Service\ConfigurationService
     */
    protected $configurationManager;
    
    /**
     * 
     * @return \Tecnocreaciones\Bundle\ToolsBundle\Service\ConfigurationService
     */
    public function getConfigurationManager() {
        return $this->container->get('tecnocreaciones_tools.configuration_service');
    }    
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
    
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('key',null,array(
                'disabled' => true,
            ))
            ->add('value')
            ->add('description', 'text',array(
                'disabled' => true,
            ))
            ->add('group',null,array(
                'disabled' => true,
            ))
            ->add('active')
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('key')
            ->add('value')
            ->add('group')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('description')
            ->add('value')
            ->add('group')
            ->add('active')
        ;
    }
    
    public function postUpdate($object) {
        $this->getConfigurationManager()->clearCache();
        
    }
    
    public function postPersist($object) {
        $this->getConfigurationManager()->clearCache();
    }
    
    public function postRemove($object) {
        $this->getConfigurationManager()->clearCache();
    }
}
