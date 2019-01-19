<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Tecnoready\Common\Model\Tab\Tab;

/**
 * Controlador generico para guardar acciones
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ToolsController extends Controller
{
    public function introAction(Request $request)
    {
        $typeLog = $request->get("type_log",null);
        $introId = $request->get("intro",null);
        $user = $this->getUser();
        
        $introService= $this->getIntroService();
        $introService->log($introId, $user, $typeLog);
        $response = new \Symfony\Component\HttpFoundation\JsonResponse();
        return $response;
    }
    
    public function tabSaveCurrentAction(Request $request) {
        if($request->query->has(Tab::NAME_CURRENT_TAB)){
            $request->getSession()->set(Tab::NAME_CURRENT_TAB,$request->query->get(Tab::NAME_CURRENT_TAB));
        }
        return new \Symfony\Component\HttpFoundation\JsonResponse();
    }
    
    /**
     * Intro Service
     * @return \Tecnocreaciones\Bundle\ToolsBundle\Service\Intro\IntroService
     */
    private function getIntroService() 
    {
        return $this->get("tecnocreaciones_tools.service.intro");
    }
}
