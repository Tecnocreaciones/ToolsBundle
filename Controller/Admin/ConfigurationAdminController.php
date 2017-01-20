<?php

/*
 * This file is part of the Witty Growth C.A. - J406095737 package.
 * 
 * (c) www.mpandco.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

/**
 * Controlador de configuracion
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ConfigurationAdminController extends Controller
{
    /**
     * Limplia la cache
     * @return \Tecnocreaciones\Bundle\ToolsBundle\Controller\Admin\RedirectResponse
     */
    public function clearCacheAction() {
        $this->getConfigurationManager()->clearCache();
        $this->addFlash('sonata_flash_success', 'Cache clear successfully');

        return new \Symfony\Component\HttpFoundation\RedirectResponse($this->admin->generateUrl('list'));
    }
    
    /**
     * 
     * @return \Tecnoready\Common\Service\ConfigurationService\ConfigurationManager
     */
    public function getConfigurationManager() {
        return $this->container->get($this->container->getParameter("tecnocreaciones_tools.configuration_manager.name"));
    }
}
