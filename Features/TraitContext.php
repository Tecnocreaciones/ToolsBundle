<?php

/*
 * This file is part of the Witty Growth C.A. - J406095737 package.
 * 
 * (c) www.mpandco.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Features;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Trait de metodos comunes en el contexto
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
trait TraitContext {
     /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected function getDoctrine() {
        return $this->container->get("doctrine");
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route         The name of the route
     * @param mixed  $parameters    An array of parameters
     * @param int    $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH) {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }
    
    /**
     * Traduce un indice
     * @param type $id
     * @param array $parameters
     * @param type $domain
     * @return type
     */
    protected function trans($id,array $parameters = array(), $domain = 'flashes')
    {
        return $this->container->get('translator')->trans($id, $parameters, $domain);
    }
}
