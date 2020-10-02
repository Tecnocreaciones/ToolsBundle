<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\HttpGuzzle;

use Tecnocreaciones\Bundle\ToolsBundle\Service\HttpGuzzle\Handler\AbstractHandler;
use RuntimeException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tecnoready\Common\Util\ConfigurationUtil;

/**
 * Base de cliente http con guzzle
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class BaseClientGuzzle
{
    /**
     * Opciones de peticion
     * @var array
     */
    protected $options;
    
    /**
     * Data de la peticion a enviar
     * @var object
     */
    protected $request;
    
    /**
     * Ultima peticion enviada (parseada con los parametros)
     * @var string
     */
    protected $lastRequest;

    /**
     * Manejador de peticiones
     * @var AbstractHandler 
     */
    protected $handler;
    
    public function __construct(array $options = [])
    {
        ConfigurationUtil::checkLib("guzzlehttp/guzzle");
        ConfigurationUtil::checkLib("optionsResolver");
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'connect_timeout' => 0, //Tiempo de espera por establecer la conexion
            'timeout' => 15, //Tiempo de espera de respuesta
            "verify_ssl" => false, //Verificar ssl
            "debug" => false, //Mostrar debug del client guzzle
            "exceptions" => false, //Lanzar excepcione
            "base_uri" => null,//Base para las peticiones
            "handler" => null,
        ]);
        $resolver->setAllowedTypes("timeout", "integer");
        $resolver->setAllowedTypes("connect_timeout", "integer");
        $resolver->setAllowedTypes("verify_ssl", "bool");
        $resolver->setAllowedTypes("debug", "bool");
        $resolver->setAllowedTypes("exceptions", "bool");
        $resolver->setRequired(["default_handler"]);
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
        
        $this->handler = $this->options["handler"];
        unset($this->options["handler"]);
        if($this->handler === null){
            $this->handler = $this->options["default_handler"];
        }
    }
    
    protected function configureOptions(OptionsResolver $resolver)
    {
        
    }
    
    /**
     * Establece la data a enviar en el request y los parametros dentro del contenido.
     * @param type $request
     * @param array $parameters
     * @return $this
     * @throws RuntimeException
     */
    public function setRequest($request,array $parameters = [])
    {
        $invalidKey = [];
        foreach ($parameters as $key => $value) {
            $pattern = '/'.$key.'/i';
            $count = 0;
            $request = preg_replace($pattern, $value, $request,-1,$count);
            if($count === 0){
                $invalidKey[] = $key;
            }
        }
        if(count($invalidKey) > 0){
            throw new RuntimeException(sprintf("Los parametros %s no estan definidos en el cuerpo de la peticion. \n\n%s", implode(", ", $invalidKey),$request));
        }
        $this->request = $request;
        return $this;
    }
    
    /**
     * Realiza la peticion con el handler
     * @param type $request
     * @param type $uri
     * @param type $action
     * @param type $version
     * @return string
     */
    protected function doRequest($request, $uri,$options)
    {
        $this->handler->setOptions($options);
        $body = $this->handler->doRequest($request, $uri);
        return $body;
    }
    
    /**
     * ¿Peticion exitosa?
     * @return bool
     */
    public function isSuccess()
    {
        return $this->handler->isSuccess();
    }
    
    /**
     * Codigo de respuesta de la ultima peticion
     * @return int
     */
    public function getStatusCode()
    {
        return (int)$this->handler->getStatusCode();
    }

    /**
     * Retorna el ultimo mensaje de error registrado
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->handler->getErrorMessage();
    }

    /**
     * Data de la ultima peticion
     * @param bool $clear ¿Eliminar espacios?
     * @return string
     */
    public function getLastRequest($clear = false)
    {
        $lastRequest = $this->lastRequest;
        if($clear){
            $lastRequest = trim(preg_replace('/\s+/', ' ', $lastRequest));
        }
        return $lastRequest;
    }
    
    /**
     * Obtiene la ultima respuesta
     * @return string|null
     */
    public function getLastResponse()
    {
        $response = null;
        if($this->handler->getLastResponse()){
            $response = $this->handler->getLastResponse()->getBody();
        }
        return $response;
    }

    /**
     * La ultima peticion dio timeout?
     * @return bool
     */
    public function isTimeout()
    {
        return $this->handler->isTimeout();
    }
    
    /**
     * Retorna la ultima url llamada
     * @return string
     */
    public function getLastUri()
    {
        return $this->options["base_uri"].($this->options["uri"] ?? $this->options["uri"]);
    }
    
    /**
     * Obtiene el manejador de la peticion
     * @return AbstractHandler
     */
    public function handler(): AbstractHandler
    {
        return $this->handler;
    }
}
