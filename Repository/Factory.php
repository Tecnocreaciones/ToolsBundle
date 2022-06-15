<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Repository;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\RepositoryFactory;
use ReflectionClass;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Contructor de repositorios, permite usar servicios como repositorios de las entidades
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class Factory implements RepositoryFactory
{
    private $ids;
    private $container;
    private $default;
    private $managerRegistry;

    /**
     * The list of EntityRepository instances.
     *
     * @var ObjectRepository[]
     */
    private $repositoryList = [];

    public function __construct(array $ids, ContainerInterface $container, RepositoryFactory $default,ManagerRegistry $managerRegistry)
    {
        $this->ids = $ids;
        $this->container = $container;
        $this->default = $default;
        $this->managerRegistry = $managerRegistry;
    }
 
    public function getRepository(EntityManagerInterface $entityManager, $entityName)
    {
        if(preg_match('/'. \Doctrine\Persistence\Proxy::MARKER .'/',$entityName)){
            $entityName = \Doctrine\Common\Util\ClassUtils::getRealClass($entityName);
        }
        
        $repositoryHash = $entityManager->getClassMetadata($entityName)->getName() . spl_object_hash($entityManager);

        if (isset($this->repositoryList[$repositoryHash])) {
            return $this->repositoryList[$repositoryHash];
        }

        return $this->repositoryList[$repositoryHash] = $this->createRepository($entityManager, $entityName);
    }
    
    /**
     * Create a new repository instance for an entity class.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager The EntityManager instance.
     * @param string                               $entityName    The name of the entity.
     *
     * @return ObjectRepository
     */
    private function createRepository(EntityManagerInterface $entityManager, $entityName)
    {
        $metadata            = $entityManager->getClassMetadata($entityName);
        $repositoryClassName = $metadata->customRepositoryClassName
            ?: $entityManager->getConfiguration()->getDefaultRepositoryClassName();
        
        $reflection = new ReflectionClass($repositoryClassName);
        $class1  = "Doctrine\ORM\EntityRepository";
        $class2  = "Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository";
        
        if($reflection->getName() ==  $class2 || $reflection->isSubclassOf($class2)){ 
            $repository = new $repositoryClassName($this->managerRegistry, $metadata);
        }else if($reflection->getName() == $class1 || $reflection->isSubclassOf($class1)){
            $repository = new $repositoryClassName($entityManager, $metadata);
        }else{
            throw new RuntimeException(sprintf("No se pudo generar el repositorio para la entidad '%s'",$repositoryClassName));
        }
        if($repository instanceof ContainerAwareInterface || $reflection->hasMethod("setContainer")){
            $repository->setContainer($this->container);
        }
        return $repository;
    }
}
