<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Exporter;

use Symfony\Component\Filesystem\Filesystem;
use InvalidArgumentException;

/**
 * Modelo de exportador de documento base
 */
abstract class ModelDocument
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    
    /**
     * @var ChainModel
     */
    private $chainModel;
    
    /**
     * Nombre a mostrar
     * @var string 
     */
    private $name;
    /**
     * Nombre que se usara para generar el archivo, si es null se toma el nombre.
     * @var string 
     */
    private $fileName;
    
    /**
     * Ruta del archivo contenido del documento
     * @var string
     */
    private $filePathContent = null;
    /**
     * Ruta del archivo de la cabecera del documento
     * @var string 
     */
    private $filePathHeader = null;
    
    /**
     * Ruta del archivo generado.<b>Importante:Setear la ruta de este archivo luego de crearlo con el write</b>
     * @var string
     */
    protected $pathFileOut;

    public function __construct($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }

    /**
     * Busca la ruta del archivo que se usara como contenido
     * @return type
     * @throws InvalidArgumentException
     */
    public function getFilePathContent() {
        if($this->filePathContent === null){
            throw new InvalidArgumentException(sprintf("The filePathContent must be setter."));
        }
        $path = $this->filePathContent;
        if($this->container){
            $path = $this->container->get("kernel")->locateResource($this->filePathContent);
        }
        if(!$this->chainModel->getExporterManager()->getFs()->exists($path)){
            throw new InvalidArgumentException(sprintf("The filePathContent '%s' does not exist.",$path));
        }
        return $path;
    }
    
    /**
     * Busca la ruta del archivo que se usara como encabezado
     * @return type
     * @throws InvalidArgumentException
     */
    public function getFilePathHeader() {
        if($this->filePathHeader === null){
            throw new InvalidArgumentException(sprintf("The filePathContent must be setter."));
        }
        $path = $this->filePathHeader;
        if($this->container){
            $path = $this->container->get("kernel")->locateResource($path);
        }
        if(!$this->chainModel->getExporterManager()->getFs()->exists($path)){
            throw new InvalidArgumentException(sprintf("The filePathHeader '%s' does not exist.",$path));
        }
        return $path;
    }
    
    /**
     * Hay ruta en el archivo de contenido?
     * @return boolean
     */
    public function hasFilePathContent() {
        return $this->filePathContent !== null;
    }
    /**
     * ¿Hay ruta de archivo de la cabecera?
     * @return boolean
     */
    public function hasFilePathHeader() {
        return $this->filePathHeader !== null;
    }
    
    public function setFilePathContent($filePathContent) {
        $this->filePathContent = $filePathContent;
        return $this;
    }

    public function setFilePathHeader($filePathHeader) {
        $this->filePathHeader = $filePathHeader;
        return $this;
    }

    /**
     * Retorna el directorio de salida del documento
     * @return string
     */
    protected function getDirOutput() {
        return $this->chainModel->getDirOutput();
    }
    
    /**
     * Retorna la ruta completa del archivo
     * @return string
     */
    protected function getDocumentPath(array $parameters = []) {
        $dirOut = $this->getDirOutput();
        $dirOut = $dirOut.DIRECTORY_SEPARATOR.$this->getFileNameTranslate($parameters).'.'.$this->getFormat();
        return $dirOut;
    }


    public function getNameTranslate() {
        return $this->container->get("translator")->trans($this->name,[],"labels");
    }
    
    public function getFileNameTranslate(array $parameters = []) {
        $parameters = [
        ];
        $name = $this->fileName;
        if(empty($name)){
            $name = $this->name;
        }
        return $this->container->get("translator")->trans($name,$parameters,"labels");
    }
    
    /**
     * Retorna el tipo de document (PDF,XLS,DOC,TXT)
     * @return string
     */
    public abstract function getFormat();
    
    /**
     * Escribe el archivo en el disco
     */
    public abstract function write(array $parameters = []);
    
    public function setChainModel(ChainModel $chainModel) {
        $this->chainModel = $chainModel;
        return $this;
    }
    
    public function setSubPath($subPath) {
        $this->subPath = $subPath;
        return $this;
    }

    function setFileName($fileName) {
        $this->fileName = $fileName;
    }
    
    /**
     * @return ChainModel
     */
    protected function getChainModel()
    {
        return $this->chainModel;
    }
}
