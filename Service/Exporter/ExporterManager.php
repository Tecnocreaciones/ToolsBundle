<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\Exporter;

use InvalidArgumentException;
use Tecnocreaciones\Bundle\ToolsBundle\Model\Exporter\ChainModel;
use RuntimeException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tecnocreaciones\Bundle\ToolsBundle\Service\Exporter\Adapter\ExporterAdapterInterface;

/**
 * Servicio para exportar documentos PDF, XLS, DOC, TXT de los modulos (app.service.exporter)
 */
class ExporterManager
{   
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    
    /**
     * Modelos disponibles para exportar
     * @var array ChainModel
     */
    private $chainModels;
    
    /**
     * Opciones de configuracion
     * @var array
     */
    private $options;
    
    /**
     * Adaptador para buscar en bases de datos
     * @var ExporterAdapterInterface
     */
    private $adapter;
    
    /**
     * @var Symfony\Component\Filesystem\Filesystem 
     */
    private $fs;
    
    public function __construct(array $options = []) {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            "debug" => false,
        ]);
        $resolver->setDefined(["documents_path","env"]);
        $resolver->addAllowedTypes("documents_path","string");
        $resolver->setRequired(["debug","documents_path","env"]);
        $this->options = $resolver->resolve($options);
        
        $this->chainModels = [];
        $this->fs = new \Symfony\Component\Filesystem\Filesystem();
    }
    
    /**
     * Establece el adaptador a usar para consultas
     * @param ExporterAdapterInterface $adapter
     * @return $this
     * @required
     */
    public function setAdapter(ExporterAdapterInterface $adapter) 
    {
        $this->adapter = $adapter;
        return $this;
    }
    
    /**
     * Agrega un modelo de exportacion
     * @param ChainModel $chainModel
     * @throws InvalidArgumentException
     */
    public function addChainModel(ChainModel $chainModel) {
        if(isset($this->chainModels[$chainModel->getId()])){
           throw new InvalidArgumentException(sprintf("The chain model to '%s' is already added, please add you model to tag '%s'",$chainModel->getClassName(),$chainModel->getClassName())); 
        }
        $chainModel->setContainer($this->container);
        $this->chainModels[$chainModel->getId()] = $chainModel;
    }
    
    /**
     * Verifica si existe un modelo de exportacion
     * @param type $className
     * @return type
     */
    public function hasChainModel($className)
    {
        return isset($this->chainModels[$className]);
    }
    
    /**
     * Retorna un modelo de exportacion
     * @param type $id
     * @return ChainModel
     * @throws InvalidArgumentException
     */
    protected function getChainModel($id) {
        if(!isset($this->chainModels[$id])){
           throw new InvalidArgumentException(sprintf("The chain model is not added or the id '%s' is invalid.",$id)); 
        }
        return $this->chainModels[$id];
    }
    
    /**
     * Retorna una opcion
     * @param type $name
     * @return type
     * @throws InvalidArgumentException
     */
    public function getOption($name) {
        if(!isset($this->options[$name])){
            throw new InvalidArgumentException(sprintf("The option name '%s' is invalid, available are %s.",$name, implode(",",array_keys($this->options))));
        }
        return $this->options[$name];
    }
    
    /**
     * Genera un documento de un modulo
     * @param type $idChain
     * @param type $name
     * @param array $options
     * @return string La ruta del archivo generado
     * @throws RuntimeException
     */
    public function generate($idChain,$name,array $options = []) {
        $chainModel = $this->resolveChainModel($idChain, $options);
        
        $modelDocument = $chainModel->getModel($name);
        $pathFileOut = $modelDocument
                ->setChainModel($chainModel)
                ->write($options["data"]);
        
        if($pathFileOut === null){
            throw new RuntimeException(sprintf("Failed to generate document '%s' with name '%s'",$idChain,$name));
        }
        if(!is_readable($pathFileOut)){
            throw new RuntimeException(sprintf("Failed to generate document '%s' with name '%s'. File '%s' is not readable.",$idChain,$name,$pathFileOut));
        }
        return $pathFileOut;
    }
    
    /**
     * Genera un documento a partir de un id
     * @param type $id
     * @param type $idChain
     * @param type $name
     * @param array $options
     * @return string La ruta del archivo generado
     * @throws RuntimeException
     */
    public function generateWithSource($id,$idChain,$name,$output,array $options = []) {
        if(!$this->adapter){
            throw new RuntimeException(sprintf("The adapter must be set for enable this feature."));
        }
        $chainModel = $this->getChainModel($idChain);
        $className = $chainModel->getClassName();
        $entity = $this->adapter->find($chainModel->getClassName(),$id);
        if(!$entity){
            throw new RuntimeException(sprintf("The source '%s' with '%s' not found.",$className,$id));
        }
        $options["data"]["entity"] = $entity;
        $options["data"]["output"] = $output;
        $options["data"]["name"] = $this->trans($name,[],'labels');
        if($entity instanceof \Tecnocreaciones\Bundle\ToolsBundle\Service\SequenceGenerator\ItemReferenceInterface){
            $options["data"]["%ref%"] = $entity->getRef();
        }
        
        $options["sub_path"] = $id;
        return $this->generate($idChain, $name,$options);
    }
    
    /**
     * Resuelve el modelo de exportacion y le establece los parametros
     * @param type $idChain
     * @param array $options
     * @return ChainModel
     */
    public function resolveChainModel($idChain,array $options = []) {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            "sub_path" => null,
            "data" => []
        ]);
        $resolver->setAllowedTypes("data","array");
        $options = $resolver->resolve($options);
        $chainModel = $this->getChainModel($idChain);
        $chainModel
            ->setExporterManager($this)
            ->setSubPath($options["sub_path"])
            ;
        return $chainModel;
    }
    

    public function getFs() {
        return $this->fs;
    }
    
    /**
     * Traduce un indice
     * @param type $id
     * @param array $parameters
     * @param type $domain
     * @return type
     */
    protected function trans($id, array $parameters = array(), $domain = 'flashes') {
        return $this->container->get('translator')->trans($id, $parameters, $domain);
    }
}
