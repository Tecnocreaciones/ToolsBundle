<?php

/*
 * This file is part of the Witty Growth C.A. - J406095737 package.
 * 
 * (c) www.mpandco.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Service;

/**
 * Description of ToolsUtils
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ToolsUtils 
{
    public static function addFilters($manager,$filters,$filterBlock) {
        $i = 1;
        foreach ($filters as $key => $filter) {
            $modelName = null;
            if(is_array($filter)){
                $modelName = $filter["modelName"];
                $filter = $key;
            }
            $filterAdded = new FilterAdded();
            $filterAdded
                ->setOrderFilter($i)
                ->setFilter($this->getReference("filter-".$filter));
                if($modelName !== null){
                    $filterAdded->setModelName($modelName);
                }
            $manager->persist($filterAdded);
            $filterBlock->addFilterAdded($filterAdded);
            $i++;
        }
        $manager->persist($filterBlock);
    }
}
