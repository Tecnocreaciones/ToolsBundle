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

use Exception;

/**
 * Description of ToolsUtils
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ToolsUtils {

    public static function addFilters($manager, $filters, $filterBlock, $filterAddedClass, $context) {
        $i = 1;
        $orderDefault = 10;
        foreach ($filters as $key => $filter) {
            $filterId = $filter;
            if (is_array($filterId)) {
                $filterId = $key;
            }
            $filterInstance = $context->getReference("filter-" . $filterId);
            $modelName = null;
            $orderFilter = null;
            $filterGroup = null;
            $label = null;
            if (is_array($filter)) {
                if (isset($filter["modelName"])) {
                    $modelName = $filter["modelName"];
                }
                if (isset($filter["orderFilter"])) {
                    $orderFilter = $filter["orderFilter"];
                }
                if (isset($filter["label"])) {
                    $label = $filter["label"];
                }
                if (isset($filter["filterGroup"])) {
                    $filterGroup = $context->getReference("filterGroup-" . $filter["filterGroup"]);
                }
            }
            if ($orderFilter === null) {
                $orderFilter = $orderDefault;
            }
            $filterAdded = new $filterAddedClass();
            $filterAdded
                    ->setLabel($label)
                    ->setOrderFilter($orderFilter)
                    ->setFilterGroup($filterGroup)
                    ->setFilter($filterInstance);
            if ($modelName !== null) {
                $filterAdded->setModelName($modelName);
            }
            $manager->persist($filterAdded);
            $filterBlock->addFilterAdded($filterAdded);
            $i++;
            $orderDefault += 10;
        }
        $manager->persist($filterBlock);
    }

    public static function addFilters2($manager,$filterAddedClass,$filterBlock, array $filters, array $allFilters) {
        $orderDefault = 10;
        foreach ($filters as $key => $filter) {
            $filterId = $filter;
            if (is_array($filterId)) {
                $filterId = $key;
            }
            $modelName = null;
            $orderFilter = null;
            $filterGroup = null;
            $label = null;
            if (is_array($filter)) {
                if (isset($filter["modelName"])) {
                    $modelName = $filter["modelName"];
                }
                if (isset($filter["orderFilter"])) {
                    $orderFilter = $filter["orderFilter"];
                }
                if (isset($filter["label"])) {
                    $label = $filter["label"];
                }
//                if (isset($filter["filterGroup"])) {
//                    $filterGroup = $context->getReference("filterGroup-" . $filter["filterGroup"]);
//                }
            }
            if ($orderFilter === null) {
                $orderFilter = $orderDefault;
            }
            $filterAdded = new $filterAddedClass();
            $filterAdded
                    ->setLabel($label)
                    ->setOrderFilter($orderFilter)
                    ->setFilterGroup($filterGroup)
                    ->setFilter($allFilters[$filterId]);
            if ($modelName !== null) {
                $filterAdded->setModelName($modelName);
            }
            $manager->persist($filterAdded);
            $filterBlock->addFilterAdded($filterAdded);
            $orderDefault += 10;
        }
    }
    public static function addNewFilters(&$filtersArray,array $news) {
        foreach ($news as $new) {
            $filtersArray[$new->getRef()] = $new;
        }
    }
    public static function iconExtension($extension) {
        static $icons = null;
        if($icons === null){
            $icons = [];
            $finder = new \Symfony\Component\Finder\Finder();
            $ds = DIRECTORY_SEPARATOR;
            $dir = __DIR__.$ds."..".$ds."Resources/public/tabs/sprites/icons";
            $finder->in($dir)->files();
            foreach ($finder as $file) {
                $icons[] = basename($file->getFilename(),".png");
            }
        }
        $sprite = 'file';
        if(in_array($extension,$icons)){
            $sprite = $extension;
        }
        $icon = '<i class="file file-'.$sprite.'"></i>';
        return $icon;
    }
    
    public static function testQuantityExp($expresion,$quantity){
        $expAmount = explode(" ", $expresion);
        if(count($expAmount) == 1){
            throw new Exception(sprintf("The expresion '%s' is malformed",$expresion));
        }
        $quantityExpected = self::fotmatToNumber($expAmount[1]);
        if (version_compare($quantity, $quantityExpected, $expAmount[0]) === false) {
            throw new Exception(sprintf("Value expected '%s' but there value is '%s' on expresion '%s'.", $quantityExpected,$quantity, $expresion));
        }
    }
    
    /**
     * Formatea un número usando como decimales la coma (,)
     * @param type $amount
     * @param type $decimals
     * @return type
     */
    public static function fotmatToNumber($amount, $decimals = 2) {
        $numberFormated = number_format($amount, $decimals, ".", "");
        return (double) $numberFormated;
    }
}
