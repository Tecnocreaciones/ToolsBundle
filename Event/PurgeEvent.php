<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Event;

/**
 * Description of PurgeEvent
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class PurgeEvent extends \Symfony\Component\EventDispatcher\Event
{
    /**
     * Instance used for persistence.
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    
    private $sqlConditions;
    
    public function __construct($em) 
    {
        $this->em = $em;
    }
    
    function addSqlCondition(\Doctrine\ORM\QueryBuilder $qb)
    {
        $this->sqlConditions[] = $qb->getQuery()->getSQL();
    }
    
    public function getSqlConditions()
    {
        return $this->sqlConditions;
    }
    
    /**
     * Create a QueryBuilder instance
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    function createQueryBuilder()
    {
        return $this->em->createQueryBuilder();
    }
}
