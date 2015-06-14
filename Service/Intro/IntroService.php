<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\Intro;

/**
 * Servicio de introduccion
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class IntroService 
{
    protected $adapters;
    
    protected $config;
    
    /**
     *
     * @var Twig
     */
    protected $templating;
    /**
     *
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;
    
    public function __construct() {
        $this->adapters = array();
    }
    
    function addAdapter(Adapter\IntroAdapterInterface $adapter)
    {
        
    }
    
    public function renderArea($area)
    {
        $introClass = $this->config['intro_class'];
        $em = $this->doctrine->getManager();
        $intros = $em->getRepository($introClass)->findBy(array(
            'area' => $area,
            'enabled' => true,
        ));
        $template = 'TecnocreacionesToolsBundle:Intro:intro.js.twig';
        return $this->renderView($template,array(
            'intros' => $intros
        ));
    }


    /**
     * Returns a rendered view.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     *
     * @return string The rendered view
     */
    private function renderView($view, array $parameters = array())
    {
        return $this->templating->render($view, $parameters);
    }
    
    function getAreas() {
        return $this->config['areas'];
    }

    function setConfig(array $config) 
    {
        $this->config = $config;
    }
    
    function setTemplating($templating)
    {
        $this->templating = $templating;
    }
    
    function setDoctrine(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine) {
        $this->doctrine = $doctrine;
    }
}
