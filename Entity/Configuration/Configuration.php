<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Entity\Configuration;

use Tecnoready\Common\Model\Configuration\BaseEntity\DoctrineORMConfiguration as BaseConfiguration;

use Doctrine\ORM\Mapping as ORM;

/**
 * Configuracion
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 * 
 * @ORM\Entity()
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="config_idx", columns={"item_key", "name_wrapper"})})
 */
class Configuration extends BaseConfiguration
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    public function getId() {
        return $this->id;
    }
}
