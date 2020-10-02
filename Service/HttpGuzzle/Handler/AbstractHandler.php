<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\HttpGuzzle\Handler;

use GuzzleHttp\Psr7\Response;

/**
 * Base de manejador de peticiones
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class AbstractHandler
{
    /**
     * Estatus http: Error estableciendo conexion con el cliente
     */
    const STATUS_CONNECTION_TIMEOUT = 504;
    /**
     * Estatus http: El cliente tardo mucho en responder la peticion
     */
    const STATUS_REQUEST_TIMEOUT = 408;
    /**
     * Estatus http: Error de validacion
     */
    const STATUS_REQUEST_ERROR = 400;
    /**
     * Estaus: Todo bien
     */
    const STATUS_OK = 200;
    
    /**
     * Opciones de peticion
     * @var array
     */
    protected $options;
    
    /**
     * Respuesta de la peticion
     * @var Response 
     */
    protected $lastResponse;
    
    /**
     * Codigo de estatus
     * @var integer
     */
    protected $statusCode;
    
     /**
     * Mensaje de error
     * @var string
     */
    protected $errorMessage;
    
    /**
     * Ejecuta la peticion
     */
    public abstract function doRequest($request, $location);
    
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }
    
    /**
     * Codigo de respuesta de la ultima peticion
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
    
    /**
     * Retorna el ultimo mensaje de error registrado
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
    
    /**
     * ¿Peticion exitosa?
     * @return bool
     */
    public function isSuccess()
    {
        return $this->statusCode === 200;
    }
    
    /**
     * Obtiene la ultima respuesta
     * @return Response|null
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }
    
    /**
     * La ultima peticion dio timeout?
     * @return bool
     */
    public function isTimeout()
    {
        return in_array($this->statusCode, [self::STATUS_CONNECTION_TIMEOUT,self::STATUS_REQUEST_TIMEOUT]);
    }
    
    /**
     * Retorna el ultimo cuerpo de la peticion realizada
     * @return string|null
     */
    public function getLastBody()
    {
        if($this->lastResponse){
            return (string)$this->lastResponse->getBody();
        }
    }
}
