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
 * Servicio para hacer dumper de fixtures a raiz de un xls (tecno.service.fixtures_dumper)
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class FixturesDumperService
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    
    public function getDumper($className) 
    {
        $dumper = new $className($this->container);
        return $dumper;
    }
    
    public function dumpAndSave($className)
    {
        $resolver = new \Symfony\Component\OptionsResolver\OptionsResolver();
        $dumper = $this->getDumper($className);
        $dumper->configureOptions($resolver);
        $parameters = $resolver->resolve();
        var_dump($parameters);
        $dumper->setParameters($parameters);
        $dumper->dumpAndSave();
    }
}
