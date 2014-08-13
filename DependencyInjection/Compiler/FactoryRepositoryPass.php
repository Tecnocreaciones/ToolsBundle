<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Agrega repositorios como servicios a las clases
 * @example <service id="repository.plant" class="Coramer\Sigtec\CompanyBundle\Repository\PlantRepository">
                <call method="setContainer">
                    <argument type="service" id="service_container" />
                </call>
                <tag name="app.repository" class="Coramer\Sigtec\CompanyBundle\Entity\Plant" />
            </service>
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class FactoryRepositoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container) {
        $factory = $container->findDefinition('tecnocreaciones.doctrine.repository.factory');
 
        $repositories = [];
        foreach ($container->findTaggedServiceIds('app.repository') as $id => $params) {
            foreach ($params as $param) {
                $class = $param['class'];
                $repositories[$class] = $id;
                $repository = $container->findDefinition($id);
                $repository->addArgument(new Reference('doctrine.orm.default_entity_manager'));
 
                $definition = new Definition;
                $definition->setClass('Doctrine\ORM\Mapping\ClassMetadata');
                $definition->setFactoryService('doctrine.orm.default_entity_manager');
                $definition->setFactoryMethod('getClassMetadata');
                $definition->setArguments([$class]);
                
                $reflectionClass = new \ReflectionClass($repository->getClass());
                if($reflectionClass->isSubclassOf('Symfony\Component\DependencyInjection\ContainerAwareInterface')){
                    $repository->addMethodCall('setContainer',array(new Reference('service_container')));
                }
                $repository->addArgument($definition);
            }
        }
        $factory->replaceArgument(0, $repositories);
 
        $container->findDefinition('doctrine.orm.default_configuration')->addMethodCall('setRepositoryFactory', [$factory]);
    }
}
