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

/**
 * Funciones para construir breadcumb y page title con twig
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class TemplateUtilsExtension extends Twig_Extension implements ContainerAwareInterface
{
    private $container;
    
    public function getName() 
    {
        return 'tecnocreaciones_tools_template_utils_extension';
    }
    
    public function getFunctions() 
    {
        return array(
            new \Twig_SimpleFunction('breadcrumb', array($this,'breadcrumb'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('page_header', array($this,'pageHeader'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('print_error', array($this,'printError'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('print_intro', array($this,'renderIntro'), array('is_safe' => array('html'))),
        );
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

    private function trans($id,array $parameters = array(), $domain = 'messages')
    {
        return $this->container->get('translator')->trans($id, $parameters, $domain);
    }
    
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) 
    {
        $this->container = $container;
    }

}
