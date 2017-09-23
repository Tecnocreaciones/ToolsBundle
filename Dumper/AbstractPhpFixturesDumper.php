<?php

/*
 * This file is part of the Witty Growth C.A. - J406095737 package.
 * 
 * (c) www.mpandco.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Dumper;

/**
 * Crea fixtures a partir de archivos excel
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class AbstractPhpFixturesDumper
{
    protected $container;
    /**
     *
     * @var \ReflectionClass
     */
    protected $reflection;
    /**
     *
     * @var \PHPExcel
     */
    private $objPHPExcel;
    /**
     *
     * @var array Parametros
     */
    private $parameters;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $rootDir = dirname($this->container->getParameter("kernel.root_dir"));
        $ds = DIRECTORY_SEPARATOR;
        $resolver->setDefaults(array(
            'rootDir' => $rootDir,
            'baseDirXls' => dirname($rootDir).$ds.'importar',
            'baseClass' => 'AbstractFixture implements OrderedFixtureInterface,ContainerAwareInterface',
            'uses' => ["Symfony\Component\DependencyInjection\ContainerAwareInterface"],
        ));

        $resolver->setRequired([
            "namespace",
            "fixturesDir",
            "fileName",
            "order",
            "nameFixture",
            "entity"
        ]);
    }
    
    public function __construct($container) {
        $this->container = $container;
        
        $this->reflection = new \ReflectionClass($this);
        $this->objPHPExcel = null;
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
        $usesArray = array_merge($this->parameters['uses'],$this->getUses());
        $uses = '';
        foreach ($usesArray as $use) {
            $uses .= sprintf("use %s;",$use);
        }
        $flush = "\$manager->flush();";
        if(!$this->isEnabled()){
            return "";
        }
        $classDumper = $this->reflection->getName();
        $dateDumper = new \DateTime();
        $dateDumper = $dateDumper->format("Y-m-d h:i:s a");
        return <<<EOF
<?php
        
/*
 * This file is part of the TecnoReady Solutions C.A. (J-40629425-0) package.
 * 
 * (c) www.tecnoready.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

{$this->parameters['namespace']}
                
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use {$this->parameters["entity"]};
$uses
                
/**
 *
 * This class has been auto-generated
 * by the fixtures importer "{$classDumper}"
 * dumper at {$dateDumper}.
 */
class {$this->parameters['nameFixture']} extends {$this->parameters['baseClass']}
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    
    protected function get(\$id){
        return \$this->container->get(\$id);
    }

    public function getOrder() {
        return {$this->parameters['order']};
    }

    public function load(ObjectManager \$manager) {
        {$this->generateFixtures()}
        
        $flush
    }
    {$this->generateBottom()}
}

EOF;
    }
    
    public function dumpAndSave()
    {
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $ds = DIRECTORY_SEPARATOR;
        $fileName = $this->parameters['rootDir'].$ds.$this->parameters['fixturesDir'].$ds.$this->parameters['nameFixture'].'.php';
        $content = $this->dump();
        
        if($this->objPHPExcel){
            $this->objPHPExcel->disconnectWorksheets();
        }
//        var_dump($fileName);
//        var_dump($content);
//        die;
        $fs->dumpFile($fileName,$content);
    }
    
    /**
     * 
     * @return \PHPExcel
     */
    public function getObjPHPExcel()
    {
        if($this->objPHPExcel === null){
            $ds = DIRECTORY_SEPARATOR;
            $fileName = $this->parameters['baseDirXls'].$ds.$this->parameters['fileName'];
            $this->objPHPExcel = \PHPExcel_IOFactory::load($fileName);
        }
        return $this->objPHPExcel;
    }
    protected function generateBottom(){}
    
    abstract protected function generateFixtures();
    
    protected function getUses()
    {
        return [];
    }
    
    protected function isEnabled() {
        return true;
    }
    
    public function setParameters(array $parameters) 
    {
        $this->parameters = $parameters;
        return $this;
    }
}
