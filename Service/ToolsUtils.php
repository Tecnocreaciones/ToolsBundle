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
        $orderDefault = 10;
        foreach ($filters as $key => $filter) {
            $filterId = $filter;
            if(is_array($filterId)){
                $filterId = $key;
            }
            $modelName = null;
            $orderFilter = null;
            $filterInstance = $context->getReference("filter-".$filterId);
            $filterGroup = null;
            $label = null;
            if(is_array($filter)){
                if(isset($filter["modelName"])){
                    $modelName = $filter["modelName"];
                }
                if(isset($filter["orderFilter"])){
                    $orderFilter = $filter["orderFilter"];
                }
                if(isset($filter["label"])){
                    $label = $filter["label"];
                }
                if(isset($filter["filterGroup"])){
                    $filterGroup = $context->getReference("filterGroup-".$filter["filterGroup"]);
                }
            }
            if($orderFilter === null){
                $orderFilter = $orderDefault;
            }
            $filterAdded = new $filterAddedClass();
            $filterAdded
                ->setLabel($label)
                ->setOrderFilter($orderFilter)
                ->setFilterGroup($filterGroup)
                ->setFilter($filterInstance);
                if($modelName !== null){
                    $filterAdded->setModelName($modelName);
                }
            $manager->persist($filterAdded);
            $filterBlock->addFilterAdded($filterAdded);
            $i++;
            $orderDefault += 10;
        }
        $manager->persist($filterBlock);
    }
}