<?php

/*
 * This file is part of the Witty Growth C.A. - J406095737 package.
 * 
 * (c) www.mpandco.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extension para imprimir area de filtro
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class SearchExtension extends AbstractExtension
{
    /**
     * @var \Tecnocreaciones\Bundle\ToolsBundle\Service\SearchService
     */
    private $searchService;
    
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    
    public function getFunctions() 
    {
        return array(
            new TwigFunction('renderFilterArea', array($this, 'renderFilterArea'),array('is_safe' => array('html'))),
        );
    }
    /**
     * Renderiza filtros de un area
     * @param type $areaName
     */
    public function renderFilterArea($areaName) {
        return $this->searchService->renderFilterArea($areaName);
    }

    public function setSearchService(\Tecnocreaciones\Bundle\ToolsBundle\Service\SearchService $searchService) {
        $this->searchService = $searchService;
        return $this;
    }
    public function getName() {
        return "search";
    }
}