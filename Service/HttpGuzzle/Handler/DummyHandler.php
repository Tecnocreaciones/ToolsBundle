<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\HttpGuzzle\Handler;

use RuntimeException;
use GuzzleHttp\Psr7\Response;

/**
 * Simulador de peticiones y respuestas
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DummyHandler extends AbstractHandler
{
    /**
     * Nombre del scenario a ejecutar
     * @var string
     */
    public static $scenarioName = null;
    
    /**
     * Data de prueba
     * @var array
     */
    private $dataDummy;
    
    /**
     * Agrega data dummy para usar en escenarios de prueba
     * @param type $scenarioName
     * @param type $request
     * @param type $dataResponse
     * @param type $responseCode
     * @return $this
     * @throws RuntimeException
     */
    public function addDummy($scenarioName,$request,$dataResponse,$responseCode)
    {
        if(isset($this->dataDummy[$scenarioName])){
            throw new RuntimeException(sprintf("El scenario de prueba '%s' ya existe.",$scenarioName));
        }
        $this->dataDummy[$scenarioName] = [
            "scenario_name" => $scenarioName,
            "request" => $request,
            "response" => $dataResponse,
            "statusCode" => $responseCode,
        ];
        
        return $this;
    }
    
    /**
     * Retorna la data de un scenario
     * @param type $scenarioName
     * @return type
     * @throws RuntimeException
     */
    private function getDummyData($scenarioName)
    {
        if(!isset($this->dataDummy[$scenarioName])){
            throw new RuntimeException(sprintf("El scenario de prueba '%s' no existe.",$scenarioName));
        }
        return $this->dataDummy[$scenarioName];
    }
    
    public function doRequest($request, $location)
    {
        //Parametro cuando el request es por API
        $scenario = self::$scenarioName;
        self::$scenarioName = null;
        if(empty($scenario)){
            throw new RuntimeException(sprintf("Se debe configurar el nombre del escenario a probar."));
        }
        $data = $this->getDummyData($scenario);
        $body = $data["response"];
        $this->statusCode = $data["statusCode"];
        
        $this->lastResponse = new Response($this->statusCode, [], $body);
        return $data["response"];
    }
    
}
