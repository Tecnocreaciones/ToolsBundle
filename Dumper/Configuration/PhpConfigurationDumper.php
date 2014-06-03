<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Dumper\Configuration;

use Symfony\Component\DependencyInjection\Dumper\DumperInterface;

/**
 * Genera la cache de la configuracion
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class PhpConfigurationDumper implements DumperInterface
{
    private $configurations;

    public function __construct(array $configurations) {
        $this->configurations = $configurations;
    }
    
    public function dump(array $options = array()) {
        $options = array_replace(array(
            'class'      => 'ProjectConfigurationAvailable',
            'base_class'      => 'Tecnocreaciones\\Bundle\\ToolsBundle\\Model\\Configuration\\ConfigurationAvailable',
        ), $options);

        return <<<EOF
<?php

/**
 * {$options['class']}
 *
 * This class has been auto-generated
 * by the Tecnocreaciones Tools Component.
 */
class {$options['class']} extends {$options['base_class']}
{
{$this->generateConfiguration()}
}

EOF;
    }
    
    private function generateConfiguration()
    {
        $code = rtrim($this->compileConfiguration());

        return <<<EOF

    protected \$configurations = array(
        $code
    );
EOF;
    }
    
     /**
     * Generates PHP code 
     *
     * @return string PHP code
     */
    private function compileConfiguration()
    {
        $code = '';
        
        foreach ($this->configurations as $key => $configuration) {
            $data = array();
            $data['key'] = $configuration->getKey();
            $data['value'] = $configuration->getValue();
            $data['active'] = $configuration->getActive();
//            $data['createdAt'] = $configuration->getCreatedAt();
//            $data['updatedAt'] = $configuration->getUpdatedAt();
            $data['id'] = $configuration->getId();
            $code .= sprintf("'%s' => %s,",$configuration->getKey(),var_export($data,true));
        }

        return $code;
    }
}
