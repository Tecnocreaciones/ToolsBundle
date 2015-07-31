<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Paginator;

use Pagerfanta\Pagerfanta as BasePagerfanta;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Router;

/**
 * Pagerfanta modificado para serializarlo
 *
 * @author Anais Ortega <adcom23@tecnocreaciones.com.ve>
 */
class Paginator extends BasePagerfanta implements ContainerAwareInterface
{
    protected $route = null;
    protected $container;
    protected $defaultFormat = self::FORMAT_ARRAY_DEFAULT;
    protected $draw = 1;
    /**
     *
     * @var \Symfony\Component\HttpFoundation\Request 
     */
    protected $request;

    /**
     * Devuelve un formato estandar de trabajo
     */
    const FORMAT_ARRAY_DEFAULT = 'default';
    
    /**
     * Devuelve un formato para que pueda ser leido por el plugin DataTables de jQuery
     */
    const FORMAT_ARRAY_DATA_TABLES = 'dataTables';
    
    private $formatArray = array(
        self::FORMAT_ARRAY_DEFAULT,self::FORMAT_ARRAY_DATA_TABLES
    );
            
    function formatToArrayDefault($route = null,array $parameters = array()) {
        $links = array(
            'self'  => array('href' => ''),
            'first' => array('href' => ''),
            'last'  => array('href' => ''),
            'next'  => array('href' => ''),
            'previous'  => array('href' => ''),
        );
        $paginator = array(
                        'getNbResults' => $this->getNbResults(),
                        'getCurrentPage' => $this->getCurrentPage(),
                        'getNbPages' => $this->getNbPages(),
                        'getMaxPerPage' => $this->getMaxPerPage(),
                    );
        
        $pageResult = $this->getCurrentPageResults();
        if(is_array($pageResult)){
            $results = $pageResult;
        }else{
            $results = $this->getCurrentPageResults()->getArrayCopy();
        }
        return array(
            '_links' => $this->getLinks($route,$parameters),
            '_embedded' => array(
                'results' => $results,
                'paginator' => $paginator
            ),
        );
    }
    
    function formatToArrayDataTables($route = null,array $parameters = array()) {
        $results = $this->getCurrentPageResults()->getArrayCopy();
        $data = array(
            'draw' => $this->draw,
            'recordsTotal' => $this->getNbResults(),
            'recordsFiltered' => $this->getNbResults(),
            'data' => $results,
            '_links' => $this->getLinks($route,$parameters),
        );
        return $data;
    }
    
    function toArray($route = null,array $parameters = array(),$format = null) {
        if($format === null){
            $format = $this->defaultFormat;
        }
        if(in_array($format, $this->formatArray)){
            $method = 'formatToArray'.ucfirst($format);
            return $this->$method($route,$parameters);
        }
    }
    
    protected function  generateUrl($route,array $parameters){
        return $this->container->get('router')->generate($route, $parameters, Router::ABSOLUTE_URL);
    }
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
    
    function setDefaultFormat($defaultFormat) 
    {
        $this->defaultFormat = $defaultFormat;
    }
    
    protected function getLinks($route,array $parameters = array()){
        $links = array();
        if($route != null){
            $links['first']['href'] = $this->generateUrl($route, array_merge($parameters, array('page' => 1)));
            $links['self']['href'] = $this->generateUrl($route, array_merge($parameters, array('page' => $this->getCurrentPage())));
            $links['last']['href'] = $this->generateUrl($route, array_merge($parameters, array('page' => $this->getNbPages())));
            if($this->hasPreviousPage()){
                $links['previous']['href'] = $this->generateUrl($route, array_merge($parameters, array('page' => $this->getPreviousPage())));
            }
            if($this->hasNextPage()){
                $links['next']['href'] = $this->generateUrl($route, array_merge($parameters, array('page' => $this->getNextPage())));
            }
        }
        return $links;
    }
            
    function setRequest(\Symfony\Component\HttpFoundation\Request $request) {
        $this->request = $request;
        
        if(self::FORMAT_ARRAY_DATA_TABLES == $this->defaultFormat){
            $start = $request->get("start",0);
            $length = (int)$request->get("length",10);
            $this->draw = $request->get("draw",  $this->draw) + 1;
            if($start > 0){
                $page = $start / $length;
                $page = $page + 1;
            }else{
                $page = 1;
            }
            if(!is_int($length)){
                $length = 10;
            }
            if(!is_int($page)){
                $page = 1;
            }
            $this->setCurrentPage($page);
            $this->setMaxPerPage($length);
        }
        
    }
}
