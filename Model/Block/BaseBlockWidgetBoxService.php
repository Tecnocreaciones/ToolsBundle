<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tecnocreaciones\Bundle\ToolsBundle\Model\Block\DefinitionBlockWidgetBoxInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Tecnocreaciones\Bundle\ToolsBundle\Service\Block\Event\MainSummaryBlockEvent;
use InvalidArgumentException;

/**
 * Base de un bloque en un widget box
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
abstract class BaseBlockWidgetBoxService extends AbstractBlockService implements DefinitionBlockWidgetBoxInterface
{
    protected $cachePermission = [];
    
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    
    public function execute(BlockContextInterface $blockContext, Response $response = null) {
        // merge settings
        $settings = $blockContext->getSettings();
        
        return $this->renderResponse($blockContext->getTemplate(),array(
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings,
        ),$response);
    }
    
    public function setDefaultSettings(OptionsResolverInterface $resolver) {
        $this->configureSettings($resolver);
    }
    
    public function getDescription() {
        return $this->getType()."_desc";
    }
    
    /**
     * Eventos que escucha el widget para renderizarse
     */
    protected abstract function getEvents();
    
    public function getParseEvents() {
        $events = [];
        foreach ($this->getEvents() as $event) {
            $events[] = MainSummaryBlockEvent::EVENT_BASE.$event;
        }
        return $events;
    }

    
    public function configureSettings(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'url'      => false,
            'title'    => 'Titulo',
            'name'    => 'Nombre',
            'template' => 'TecnocreacionesToolsBundle:WidgetBox:block_widget_box.html.twig',
            'blockBase' => 'TecnocreacionesToolsBundle:WidgetBox:block_widget_box.html.twig',
            'positionX' => 1,
            'positionY' => 1,
            'sizeX' => 4,
            'sizeY' => 4,
            'oldSizeY' => 4,
            'icon' => '<i class="fa fa-sort"></i>',
            'isMaximizable' => true,
            'isReloadble' => true,
            'isCollapsible' => true,
            'isClosable' => true,
            'isCollapsed' => false,//Esta minimizada
            'isLoadedData' => true,//Esta cargada la data
            'isTransparent' => false,//Transparente
            'isColorable' => true,//Se puede cambiar el color del wiget
            'widgetColor' => null,//Color del widget
            'renderTitle' => true,//¿Renderizar el titulo del widget?
            'translationDomain' => $this->getTranslationDomain(),//Color del widget
        ));
    }
    
    public function getTranslationDomain() {
        return 'widgets';
    }
    
    public function countWidgets() {
        $count = 0;
        foreach ($this->getNames() as $name => $values) {
            if($this->hasPermission($name)){
                $count++;
            }
        }
        return $count;
    }
    
    public function hasPermission($name = null) 
    {
//        var_dump($name);
        $isGranted = true;
        if($name != null){
            if(isset($this->cachePermission[$name])){
                return $this->cachePermission[$name];
            }
            $names = $this->getNames();
            if(isset($names[$name]['rol'])){
                $isGranted = $this->isGranted($names[$name]['rol']);
                $this->cachePermission[$name] = $isGranted;
            }
        }
        return $isGranted;
    }
    
    public function getInfo($name,$key,$default = null) {
        $result = null;
        $names = $this->getNames();
        if(isset($names[$name])){
            $info = $names[$name];
            if(isset($info[$key]) && !empty($info[$key])){
                $result = $info[$key];
                if($key === "created_at"){
//                    19-06-2018
                    $resultOld = $result;
                    $result = \DateTime::createFromFormat("d-m-Y", $result);
                    if($result === false){
                        throw new InvalidArgumentException(sprintf("El formato de la fecha '%s' debeser d-m-Y por ejemplo %s",$resultOld,"19-06-2018"));
                    }
                }
            }
        }
        return $result;
    }
    
    public function isNew($name) {
        $createdAt = $this->getInfo($name,"created_at");
        $result = false;
        if($createdAt !== null){
            $now = new \DateTime();
            $diff = $createdAt->diff($now);
            if($diff->invert === 0 && $diff->days < 15){
                $result = true;
            }
        }
        return $result;
    }
    
    public function countNews(){
        $names = $this->getNames();
        $news = 0;
        foreach ($names as $name => $values) {
            if($this->hasPermission($name) && $this->isNew($name)){
                $news++;
            }
        }
        return $news;
    }
    
    protected function isGranted($rol) {
        $user = $this->getUser();
        return $user->hasRole($rol);
    }
    
    /**
     * Get a user from the Security Token Storage.
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see \Symfony\Component\Security\Core\Authentication\Token\TokenInterface::getUser()
     */
    protected function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }
    
    /**
     * Traduce un indice
     * @param type $id
     * @param array $parameters
     * @param type $domain
     * @return type
     */
    protected function trans($id,array $parameters = array(), $domain = 'flashes')
    {
        return $this->container->get('translator')->trans($id, $parameters, $domain);
    }
}
