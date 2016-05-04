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
    public static function addFilters($manager,$filters,$filterBlock,$filterAddedClass,$context) {
        $i = 1;
        foreach ($filters as $key => $filter) {
            $modelName = null;
            $filterInstance = $context->getReference("filter-".$filter);
            $filterGroup = $filterInstance->getFilterGroup();
            if(is_array($filter)){
                $modelName = $filter["modelName"];
                if(isset($filter["filterGroup"])){
                    $filterGroup = $context->getReference("filterGroup-".$filter["filterGroup"]);
                }
                $filter = $key;
            }
            $filterAdded = new $filterAddedClass();
            $filterAdded
                ->setOrderFilter($i)
                ->setFilterGroup($filterGroup)
                ->setFilter($filterInstance);
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
