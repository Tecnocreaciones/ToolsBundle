<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\Exporter\Adapter;

/**
 * Adaptador de doctrine para el exportador
 */
class Doctrine2Adapter implements ExporterAdapterInterface 
{   
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    
    public function find($className,$id) {
        return $this->getDoctrine()->getManager()->find($className, $id);
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

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
