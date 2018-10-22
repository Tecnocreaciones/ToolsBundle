<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Busca los chain y modelos para agregarlos al exportador
 */
class ExporterCompilerPass implements CompilerPassInterface 
{
    public function process(ContainerBuilder $container) 
    {
        if ($container->getParameter('tecnocreaciones_tools.service.exporter.enable') === false) {
            return;
        }
        
        $exporter = $container->getDefinition("app.service.exporter");

        $chaines = $container->findTaggedServiceIds("exporter.chain");
        
        $models = $container->findTaggedServiceIds("exporter.chain.model");
        foreach ($models as $id => $model) {
            $idChain = $model[0]["chain"];
            if(!isset($chaines[$idChain])){
                throw new \InvalidArgumentException(sprintf("The exporter chain '%s' is not exists.",$idChain));
            }
            $chain = $container->getDefinition($idChain);
            $chain->addMethodCall("add",[$container->getDefinition($id)]);
        }
        foreach ($chaines as $id => $chain) {
            $exporter->addMethodCall("addChainModel",[$container->getDefinition($id)]);
        }
    }
}
