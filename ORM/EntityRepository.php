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
    /**
     * @var SecurityContext
     */
    protected $securityContext;
    
    protected $container;
    public function getPaginator(QueryBuilder $queryBuilder)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $columns = $request->get("columns");
        if($this->getFormatPaginator() == Paginator::FORMAT_ARRAY_DATA_TABLES && $columns !== null){
            $orx = $queryBuilder->expr()->andX();
            foreach ($columns as $column) {
                $data = $column['name'];
                $value = $column['search']['value'];
                if($data != "" && $value != ""){
                    $field = sprintf("%s.%s",  $this->getAlias(),$data);
                    $orx->add($queryBuilder->expr()->like($field, $queryBuilder->expr()->literal("%".$value."%")));
                }

            }
            if($orx->count() > 0){
                $queryBuilder->andWhere($orx);
            }
        }
        
        $pagerfanta = new Paginator(new DoctrineORMAdapter($queryBuilder));
        $pagerfanta->setDefaultFormat($this->getFormatPaginator());
        $pagerfanta->setContainer($this->container);
        $pagerfanta->setRequest($request);
        return $pagerfanta;
    }
    
    public function findAllPaginated()
    {
        return $this->getPaginator($this->getQueryBuilder())
        ;
    }
    
    public function setSecurityContext(SecurityContext $securityContext) {
        $this->securityContext = $securityContext;
    }
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
    
    /**
     * Get a user from the Security Context
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see Symfony\Component\Security\Core\Authentication\Token\TokenInterface::getUser()
     */
    public function getUser()
    {
        if (!$this->container->has('security.context')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->container->get('security.context')->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }
    
    /**
     * Retorna un paginador con valores escalares (Sin hidratar)
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return \Tecnocreaciones\Bundle\ResourceBundle\Model\Paginator\Paginator
     */
    public function getScalarPaginator(\Doctrine\ORM\QueryBuilder $queryBuilder)
    {
        $pagerfanta = new Paginator(new ArrayAdapter($queryBuilder->getQuery()->getScalarResult()));
        $pagerfanta->setDefaultFormat($this->getFormatPaginator());
        $pagerfanta->setContainer($this->container);
        return $pagerfanta;
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
    
    function getAlias()
    {
        return "e";
    }
}
