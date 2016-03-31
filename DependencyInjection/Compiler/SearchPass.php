<?php

/*
 * This file is part of the Witty Growth C.A. - J406095737 package.
 * 
 * (c) www.mpandco.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Carga la configuracion de los filtros de busqueda
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class SearchPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container) 
    {
        if($container->getParameter('tecnocreaciones_tools.service.search.enable') === false){
            return;
        }
        $config = $container->getParameter("tecnocreaciones_tools.search.config");
        $definition = $container->getDefinition('tecnocreaciones_tools.search');
        
        $definition->addMethodCall("setStandardFilters",[$config["standard_filters"]]);
    }
}
