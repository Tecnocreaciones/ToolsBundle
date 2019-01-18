<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Tecnoready\Common\Model\Tab\Tab;

/**
 * Manejador de tabs
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class TabsManager
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    
    /**
     * @return Tab
     */
    public function createNew(array $options = [])
    {
        $tab = new Tab($options);
        $tab->setRequest($this->requestStack->getCurrentRequest());
        return $tab;
    }
}
