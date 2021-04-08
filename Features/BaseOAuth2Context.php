<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Features;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Exception;
use LogicException;
use Symfony\Component\HttpFoundation\Response;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Tecnocreaciones\Bundle\ToolsBundle\Service\ToolsUtils;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Base para peticiones oauth2
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class BaseOAuth2Context implements Context
{

    use TraitContext;
    /**
     *
     * @var BaseDataContext
     */
    protected $dataContext;
    
    protected $serverParameters;
    protected $lastRequestBody;
    protected $parameters;
    protected $requestFiles;
    /**
     * Headers adicionales
     * @var array
     */
    protected $requestHeaders;

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
    
    /**
     * Localizador de archivos en %kernel.root_dir%/Resources
     * @var FileLocator
     */
    protected $fileLocator;
    
    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    protected $accessor;

    public function __construct(FileLocator $fileLocator)
    {
        $this->fileLocator = $fileLocator;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

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
        $this->requestHeaders = [];
    }

    /**
     * @Then echo last response
     */
    public function echoLastResponse()
    {
//        $this->printDebug(sprintf("Request:\n %s \n\n Response:\n %s", var_export($this->lastRequestBody, true), $this->response->getContent()));
        $content = $this->response->getContent();
        $this->printDebug(sprintf("Request:\n %s \n\n Response:\n %s", json_encode($this->lastRequestBody, JSON_PRETTY_PRINT, 10), $content));
        $contentJson = @json_encode(@json_decode($content,true),JSON_PRETTY_PRINT, 10);
        if($contentJson !== null && $contentJson !== "null" && json_last_error() === JSON_ERROR_NONE){
//            $content = $contentJson;
            echo(sprintf("Response pretty json:\n %s", $contentJson));
        }
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
     * Verifica que no exista propiedades
     * Ejemplo: And the response does not have "data.properties.ship_vessel,data.properties.vehicle_type" properties
     * @When the response does not have :properties properties
     */
    public function theResponseDoesNotHaveProperties($properties)
    {
        $propertiesName = explode(",", $properties);
        foreach ($propertiesName as $propertyName) {
            $this->theResponseDoesNotHaveProperty($propertyName);
        }
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
    
    /**
     * @When I log in with client credentials
     */
    public function iLogInWithClientCredentials() {
        $this->iCreateOAuth2Request();
        $this->dataContext->setRequestBody('grant_type', 'client_credentials');
        $this->iAddResourceOwnerCredentials();
        $this->iMakeAAccessTokenRequest();
        $this->theResponseStatusCodeIs('200');
        $this->theResponseHasTheOAuth2Format();
        $this->dataContext->getClient()->setServerParameter("HTTP_AUTHORIZATION", sprintf("Bearer %s", $this->getPropertyValue("access_token")));
    }
    
    /**
     * @When I log in with password
     */
    public function iLogInWithPassword() {
        $this->iCreateOAuth2Request();
        $this->dataContext->setRequestBody('grant_type', 'password');
        $this->iAddResourceOwnerCredentials();
        $this->iMakeAAccessTokenRequest();
        $this->theResponseStatusCodeIs('200');
        $this->theResponseHasTheOAuth2Format();
        $this->dataContext->getClient()->setServerParameter("HTTP_AUTHORIZATION", sprintf("Bearer %s", $this->getPropertyValue("access_token")));
    }
    
    /**
     * Limpia el token de acceso actual
     * @When I clear access token
     */
    public function iClearAccessToken()
    {
        $this->dataContext->getClient()->setServerParameter("HTTP_AUTHORIZATION",null);
    }
    
    /**
     * Agrega data tipo json al siguiente request
     * @Given I add the request data:
     */
    public function iAddTheRequestData(PyStringNode $string,$andSave = false) {
//        $string = $this->replaceParameters((string) $string);
//        $parameters = json_decode($string, true);
        $parameters = json_decode((string) $string, true);
        if ($parameters === null) {
            throw new Exception(sprintf("Json invalid: %s, %s", json_last_error_msg(), json_last_error()));
        }
        $this->dataContext->replaceParameters($parameters);
        foreach ($parameters as $key => $row) {
            $this->dataContext->setRequestBody($key, $row);
        }
        if($andSave === true){
            $this->lastRequestBodySave = $this->dataContext->getRequestBody();//$parameters;
        }else{
            $this->lastRequestBodySave = null;
        }
    }

    public function saveLastRequestBody()
    {
        $this->lastRequestBodySave = $this->dataContext->getRequestBody();
    }
    
     /**
     * @Given that I have an refresh token
     */
    public function thatIHaveAnRefreshToken() {
        $this->dataContext->createClient();
        $parameters = $this->parameters['oauth2'];
        $parameters['grant_type'] = 'password';
        $url = $this->parameters['token_url'];
        $this->dataContext->getClient()->request('GET', $url, $parameters);
        $response = $this->dataContext->getClient()->getResponse();
        $data = json_decode($response->getContent(), true);
        if (!isset($data['refresh_token'])) {
            throw new Exception(sprintf("Error refresh token. Response: %s", $response->getContent()));
        }
        $this->refreshToken = $data['refresh_token'];
    }
    
    /**
     * @When I make a access token request with given refresh token
     */
    public function iMakeAAccessTokenRequestWithGivenRefreshToken() {
        $this->dataContext->setRequestBody('refresh_token', $this->refreshToken);
        $this->iMakeAAccessTokenRequest();
    }
    
        /**
     * Ejecuta un request con la ultima data enviada en otro request
     * @example When I send last request body to "POST /api/payment-intent/execute/request.json"
     * @When I send last request body to :fullUrl
     */
    public function iSendLastRequestBodyTo($fullUrl)
    {
        $this->iRequest($fullUrl, $this->lastRequestBodySave);
    }
    /**
     * Realiza una peticion a la API Rest
     * @When I request :fullUrl with options :options
     */
    public function iRequestOptions($fullUrl, $options) {
        $options = json_decode($options, true);
        $this->iRequest($fullUrl,null,null,$options);
    }
    
    /**
     * Realiza una peticion a la API Rest
     * @When I request :fullUrl
     */
    public function iRequest($fullUrl, array $parameters = null, array $files = null,array $options = [])
    {
        $this->dataContext->setScenarioParameter("%lastUrlRequest%",$fullUrl);

        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            "clear_request" => true,
        ]);
        $options = $resolver->resolve($options);
        $explode = explode(" ", $fullUrl);
        $method = $explode[0];
        $url = $explode[1];
        if ($parameters === null) {
            $parameters = $this->dataContext->getRequestBody();
        }
        if ($files === null) {
            $files = $this->requestFiles;
        }
        if ($parameters === null) {
            $parameters = [];
        }
        if ($files === null) {
            $files = [];
        }
        $server = $this->requestHeaders;
        if(!is_array($server)){
            $server = [];
        }
        
        $this->dataContext->getClient()->request($method, $url, $parameters, $files,$server);
        $this->response = $this->dataContext->getClient()->getResponse();
        $_server = $this->response->headers->get("_server");//Headers especiales
        if($_server !== null){
            echo sprintf("Response headers: %s",$_server);
        }
        if($options["clear_request"] === true){
            $this->initRequest();
        }
        $contentType = (string) $this->response->headers->get('Content-type');
        if ($this->response->getStatusCode() != 404 && !empty($contentType) && $contentType !== 'application/json') {
            throw new \Exception(sprintf("Content-type must be application/json received '%s' \n%s", $contentType, $this->echoLastResponse()));
        }
        $content = $this->response->getContent();
        $this->data = [];
        if ($content) {
            $this->data = json_decode($this->response->getContent(), true);
            $this->lastErrorJson = json_last_error();
            if ($this->response->getStatusCode() != 404 && $this->lastErrorJson != JSON_ERROR_NONE) {
                throw new \Exception(sprintf("Error parsing response JSON " . "\n\n %s", $this->echoLastResponse()));
            }
        }
        if (isset($this->data["id"])) {
            $this->dataContext->setScenarioParameter("%lastId%", $this->data["id"]);
        }
        $this->dataContext->setScenarioParameter("request",$this->data);
        $this->dataContext->setScenarioParameter("%lastResponse%",$this->data,true);
        $this->dataContext->restartKernel();
        return $this->data;
    }
    
    /**
     * Verifica que exista un mensaje de error
     * @example And the response has a errors property and contains "validators.not_blank.driver.service_status"
     * @Then the response has a errors property and contains :message
     */
    public function theResponseHasAErrorsPropertyAndContains($message) {
        $message = $this->dataContext->parseParameter($message, [], "validators");
        $errors = $this->getPropertyValue("errors");
        $found = false;
        $internalErrors = isset($errors['errors']) ? $errors['errors'] : [];
        if (is_array($internalErrors)) {
            foreach ($internalErrors as $error) {
                if ($error === $message) {
                    $found = true;
                    break;
                }
            }
        } else {
            throw new Exception(sprintf("The error property no contains error message. '%s' \n \n %s", $message, var_export($internalErrors, true), $this->echoLastResponse()));
        }
        if ($found === false) {
            throw new Exception(sprintf("The error response no contains error message '%s', response with '%s'", $message, implode(",", $internalErrors)));
        }
    }
    
    /**
     * Verifica que en la propiedad global contiene un error
     * @example Then the response has a errors in property "nombre"
     * @Then the response has a errors in property :propertyName
     */
    public function theResponseHasAErrorsInProperty($propertyName) {
        $this->theResponseHasAErrorsInPropertyAndContains($propertyName);
    }
    
    /**
     * Verifica que existan errores en el response separados por coma
     * Ejemplo: Then the response has a errors in properties "bodywork_serial,engine_serial,vehicle_color,seats"
     * @Then the response has a errors in properties :properties
     */
    public function theResponseHasAErrorsInProperties($properties)
    {
        $properties = explode(",", $properties);
        foreach ($properties as $propertyName) {
            $this->theResponseHasAErrorsInProperty($propertyName);
        }
    }

    /**
     * Verifca que una propiedad no contenga un error con un mensaje especifico
     * @Then the response has a errors in property :propertyName and not contains :message
     */
    public function theResponseHasAErrorsInPropertyAndNotContains($propertyName, $message = null) {
        $this->theResponseHasAErrorsInPropertyAndContains($propertyName,$message,true);
    }
    
    /**
     * Verifica que una propiedad este en la respuesta pero no tenga ningun error
     * Ejemplo: And the response has property "billingAddresses.0.address2" and not contains any errors
     * @Then the response has property :arg1 and not contains any errors
     */
    public function theResponseHasPropertyAndNotContainsAnyErrors($propertyName)
    {
        try {
            $this->theResponseHasAErrorsInPropertyAndContains($propertyName);
        } catch (Exception $ex) {
            if($ex->getCode() === 1000){
                //fino, no tiene errores
            }else{
                throw $ex;
            }
        }
    }
    
    /**
     * Verifica que una propiedad x contiene un error
     * @example And the response has a errors in property "number" and contains "El número de la cuenta bancaria debe tener minimo 19 digitos."
     * @Then the response has a errors in property :propertyName and contains :message
     */
    public function theResponseHasAErrorsInPropertyAndContains($propertyName, $message = null,$negate = false) {
        $properties = explode(".", $propertyName);
        $errors = $this->getPropertyValue("errors");
        $children = $errors["children"];
        if (count($properties) > 1) {
            $data = $children;
            foreach ($properties as $property) {
//                echo(var_export($data,true));
                if (isset($data[$property]) && isset($data[$property]["children"])) {
                    $data = $data[$property]["children"];
                }
                if (isset($data[$property])) {
                    if(!isset($data[$property]["errors"])){
                        $data[$property]["errors"] = [];
                    }
                    $children = $data;
                    $propertyName = $property;
                    break;
                }
            }
        }
        if (!isset($children[$propertyName])) {
            $this->echoLastResponse();
            throw new Exception(sprintf("The response no contains error property '%s' \n Available are %s", $propertyName, implode(", ", array_keys($children))));
        }
        $message = $this->dataContext->parseParameter($message, [], 'validators');
        if (isset($children[$propertyName]["errors"]) && count($children[$propertyName]["errors"]) > 0) {
            if ($message === null) {
                if (count($children[$propertyName]["errors"]) == 0) {
                    throw new Exception(sprintf("The error property no contains errors in '%s', response with '%s'", $propertyName, json_encode($errors, JSON_PRETTY_PRINT)));
                }
            } else {
                $found = false;
                foreach ($children[$propertyName]["errors"] as $error) {
                    if ($error === $message) {
                        $found = true;
                        break;
                    }
                }
                if ($negate === false && $found === false) {
                    throw new Exception(sprintf("The error property no contains error '%s' message in '%s', response with '%s'",$message,$propertyName, implode(", ", $children[$propertyName]["errors"])));
                }else if ($negate === true && $found === true) {
                    throw new Exception(sprintf("The error property contains error message '%s', response with '%s'", $propertyName, implode(", ", $children[$propertyName]["errors"])));
                }
            }
        } else {
            throw new Exception(sprintf("The error property no contains errors in '%s', response with '%s'", $propertyName, json_encode($errors, JSON_PRETTY_PRINT)),1000);
        }
    }

    /**
     * Afirma que la respuesta es un paginador
     * @Then the response is a paginator
     */
    public function theResponseIsAPaginator() {
        $this->theResponseHasAProperty("links");
        $this->theResponseHasAProperty("meta");
        $this->theResponseHasAProperty("data");
        \assertCount(3, $this->data);
//        echo json_encode($this->data,JSON_PRETTY_PRINT);
    }

    /**
     * Verifica que el ultimo resultado tenga unas propiedades separadas por coma (id,name,description)
     * @example And the response has a "id,number,secure_label,email,bank,alias,digital_account" properties
     * @Then the response has a :propertiesName properties
     */
    public function theResponseHasAProperties($propertiesName) {
        $propertiesName = explode(",", $propertiesName);
        foreach ($propertiesName as $propertyName) {
            $this->theResponseHasAProperty($propertyName);
        }
    }
    
     /**
     * Agrega archivos a partir del json al siguiente request
     * @Given I add the request files:
     */
    public function iAddTheRequestF1iles(PyStringNode $string) {
//        $string = $this->replaceParameters((string) $string);
        $parameters = json_decode((string) $string, true);
        if ($parameters === null) {
            throw new \Exception(sprintf("Json invalid: %s, %s", json_last_error_msg(), json_last_error()));
        }
        $this->dataContext->replaceParameters($parameters);
        foreach ($parameters as $key => $row) {
            $this->requestFiles[$key] = $row;
        }
    }

    /**
     * Prepara un archivo y lo añade a un parametro para ser enviado por POST
     * @Given a file :filepath name :nameParameter
     */
    public function aFileName($filepath, $nameParameter) {
//        $filename = $this->getKernel()->locateResource($filepath);//deprecated
        $filename = $this->fileLocator->locate($filepath);
        $file = new \Symfony\Component\HttpFoundation\File\File($filename);
        if (!$file->isFile() || !$file->isReadable()) {
            throw new Exception(sprintf("The file '%s' is not exist or is not readable", $filename));
        }
        $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $file->getBasename();
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->copy($filename, $tmpFile);
        if (!$fs->exists($tmpFile)) {
            throw new Exception(sprintf("The tmp file '%s' is not exist", $tmpFile));
        }

        $file = new UploadedFile(
                $tmpFile, $file->getFilename(), $file->getMimeType(), $file->getSize()
        );
        $this->dataContext->setScenarioParameter($nameParameter, $file);
    }
    
    /**
     * Busca una propiedad y verifica que la cantidad de elmentos sea la deseada
     * @example Then the response has a "transactions.0.pay_tokens" property and contains "= 0" values
     * @Then the response has a :propertyName property and contains :expresion values
     */
    public function theResponseHasAPropertyAndContainsValues($propertyName, $expresion) {
        $this->theResponseHasAPropertyAndItsTypeIs($propertyName, "array");
        $value = $this->theResponseHasAProperty($propertyName);
        $expresionExplode = explode(" ", $expresion);
        $quantity = count($value);
        if (version_compare($quantity, (int) $expresionExplode[1], $expresionExplode[0]) === false) {
            throw new Exception(sprintf("Expected '%s' but there is '%s' elements.\n%s", $expresion, $quantity, var_export($value, true)));
        }
    }
    
    /**
     * @example And the response its type is "array" and contains "= 1" values
     * @Then the response its type is :typeString and contains :expresion values
     * @Then the response its type is :typeString
     */
    public function theResponseItsTypeIsAndContainsValues($typeString, $expresion = null) {
        $value = $this->data;

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
        
        if ($expresion !== null) {
            $quantity = count($value);
            ToolsUtils::testQuantityExp($expresion, $quantity);
        }
    }
    
    /**
     * Agrega un header al siguiente request
     * Ejemplo: And I add header "HTTP_USER_AGENT" value "Google-HTTP-Java-Client"
     * @Given I add header :header value :value
     */
    public function iAddHeaderValue($header, $value)
    {
        if(!is_array($this->requestHeaders)){
            $this->requestHeaders = [];
        }
        $this->requestHeaders[$header] = $value;
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
