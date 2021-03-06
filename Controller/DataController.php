<?php

/*
 * This file is part of the Witty Growth C.A. - J406095737 package.
 * 
 * (c) www.mpandco.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controlador de data
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DataController extends \FOS\RestBundle\Controller\FOSRestController
{
    public function searchAction(Request $request) 
    {
        $em = $this->getDoctrine()->getManager();
        $query = $request->get("q");
        $master = $request->get("m");
        if(empty($master)){
            throw new \InvalidArgumentException("El parametro 'm' es incorrecto.");
        }
        $useDataManager = false;
        if($master == "data_manager"){
            $useDataManager = true;
        }
        if(!$useDataManager){
            $criteria = [
                'query' => $query, 
            ];
            if(class_exists($master)){
                $paginator = $em->getRepository($master)->findForSearch($criteria);;
            }else{
                $paginator = $this->get($master)->findForSearch($criteria);
            }
            $paginator->setCurrentPage($request->get("page",1));
            $paginator->setDefaultFormat(\Tecnocreaciones\Bundle\ToolsBundle\Model\Paginator\Paginator::FORMAT_ARRAY_STANDARD);
            return new \Symfony\Component\HttpFoundation\JsonResponse($paginator->toArray());
            
        }else {
            $config = $this->container->getParameter("tecnocreaciones_tools.search.config");
            $view = $this->view();
            $this->get($config['data_manager_service'])->search($request,$view);
            return $this->handleView($view);
        }
    }
}
