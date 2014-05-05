<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\UnitConverter;

/**
 * Base de unidades
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
abstract class UnitType implements UnitTypeInterface
{
    private $units;
    
    public function __construct() {
        $this->units = array();
        $this->init();
    }
    
    /**
     * Insertar unidad y razón
     *
     * @param $name Nombre de la unidad
     * @param $aliases Alias de la unidad
     * @param $ratio Razón de conversión con la unidad anterior
     */
    protected function insertUnit($name, $aliases, $ratio,$enable = true) {
        $this->units[] = array(
            'name' => $name,
            'aliases' => explode(',', $aliases),
            'ratio' => $ratio,
            'enable' => true,
        );
    }
    
    public function getUnits() {
        return $this->units;
    }
    
    /**
     * Encuentra un nombre de unidad o un alias
     *
     * @param $type Tipo de unidad
     * @param $unitName Nombre de la unidad
     * @param $validUnits Unidades que son válidas para nosotros (puede que no queramos convertir a todas las unidades)
     *
     * @return Índice dentro del array donde está la unidad (si se encuentra)
     */
    private function findUnit($type, $unitName, $validUnits = null) {
        $units = $this->getUnitsByType($type);
        $nunits = count($units);
        for ($i = 0; $i < $nunits; $i++) {
            if (($validUnits != null) && (!array_search($units[$i]['name'], $validUnits) )){
                continue;
            }

            if (($units[$i]['name'] == $unitName) || ($this->aliasMatch($units[$i], $unitName) )){
                return $i;
            }
        }
        throw new \InvalidArgumentException(sprintf('Invalid unit "%s" for type "%s"',$unitName,$type));
    }
    
    /**
     * Mira en los alias de un tipo de unidad
     *
     * @param $unitInfo Array de información sobre la unidad
     * @param $unitName Nombre de la unidad
     *
     * @return true if unitName is in aliases
     */
    private function aliasMatch($unitInfo, $unitName) {
        foreach ($unitInfo['aliases'] as $alias) {
            if ($unitName == $alias)
                return true;
        }
    }
    
    /**
     * Convierte unidades
     *
     * @param $type Tipo de unidad
     * @param $qty Cantidad a convertir
     * @param $fromUnit Unidad de origen
     * @param $toUnit Unidad de destino
     *
     * @return Resultado o falso (si hay error)
     */
    public function convert($type, $qty, $fromUnit, $toUnit) 
    {
        $fromUnitNdx = $this->findUnit($type, $fromUnit);
        $toUnitNdx = $this->findUnit($type, $toUnit);

        if (($fromUnitNdx === false) || ($toUnitNdx === false))
            return false;
        $units = $this->getUnitsByType();
        /* It wont be ever possible, but maybe it is useful for debugging */
        if (($fromUnitNdx < 0) || ($toUnitNdx >= count($units) ))
            return false;

        /* if ($fromUnitNdx==$toUnitNdx) */
        if ($fromUnitNdx > $toUnitNdx) {
            /* Convert Down */
            for ($i = $fromUnitNdx; $i > $toUnitNdx; $i--){
                $qty*=$units[$i]['ratio'];
            }
        } else {
            /* Convert up */
            for ($i = $fromUnitNdx; $i < $toUnitNdx; $i++){
                $qty/=$units[$i + 1]['ratio'];
            }
        }
        return $qty;
    }
    
    function getUnitsByType() 
    {
        return $this->units;
    }
    
    function toArray()
    {
        return array(
            'description' => $this->getDescription(),
            'units' => $this->getUnits(),
        );
    }
}
