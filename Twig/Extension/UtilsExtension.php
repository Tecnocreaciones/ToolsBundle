<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Funciones para construir breadcumb y page title con twig
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class UtilsExtension extends Twig_Extension implements ContainerAwareInterface
{
    private $container;
    private $config;

    public function getName() 
    {
        return 'tecnocreaciones_tools_utils_extension';
    }
    
    public function getFunctions() 
    {
        $config = $this->config;
        $functions = [];
        
        if($config['intro']['enable']  === true){
            $functions[] = new \Twig_SimpleFunction('print_intro', array($this,'renderIntro'), array('is_safe' => array('html')));
        }
        if($config['twig'] != ''){
            if($config['twig']['breadcrumb'] === true){
                $functions[] = new \Twig_SimpleFunction('breadcrumb', array($this,'breadcrumb'), array('is_safe' => array('html')));
                $functions[] = new \Twig_SimpleFunction('breadcrumb_render', array($this,'breadcrumbRender'), array('is_safe' => array('html')));
            }
            if($config['twig']['page_header'] === true){
                $functions[] = new \Twig_SimpleFunction('page_header', array($this,'pageHeader'), array('is_safe' => array('html')));
            }
        }
        if($config['widget_block_grid']['enable']  === true){
            $functions[] = new \Twig_SimpleFunction('widgets_render_area', array($this,'widgetsRenderArea'), array('is_safe' => array('html')));
            $functions[] = new \Twig_SimpleFunction('widgets_render_assets', array($this,'widgetsRenderAssets'), array('is_safe' => array('html')));
            $functions[] = new \Twig_SimpleFunction('widgets_init_grid', array($this,'widgetsInitGrid'), array('is_safe' => array('html')));
        }
        
        if($config['tabs']['enable']  === true){
            $functions[] = new Twig_SimpleFunction('render_tabs', array($this, 'renderTabs'),array('is_safe' => ['html']));
        }
        
        $functions[] = new \Twig_SimpleFunction('uniqueId', array($this, 'uniqueId'));
        $functions[] = new \Twig_SimpleFunction('print_error', array($this,'printError'), array('is_safe' => array('html')));
        $functions[] = new \Twig_SimpleFunction('strpadleft', array($this, 'strpadleft'));
        $functions[] = new \Twig_SimpleFunction('staticCall', array($this, 'staticCall'));
        return $functions;
    }
    
    public function breadcrumb()
    {
        $parameters = array();
        $args = func_get_args();
        foreach ($args as $key => $arg) {
            if(empty($arg)){
                continue;
            }
            $item = new \stdClass();
            $item->link = null;
            $item->label = null;
            if(is_array($arg)){
                $count = count($arg);
                if($count > 1){
                    throw new \LogicException('The array elment must be one, count');
                }
                foreach ($arg as $key => $value) {
                    $item->link = $key;
                    $item->label = $value;
                }
            }else{
                $item->label = $arg;
            }
            $parameters[] = $item;
        }
        
        $emplate = $this->container->getParameter('tecnocreaciones_tools.twig.breadcrumb.template');
        return $this->container->get('templating')->render($emplate, 
            array(
                'breadcrumbs' => $parameters,
            )
        );
    }
    
    /**
     * Renderiza un encabezado de pagina
     * @return type
     * @throws \LogicException
     */
    public function pageHeader()
    {
        $parameters = array();
        $args = func_get_args();
        foreach ($args as $key => $arg) {
            if(empty($arg)){
                continue;
            }
            $item = new \stdClass();
            $item->link = null;
            $item->label = null;
            if(is_array($arg)){
                $count = count($arg);
                if($count > 1){
                    throw new \LogicException('The array elment must be one, count');
                }
                foreach ($arg as $key => $value) {
                    $item->link = $key;
                    $item->label = $value;
                }
            }else{
                $item->label = $arg;
            }
            $parameters[] = $item;
        }
        
        $template = $this->container->getParameter('tecnocreaciones_tools.twig.page_header.template');
        return $this->container->get('templating')->render($template, 
            array(
                'page_headers' => $parameters,
            )
        );
    }
    
    public function widgetsRenderArea($areaName,$renderAssets = true)
    {
        return $this->container->get('templating')->render("TecnocreacionesToolsBundle:BlockWidgetBox:area.html.twig", 
            array(
                'name_area' => $areaName,
                'render_assets' => $renderAssets,
                'gridWidgetBoxService' => $this->getGridWidgetBoxService(),
            )
        );
    }
    public function widgetsRenderAssets()
    {
        return $this->container->get('templating')->render("TecnocreacionesToolsBundle:BlockWidgetBox:assets.html.twig", 
            array(
            )
        );
    }
    public function widgetsInitGrid()
    {
        return $this->container->get('templating')->render("TecnocreacionesToolsBundle:BlockWidgetBox:initGrid.html.twig", 
            array(
            )
        );
    }
    
    function printError($error,array $parameters = array(),$translationDomain = 'messages') {
        $errorTrans = $this->trans($error,$parameters,$translationDomain);
        $base = '<div class="alert alert-danger fade in radius-bordered alert-shadowed">
                        <button class="close" data-dismiss="alert">
                            ×
                        </button>
                        <i class="fa-fw fa fa-times"></i>
                        <strong>Error!</strong> '.$errorTrans.'.
                    </div>';
        return $base;
    }
    
    private function generateAsset($path,$packageName = null){
        return $this->container->get('templating.helper.assets')
               ->getUrl($path, $packageName);
    }
    
    /**
     * Renderiza una o multiples introducciones en una area
     * @param type $area
     */
    public function renderIntro($area)
    {
        return $this->container->get('tecnocreaciones_tools.service.intro')->renderArea($area);
    }
    
    public function uniqueId() {
        return md5(uniqid(rand(), true));
    }
    
    /**
     * Add the str_pad left php function
     *
     * @param  string $string
     * @param  int $pad_lenght
     * @param  string $pad_string
     * @return mixed
     */
    public function strpadleft($string, $pad_lenght, $pad_string = " ")
    {
        return str_pad($string, $pad_lenght, $pad_string, STR_PAD_LEFT);
    }
    
    /**
     * Llama un metodo estatico de una clase
     * @param type $class
     * @param type $function
     * @param type $args
     * @return type
     */
    function staticCall($class, $function, $args = array())
    {
        if (class_exists($class) && method_exists($class, $function))
            return call_user_func_array(array($class, $function), $args);
        return null;
    }
    
    public function breadcrumbRender($idService = "tecno.service.breadcrumb"){
        return $this->container->get($idService)->breadcrumbRender();
    }
    
    /**
     * Render base tabs
     * @author Máximo Sojo maxsojo13@gmail.com <maxtoan at atechnologies>
     * @param  \Atechnologies\ToolsBundle\Model\Core\Tab\Tab
     * @param  array
     * @return [type]
     */
    public function renderTabs(\Tecnoready\Common\Model\Tab\Tab $tab,array $parameters = []) 
    {
        $parameters["tab"] = $tab;
        return $this->container->get('templating')->render($this->config["tabs"]["template"], 
            $parameters
        );
    }

    private function trans($id,array $parameters = array(), $domain = 'messages')
    {
        return $this->container->get('translator')->trans($id, $parameters, $domain);
    }
    
    /**
     * 
     * @return \Tecnocreaciones\Bundle\ToolsBundle\Service\GridWidgetBoxService
     */
    private function getGridWidgetBoxService()
    {
        return $this->container->get('tecnocreaciones_tools.service.grid_widget_box');
    }
    
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) 
    {
        $this->container = $container;
    }
    public function setConfig($config) {
        $this->config = $config;
        return $this;
    }
}
