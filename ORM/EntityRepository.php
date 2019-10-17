<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\ORM;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Tecnocreaciones\Bundle\ToolsBundle\Model\Paginator\Paginator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Pagerfanta\Adapter\ArrayAdapter;
use Doctrine\ORM\EntityRepository as Base;

/**
 * Doctrine ORM driver entity repository.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class EntityRepository extends Base implements ContainerAwareInterface
{
    use TraitEntityRepository;
    
    /**
     * @var SecurityContext
     */
    protected $securityContext;
    
    
    public function setSecurityContext(SecurityContext $securityContext) {
        $this->securityContext = $securityContext;
    }
    
    public function getSecurityContext()
    {
        if (!$this->container->has('security.context')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        return $this->container->get('security.context');
    }
    
    public function getFormatPaginator()
    {
        return Paginator::FORMAT_ARRAY_DEFAULT;
    }
}
