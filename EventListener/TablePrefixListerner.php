<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * Description of TablePrefixListerner
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class TablePrefixListerner implements \Doctrine\Common\EventSubscriber {
    protected $prefix = '';
    protected $tableNameLowercase = false;
    private $config;
            
    function __construct($prefix, $tableNameLowercase) {
        $this->prefix = (string) $prefix;
        $this->tableNameLowercase = (bool)$tableNameLowercase;
    }

    public function setConfig(array $config) {
        $this->config = $config;
        return $this;
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
        $nameTable = $this->prefix . $classMetadata->getTableName();
        if($this->tableNameLowercase === true){
           $nameTable = mb_strtolower($nameTable);
        }
        $classMetadata->setTableName($nameTable);

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if ($this->config['on_delete'] !== null &&
                    ($mapping['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_ONE || $mapping['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_ONE)
                    && $classMetadata->associationMappings[$fieldName]['isOwningSide']) {
                foreach ($classMetadata->associationMappings[$fieldName]['joinColumns'] as $key => $joinColumn) {
                    $classMetadata->associationMappings[$fieldName]['joinColumns'][$key]['onDelete'] = $this->config['on_delete'];
                }
            }
            if ($mapping['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY) {
                if(isset($classMetadata->associationMappings[$fieldName]['joinTable']['name'])){
                    $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
                }else{
                    $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable'];
                }
                if(is_array($mappedTableName)){
                    continue;
                }
                $mappedTableName = $this->prefix . $mappedTableName;
                if($this->tableNameLowercase === true){
                    $mappedTableName = mb_strtolower($mappedTableName);
                }
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $mappedTableName;
                
            }
        }
    }
}
