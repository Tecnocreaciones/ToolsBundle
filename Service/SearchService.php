<?php

/*
 * This file is part of the Witty Growth C.A. - J406095737 package.
 * 
 * (c) www.mpandco.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Service;
use Tecnocreaciones\Bundle\ToolsBundle\Model\Search\Filters\StandardFilters;

/**
 * Manejador de filtros (tecnocreaciones_tools.search)
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class SearchService
{
    /**
     * Filtros estandares
     */
    private $standardFilters = "TecnocreacionesToolsBundle:Search:standard_filters.html.twig";
    /**
     * Filtros adicionales
     */
    private $additionalFilters = null;
    /**
     * Template para renderizar los filtros
     */
    private $templateFilters = "TecnocreacionesToolsBundle:Search:template_filters.html.twig";
    
    /**
     * @var \Tecnocreaciones\Bundle\ToolsBundle\Model\Search\FilterBlockRepository
     */
    private $filterBlockRepository;
    
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Filtros disponibles
     * @var \Tecnocreaciones\Bundle\ToolsBundle\Model\Search\Filters\GroupFilterInterface
     */
    private $groupFilters;
    /**
     *
     * @var type 
     */
    private $transDefaultDomains;
    
    /**
     * Filtros a ocultar en la renderizacion
     * @var array
     */
    private $hideFilters;

    public function __construct() {
        $this->groupFilters = [];
        $this->hideFilters = [];
    }
    
    public function renderFilterArea($areaName) {
        $filterBlocks = $this->filterBlockRepository->findByArea($areaName);
        return $this->renderFilterBlock($filterBlocks);
    }
    
    public function renderFilterBlock($filterBlocks)
    {
        return $this->twig->render($this->templateFilters,[
            "filterBlocks" => $filterBlocks,
            "searchService" => $this,
        ]);
    }

    public function getStandardFilters() {
        return $this->standardFilters;
    }

    public function getAdditionalFilters() {
        return $this->additionalFilters;
    }

    public function getTemplateFilters() {
        return $this->templateFilters;
    }

    public function setStandardFilters($standardFilters) {
        $this->standardFilters = $standardFilters;
        return $this;
    }

    public function setAdditionalFilters($additionalFilters) {
        $this->additionalFilters = $additionalFilters;
        return $this;
    }

    public function setTemplateFilters($templateFilters) {
        $this->templateFilters = $templateFilters;
        return $this;
    }
    
    public function setFilterBlockRepository(\Tecnocreaciones\Bundle\ToolsBundle\Model\Search\FilterBlockRepository $filterBlockRepository) {
        $this->filterBlockRepository = $filterBlockRepository;
        return $this;
    }
    
    public function setTwig(\Twig_Environment $twig) {
        $this->twig = $twig;
        return $this;
    }
    
    /**
     * [setMacroTemplateGroupFilter Se agrega nueva plantilla base para filtros]
     *  
     * @author Máximo Sojo <maxsojo13@gmail.com>
     * @param  [String] $filter
     * @param  [String] $macroTemplate
     */
    public function setMacroTemplateGroupFilter($filter, $macroTemplate = null)
    {
        if(!isset($this->groupFilters[$filter])){
            throw new \RuntimeException(sprintf("The group filter %s is not added.", $filter));
        }

        $groupFilter = $this->groupFilters[$filter];
        $groupFilter->setMacroTemplate($macroTemplate);
    }

    public function addGroupFilter(\Tecnocreaciones\Bundle\ToolsBundle\Model\Search\Filters\GroupFilterInterface $filter)
    {
        if(isset($this->groupFilters[$filter->getName()])){
            throw new \RuntimeException(sprintf("The group filter %s is already added.", $filter->getName()));
        }
        $this->groupFilters[$filter->getName()] = $filter;
        return $this;
    }
    
    public function getFilterGroupByFilter($filterName)
    {
        $foundGroupFilter = null;
        foreach ($this->groupFilters as $groupFilter)
        {
            if(array_key_exists($filterName,$groupFilter->getTypes())){
                $foundGroupFilter = $groupFilter;
                break;
            }
        }

        return $foundGroupFilter;
    }
    
    /**
     * Renderiza un filtro
     * @param type $groupFilter
     * @param string $filterName
     * @param \Tecnocreaciones\Bundle\ToolsBundle\Model\Search\BaseFilter $filter
     * @return type
     */
    public function renderFilter($groupFilter,$filterName,\Tecnocreaciones\Bundle\ToolsBundle\Model\Search\BaseFilter $filter)
    { 

        if(empty($groupFilter)){
             return "Error de filtro: ".$filterName." - ref: ".$filter->getRef();
        }
        $template = $this->twig->loadTemplate($groupFilter->getMacroTemplate());
        
        return $template->renderBlock($filterName,[
            "id" => null,
            "label" => $filter->getLabel(),
            "modelName" => $filter->getModelName(),
            "searchService" => $this,
            "currentFilter" => $filter
        ]);
    }

    public function getTransDefaultDomains() {
        return $this->transDefaultDomains;
    }

    public function setTransDefaultDomains(array $transDefaultDomains) {
        $this->transDefaultDomains = $transDefaultDomains;
        return $this;
    }
    
    /**
     * Añade la referencia de un filtro para ignorarlo en la renderizacion
     * @param type $filterRef
     * @return $this
     */
    public function addHideFilters($filterRef) {
        $this->hideFilters[$filterRef] = $filterRef;
        return $this;
    }
    
    /**
     * ¿Esta oculto?
     * @param \Tecnocreaciones\Bundle\ToolsBundle\Model\Search\BaseFilter $filter
     * @return type
     */
    public function isHidden(\Tecnocreaciones\Bundle\ToolsBundle\Model\Search\BaseFilter $filter) {
        return isset($this->hideFilters[$filter->getRef()]);
    }
    
    public function finish() {
        $this->hideFilters = [];
    }
}