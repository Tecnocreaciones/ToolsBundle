<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Traits\ConfigurationManager;

/**
 * Trait de configuracion en el easy admin
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
trait ConfigEasyAdminTrait
{
    public function createParameterConfigurationEditForm($entity, array $entityProperties)
    {
        $form = $this->createEntityForm($entity, $entityProperties, 'edit');
        $configurationManager = $this->getConfigurationManager();
        $key = $entity->getKey();
        $config = $configurationManager->getFormEditConfig($key);
        $val = $configurationManager->get($entity->getKey(), $entity->getNameWrapper(), $entity->getValue());
        $entity->setValue($val);
        if($config !== null){
            $form->remove("value");
            $form->add("value",$config[0],$config[1]);
        }
        return $form;
    }
    
    public function updateParameterConfigurationEntity($entity)
    {
        $configurationManager = $this->getConfigurationManager();
        $configurationManager->set($entity->getKey(), $entity->getValue(), $entity->getNameWrapper(),null);
        $configurationManager->flush(true);
    }
    
    /**
     * Manejador de configuraciones
     * @return \Tecnoready\Common\Service\ConfigurationService\ConfigurationManager
     */
    protected function getConfigurationManager() {
        return $this->container->get($this->container->getParameter("tecnocreaciones_tools.configuration_manager.name"));
    }
}
