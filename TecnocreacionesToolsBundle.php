<?php

namespace Tecnocreaciones\Bundle\ToolsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TecnocreacionesToolsBundle extends Bundle
{
    public function build(\Symfony\Component\DependencyInjection\ContainerBuilder $container) 
    {
        parent::build($container);
        //Agrega repositorios como servicios e inyecta contenedor de dependencias
        $container->addCompilerPass(new DependencyInjection\Compiler\FactoryRepositoryPass());
        
        $container->addCompilerPass(new DependencyInjection\Compiler\UnitTypePass());
        $container->addCompilerPass(new DependencyInjection\Compiler\WidgetBoxPass());
    }
}
