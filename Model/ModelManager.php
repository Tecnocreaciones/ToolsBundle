<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Sonata\DoctrineORMAdminBundle\Admin\FieldDescription;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;

use Doctrine\ORM\QueryBuilder;
use Doctrine\DBAL\DBALException;

use Symfony\Component\Form\Exception\PropertyAccessDeniedException;
use Symfony\Bridge\Doctrine\RegistryInterface;

use Exporter\Source\DoctrineORMQuerySourceIterator;

class ModelManager
{
    protected $registry;

    protected $cache = array();

    const ID_SEPARATOR = '~';

    /**
     * @param \Symfony\Bridge\Doctrine\RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getMetadata($class)
    {
        return $this->getEntityManager($class)->getMetadataFactory()->getMetadataFor($class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getIdentifierValues($entity)
    {
        // Fix code has an impact on performance, so disable it ...
        //$entityManager = $this->getEntityManager($entity);
        //if (!$entityManager->getUnitOfWork()->isInIdentityMap($entity)) {
        //    throw new \RuntimeException('Entities passed to the choice field must be managed');
        //}


        $class = $this->getMetadata(ClassUtils::getClass($entity));

        $identifiers = array();

        foreach ($class->getIdentifierValues($entity) as $value) {
            if (!is_object($value)) {
                $identifiers[] = $value;
                continue;
            }

            $class = $this->getMetadata(ClassUtils::getClass($value));

            foreach ($class->getIdentifierValues($value) as $value) {
                $identifiers[] = $value;
            }
        }

        return $identifiers;
    }
    
    /**
     * @param string $class
     *
     * @return EntityManager
     */
    public function getEntityManager($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (!isset($this->cache[$class])) {
            $em = $this->registry->getManagerForClass($class);

            if (!$em) {
                throw new \RuntimeException(sprintf('No entity manager defined for class %s', $class));
            }

            $this->cache[$class] = $em;
        }

        return $this->cache[$class];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getModelCollectionInstance($class)
    {
        return new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * {@inheritdoc}
     */
    public function find($class, $id)
    {
        if (!isset($id)) {
            return null;
        }

        $values = array_combine($this->getIdentifierFieldNames($class), explode(self::ID_SEPARATOR, $id));

        return $this->getEntityManager($class)->getRepository($class)->find($values);
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function getIdentifierFieldNames($class)
    {
        return $this->getMetadata($class)->getIdentifierFieldNames();
    }
}
