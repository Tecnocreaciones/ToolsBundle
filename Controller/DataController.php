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
class DataController extends Controller
{
    public function searchAction(Request $request) 
    {
        $query = $request->get("q");
        $master = $request->get("m");
        if(empty($master)){
            throw new \InvalidArgumentException("El parametro 'm' es incorrecto.");
        }
        $criteria = [
            'query' => $query,
        ];
        $paginator = $this->get($master)->findForSearch($criteria);
        $paginator->setCurrentPage($request->get("page",1));
        
        return new \Symfony\Component\HttpFoundation\JsonResponse($paginator->toArray());
    }
}
