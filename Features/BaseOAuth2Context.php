<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Features;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Exception;
use LogicException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base para peticiones oauth2
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class BaseOAuth2Context implements Context
{

    protected $serverParameters;
    protected $lastRequestBody;
    protected $parameters;

    /**
     *
     * @var Response
     */
    protected $response;

    /**
     * Respuesta de la ultima peticion http
     * @var array
     */
    protected $data;
    protected $kernel;

    public function setDataContext(BaseDataContext $dataContext)
    {
        $this->dataContext = $dataContext;
        $this->dataContext->setOAuth2Context($this);
        $this->kernel = $this->dataContext->getKernel();
        return $this;
    }

    /**
     * Get property value from response data
     *
     * @param string $propertyName property name
     */
    public function getPropertyValue($propertyName)
    {
        return $this->getValue($propertyName, $this->data);
    }

    /**
     * Get property value from data
     *
     * @param string $propertyName property name
     * @param mixed $data data as array or object
     */
    protected function getValue($propertyName, $data)
    {
        if ($data === null) {
            throw new Exception(sprintf("Response was not set\n %s", var_export($data, true)));
        }

        $properties = explode(".", $propertyName);
        $totalProperties = count($properties);
        if (count($properties) > 1) {
            $data2 = $data;
            $i = 0;
            foreach ($properties as $property) {
                $i++;
                if (is_numeric($property)) {
                    $data2 = $data2[(int) $property];
                } else if (isset($data2[$property]) && is_array($data2[$property]) && $i < $totalProperties) {
                    $data2 = $data2[$property];
                }
                if ($i == $totalProperties && isset($data2[$property])) {
                    $data = $data2;
                    $propertyName = $property;
                    break;
                }
            }
        }

        if (is_array($data) && array_key_exists($propertyName, $data)) {
            $data = $data[$propertyName];
            return $data;
        }
        if (is_object($data) && property_exists($data, $propertyName)) {
            $data = $data->$propertyName;
            return $data;
        }
        if (is_string($data)) {
            throw new LogicException(sprintf("The response is a string data, verify call 'I request' not 'I html request'."));
        }
        throw new LogicException(sprintf("Property '%s' is not set! \n%s", $propertyName, var_export($data, true)));
    }

    /**
     * Creo un request oauth2
     * @When I create oauth2 request
     */
    public function iCreateOAuth2Request()
    {
        $this->dataContext->createClient();
        $this->initRequest();
        $this->serverParameters = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json',
            'HTTP_HOST' => $this->getContainer()->getParameter("router.request_context.host"),
            'HTTPS' => ($this->getContainer()->getParameter("router.request_context.scheme") === "https" ? true : false),
        ];
        $this->dataContext->getClient()->setServerParameters($this->serverParameters);

        $this->dataContext->setRequestBody('client_id', $this->parameters['oauth2']['client_id']);
        $this->dataContext->setRequestBody('client_secret', $this->parameters['oauth2']['client_secret']);
    }

    /**
     * Inicializa los datos para un siguiente request
     */
    protected function initRequest()
    {
        $this->lastRequestBody = $this->dataContext->getRequestBody();
        $this->lastResponse = $this->response;
        $this->dataContext->initRequestBody();
        $this->requestFiles = [];
    }

    /**
     * @Then echo last response
     */
    public function echoLastResponse()
    {
//        $this->printDebug(sprintf("Request:\n %s \n\n Response:\n %s", var_export($this->lastRequestBody, true), $this->response->getContent()));
        $this->printDebug(sprintf("Request:\n %s \n\n Response:\n %s", json_encode($this->lastRequestBody, JSON_PRETTY_PRINT, 10), $this->response->getContent()));
    }

    /**
     * Agrego parametros al request
     * @When I add the request parameters:
     */
    public function iAddTheRequestParameters(TableNode $parameters)
    {
        if ($parameters !== null) {
            foreach ($parameters->getRowsHash() as $key => $row) {
                $row = trim($row);
                $row = $this->dataContext->parseParameter($row);
                $this->dataContext->setRequestBody($key, $row);
            }
        }
    }

    /**
     * @When I send a access token request
     */
    public function iMakeAAccessTokenRequest()
    {
        $url = $this->parameters['token_url'];
        $body = $this->dataContext->getRequestBody();
        $this->dataContext->getClient()->request("GET", $url, $body);
        $this->response = $this->dataContext->getClient()->getResponse();
        $contentType = (string) $this->response->headers->get('Content-type');
        $this->initRequest();
        if ($contentType !== 'application/json') {
            throw new \Exception(sprintf("Content-type must be application/json %s", $this->echoLastResponse()));
        }
        $this->data = json_decode($this->response->getContent(), true);
        $this->lastErrorJson = json_last_error();
        if ($this->lastErrorJson != JSON_ERROR_NONE) {
            throw new \Exception(sprintf("Error parsing response JSON " . "\n\n %s", $this->echoLastResponse()));
        }
    }

    /**
     * @Then the response status code is :httpStatus
     */
    public function theResponseStatusCodeIs($httpStatus)
    {
        if ((string) $this->response->getStatusCode() !== (string) $httpStatus) {
//            $this->echoLastResponse();
            throw new \Exception(sprintf("HTTP code does not match %s (actual: %s)\n\n %s", $httpStatus, $this->response->getStatusCode(), $this->echoLastResponse()));
        }
    }

    /**
     * Verifica que la ultima respuesta tenga una propiedad
     * @Then the response has a :propertyName property
     */
    public function theResponseHasAProperty($propertyName)
    {
        if ((isset($this->parameters['recommended'][$propertyName]) && !$this->parameters['recommended'][$propertyName])) {
            return;
        }
        if ((isset($this->parameters['optional'][$propertyName]) && !$this->parameters['optional'][$propertyName])) {
            return;
        }
        try {
            return $this->getPropertyValue($propertyName);
        } catch (\LogicException $e) {
            throw new \Exception(sprintf("%s\n\n %s", $e->getMessage(), $this->echoLastResponse()));
        }
    }

    /**
     * Verifica que la respuesta no tenga una propiedad en especifico
     * @Then the response does not have :propertyName property
     */
    public function theResponseDoesNotHaveProperty($propertyName)
    {
        try {
            $this->getPropertyValue($propertyName);
        } catch (\LogicException $e) {
            //La propiedad no existe
            return;
        }
        throw new \Exception(sprintf("Property %s is exists in response!\n\n %s", $propertyName, $this->echoLastResponse()));
    }

    /**
     * @Given the response has a :propertyName property and it is equals :propertyValue
     */
    public function theResponseHasAPropertyAndItIsEquals($propertyName, $propertyValue)
    {
        if ($this->dataContext->isScenarioParameter($propertyValue)) {
            $propertyValue = $this->dataContext->getScenarioParameter($propertyValue);
        }
        $propertyValue = $this->dataContext->parseParameter($propertyValue);
        $value = $this->theResponseHasAProperty($propertyName);
        if ($value == $propertyValue) {
            return;
        }
        throw new \Exception(sprintf("Given %s value is not %s is equals to '%s'\n\n %s", $propertyName, $propertyValue, $value, $this->echoLastResponse()));
    }

    /**
     * Verifica que un campo sea de un tipo en especifico
     * @example And the response has a "files" property and its type is "array"
     * @Then the response has a :propertyName property and its type is :typeString
     */
    public function theResponseHasAPropertyAndItsTypeIs($propertyName, $typeString)
    {
        $value = $this->theResponseHasAProperty($propertyName);
        // check our type
        switch (strtolower($typeString)) {
            case 'numeric':
                if (is_numeric($value)) {
                    break;
                }
            case 'array':
                if (is_array($value)) {
                    break;
                }
            case 'null':
                if ($value === NULL) {
                    break;
                }
            default:
                throw new \Exception(sprintf("Property %s is not of the correct type: %s!\n\n %s", $propertyName, $typeString, $this->echoLastResponse()));
        }
    }

    /**
     * @Then the response is oauth2 format
     */
    public function theResponseHasTheOAuth2Format()
    {
        $expectedHeaders = [
            'cache-control' => 'no-store, private',
            'pragma' => 'no-cache'
        ];
        foreach ($expectedHeaders as $name => $value) {
            if ($this->response->headers->get($name) != $value) {
                throw new \Exception(sprintf("Header %s is should be %s, %s given", $name, $value, $this->response->headers->get($name)));
            }
        }
    }
    
    /**
     * @When I add resource owner credentials
     */
    public function iAddResourceOwnerCredentials() {
        $this->dataContext->setRequestBody('username', $this->parameters['oauth2']['username']);
        $this->dataContext->setRequestBody('password', $this->parameters['oauth2']['password']);
    }
    
    protected function trans($id, array $parameters = array(), $domain = 'flashes') {
        return $this->getContainer()->get('translator')->trans($id, $parameters, $domain);
    }

    public function getKernel()
    {
        return $this->kernel;
    }

    public function getContainer()
    {
        return $this->kernel->getContainer();
    }
    
        /**
     * Prints beautified debug string.
     *
     * @param string $string debug string
     */
    protected function printDebug($string)
    {
        echo sprintf("\n\033[36m| %s\033[0m\n\n", strtr($string, ["\n" => "\n|  "]));
    }

}
