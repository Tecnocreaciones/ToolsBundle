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
        
        $emplate = $this->container->getParameter('tecnocreaciones_tools.twig.page_header.template');
        return $this->container->get('templating')->render($emplate, 
            array(
                'page_header' => $parameters,
            )
        );
    }
    
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) 
    {
        $this->container = $container;
    }

}
