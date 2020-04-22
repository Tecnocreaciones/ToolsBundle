<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use RuntimeException;

/**
 * Manejador de imagenes para generar URLs absolutas y retornar imagenes por defecto
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ImageManager
{
    /**
     * @var \Vich\UploaderBundle\Templating\Helper\UploaderHelper
     */
    private $uploaderHelper;
    
    /**
     * Construye url abtolutas
     * @var \Symfony\Component\HttpFoundation\UrlHelper
     */
    private $urlHelper;
    
    /**
     * @var \Liip\ImagineBundle\Imagine\Cache\CacheManager
     */
    private $cacheManager;
    
    /**
     * @var PropertyAccessor 
     */
    private $accessor;
    
    /**
     * Opciones
     * @var araay
     */
    private $options;

    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(["public_dir"]);
        $this->options = $resolver->resolve($options);
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }
    
    /**
     * Genera una url una imagen guardada con Vich
     * @param type $entity
     * @param type $property
     * @param array $options
     * @return type
     */
    public function generateUrl($entity,$property,array $options = [])
    {
        if($this->cacheManager === null){
            throw new RuntimeException("Debe instalar y configurar 'Liip\ImagineBundle\LiipImagineBundle' para usar este servicio. https://symfony.com/doc/master/bundles/LiipImagineBundle/installation.html#step-1-download-the-bundle");
        }
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            "default" => null,
            "filter_default" => "my_thumb",
        ]);
        $options = $resolver->resolve($options);
        
        $url = $this->generateAbsoluteUrl($entity, $property);

        if(empty($url) && !empty($options["default"])){
            $url = $this->cacheManager->getBrowserPath($options["default"],$options["filter_default"]);
        }
        return $url;
    }
    
    /**
     * Genera una url absoluta de un archivo subido con vich_uploader
     * @param type $entity
     * @param type $property
     * @return string
     */
    private function generateAbsoluteUrl($entity,$property)
    {
        $url = null;
        $path = $this->uploaderHelper->asset($entity,$property);
        
        $publicDir = $this->options["public_dir"];
        if(!is_readable($publicDir.$path)){
            $path = null;
        }
        //Intentar buscar la ruta absoluta del archivo
        if($path === null){
            $property = str_replace("File","", $property);//Se elimina la palabra "File" para acceder a la propiedad correcta
            if($this->accessor->isReadable($entity, $property)){
                $valueProperty = $this->accessor->getValue($entity, $property);
                if(!empty($valueProperty)){
                    $propertyValue = DIRECTORY_SEPARATOR.$valueProperty;
                    if(is_readable($publicDir.$propertyValue)){
                        $path = $propertyValue;
                    }
                }
            }
        }
        if(!empty($path)){
            $url = $this->urlHelper->getAbsoluteUrl($path);
        }
        return $url;
    }
    
    /**
     * @required
     * @param \Vich\UploaderBundle\Templating\Helper\UploaderHelper $uploaderHelper
     * @return $this
     */
    public function setUploaderHelper(\Vich\UploaderBundle\Templating\Helper\UploaderHelper $uploaderHelper)
    {
        $this->uploaderHelper = $uploaderHelper;
        return $this;
    }
    
    /**
     * @required
     * @param \Symfony\Component\HttpFoundation\UrlHelper $urlHelper
     * @return $this
     */
    public function setUrlHelper(\Symfony\Component\HttpFoundation\UrlHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
        return $this;
    }

    /**
     * @required
     * @param \Liip\ImagineBundle\Imagine\Cache\CacheManager $cacheManager
     * @return $this
     */
    public function setCacheManager(\Liip\ImagineBundle\Imagine\Cache\CacheManager $cacheManager = null)
    {
        $this->cacheManager = $cacheManager;
        return $this;
    }
}
