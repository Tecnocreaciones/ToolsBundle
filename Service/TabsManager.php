<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Tecnoready\Common\Model\Tab\Tab;
use Tecnoready\Common\Model\Tab\TabContent;
use Tecnoready\Common\Service\ObjectManager\ObjectDataManager;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tecnocreaciones\Bundle\ToolsBundle\Form\Tab\DocumentsType;
use Tecnocreaciones\Bundle\ToolsBundle\Form\Tab\ExporterType;
use RuntimeException;
use Tecnoready\Common\Service\ObjectManager\ConfigureInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Manejador de tabs
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class TabsManager implements ConfigureInterface
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    use \Tecnoready\Common\Service\ObjectManager\TraitConfigure;
    
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
    
    public function configure($objectId, $objectType)
    {
        $this->objectId = $objectId;
        $this->objectType = $objectType;
        $this->getObjectDataManager()->configure($this->objectId, $this->objectType);
    }
    
    /**
     * @return Tab
     */
    public function createNew(array $options = [])
    {
        if(!in_array($this->objectType,$this->options["object_types"])){
            throw new RuntimeException(sprintf("The objectType '%s' is not managed. Olny are '%s'",$this->objectType,implode(",",$this->options["object_types"])));
        }
        $request = $this->requestStack->getCurrentRequest();
        $this->parametersToView = [];
        $this->getObjectDataManager()->configure($this->objectId, $this->objectType);
        $tab = new Tab($options);
        $tab->setRequest($request);
        $this->tab = $tab;
        $this->parametersToView["objectDataManager"] = $this->getObjectDataManager();
        $this->parametersToView["tabsManager"] = $this;
        $this->parametersToView["parameters_to_route"] = $this->getParametersToRoute();
        return $tab;
    }
    
    /**
     * Retorna los parametros para la ruta del manejador de objetos
     * @return array
     */
    private function getParametersToRoute()
    {
        $request = $this->requestStack->getCurrentRequest();
        return [
            "_conf" => [
                "returnUrl" => $request->getSchemeAndHttpHost().$request->getBaseUrl().$request->getPathInfo(),
                "objectId" => $this->objectId,
                "objectType" => $this->objectType,
            ]
        ];
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
            "icon" => "vf vf-history-clock",
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
        $tab->resolveCurrentTab();
        //$this->tab = null;
        //$this->parametersToView = [];
        return $tab;
    }
    
    /**
     * Renderiza el modulo para generar archivos del moduloe
     * @param $entity
     * @param type $idChain
     * @return type
     */
    public function renderFilesGenerated($entity) {
        $chain = $this->getObjectDataManager()->exporter()->resolveChainModel();
        $choices = [];
        $models = $chain->getModels();
        foreach ($models as $model) {
            $choices[$this->trans($model->getName())." [".strtoupper($model->getFormat())."]"] = $model->getName();
        }
        $form = $this->createForm(ExporterType::class,$choices);
        $this->parametersToView["parameters_to_route"]["_conf"]["folder"] = "generated";
        return $this->container->get('templating')->render($this->options["exporter"]["template"], 
            [
                'chain' => $chain,
                'entity' => $entity,
                'objectDataManager' => $this->getObjectDataManager(),
                'form' => $form->createView(),
                'tab' => $this->tab,
                'parametersToView' => $this->parametersToView,
            ]
        );
    }
    
    /**
     * Retorna la url de descarga de un archivo
     * @param type $fileName
     * @param type $disposition
     * @return type
     */
    public function documentsDownloadUrl($fileName,$disposition = \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT,$folder = "uploaded")
    {
        $url = null;
        $this->getObjectDataManager()->documents()->folder($folder);
        $file = $this->getObjectDataManager()->documents()->get($fileName);
        if($file !== null){
            $params = $this->getParametersToRoute();
            $params["_conf"]["folder"] = $folder;
            $params["filename"] = $fileName;
            $params["disposition"] = $disposition;
            $url = $this->generateUrl("tabs_object_manager_documents_get",$params);
        }
        return $url;
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
    
    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route         The name of the route
     * @param array  $parameters    An array of parameters
     * @param int    $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     *
     * @final since version 3.4
     */
    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }
}
