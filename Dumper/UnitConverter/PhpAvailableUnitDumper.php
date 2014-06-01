<?php


namespace Tecnocreaciones\Bundle\ToolsBundle\Dumper\UnitConverter;

/**
 * PhpMatcherDumper creates a PHP class cache for units
 *
 */
class PhpAvailableUnitDumper implements \Symfony\Component\DependencyInjection\Dumper\DumperInterface
{
    private $unitTypes;

    public function __construct(array $unitTypes) {
        $this->unitTypes = $unitTypes;
    }
    
    /**
     * Dumps a set of routes to a PHP class.
     *
     * Available options:
     *
     *  * class:      The class name
     *  * base_class: The base class name
     *
     * @param array $options An array of options
     *
     * @return string A PHP class representing the matcher class
     */
    public function dump(array $options = array())
    {
        $options = array_replace(array(
            'class'      => 'ProjectAvailableUnit',
            //'base_class'      => 'ProjectAvailableUnit',
        ), $options);

        return <<<EOF
<?php

/**
 * {$options['class']}
 *
 * This class has been auto-generated
 * by the Tecnocreaciones Tools Component.
 */
class {$options['class']}
{
   
{$this->generateAvailableUnits()}
function getUnitsTypes(){ return \$this->unitTypes;}
}

EOF;
    }

    /**
     * Generates the code for the match method implementing.
     */
    private function generateAvailableUnits()
    {
        $code = rtrim($this->compileUnitTypes());

        return <<<EOF

    private \$unitTypes = array(
        $code
    );
EOF;
    }

    /**
     * Generates PHP code 
     *
     * @return string PHP code
     */
    private function compileUnitTypes()
    {
        $code = '';
        
        foreach ($this->unitTypes as $unitType) {
            $code .= sprintf("'%s' => %s,",$unitType->getType(),var_export($unitType->toArray(),true));
        }

        return $code;
    }
}
