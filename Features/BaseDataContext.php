<?php

/*
 * This file is part of the Witty Growth C.A. - J406095737 package.
 * 
 * (c) www.mpandco.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Features;

use Behat\MinkExtension\Context\RawMinkContext;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

//Version vieja PHPUnit\Framework\Assert
if(!class_exists("PHPUnit\Framework\Assert") &&
        !class_exists("PHPUnit\Exception") && php_sapi_name() === 'cli'){
    $base = realpath(__DIR__."/../../../");
    $pathPhpunit = $base."/bin/.phpunit";
    if(!file_exists($pathPhpunit)){
        throw new Exception("No se detecto instalado phpunit, corra el comando './vendor/bin/simple-phpunit' para instalarlo.");
    }
    $finder = new \Symfony\Component\Finder\Finder();
    $finder->directories()->depth(0)->in($pathPhpunit);
    foreach ($finder as $file) {
        $loader = include $file->getRealPath()."/vendor/autoload.php";
        $loader->register();
        break;
    }
}

if(class_exists("PHPUnit_Framework_Exception")){
    $reflection = new \ReflectionClass("PHPUnit_Framework_Exception");
    require_once dirname($reflection->getFileName()) . '/Assert/Functions.php';
}else{
    $reflection = new \ReflectionClass("PHPUnit\Exception");
    require_once dirname($reflection->getFileName()) . '/Framework/Assert/Functions.php';
}

/**
 * Base de contexto para generar data
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class BaseDataContext extends RawMinkContext implements \Behat\Symfony2Extension\Context\KernelAwareContext {

    use \Behat\Symfony2Extension\Context\KernelDictionary;

    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    
    use TraitContext;

    /**
     *
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;
    
    /**
     * Clase de usuario
     * @var string 
     */
    protected $userClass;

    /**
     * Usuario logueado
     * @var UserInterface
     */
    protected $currentUser;
    protected $scenarioParameters;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    protected $accessor;
    
    protected $requestBody;

    /**
     * Faker.
     *
     * @var \Faker\Generator
     */
    protected $faker;
    
    /**
     * Funcion para hacer parse de parametros customs
     * @var callable
     */
    protected $parseParameterCallBack;

    public function __construct() {
        $this->accessor = PropertyAccess::createPropertyAccessor();
//        $this->faker = \Faker\Factory::create("es_VE");
        $this->requestBody = [];
    }

    /**
     * Sets Kernel instance.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel) {
        $this->kernel = $kernel;
        $this->container = $kernel->getContainer();
    }

    /**
     * Inicializa los parametros.
     * $this->scenarioParameters = [
            "%example%" => "valor",
            "%example%" => function() use ($self){
                return "valor";
            },
        ];
     */
    protected abstract function initParameters();

    /**
     * Crea un cliente para hacer peticiones
     * @return \Symfony\Component\BrowserKit\Client
     */
    public function createClient() {
        $this->client = $this->getKernel()->getContainer()->get('test.client');
        $client = $this->client;
        return $client;
    }

    /**
     * Realiza el parse de un string para traducirlo
     * validators.sale.registration.code_quantity_min::{"%n%":2}::validators seria un ejemplo
     * validators.sale.registration.code_quantity_min::{}::validators seria un ejemplo
     * @param type $id
     */
    protected function parseTrans($id, array $parameters = [], $domain = "flashes") {
        $text = $id;
        $separator = "::";
        $subSeparator = ";;";

        $textExplode = explode($separator, $id);
        if (is_array($textExplode)) {
            //id a traducir
            if (isset($textExplode[0])) {
                $text = $textExplode[0];
            }
            //Parametros de la traduccion
            if (isset($textExplode[1])) {
                $paramsString = $textExplode[1];
//                var_dump($paramsString);
                $parametersParsed = json_decode($paramsString, true);
                if (is_array($parametersParsed)) {
                    $this->parseScenarioParameters($parametersParsed);
                    foreach ($parametersParsed as $x => $v) {
                        if (strpos($v, $subSeparator) !== false) {
                            $v = str_replace($subSeparator, $separator, $v);
                            $parametersParsed[$x] = $this->parseTrans($v);
                        }
                    }
//                    var_dump($parametersParsed);
                    $parameters = $parametersParsed;
                }
            }
            //Dominio e la traduccion
            if (isset($textExplode[2])) {
                $domain = $textExplode[2];
            }
        }
        if (strpos($text, "|") && isset($parameters["{{ limit }}"])) {
            $trans = $this->container->get('translator')->transChoice($text, (int) $parameters["{{ limit }}"], $parameters, $domain);
        } else {
            $trans = $this->trans($text, $parameters, $domain);
        }
        return $trans;
    }

    /**
     * Busca los parametros dentro de un array por su indice y le hace el parse a su valor final.
     * @param array $parameters
     * @param type $checkExp
     */
    public function parseScenarioParameters(array &$parameters, $checkExp = false) {
        foreach ($parameters as $key => $value) {
            if ($this->isScenarioParameter($value, $checkExp)) {
                $parameters[$key] = $this->getScenarioParameter($value);
            }
        }
        return $parameters;
    }

    /**
     * Verifica si el texto es un parametro
     * @param type $value
     * @return boolean
     */
    public function isScenarioParameter($value, $checkExp = false) {
        if ($this->scenarioParameters === null) {
            $this->initParameters();
        }
        $result = isset($this->scenarioParameters[$value]);
        if (!$result) {
            if (substr($value, 0, 1) === "%" && substr($value, strlen($value) - 1, 1) === "%") {
                $result = true;
            }
        }
        if (!$result && $checkExp === true) {
            foreach ($this->scenarioParameters as $key => $v) {
                if (preg_match("/" . $key . "/", $value)) {
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Obtiene el valor de un parametro en el escenario
     * @param type $key
     * @return type
     * @throws Exception
     */
    public function getScenarioParameter($key, $checkExp = false) {
        $parameters = $this->getScenarioParameters();
        // var_dump(array_keys($parameters));
        $user = null;
        if ($this->currentUser) {
            $user = $this->find($this->userClass, $this->currentUser->getId());
        }
        $value = null;
        if (empty($key)) {
            xdebug_print_function_stack();
            throw new Exception("The scenario parameter can not be empty.");
        }
        if (isset($parameters[$key])) {
            if (is_callable($parameters[$key])) {
                $value = call_user_func_array($parameters[$key], [$user, $this]);
            } else {
                $value = $parameters[$key];
            }
        } else {
            $found = false;
            if ($checkExp === true) {
                foreach ($parameters as $k => $v) {
                    if (preg_match("/" . $k . "/", $key)) {
                        $value = str_replace($k, $this->getScenarioParameter($k), $key);
                        $found = true;
                        break;
                    }
                }
            }
            if (!$found) {
                throw new Exception(sprintf("The scenario parameter '%s' is not defined", $key));
            }
        }
        return $value;
    }

    /**
     * Busca una entidad
     * @param type $className
     * @param type $id
     * @return type
     */
    public function find($className, $id) {
        return $this->getDoctrine()->getManager()->find($className, $id);
    }

    public function setScenarioParameter($key, $value) {
        if ($this->scenarioParameters === null) {
            $this->initParameters();
        }
        if (is_array($value)) {
            foreach ($value as $subKey => $val) {
                $newKey = $key . "." . $subKey;
                $this->setScenarioParameter($newKey, $val);
            }
        } else {
            if (!$this->isScenarioParameter($key)) {
                $key = "%" . $key . "%";
            }
            $this->scenarioParameters[$key] = $value;
        }
    }

    public function refreshEntity($entity) {
        $em = $this->getDoctrine()->getManager();
        $em->refresh($entity);
    }

    public function detachEntity($entity) {
        $em = $this->getDoctrine()->getManager();
        $em->detach($entity);
    }

    /**
     * Genera un password estandar en base a un nombre de usuario
     * @param type $username
     * @return type
     */
    public function getPassword($username) {
        $pass = substr(md5($username), 0, 8) . '.5$';
        return $pass;
    }

    /**
     * Retorna los parametros definidos
     * @return type
     */
    private function getScenarioParameters() {
        if ($this->scenarioParameters === null) {
            $this->initParameters();
        }
        return $this->scenarioParameters;
    }

    protected function restartKernel() {
//        $kernel = clone ($this->kernel);
        $kernel = $this->getKernel();
        $kernel->shutdown();
        $kernel->boot();
        $this->setKernel($kernel);
    }

    /**
     * Permite cargar datos en una tabla de una entidad
     * Given there are operations in entity "Pandco\Bundle\AppBundle\Entity\App\BankTransaction":
      | username | password | email               |
      | everzet  | 123456   | everzet@knplabs.com |
      | fabpot   | 22@222   | fabpot@symfony.com  |
     * @Given there are operations in entity :entityClass:
     */
    public function thereAreOperationsInEntity($entityClass, TableNode $table) {
        foreach ($table as $row) {
            $entity = new $entityClass();
            foreach ($row as $propertyPath => $value) {
                $value = $this->parseParameter($value);
                $this->accessor->setValue($entity, $propertyPath, $value);
            }
            $this->saveEntity($entity, true);
        }
    }

    /**
     * Actualiza la configuracion del sistema
     * @example Given I set configuration 'WRAPPER_EPR_SALES' key "ENABLE_DIGITAL_INVOICING" with value "1"
     * @Given I set configuration :wrapperName key :key with value :value
     */
    public function iSetConfigurationKeyWithValue($wrapperName, $key, $value) {
        if ($value === "false") {
            $value = false;
        } else if ($value === "true") {
            $value = true;
        }
        $configurationManager = $this->container->get($this->container->getParameter("tecnocreaciones_tools.configuration_manager.name"));
        $wrapper = $configurationManager->getWrapper($wrapperName);
        $success = false;
        if ($this->accessor->isWritable($wrapper, $key) === true) {
            $success = $this->accessor->setValue($wrapper, $key, $value);
        } else {
            $success = $configurationManager->set($key, $value, $wrapperName, null, true);
        }
        if ($success === false) {
            throw new Exception(sprintf("The value of '%s' can not be update with value '%s'.", $key, $value));
        }

        $configurationManager->flush(true);
        if ($this->accessor->isReadable($wrapper, $key)) {
            $newValue = $this->accessor->getValue($wrapper, $key);
        } else {
            $newValue = $configurationManager->get($key, $wrapperName, null);
        }

        if ($value != $newValue) {
            throw new Exception(sprintf("Failed to update '%s' key '%s' with '%s' configuration.", $wrapperName, $key, $value));
        }
    }

    /**
     * Limia una tabla de la base de datos
     * @example Given a clear entity "Pandco\Bundle\AppBundle\Entity\EPR\Sales\SalesInvoice" table
     * @Given a clear entity :className table
     * @Given a clear entity :className table and where :where
     */
    public function aClearEntityTable($className, $andWhere = null) {
        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();
        if ($em->getFilters()->isEnabled('softdeleteable')) {
            $em->getFilters()->disable('softdeleteable');
        }
        if ($className === \Pandco\Bundle\AppBundle\Entity\User\DigitalAccount\TimeWithdraw::class) {
            $query = $em->createQuery("UPDATE " . \Pandco\Bundle\AppBundle\Entity\App\User\DigitalAccount\DigitalAccountConfig::class . " dac SET dac.timeWithdraw = null");
            $query->execute();
        }
        $query = $em->createQuery("DELETE FROM " . $className . " " . $andWhere);
        $query->execute();
        $em->flush();
        $em->clear();
    }

    public function flush() {
        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();
        $em->flush();
        try {
            $em->getConnection()->commit();
        } catch (Exception $exc) {
//            echo $exc->getMessage();
        }

        $em->clear();
    }

    public function saveEntity($entity, $andFlush = true) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        if ($andFlush) {
            $em->flush();
        }
    }

    /**
     * Cuenta la cantidad de elementos de una tabla
     * @example And the quantity of element in entity "Pandco\Bundle\AppBundle\Entity\Core\Email\EmailQueue" is "= 2"
     * @example And the quantity of element in entity "Pandco\Bundle\AppBundle\Entity\Core\Email\EmailQueue" is "> 0"
     * @Given the quantity of element in entity :className is :expresion
     */
    public function theQuantityOfElementInEntityIs($className, $expresion) {
        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();
        $query = $em->createQuery('SELECT COUNT(u.id) FROM ' . $className . ' u');
        $count = $query->getSingleScalarResult();
        $expAmount = explode(" ", $expresion);
        $amount2 = \Pandco\Bundle\AppBundle\Service\Util\CurrencyUtil::fotmatToNumber($expAmount[1]);
        if (version_compare($count, $amount2, $expAmount[0]) === false) {
            throw new Exception(sprintf("Expected '%s' but there quantity is '%s'.", $expresion, $count));
        }
    }

    /**
     * Devuelve el primer elemento que encuentr de la base de datos
     * @param type $class
     * @return type
     */
    public function findOneElement($class, UserInterface $user = null) {
        $em = $this->getDoctrine()->getManager();
        $alias = "o";
        $qb = $em->createQueryBuilder()
                ->select($alias)
                ->from($class, $alias);
        if ($user !== null) {
            $qb
                    ->andWhere("o.user = :user")
                    ->setParameter("user", $user)
            ;
        }
        $qb->orderBy("o.createdAt", "DESC");
        $entity = $qb->setMaxResults(1)->getQuery()->getOneOrNullResult();
        return $entity;
    }

    /**
     * Contruye un queryBuilder de una clase
     * @param type $class
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findQueryBuilder($class, $alias = "o") {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder()
                ->select($alias)
                ->from($class, $alias);
        return $qb;
    }

    public function replaceParameters(&$array) {
        $this->arrayReplaceRecursiveValue($array, $this->scenarioParameters);
    }

    /**
     * Reemplaza recursivamente los parametros en un array
     * @param type $array
     * @param type $parameters
     * @return type
     */
    private function arrayReplaceRecursiveValue(&$array, $parameters) {
        foreach ($array as $key => $value) {
            // create new key in $array, if it is empty or not an array
            if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
                $array[$key] = array();
            }

            // overwrite the value in the base array
            if (is_array($value)) {
                $value = $this->arrayReplaceRecursiveValue($array[$key], $parameters);
            } else {
                $value = $this->parseParameter($value, $parameters);
            }
            $array[$key] = $value;
        }
        return $array;
    }

    /**
     * Elimina un usuario de prueba
     * Ejemplo: Given I delete user "584140000011" for test
     * @Given I delete user :username for test
     */
    public function iDeleteUserForTest($username) {
        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();
        if ($em->getFilters()->isEnabled('softdeleteable')) {
            $em->getFilters()->disable('softdeleteable');
        }
        $query = $em->createQuery("DELETE FROM " . \Application\Sonata\UserBundle\Entity\User::class . " e WHERE e.username = '" . $username . "'");
        $query->execute();
        $em->flush();
//        $em->clear();
    }

    /**
     * Contruye un queryBuilder de una clase
     * @param type $class
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findQueryBuilderForClass($class, array $method = [], $queryResult = null) {
        $em = $this->getDoctrine()->getManager();
        $alias = "c";
        $qb = $em->createQueryBuilder()
                ->select($alias)
                ->from($class, $alias);
        foreach ($method as $key => $value) {
            $qb
                    ->andWhere(sprintf("%s.%s = :%s", $alias, $key, $key))
                    ->setParameter($key, $value)
            ;
        }
        if ($queryResult == "OneOrNull") {
            return $qb->getQuery()->getOneOrNullResult();
        } else {
            return $qb->getQuery()->getResult();
        }
    }

    /**
     * Executa un comando
     * @Given a execute command to :command
     */
    public function aExecuteCommandTo($command) {
        $this->restartKernel();
        $kernel = $this->getKernel();

        $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
        $application->setAutoExit(false);

        $exploded = explode(" ", $command);

        $commandsParams = [
        ];
        foreach ($exploded as $value) {
            if (!isset($commandsParams["command"])) {
                $commandsParams["command"] = $value;
            } else {
                $e2 = explode("=", $value);
//                var_dump($e2);
                if (count($e2) == 1) {
                    $commandsParams[] = $e2[0];
                } else if (count($e2) == 2) {
                    $commandsParams[$e2[0]] = $e2[1];
                }
            }
        }
        foreach ($commandsParams as $key => $value) {
            $commandsParams[$key] = $value;
        }
        $input = new \Symfony\Component\Console\Input\ArrayInput($commandsParams);
        if ($output === null) {
            $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        }
        $application->run($input, $output);
//         $content = $output->fetch();
    }
    
    /**
     * Parsea un parametro para ver si es una constante o una traduccion con parametros
     * Constante seria "Pandco\Bundle\AppBundle\Model\Base\TransactionItemInterface__STATUS_FINISH"
     * Traduccion con 'validators.invalid.phone.nro::{"%phoneNro%":"02475550001"}'
     * @param type $value
     * @param array $parameters
     * @param type $domain
     * @return type
     * @throws \RuntimeException
     */
    public function parseParameter($value, $parameters = [], $domain = "flashes") {
        if ($value === "now()") {
            return new \DateTime();
        }
        if (strpos($value, "date::") !== false) {
            $exploded = explode("::", $value);
            $value = \DateTime::createFromFormat("Y-m-d", $exploded[1]);
            if ($value === null) {
                throw new \RuntimeException(sprintf("The date format must be Y-m-d of '%s'", $exploded[1]));
            }
            return $value;
        }
        if($this->parseParameterCallBack){
            $value = call_user_func_array($this->parseParameterCallBack, [$value, $parameters,$domain,$this]);
        }
        $valueExplode = explode("__", $value);
        if (is_array($valueExplode) && count($valueExplode) == 2) {
//            var_dump($valueExplode[0]);
            $reflection = new \ReflectionClass($valueExplode[0]);
            if (!$reflection->hasConstant($valueExplode[1])) {
                throw new \RuntimeException(sprintf("The class '%s' no has a constant name '%s'", $valueExplode[0], $valueExplode[1]));
            }
            $value = $reflection->getConstant($valueExplode[1]);
        } else if ($this->isScenarioParameter($value)) {
            $value = $this->getScenarioParameter($value);
        } else {
            if ($parameters === null) {
                $parameters = [];
            }
            $value = $this->parseTrans($value, $parameters, $domain);
        }
        return $value;
    }
    
    public function getRequestBody($key = null,$default = null) {
        if($key === null){
            return $this->requestBody;
        }
        if(isset($this->requestBody[$key])){
            $default = $this->requestBody[$key];
        }
        return $default;
    }

    public function setRequestBody($key,$value) {
        $this->requestBody[$key] = $value;
        return $this;
    }
    public function initRequestBody(array $body = []) {
        $this->requestBody = $body;
        return $this;
    }

    /**
     * 
     *  \Symfony\Component\BrowserKit\Client
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    function getClient() {
        return $this->client;
    }

    /**
     * @return \Application\Sonata\UserBundle\Entity\User
     */
    public function getCurrentUser() {
        $user = null;
        if ($this->currentUser) {
            $user = $this->findUser($this->currentUser->getUsername());
        }
        return $user;
    }
    
    
    
    public function setParseParameterCallBack($parseParameterCallBack) {
        $this->parseParameterCallBack = $parseParameterCallBack;
        return $this;
    }
}
