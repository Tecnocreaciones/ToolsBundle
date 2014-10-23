<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Configuration;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Manejador de configuracion
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
abstract class ConfigurationManager implements ContainerAwareInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    
    /**
     *
     * @var \Tecnocreaciones\Bundle\ToolsBundle\Service\ConfigurationService
     */
    protected $configurationManager;
    
    /**
     * Guarda o actualiza la configuracion en la base de datos y regenera la cache
     * 
     * @param type $key
     * @param type $value
     * @param type $description
     * @return \Tecnocreaciones\Bundle\ToolsBundle\Model\Configuration\ConfigurationManager
     */
    protected function set($key,$value = null,$description = null,\Tecnocreaciones\Bundle\ToolsBundle\Entity\Configuration\BaseGroup $group = null)
    {
        $this->getConfigurationManager()->set($key, $value, $description,$group);
        
        return $this;
    }
    
    /**
     * Obtiene el valor del indice
     * 
     * @param type $key
     * @param type $default
     * @return type
     */
    protected function get($key,$default = null)
    {
        return $this->getConfigurationManager()->get($key, $default);
    }
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
    
    /**
     * Guarda los cambios en la base de datos y regenera la cache
     */
    public function flush()
    {
        $this->getConfigurationManager()->flush();
    }
    
    /**
     * Retorna el servicio que maneja la configuracion del sistema
     * @return \Tecnocreaciones\Bundle\ToolsBundle\Service\ConfigurationService
     */
    protected function getConfigurationManager() {
        return $this->container->get('tecnocreaciones_tools.configuration_service');
    }
    
    /**
     * Shortcut to return the Doctrine Registry service.
     *
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     *
     * @throws \LogicException If DoctrineBundle is not available
     */
    public function getDoctrine()
    {
        if (!$this->container->has('doctrine')) {
            throw new \LogicException('The DoctrineBundle is not registered in your application.');
        }

        return $this->container->get('doctrine');
    }
}
