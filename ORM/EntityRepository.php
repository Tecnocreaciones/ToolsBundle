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
        $request = $this->container->get('request_stack')->getCurrentRequest();
        
        $pagerfanta = new Paginator(new ArrayAdapter($queryBuilder->getQuery()->getScalarResult()));
        $pagerfanta->setDefaultFormat($this->getFormatPaginator());
        $pagerfanta->setContainer($this->container);
        $pagerfanta->setRequest($request);
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
    
    public function findForSearch(array $criteria = [], array $orderBy = null)
    {
        $criteria = $this->parseCriteria($criteria);
        
        $a = $this->getAlias();
        $qb = $this->getQueryBuilder();
        $qb
            ->select($a.".id id")
            ->addSelect($a.".description ".$a."_description")
            ;
        $this->addFieldDescription($criteria, $qb);
        
        $this->applyCriteria($qb, $criteria->toArray());
        $this->applySorting($qb, $orderBy);
        $qb->orderBy($a.".description","ASC");
        return $this->getScalarPaginator($qb);
    }
    
    protected function addFieldDescription($criteria,$qb) {
        if(($description = $criteria->remove('description')) != null){
            $qb
                ->andWhere($qb->expr()->like($this->getAlias().'.description', $qb->expr()->literal("%$description%")));
        }
    }
    
    private function normalizeField($alias,$field) {
        $fieldResponse = "";
        $fieldExplode = explode("__", $field);
        if(count($fieldExplode) == 2){
            $fieldResponse = sprintf("%s_%s.%s",$alias,$fieldExplode[0],$fieldExplode[1]);
        }else{
            $fieldResponse = $fieldExplode[0];
        }
        return $fieldResponse;
    }
    protected function addFieldIn(array $fields,\Doctrine\ORM\QueryBuilder $qb,$criteria)
    {
        foreach ($fields as $field){
            $normalizeField = $this->normalizeField($this->getAlias(),$field);
            $valueField = json_decode($criteria->remove($field),false);
            if(count($valueField) > 0){
                $qb
                    ->andWhere($qb->expr()->in($normalizeField, $valueField))
                    ;
                
            }
        }
    }
    protected function addFieldEquals(array $fields,\Doctrine\ORM\QueryBuilder $qb,$criteria)
    {
        foreach ($fields as $field){
            $normalizeField = $this->normalizeField($this->getAlias(),$field);
            $valueField = $criteria->remove($field);
            if($valueField !== null){
                $qb->andWhere(sprintf("%s = %s",$normalizeField,$qb->expr()->literal($valueField)));
            }
        }
    }
    protected function addFieldLike(array $fields,\Doctrine\ORM\QueryBuilder $qb,$criteria)
    {
        foreach ($fields as $field){
            $normalizeField = $this->normalizeField($this->getAlias(),$field);
            $valueField = $criteria->remove($field);
            if($valueField !== null){
                $qb->andWhere($qb->expr()->like($normalizeField,$qb->expr()->literal("%".$valueField."%")));
            }
        }
    }
    
    protected function addFieldFromTo(array $fields,$qb,$criteria)
    {
        foreach ($fields as $field){
            $fieldFrom = $field."_from";
            $fieldTo = $field."_to";
            $valueFieldFrom = $criteria->remove($fieldFrom);
            $valuefieldTo = $criteria->remove($fieldTo);
            $normalizeField = $this->normalizeField($this->getAlias(),$field);
            if($valueFieldFrom != null){
                $qb
                    ->andWhere(sprintf("%s >= :%s",$normalizeField,$fieldFrom))
                    ->setParameter($fieldFrom,$valueFieldFrom)
                    ;
            }
            if($valuefieldTo != null){
                $qb
                    ->andWhere(sprintf("%s <= :%s",$normalizeField,$fieldTo))
                    ->setParameter($fieldTo,$valuefieldTo)
                    ;
            }
        }
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
    
    protected function addFieldDate(array $fieldDates,$qb,$criteria) {
        foreach ($fieldDates as $fieldDate) {
            $fieldDateDay = $criteria->remove("day_".$fieldDate);
            $fieldDateMonth = $criteria->remove("month_".$fieldDate);
            $fieldDateYear = $criteria->remove("year_".$fieldDate);
            if(empty($fieldDateDay) && empty($fieldDateMonth) && empty($fieldDateYear) ){
                continue;
            }
            
            if($fieldDateDay !== null && $fieldDateMonth !== null && $fieldDateYear !== null){
                $fieldDateDay = str_pad($fieldDateDay, 2,"0",STR_PAD_LEFT);
                $fieldDateMonth = str_pad($fieldDateMonth, 2,"0",STR_PAD_LEFT);
                
                $fieldDateValue = sprintf("%s/%s/%s",$fieldDateDay,$fieldDateMonth,$fieldDateYear);
                $dateTime = \DateTime::createFromFormat("d/m/Y", $fieldDateValue);
                $qb->andWhere($qb->expr()->like($this->getAlias().".".$fieldDate,$qb->expr()->literal("%".$dateTime->format("Y-m-d")."%")));
            }else{
                
                if($fieldDateDay !== null){
                    $fieldDateDay = str_pad($fieldDateDay, 2,"0",STR_PAD_LEFT);
                    $qb
                        ->andWhere(
                            $qb->expr()->like(
                                $this->getAlias().".".$fieldDate,$qb->expr()->literal("%-%-".$fieldDateDay)
                                             )
                                );
                }
                if($fieldDateMonth !== null){
                    $fieldDateMonth = str_pad($fieldDateMonth, 2,"0",STR_PAD_LEFT);
                    $qb
                        ->andWhere(
                            $qb->expr()->like(
                                $this->getAlias().".".$fieldDate,$qb->expr()->literal("%-".$fieldDateMonth."-%")
                                             )
                                );
                }
                if($fieldDateYear !== null){
                    $qb
                        ->andWhere(
                            $qb->expr()->like(
                                $this->getAlias().".".$fieldDate,$qb->expr()->literal($fieldDateYear."-%-%")
                                             )
                                );
                }
            }
        }
    }
    
    protected function addFieldMaxResults($qb,$criteria) {
        if(($maxResults = $criteria->remove('max_results')) != null && $maxResults > 0){
            $qb
                ->setMaxResults($maxResults)
                ;
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
     * @param QueryBuilder $queryBuilder
     *
     * @param array $sorting
     */
    protected function applySorting(QueryBuilder $queryBuilder, array $sorting = null)
    {
        if (null === $sorting) {
            return;
        }

        foreach ($sorting as $property => $order) {
            if (!empty($order)) {
                $queryBuilder->orderBy($this->getPropertyName($property), $order);
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
