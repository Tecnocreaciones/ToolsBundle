<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Exporter;

use RuntimeException;
use Tecnocreaciones\Bundle\ToolsBundle\Service\Exporter\ExporterManager;

/**
 * Agrupa los modelos de exportacion de un modulo
 *
 */
class ChainModel {
    
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    
    /**
     * Identificador del modelo
     * @var string
     */
    private $id;
    
    /**
     * @var \AppBundle\Services\Core\ExporterManager
     */
    protected $exporterManager;

    /**
     * Clase que soporta el modelo
     * @var type 
     */
    private $className;
    
    /**
     * Documentos
     * @var ModelDocument
     */
    private $models;
    
    /**
     * Path base de la ruta (si es nulo se calculara con los parametros por defecto)
     * @var string 
     */
    private $basePath = null;
    
    /**
     * Sub path para almacenar los documentos dentro de la carpeta del modulo
     * @var string 
     */
    private $subPath;
    
    public function __construct($id,$className) {
        $this->id = $id;
        $this->className = $className;
        $this->models = [];
    }
    
    /**
     * Añade un modelo de documentos
     * @param \AppBundle\Model\Core\Exporter\ModelDocument $modelDocument
     * @return $this
     * @throws \RuntimeException
     */
    public function add(ModelDocument $modelDocument) {
        if(isset($this->models[$modelDocument->getName()])){
            throw new RuntimeException(sprintf("The model document name '%s' is already added in module '%s'",$modelDocument->getName(),$this->id));
        }
        $this->models[$modelDocument->getName()] = $modelDocument;
        return $this;
    }
    
    /**
     * Retorna el modelo de un documento por su nombre
     * @param type $name
     * @return ModelDocument
     * @throws RuntimeException
     */
    public function getModel($name) {
        if(!isset($this->models[$name])){
            throw new RuntimeException(sprintf("The model document name '%s' is not added in chain model '%s'",$name,$this->id));
        }
        $this->models[$name]->setContainer($this->container);
        return $this->models[$name];
    }
    
    public function getClassName() {
        return $this->className;
    }

    public function getModels() {
        $models = [];
        foreach ($this->models as $k => $v) {
            $models[] = $this->getModel($k);
        }
        return $models;
    }
    
    public function setExporterManager(ExporterManager $exporterManager) {
        $this->exporterManager = $exporterManager;
        return $this;
    }
    
    public function getExporterManager() {
        return $this->exporterManager;
    }
    
    public function getId() {
        return $this->id;
    }
    
    /**
     * Retorna el directorio de salida de los documentos del model
     * @return string
     */
    public function getDirOutput() {
        $ds = DIRECTORY_SEPARATOR;
        $id = $this->getId();
        $documentsPath = $this->exporterManager->getOption("documents_path");
        $env = $this->exporterManager->getOption("env");
        if($this->basePath === null){
            $fullPath = $documentsPath.$ds.$env.$ds.$id;
        }else{
            $fullPath = $this->basePath;
        }
        if($this->subPath !== null){
            $fullPath .= $ds.$this->subPath;
        }
        
        if(!$this->exporterManager->getFs()->exists($fullPath)){
            $this->exporterManager->getFs()->mkdir($fullPath);
        }
        return $fullPath;
    }
    
    /**
     * Obtiene los archivos en la carpeta del chain en el modulo
     * @return \AppBundle\Model\Core\Exporter\Finder
     */
    public function getFiles() {
        $dir = $this->getDirOutput();
        $finder = new \Symfony\Component\Finder\Finder();
        $finder->files()->in($dir);
        return $finder;
    }
    
    /**
     * Elimina un archivo dentro del modulo con su nombre
     * @param type $fileName
     * @throws RuntimeException
     */
    public function deleteFile($fileName) {
        $files = $this->getFiles();
        $success = false;
        foreach ($files as $file) {
            if($file->getRelativePathname() === $fileName){
                if(!$file->isWritable()){
                    throw new RuntimeException(sprintf("The file '%s' is not writable and can not be deleted.",$fileName));
                }
                unlink($file->getRealPath());
                if($file->isReadable()){
                    throw new RuntimeException(sprintf("The file '%s' could not be deleted",$fileName));
                }
                $success = true;
            }
        }
        return $success;
    }
    
    /**
     * Busca un archivo por su nombre en la carpeta del exportador
     * @param type $fileName
     * @return \Symfony\Component\Finder\SplFileInfo
     * @throws RuntimeException
     */
    public function getFile($fileName) {
        $files = $this->getFiles();
        $found = null;
        foreach ($files as $file) {
            if($file->getRelativePathname() === $fileName){
                if(!$file->isReadable()){
                    throw new RuntimeException(sprintf("The file '%s' could not be read.",$fileName));
                }
                $found = $file;
                break;
            }
        }
        return $found;
    }
    
    public function filePathToArray($id , \Symfony\Component\Finder\SplFileInfo $file) {
        $chain = $this;
        $fileName = $file->getFilename();
        $date = new \DateTime();
        $date->setTimestamp($file->getMTime());
        
        $deleteUrl = $this->container->get("router")->generate("core_exporter_delete",["idChain" => $chain->getId(),"id" => $id,"fileName" => rawurlencode($fileName) ]);
        $downloadUrl = $this->container->get("router")->generate("core_exporter_download",["idChain" => $chain->getId(),"id" => $id,"fileName" => rawurlencode($fileName) ]);
        return [
            "idChain" => $chain->getId(),
            "id" => $id,
            "fileName" => $fileName,
            "icon" => \Tecnocreaciones\Bundle\ToolsBundle\Service\ToolsUtils::iconExtension($file->getExtension()),
            "date" => ($date->format('d/m/Y h:i a')),
            "deleteUrl" => $deleteUrl,
            "downloadUrl" => $downloadUrl,
        ];
    }
    
    /**
     * Establecer un sub-directorio donde guardar el documento
     * @param type $subPath
     * @return \Tecnocreaciones\Bundle\ToolsBundle\Model\Exporter\ChainModel
     */
    public function setSubPath($subPath) {
        $this->subPath = $subPath;
        return $this;
    }
    
    /**
     * Reemplazar ruta base del documento
     * @param type $basePath
     * @return \Tecnocreaciones\Bundle\ToolsBundle\Model\Exporter\ChainModel
     */
    public function setBasePath($basePath) {
        $this->basePath = $basePath;
        return $this;
    }
}
