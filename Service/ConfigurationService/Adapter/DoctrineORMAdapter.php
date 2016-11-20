<?php

/*
 * This file is part of the Witty Growth C.A. - J406095737 package.
 * 
 * (c) www.mpandco.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\ConfigurationService\Adapter;

use Tecnoready\Common\Service\ConfigurationService\Adapter\DoctrineORMAdapter as Base;
use Tecnocreaciones\Bundle\ToolsBundle\Entity\Configuration\Configuration;

/**
 * Adaptador de doctrine2
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DoctrineORMAdapter extends Base {
    public function createNew() {
        return new Configuration();
    }

    public function find($key) {
        return $this->em->getRepository("Tecnocreaciones\Bundle\ToolsBundle\Entity\Configuration\Configuration")->findBy([
            "key" => $key,
        ]);
    }

    public function findAll() {
        return $this->em->getRepository("Tecnocreaciones\Bundle\ToolsBundle\Entity\Configuration\Configuration")->findAll();
    }

    public function flush() {
        $this->em->flush();
    }
}
