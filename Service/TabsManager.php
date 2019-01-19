<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Tecnoready\Common\Model\Tab\Tab;
use Tecnoready\Common\Model\Tab\TabContent;
use Tecnoready\Common\Service\ObjectManager\ObjectDataManager;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Manejador de tabs
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class TabsManager
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    
    /**
     * @var RequestStack
     */
    private $requestStack;
    
    /**
     * Parametros que necesita la vista al renderizarse
     * @var array
     */
    private $parametersToView = [];
    
    private $tab;
    
    /**
     * @var array
     */
    private $options;

    public function __construct(RequestStack $requestStack,array $options)
    {
        $this->requestStack = $requestStack;
        $this->options = $options;
    }
    
    /**
     * @return Tab
     */
    public function createNew(array $options = [],$objectId, $objectType)
    {
        $this->parametersToView = [];
        $this->getObjectDataManager()->configure($objectId, $objectType);
        $tab = new Tab($options);
        $tab->setRequest($this->requestStack->getCurrentRequest());
        $this->tab = $tab;
        $this->parametersToView["objectDataManager"] = $this->getObjectDataManager();
        return $tab;
    }
    
    public function addTabContent(TabContent $tabContent)
    {
        $this->tab->addTabContent($tabContent);
    }
    
    /**
     * Añade la tab de documentos
     * @param array $options
     */
    public function addDocuments(array $options = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            "add_content_div" => false,
            "template" => $this->options["document_manager"]["template"],
            "title" => $this->trans("messages.tab.documents", [], "messages"),
            "icon" => "vf vf-documents",
        ]);
        $options = $resolver->resolve($options);
        $tabContentDocuments = new TabContent($options);
        $this->tab->addTabContent($tabContentDocuments);
        
    }
    
    /**
     * Añade la tab de historiales
     * @param array $options
     */
    public function addHistories(array $options = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            "add_content_div" => false,
            "template" => $this->options["history_manager"]["template"],
            "title" => $this->trans("messages.tab.history", [], "messages"),
        ]);
        $options = $resolver->resolve($options);
        $tabContentHistory = new TabContent($options);
        $this->tab->addTabContent($tabContentHistory);
    }
    
    /**
     * Retorna la tab listas
     * @return Tab
     */
    public function buildTab()
    {
        $tab = $this->tab;
        $tab->setParameters($this->parametersToView);
        $this->tab = null;
        $this->parametersToView = [];
        return $tab;
    }
    
    public function getObjectDataManager()
    {
        return $this->container->get(ObjectDataManager::class);
    }
    
    /**
     * Traduce un indice
     * @param type $id
     * @param array $parameters
     * @param type $domain
     * @return type
     */
    protected function trans($id,array $parameters = array(), $domain = 'messages')
    {
        return $this->container->get('translator')->trans($id, $parameters, $domain);
    }
}
