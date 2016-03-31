<?php

/*
 * This file is part of the TecnoReady Solutions C.A. package.
 * 
 * (c) www.tecnoready.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Search;

use Tecnocreaciones\Bundle\ToolsBundle\ORM\EntityRepository as Base;

/**
 * Repositorio de Bloque de filtros (app.repository.search.filter_block)
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class FilterBlockRepository extends Base 
{
    /**
     * Busca los bloques de una area
     * @param type $area
     * @return type
     */
    public function findByArea($area)
    {
        $qb = $this->getQueryBuilder();
        $qb
            ->addSelect("fb_fa")
            ->addSelect("fb_fa_f")
            ->innerJoin("fb.filterAddeds", "fb_fa")
            ->innerJoin("fb_fa.filter", "fb_fa_f")
            ->andWhere("fb.area = :area")
            ->setParameter("area", $area)
            ;
        $qb
            ->orderBy("fb.orderBlock","ASC")
            ->addOrderBy("fb_fa.orderFilter","ASC")
            ;
        return $qb->getQuery()->getResult();
    }
    
    public function getAlias() {
        return "fb";
    }
}
