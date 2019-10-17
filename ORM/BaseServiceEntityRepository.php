<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Tecnocreaciones\Bundle\ToolsBundle\Model\Paginator\Paginator;

/**
 * Description of BaseServiceEntityRepository
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class BaseServiceEntityRepository extends ServiceEntityRepository
{
    use TraitEntityRepository;
    
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
    
    public function getFormatPaginator()
    {
        return Paginator::FORMAT_ARRAY_STANDARD;
    }
}
