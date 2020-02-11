<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\ORM;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Tecnocreaciones\Bundle\ToolsBundle\Model\Paginator\Paginator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Pagerfanta\Adapter\ArrayAdapter;

/**
 * Trait para repositorios base
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
trait TraitEntityRepository
{
    protected $container;
    
    /**
     * 
     * @param QueryBuilder $queryBuilder
     * @return Paginator
     */
    public function getPaginator(QueryBuilder $queryBuilder)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        if($request){
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
        }
        
        $pagerfanta = new Paginator(new DoctrineORMAdapter($queryBuilder));
        $pagerfanta->setDefaultFormat($this->getFormatPaginator());
        $pagerfanta->setContainer($this->container);
        if($request){
            $pagerfanta->setRequest($request);
        }
        return $pagerfanta;
    }
    
    public function findAllPaginated()
    {
        return $this->getPaginator($this->getQueryBuilder())
        ;
    }
    
    /**
     * Anotacion de inyeccion para symfony 3.x
     * @required
     * @param \Tecnocreaciones\Bundle\ToolsBundle\ORM\ContainerInterface $container
     */
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
        $request = $this->container->get('request_stack')->getCurrentRequest();
        
        $pagerfanta = new Paginator(new ArrayAdapter($queryBuilder->getQuery()->getScalarResult()));
        $pagerfanta->setDefaultFormat($this->getFormatPaginator());
        $pagerfanta->setContainer($this->container);
        if($request){
            $pagerfanta->setRequest($request);
        }
        return $pagerfanta;
    }
    
    public function findForSearch(array $criteria = [], array $orderBy = null)
    {
        $criteria = $this->parseCriteria($criteria);
        
        $a = $this->getAlias();
        $qb = $this->getQueryBuilder();
        $qb
            ->select($a.".id id")
            ->addSelect($a.".description ".$a."_description")
            ;
        $sqb = $this->createSearchQueryBuilder($qb, $criteria);
        $sqb->addFieldDescription();
        $sqb->addQueryField("query",["description"]);
        
        $this->applyCriteria($qb, $criteria->toArray());
        $this->applySorting($qb, $orderBy);
        $qb->orderBy($a.".description","ASC");
        return $this->getScalarPaginator($qb);
    }
    /**
     * @param type $qb
     * @param type $criteria
     * @return \Tecnocreaciones\Bundle\ToolsBundle\ORM\Query\SearchQueryBuilder
     */
    protected function createSearchQueryBuilder($qb, $criteria,array $orderBy = []) 
    {
        return new Query\SearchQueryBuilder($qb, $criteria, $this->getAlias(),$orderBy);
    }
    
    /**
     * @param QueryBuilder $queryBuilder
     *
     * @param array $criteria
     */
    protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = null)
    {
        if (null === $criteria) {
            return;
        }

        foreach ($criteria as $property => $value) {
            if (null === $value) {
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->isNull($this->getPropertyName($property)));
            } elseif (!is_array($value)) {
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->eq($this->getPropertyName($property), ':' . $property))
                    ->setParameter($property, $value);
            } else {
                $queryBuilder->andWhere($queryBuilder->expr()->in($this->getPropertyName($property), $value));
            }
        }
    }
    
    /**
     * 
     * @param array $criteria
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function parseCriteria(array $criteria) {
        return new \Doctrine\Common\Collections\ArrayCollection($criteria);
    }
    
    /**
     * @param QueryBuilder $qb
     *
     * @param array $sorting
     */
    protected function applySorting(QueryBuilder $qb, array $sorting = null)
    {
        if (null === $sorting) {
            return;
        }

        foreach ($sorting as $property => $order) {
            if (!empty($order)) {
                $qb->orderBy($this->getPropertyName($property), $order);
            }
        }
    }
    
    /**
     * @param string $name
     *
     * @return string
     */
    protected function getPropertyName($name)
    {
        if (false === strpos($name, '.')) {
            return $this->getAlias().'.'.$name;
        }

        return $name;
    }
    
    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->createQueryBuilder($this->getAlias());
    }
    
    public function getAlias()
    {
        return "e";
    }
    
}
