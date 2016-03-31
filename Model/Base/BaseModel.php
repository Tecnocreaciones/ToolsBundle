<?php

/*
 * This file is part of the TecnoReady Solutions C.A. (J-40629425-0) package.
 * 
 * (c) www.tecnoready.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Model\Base;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * Modelo base
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 * @ORM\MappedSuperclass()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
abstract class BaseModel  implements BaseModelInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="string", length=36)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;
    
    /**
     * @var \DateTime
     * 
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;
    
    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;
    
    /**
     * Hook SoftDeleteable behavior
     * updates deletedAt field
     */
    use SoftDeleteableEntity;
    
    /**
     * @var string $createdFromIp
     *
     * @Gedmo\IpTraceable(on="create")
     * @ORM\Column(type="string", name="created_from_ip",length=45, nullable=true)
     */
    protected $createdFromIp;
    
    /**
     * @var string $updatedFromIp
     *
     * @Gedmo\IpTraceable(on="update")
     * @ORM\Column(type="string", name="updated_from_ip",length=45, nullable=true)
     */
    protected $updatedFromIp;
    
    /**
     * @var string $deletedFromIp
     *
     * @ORM\Column(type="string", name="deleted_from_ip",length=45, nullable=true)
     */
    protected $deletedFromIp;
    
    function getCreatedAt() {
        return $this->createdAt;
    }

    function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function getCreatedFromIp() {
        return $this->createdFromIp;
    }

    public function getUpdatedFromIp() {
        return $this->updatedFromIp;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getDeletedFromIp() {
        return $this->deletedFromIp;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setDeletedFromIp($deletedFromIp) {
        $this->deletedFromIp = $deletedFromIp;
        return $this;
    }

            
    public function __clone() {
        if($this->id){
            $this->createdAt = null;
            $this->updatedAt = null;
            $this->deletedAt = null;
        }
    }
}
