<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\HttpGuzzle\Handler;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;

/**
 * Manejador de peticiones con guzzle para http nativo
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class NativeHandler extends AbstractHandler
{
    public function doRequest($request, $location)
    {
        $method = $this->options["method"];
        $method = strtoupper($method);
        $client = new Client([
            'timeout' => $this->options["timeout"],
            'base_uri' => $this->options["base_uri"],
            'connect_timeout' => $this->options["connect_timeout"],
            "verify" => $this->options["verify_ssl"],
            "debug" => $this->options["debug"],
        ]);
        $headers = [];
        $response = null;
        $this->statusCode = 0;
        if (false) {
            $response = new Response();
        }
        $options = [
//            'body' => $request,
        ];
        if(count($headers) > 0){
            $options["headers"] = $headers;
        }
        if(isset($this->options["query"])){
            $options["query"] = $this->options["query"];
        }
        if($request){
            $options["form_params"] = $request;
        }
//        var_dump($method);
//        var_dump($options);
//        die;
        try {
            $response = $client->request(
                    $method,
                    $location,
                    $options
            );

            $this->lastResponse = $response;
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $this->lastResponse = $response;
            $this->errorMessage = $e->getMessage();
            if ($this->options["exceptions"] === true) {
                throw $e;
            }
            $context = $e->getHandlerContext();
            if (is_array($context) && isset($context["errno"]) && $context["errno"] == 28) {
                if ($e instanceof ConnectException) {
                    $this->statusCode = self::STATUS_CONNECTION_TIMEOUT;
                } else {
                    $this->statusCode = self::STATUS_REQUEST_TIMEOUT;
                }
            }
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
            if ($this->options["exceptions"] === true) {
                throw $e;
            }
        }

        $body = null;
        if ($response) {
            $this->statusCode = $response->getStatusCode();
            if ($response->getStatusCode() === 200) {
                $body = (string) $response->getBody();
            }
        }
//        var_dump($body);
//        var_dump($this->statusCode);
//        die;
        return $body;
    }
}
