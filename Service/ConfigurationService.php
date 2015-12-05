<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Service;

use Tecnocreaciones\Bundle\ToolsBundle\Entity\Configuration\BaseGroup as Group;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Config\ConfigCache;

/**
 * Servicio manejador de configuracion
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class ConfigurationService implements ContainerAwareInterface
{
    /**
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;
    
    /**
     * @var array
     */
    protected $options = array();
    
    /**
     * Configuraciones disponibles
     * @var \Tecnocreaciones\Bundle\ToolsBundle\Model\Configuration\ConfigurationAvailable
     */
    private $availableConfiguration;
            
    /**
     * @var \Tecnocreaciones\Bundle\ToolsBundle\Model\Configuration\Configuration
     */
    private $configurations = null;
    /**
     *
     * @var \Tecnocreaciones\Bundle\ToolsBundle\Model\Configuration\ConfigurationWrapper
     */
    private $configurationsWrapper;
            
    function __construct(array $options = array())
    {
        $this->setOptions($options);
        $this->configurationsWrapper = [];
    }
    
    public function addConfigurationManager(\Tecnocreaciones\Bundle\ToolsBundle\Model\Configuration\ConfigurationManager $configurationManager) 
    {
        if(isset($this->configurationsManager[$configurationManager->getId()])){
            throw new \RuntimeException(sprintf("The configurationManager id '%s' already added",$configurationManager->getId()));
        }
        $this->configurationsManager[$configurationManager->getId()] = $configurationManager;
        return $this;
    }

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
            'configuration_dumper_class' => 'Tecnocreaciones\\Bundle\\ToolsBundle\\Dumper\\Configuration\\PhpConfigurationDumper',
            'configuration_base_dumper_class' => 'Tecnocreaciones\\Bundle\\ToolsBundle\\Model\\Configuration\\ConfigurationAvailable',
            'configuration_cache_class'  => 'ProjectConfigurationAvailable',
            'configuration_class'  => null,
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
            throw new \InvalidArgumentException(sprintf('The Configuration does not support the following options: "%s".', implode('", "', $invalid)));
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
            throw new \InvalidArgumentException(sprintf('The Configuration does not support the "%s" option.', $key));
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
            throw new \InvalidArgumentException(sprintf('The Configuration does not support the "%s" option.', $key));
        }

        return $this->options[$key];
    }
    
    /**
     * Gets the Configuration Value instance associated with this Confurations.
     * 
     * @return \Tecnocreaciones\Bundle\ToolsBundle\Model\Configuration\ConfigurationAvailable
     */
    public function getAvailableConfiguration()
    {
        if (null !== $this->availableConfiguration) {
            return $this->availableConfiguration;
        }
        $class = $this->options['configuration_cache_class'];
        $cache = $this->getConfigCache();
        if (!$cache->isFresh()) {
            $dumper = $this->getAvailableConfigurationDumperInstance();

            $options = array(
                'class'      => $class,
                'base_class'      => $this->options['configuration_base_dumper_class']
            );
            $cache->write($dumper->dump($options));
        }

        require_once $cache;

        return $this->availableConfiguration = new $class();
    }
    
    /**
     * Retorna la clase que maneja la cache
     * 
     * @return \Symfony\Component\Config\ConfigCache
     */
    private function getConfigCache()
    {
        $class = $this->options['configuration_cache_class'];
        return new ConfigCache($this->options['cache_dir'].'/tecnocreaciones_tools/'.$class.'.php', $this->options['debug']);
    }
    
    /**
     * Retorna el valor de la configuracion de la base de datos
     * 
     * @param string $key Indice de la configuracion
     * @param mixed $default Valor que se retornara en caso de que no exista el indice
     * @return mixed
     */
    function get($key,$default = null) {
        return $this->getAvailableConfiguration()->get($key,$default);
    }
    
    /**
     * Establece el valor de una configuracion
     * 
     * @param string $key indice de la configuracion
     * @param mixed $value valor de la configuracion
     * @param string|null $description Descripcion de la configuracion|null para actualizar solo el key
     */
    function set($key,$value = null,$description = null,Group $group = null)
    {
        $id = $this->getAvailableConfiguration()->getIdByKey($key);
        $entity = $this->getConfiguration($id);
        if($entity === null){
            $entity = $this->createNew();
        }else{
            $entity->setUpdatedAt();
        }
        $entity->setKey($key)
               ->setValue($value);
        if($description != null){
            $entity->setDescription($description);
        }
        if($group != null){
            $entity->setGroup($group);
        }
        $em = $this->getManager();
        $em->persist($entity);
    }
    
    /**
     * Guarda los cambios en la base de datos
     */
    function flush($andClearCache = true)
    {
        $em = $this->getManager();
        $em->flush();
        if($andClearCache){
            $this->clearCache();
        }
    }
    
    /**
     * Crea la cache
     */
    function warmUp()
    {
        $this->getAvailableConfiguration();
    }
    
    /**
     * Limpia la cache
     */
    function clearCache()
    {
        $this->availableConfiguration = null;
        $cache = $this->getConfigCache();
        @unlink($cache);
        $this->warmUp();
    }
    
    /**
     * @return MatcherDumperInterface
     */
    protected function getAvailableConfigurationDumperInstance()
    {
        if($this->options['configuration_class'] === null){
            throw new \LogicException('You must assign class configuration_class');
        }
        
        $entities = $this->getConfigurationRepository()->findAll();
        return new $this->options['configuration_dumper_class']($entities);
    }
    
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        $this->container = $container;
    }
    
    protected function getManager()
    {
        return $this->container->get('doctrine')->getManager();
    }
    
    /**
     * 
     * @return \Tecnocreaciones\Bundle\ToolsBundle\Model\Configuration
     */
    protected function createNew()
    {
        $entity = new $this->options['configuration_class'];
        $entity->setCreatedAt();
        return $entity;
    }
    
    /**
     * 
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getConfigurationRepository()
    {
        return $this->getManager()->getRepository($this->options['configuration_class']);
    }
    
    /**
     * Retorna la entidad de la base de datos de la configuracion
     * @param type $id
     * @return \Tecnocreaciones\Bundle\ToolsBundle\Model\Configuration\Configuration
     */
    protected function getConfiguration($id)
    {
        if(null === $this->configurations){
            $this->getConfigurations();
        }
        if(isset($this->configurations[$id])){
            return $this->configurations[$id];
        }
        return null;
    }
    
    /**
     * Configuraciones actuales en la base de datos
     * 
     * @return \Tecnocreaciones\Bundle\ToolsBundle\Model\Configuration\Configuration
     */
    protected function getConfigurations()
    {
        if(null !== $this->configurations){
            return $this->configurations;
        }
        $configurations = $this->getConfigurationRepository()->findAll();
        $this->configurations = array();
        foreach ($configurations as $entity) {
            $this->configurations[$entity->getId()] = $entity;
        }
        return $this->configurations;
    }
}
