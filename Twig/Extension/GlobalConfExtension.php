<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Twig\Extension;

/**
 * Extension de twig que provee herramientas globales
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class GlobalConfExtension extends \Twig_Extension implements \Symfony\Component\DependencyInjection\ContainerAwareInterface
{
    private $container;
    
    public function getGlobals() {
        return array('appConfiguration' => $this->container->get('tecnocreaciones_tools.configuration_service'));
    }
    
    public function getName()
    {
        return 'tecnocreaciones_tools_global_conf_extension';
    }

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        $this->container = $container;
    }

}
