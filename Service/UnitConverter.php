<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Unit converter v0.1
 * Copyright (C) 2012  Gaspar Fernández <gaspar.fernandez@totaki.com>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Service;

use Symfony\Component\Config\ConfigCache;

/**
 * Description of UnitConverter
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class UnitConverter
{
    private $availableUnit = null;
    
    protected $units = null;
    
    protected $validUnitTypes = array();
    
    protected $unitTypes = array();

    function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Rellena todas las unidades de un tipo de unidad dado
     *
     * @param $unitType Tipo de unidad a rellenar
     */
    private function fillUnit($unitType) {
        if($this->units === null){
            $this->units = $this->getAvailableUnit()->getUnitsTypes();
        }
        if ($this->validUnitType($unitType)) {
            return true;
        } else
            return false;
    }

    /**
     * Valida un tipo de unidad
     *
     * @param $unitType Tipo de unidad a probar
     */
    private function validUnitType($unitType) {
        if(!isset($this->unitTypes[$unitType])){
            throw new \InvalidArgumentException(sprintf('The Unit Type "%s" is not register on Unit Converter Service',$unitType));
        }
        return true;
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
     * Convierte unidades
     *
     * @param $type Tipo de unidad
     * @param $qty Cantidad a convertir
     * @param $fromUnit Unidad de origen
     * @param $toUnit Unidad de destino
     *
     * @return Resultado o falso (si hay error)
     */
    public function convert($type, $qty, $fromUnit, $toUnit) {
        if (!$this->validUnitType($type))
            return false;

        return $this->unitTypes[$type]->convert($type, $qty, $fromUnit, $toUnit);
    }

    /**
     * Obtiene la cantidad expresada en la unidad $fromUnit en la unidad más grande
     * ej: 86400 segundos = 1 day (y no 0.03 months)
     *
     * @param $type Tipo de unidad
     * @param $qty Cantidad
     * @param $fromUnit Unidad de origen
     * 
     * @return array(cantidad nueva, unidad nueva)
     */
    public function getMaxUnit($type, $qty, $fromUnit) {
        if (!$this->fillUnit($type))
            return false;
        $units = $this->getUnitsByType($type);
        $fromUnitNdx = $this->findUnit($type, $fromUnit);
        $toUnitNdx = count($units);
        $currentUnit = $fromUnit;
        
        /* Convert up */
        for ($i = $fromUnitNdx; $i < $toUnitNdx - 1; $i++) {
            $tmp = $qty / $units[$i + 1]['ratio'];
            
            if (!is_int($tmp) && !is_float($tmp)){
                break;
            }
            $qty = $tmp;
            $currentUnit = $i + 1;
        }
        return array($qty, $units[$currentUnit]['aliases'][0]);
    }

    /**
     * Valida una unidad dada en cualquiera de sus alias
     *
     * @param $type Tipo de unidad
     * @param $unit Unidad
     * @param $validUnits Array de unidades válidas
     *
     * @return bool true if unit is right
     */
    public function validateUnit($type, $unit, $validUnits = null) {
        return ($this->findUnit($type, $unit, $validUnits) !== false);
    }

    /**
     * Obtiene una lista de los tipos de unidad válidos
     * Útil para depuración
     *
     * @return array de tipos de unidades
     */
    public function getValidUnitTypes() {
        $types = array();
        foreach ($types as $name => $filler) {
            $types[] = $name;
        }
        return $types;
    }
    
    function addUnit(UnitConverter\UnitTypeInterface $unit)
    {
        if(isset($this->unitTypes[$unit->getType()])){
            throw new \InvalidArgumentException(sprintf('The "%s" and type was added to the list of types of units.',$unit->getType()));
        }
        $this->unitTypes[$unit->getType()] = $unit;
    }
    
    /**
     * @var array
     */
    protected $options = array();
    
    /**
     * Sets options.
     *
     * Available options:
     *
     *   * cache_dir:     The cache directory (or null to disable caching)
     *   * debug:         Whether to enable debugging or not (false by default)
     *   * resource_type: Type hint for the main resource (optional)
     *
     * @param array $options An array of options
     *
     * @throws \InvalidArgumentException When unsupported option is provided
     */
    public function setOptions(array $options)
    {
        $this->options = array(
            'cache_dir'              => null,
            'debug'                  => false,
            'available_unit_dumper_class' => 'Tecnocreaciones\\Bundle\\ToolsBundle\\Dumper\\UnitConverter\\PhpAvailableUnitDumper',
            'available_unit_cache_class'  => 'ProjectAvailableUnit',
        );

        // check option names and live merge, if errors are encountered Exception will be thrown
        $invalid = array();
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            } else {
                $invalid[] = $key;
            }
        }
        
        if ($invalid) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the following options: "%s".', implode('", "', $invalid)));
        }
    }
    
    /**
     * Sets an option.
     *
     * @param string $key   The key
     * @param mixed  $value The value
     *
     * @throws \InvalidArgumentException
     */
    public function setOption($key, $value)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the "%s" option.', $key));
        }

        $this->options[$key] = $value;
    }
    
    /**
     * Gets an option value.
     *
     * @param string $key The key
     *
     * @return mixed The value
     *
     * @throws \InvalidArgumentException
     */
    public function getOption($key)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the "%s" option.', $key));
        }

        return $this->options[$key];
    }
    
    /**
     * Gets the UrlMatcher instance associated with this Router.
     *
     * @return UrlMatcherInterface A UrlMatcherInterface instance
     */
    public function getAvailableUnit()
    {
        if (null !== $this->availableUnit) {
            return $this->availableUnit;
        }
        $class = $this->options['available_unit_cache_class'];
        $cache = new ConfigCache($this->options['cache_dir'].'/'.$class.'.php', $this->options['debug']);
        if (!$cache->isFresh()) {
            $dumper = $this->getAvailableUnitDumperInstance();

            $options = array(
                'class'      => $class
            );
            $cache->write($dumper->dump($options));
        }

        require_once $cache;

        return $this->availableUnit = new $class();
    }
    
    /**
     * @return MatcherDumperInterface
     */
    protected function getAvailableUnitDumperInstance()
    {
        return new $this->options['available_unit_dumper_class']($this->unitTypes);
    }
    
    function getUnitsByType($type) {
        return $this->units[$type]['units'];
    }
}
