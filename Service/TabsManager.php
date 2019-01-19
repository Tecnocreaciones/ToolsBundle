<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Tecnoready\Common\Model\Tab\Tab;
use Tecnoready\Common\Model\Tab\TabContent;
use Tecnoready\Common\Service\ObjectManager\ObjectDataManager;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tecnocreaciones\Bundle\ToolsBundle\Form\Tab\DocumentsType;

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
        $request = $this->requestStack->getCurrentRequest();
        $this->parametersToView = [];
        $this->getObjectDataManager()->configure($objectId, $objectType);
        $tab = new Tab($options);
        $tab->setRequest($request);
        $this->tab = $tab;
        $this->parametersToView["objectDataManager"] = $this->getObjectDataManager();
        $this->parametersToView["parameters_to_route"] = [
            "_conf" => [
                "returnUrl" => $request->getSchemeAndHttpHost().$request->getBaseUrl().$request->getPathInfo(),
                "objectId" => $objectId,
                "objectType" => $objectType,
            ]
        ];
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
        $folder = "uploaded";
        $this->getObjectDataManager()->documents()->folder($folder);
        $this->parametersToView["parameters_to_route"]["_conf"]["folder"] = $folder;
//        $this->parametersToView["form"] = function(){
//            
//        };
        $this->parametersToView["form"] = $this->createForm(DocumentsType::class)->createView();
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
    
    /**
     * @return ObjectDataManager
     */
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
    
    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string $type    The fully qualified class name of the form type
     * @param mixed  $data    The initial data for the form
     * @param array  $options Options for the form
     *
     * @return FormInterface
     *
     * @final since version 3.4
     */
    protected function createForm($type, $data = null, array $options = array())
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }
}
