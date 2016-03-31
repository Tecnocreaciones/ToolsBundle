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

/**
 * Manejador de filtros ()
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class SearchService {
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
     *
     * @var \Twig_Environment
     */
    private $twig;


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
}
