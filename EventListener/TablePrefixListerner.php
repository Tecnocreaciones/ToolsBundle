<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * Description of TablePrefixListerner
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com.ve>
 */
class TablePrefixListerner implements \Doctrine\Common\EventSubscriber {
    protected $prefix = '';
    
    public function __construct($prefix)
    {
        $this->prefix = (string) $prefix;
    }
    
    public function getSubscribedEvents() {
        return array('loadClassMetadata');
    }
    
    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $classMetadata = $args->getClassMetadata();
        if ($classMetadata->isInheritanceTypeSingleTable() && !$classMetadata->isRootEntity()) {
                            // if we are in an inheritance hierarchy, only apply this once
            return;
        }

        $classMetadata->setTableName($this->prefix . $classMetadata->getTableName());

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if ($mapping['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY) {
                if(isset($classMetadata->associationMappings[$fieldName]['joinTable']['name'])){
                    $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
                }else{
                    $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable'];
                }
                if(is_array($mappedTableName)){
                    continue;
                }
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix . $mappedTableName;
            }
        }
    }
}
