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

use Behat\MinkExtension\Context\MinkContext;
use Exception;

/**
 * Contexto para probar la web
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class BaseWebUserContext extends MinkContext
{
    use TraitContext;
    
    protected $kernel;
    protected $container;
    
    /**
     *
     * @var BaseDataContext
     */
    protected $dataContext;
    
    /** @BeforeScenario */
    public function gatherContexts(\Behat\Behat\Hook\Scope\BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $contexts = $environment->getContexts();
        foreach ($contexts as $context) {
            if($context instanceof BaseDataContext){
                $this->setDataContext($context);
                break;
            }
        }
    }
    
    public function setDataContext(BaseDataContext $dataContext) {
        $this->dataContext = $dataContext;
        $this->kernel = $this->dataContext->getKernel();
        $this->container = $this->kernel->getContainer();
        return $this;
    }
    
    
    /**
     * Se dirige a la pagina de inicio de sesion
     * @Given /^I am on login page$/
     */
    public function iAmOnLoginPage()
    {
        $this->getSession()->visit($this->generatePageUrl('fos_user_security_login'));
    }
    
    /**
     * Espera unos segundos antes de ejecutar el siguiente paso
     * @deprecated Usar function spin() para esperar a que un elemento aparezca, o usar iWaitForTextToAppear()
     * Example: I wait for 5 seconds
     * @Given /^I wait for (\d+) seconds$/
     */
    public function iWaitForSeconds($seconds)
    {
//        trigger_error("Usar function spin() para esperar a que un elemento aparezca, o usar iWaitForTextToAppear()",E_USER_DEPRECATED);
        $this->getSession()->wait($seconds * 1000);
    }
    
    /**
     * Verifica el test usa seleninum, es decir, si el test usa el navegador se abrio.
     * @return type
     */
    protected function isOpenBrowser() {
        return get_class($this->getMink()->getSession()->getDriver()) === "Behat\Mink\Driver\Selenium2Driver";
    }
    
     /**
     * Se dirige a una ruta de symfony
     * Example: Given I am on "withdraw_config_smart" page
     * @Given I am on ":route" page 
     */
    public function iAmOnPage($route)
    {
        $parameters = [];
        $requestBody = $this->dataContext->getRequestBody();
        if($requestBody != null){
            $parameters = $requestBody;
        }
        
        $this->getSession()->visit($this->generatePageUrl($route,$parameters));
        if($this->isOpenBrowser()){
            $this->getSession()->wait(2000);
        }
    }
    
    /**
     * Click a un elemento visible por su id
     * Example: And I click the "#cb-withdraw_smart_form_smartWithdrawalEnabled" element
    * @Given I click the :selector element
    */
   public function iClickTheElement($selector)
   {
       $element = $this->findElement($selector);
       $this->getSession()->wait(2 * 1000);
       try {
           $element->click();
       } catch (\Exception $ex) {
           //esperamos 2 segundos para intentar de nuevo hacer click
           $this->getSession()->wait(2 * 1000);
           $element->click();
       }
   }
   
   /**
    * 
    * @param type $selector
    * @return \Behat\Mink\Element\NodeElement
    * @throws Exception
    */
   protected function findElement($selector){
       $page = $this->getSession()->getPage();
       $element = $page->find('css', $selector);

       if (empty($element)) {
           throw new Exception("No html element found for the selector ('$selector')");
       }
       return $element;
   }
   
    /**
     * Selecccionar datos en select de tipo UI
     * Example:  Given I fill the ui select "bf-my_digital_account" with "J" and select element "1"
     * @Given I fill the ui select :id with :text and select element :item
     */
    public function iFillTheUiSelectWithAndSelectElement($id, $text, $item)
    {
        $items = explode("-", $item);
        if(count($items) > 1){
            $item = $items[0].'-'.((int)$items[1] - 1);
        }else {
            $item = "0-".((int)$item - 1);
        }
        
        $this->iClickTheElement(sprintf("#%s > div.ui-select-match.ng-scope > span",$id));
        $idText = sprintf("#%s > input.ui-select-search",$id);
//        var_dump($idText);
        $this->spin(function($context) use ($idText){
            $element = $context->findElement($idText);
            return $element != null;
        },20);
        
        $this->getSession()->getDriver()->executeScript(
            sprintf('$("%s").val("%s");',$idText,$text)
        );
        $this->getSession()->getDriver()->executeScript(
            sprintf('$("%s").change();',$idText)
        );
//        $selectInput = $this->findElement($idText);     
//        $selectInput->setValue($text);
                
        $idItem = sprintf("#ui-select-choices-row-%s",$item);
        $this->spin(function($context) use ($idItem){
            $element = $context->findElement($idItem);
            return $element != null;
        },20);
        $element = $this->findElement($idItem);
        $element->click();
    }
    
    /**
     * Selecccionar datos en select de tipo UI
     * @Given I fill the ui select :id with element :item
     */
    public function iFillTheUiSelectWithAndSelectElement2($id, $item)
    {
        $items = explode("-", $item);
        
        if(count($items) > 0){
            $item = $items[0].'-'.((int)$items[1] - 1);
        }else {
            $item = "0-".((int)$item - 1);
        }
        $idItem = sprintf("#ui-select-choices-row-%s",$item);
        $this->iClickTheElement(sprintf("#%s > div.ui-select-match.ng-scope > span",$id));
        $this->spin(function($context) use ($idItem){
            $element = $context->findElement($idItem);
            return $element != null;
        },20);
        $element = $this->findElement($idItem);
        $element->click();
    }
    
    /**
     * Generate page url.
     * This method uses simple convention where page argument is prefixed
     * with "sylius_" and used as route name passed to router generate method.
     *
     * @param string $page
     * @param array  $parameters
     *
     * @return string
     */
    protected function generatePageUrl($route, array $parameters = array())
    {
//        $parts = explode(' ', trim($page), 2);

//        $route  = implode('_', $parts);

//        $route = str_replace(' ', '_', $route);

        $path = $this->generateUrl($route, $parameters);
//        var_dump($this->getMinkParameter('base_url'));
//        var_dump($path);
//        die;
        if ('Selenium2Driver' === strstr(get_class($this->getSession()->getDriver()), 'Selenium2Driver')) {
            return sprintf('%s%s', $this->getMinkParameter('base_url'), $path);
        }
        
        return $path;
    }
    
    /**
     * Mueve el scroll de navegador a un elemento
     * Example: And I scroll bottom
     * @Then I scroll bottom and :element appear
     * @When I scroll to bottom
     */
    public function iScrollBottomAndElementAppear($element = null)
    {
        $this->getSession()->getDriver()->executeScript("window.scrollTo(0,document.body.scrollHeight);");
        sleep(1);
        if($element !== null){
            $this->spin(function($context) use ($element) {
               return $this->findElement($element);
            },15,function() use ($element){
               echo sprintf("No se encontro el elemento '%s'",$element);
            });
        }
    }
    
    public function pressButton($button) {
        $locator = $this->dataContext->parseParameter($button,[],"buttons");
        $this->spin(function($context) use ($locator){
            return $context->getSession()->getPage()->findButton($locator) !== null;
        });
        $this->getSession()->wait(2 * 1000);
       try {
           parent::pressButton($locator);
       } catch (\Exception $ex) {
           $this->iScrollBottomAndElementAppear($locator);
           //esperamos 2 segundos para intentar de nuevo hacer click
           //$this->getSession()->wait(2 * 1000);
           parent::pressButton($locator);
       }
    }
    
    /**
    * Esperar a ver si aparece un texto en la pagina
    * Example: And I should see 'flash.success.smart_withdrawal.enabled::{"%digitalAccount%":"584125550001"}::flashes' appear
    * @When I wait for :text to appear
    * @Then I should see :text appear
    * @param $text
    * @throws \Exception
    */
   public function iWaitForTextToAppear($text)
   {
       
       $text = $this->dataContext->parseParameter($text,[],"titles");
       $this->spin(function($context) use ($text) {
           /** @var $context FeatureContext */
           return $context->getSession()->getPage()->hasContent($text);
           
//           try {
//               $context->assertPageContainsText($text);
//           }
//           catch(\Behat\Mink\Exception\ResponseTextException $e) {
//               return false;
//           }
//           return true;
       },15,function() use ($text){
           echo sprintf("No se encontro el texto '%s'",$text);
       });
       $this->iWaitForSeconds(1);
   }

   /**
    * Espera que un texto desaparesca de la pagina
    * @When I wait for :text to disappear
    * @Then I should see :text disappear
    * @param $text
    * @throws \Exception
    */
   public function iWaitForTextToDisappear($text)
   {
       $this->spin(function(FeatureContext $context) use ($text) {
           try {
               $context->assertPageContainsText($text);
           }
           catch(\Behat\Mink\Exception\ResponseTextException $e) {
               return true;
           }
           return false;
       });
   }
   
   /**
    * @When I wait for :cssSelector
    * @param $cssSelector
    * @throws \Exception
    */
   public function iWaitFor($cssSelector)
   {
       $this->spin(function($context) use ($cssSelector) {
           /** @var $context FeatureContext */
           return !is_null($context->getSession()->getPage()->find('css', $cssSelector));
       });
   }
   
   /**
    * Esperar que un elemento desaparezca de la pagina
    * Example: And I wait for "div.loading" element to disappear
    * @Then I wait for :cssSelector element to disappear
    */
    public function iWaitForElementToDisappear($cssSelector)
    {
        $this->spin(function($context) use ($cssSelector) {
            try {
                $element = $context->findElement($cssSelector);
                //Ya no se encontro
                if($element === null){
                    return true;
                }
                return false;
            } catch (Exception $ex) {
                return true;
            }
       });
    }
   
    /**
     * Example: And I should see "validators::validators.sale.registration.code_quantity_min" with params '{"%n%":2}'
     * Example: Then I should see "labels::label.smart_withdrawal_enabled"
     * @Then I should see :text with params :params
     */
    public function assertPageContainsText($text,$paramsString = null) {
        $domain = "titles";
        $textExplode = explode("::",$text);
        if(count($textExplode) > 1){
            $domain = $textExplode[0];
            $text = $textExplode[1];
        }
        $parameters = [];
        if($paramsString !== null){
            $parameters = json_decode($paramsString,true);
            if(is_array($parameters)){
                $this->dataContext->parseScenarioParameters($parameters);
            }
        }
        return parent::assertPageContainsText($this->trans($text,$parameters,$domain));
    }
    
    public function clickLink($link) {
        if($this->dataContext->isScenarioParameter($link,true)){
            $link = $this->dataContext->getScenarioParameter($link,true);
        }
        $link = $this->dataContext->parseParameter($link,[],"titles");
        
        try {
            parent::clickLink($link);
        } catch (\Exception $ex) {
            //esperamos 2 segundos para intentar de nuevo hacer click
            $this->getSession()->wait(2 * 1000);
            parent::clickLink($link);
        }
    }
    
    public function selectOption($select, $option) {       
        if($this->dataContext->isScenarioParameter($option)){
            $option = $this->dataContext->getScenarioParameter($option);
        }
        return parent::selectOption($select, $option);
    }
    
    public function fillField($field, $value){
        $value = $this->dataContext->parseParameter($value);
        parent::fillField($field, $value);
        
        $fieldOriginal = $this->fixStepArgument($field);
        $value = $this->fixStepArgument($value);
        $field = $this->getSession()->getPage()->findField($fieldOriginal);
        if($field->getValue() !== $value){
//            var_dump("diferente ".$field->getValue()." != ".$value);
            parent::fillField($fieldOriginal, $value);
        }
    }
    
    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string $url    The URL to redirect to
     * @param int    $status The status code to use for the Response
     *
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302)
    {
        return new \Symfony\Component\HttpFoundation\RedirectResponse($url, $status);
    }
    
}
