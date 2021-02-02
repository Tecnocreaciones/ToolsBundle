<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Tecnoready\Common\Model\Tab\Tab;
use Tecnoready\Common\Model\Tab\TabContent;
use Tecnoready\Common\Service\ObjectManager\ObjectDataManager;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tecnocreaciones\Bundle\ToolsBundle\Form\Tab\DocumentsType;
use Tecnocreaciones\Bundle\ToolsBundle\Form\Tab\ExporterType;
use Tecnocreaciones\Bundle\ToolsBundle\Form\Tab\UploadType;
use Tecnocreaciones\Bundle\ToolsBundle\Form\Tab\NotesType;
use RuntimeException;
use Tecnoready\Common\Service\ObjectManager\ConfigureInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Tecnoready\Common\Util\StringUtil;
use Knp\Component\Pager\PaginatorInterface;
//use Tecnocreaciones\Bundle\ToolsBundle\Model\Paginator\Paginator;
use Pagerfanta\Pagerfanta as Paginator;
use Pagerfanta\Adapter\DoctrineORMAdapter;

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

    /**
     * @var Tab
     */
    private $tab;

    /**
     * @var array
     */
    private $options;

    /**
     * Modelos a renderizar por chain
     * $models
     * @var Array
     */
    private $models;

    /**
     * Paginador de kpn
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(RequestStack $requestStack, array $options)
    {
        $this->requestStack = $requestStack;
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            "document_manager" => [
                "template" => null,
                "title" => "",
                "icon" => "",
            ],
            "history_manager" => [
                "template" => null,
                "title" => "",
                "icon" => "",
            ],
            "note_manager" => [
                "template" => null,
                "title" => "",
                "icon" => "",
            ],
            "exporter" => [
                "template" => null,
                "template_upload" => null,
            ],
            "template" => null,
            "default_icon" => null,
        ]);
        $resolver->setDefined(["object_types"]);
        $this->options = $resolver->resolve($options);
    }

    /**
     * Configura el tabs manager
     * @param type $objectId
     * @param type $objectType
     * @param array $options
     */
    public function configure($objectId, $objectType, array $options = [])
    {
        $this->objectId = $objectId;
        $this->objectType = $objectType;
        $this->getObjectDataManager()->configure($this->objectId, $this->objectType, $options);
    }

    /**
     * @return Tab
     */
    public function createNew(array $options = [])
    {
        if (!in_array($this->objectType, $this->options["object_types"])) {
            throw new RuntimeException(sprintf("The objectType '%s' is not managed. Olny are '%s'", $this->objectType, implode(",", $this->options["object_types"])));
        }
        $request = $this->requestStack->getCurrentRequest();
        $this->parametersToView = [];
        $this->getObjectDataManager()->configure($this->objectId, $this->objectType);
        $options["object_id"] = $this->objectId;
        $tab = new Tab($options);
        $tab->setRequest($request);
        $tab->setRootUrl($request->getRequestUri()); //Por defecto la misma ruta que se llamo originalmente
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
        $uri = $request->getRequestUri();
        $toClear = ["isInit", "ajax", Tab::SORT_ORDER, Tab::SORT_PROPERTY, Tab::LAST_CURRENT_TABS, Tab::NAME_CURRENT_TAB];
        $uri = StringUtil::removeQueryStringURL($uri, $toClear);
        return [
            "_conf" => [
                "returnUrl" => $uri,
                "objectId" => $this->objectId,
                "objectType" => $this->objectType,
            ]
        ];
    }

    public function addTabContent(TabContent $tabContent)
    {
        if (empty($tabContent->getId()) && $this->options["default_icon"]) {
            $tabContent->setIcon($this->options["default_icon"]);
        }
        $this->tab->addTabContent($tabContent);
    }

    /**
     * Genera una instancia de tabcontent
     * @param array $options
     * @return TabContent
     */
    public function newTabContent(array $options = [])
    {
        return new TabContent($options);
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
            "title" => $this->trans($this->options["document_manager"]["title"], [], "messages"),
            "icon" => $this->options["document_manager"]["icon"],
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
            "title" => $this->trans($this->options["history_manager"]["title"], [], "messages"),
            "icon" => $this->options["history_manager"]["icon"],
        ]);
        $options = $resolver->resolve($options);
        if (!$this->paginator) {
            throw new RuntimeException(sprintf("El servicio de paginador %s debe ser seteado.", PaginatorInterface::class));
        }
        $request = $this->requestStack->getCurrentRequest();

        $tabContentHistory = new TabContent($options);
        $tabContentHistory->setViewParameters(function()use($request,$options) {
            $sort = $request->get("sort");
            $direction = $request->get("direction");

            if(!in_array($sort,["e.user","e.description","e.createdAt"])){
                $sort = null;
            }
            
            $page = $request->get("page", 1);
            $limit = $request->get("limit", 20);
            $paginator = $this->getObjectDataManager()->histories()->getPaginator([
                "sort" => $sort,
                "direction" => $direction,
            ]);
            $histories = $this->paginator->paginate($paginator, $page, $limit, $options);
            $parameters = [];
            $parameters["histories"] = $histories;
            
            return $parameters;
        });
        $this->tab->addTabContent($tabContentHistory);
        return $tabContentHistory;
    }

    /**
     * Añade la tab de historiales
     * @param array $options
     */
    public function addNotes(array $options = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            "add_content_div" => false,
            "template" => $this->options["note_manager"]["template"],
            "title" => $this->trans($this->options["note_manager"]["title"], [], "messages"),
            "icon" => $this->options["note_manager"]["icon"],
        ]);
        $options = $resolver->resolve($options);
        $tabContentHistory = new TabContent($options);
        $tabContentHistory->setViewParameters([
            "form_notes" => $this->createForm(NotesType::class)->createView(),
        ]);
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
     * Agrega un modelo valido por chain
     * @param $model
     * @throws InvalidArgumentException
     */
    public function addModel($model)
    {
        if (isset($this->models[$model])) {
            throw new InvalidArgumentException(sprintf("The model to '%s' is already added", $model));
        }
        $this->models[] = $model;
    }

    /**
     * Renderiza el modulo para generar archivos del moduloe
     * @param $entity
     * @param type $idChain
     * @return type
     */
    public function renderFilesGenerated($entity)
    {
        $chain = $this->getObjectDataManager()->exporter()->resolveChainModel();
        $choices = [];
        $models = $chain->getModels();
        if (!is_null($this->models) && is_array($this->models)) {
            foreach ($models as $model) {
                if (in_array($model->getId(), $this->models)) {
                    $choices[$this->trans($model->getName()) . " [" . strtoupper($model->getFormat()) . "]"] = $model->getName();
                }
            }
        } else {
            foreach ($models as $model) {
                $choices[$this->trans($model->getName()) . " [" . strtoupper($model->getFormat()) . "]"] = $model->getName();
            }
        }

        $form = $this->createForm(ExporterType::class, $choices);
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
     * Renderiza el modulo para generar archivos del moduloe
     * @param $entity
     * @param type $idChain
     * @return type
     */
    public function renderFilesUploaded($entity)
    {
        $chain = $this->getObjectDataManager()->exporter()->resolveChainModel();
        $form = $this->createForm(UploadType::class);
        $this->parametersToView["parameters_to_route"]["_conf"]["folder"] = "uploaded";
        return $this->container->get('templating')->render($this->options["exporter"]["template_upload"],
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
    public function documentsDownloadUrl($fileName, $disposition = \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT, $folder = "uploaded")
    {
        $url = null;
        $this->getObjectDataManager()->documents()->folder($folder);
        $file = $this->getObjectDataManager()->documents()->get($fileName);
        if ($file !== null) {
            $params = $this->getParametersToRoute();
            $params["_conf"]["folder"] = $folder;
            $params["filename"] = $fileName;
            $params["disposition"] = $disposition;
            $url = $this->generateUrl("tabs_object_manager_documents_get", $params);
        }
        return $url;
    }

    /**
     * Renderiza la tab actual
     * @return Response
     */
    public function render()
    {
        $tab = $this->tab;
        $resolveCurrentTab = $tab->resolveCurrentTab();

        $extractParameters = function($parameters, array $base) {
            if (is_callable($parameters)) {
                $parameters = $parameters($base);
                if (!is_array($parameters)) {
                    throw new RuntimeException(sprintf("tab->getViewParameters debe retornar un array pero retorno '%s'", gettype($parameters)));
                }
            }
            if (is_null($parameters)) {
                $parameters = [];
            }
            if (!is_array($parameters)) {
                throw new RuntimeException(sprintf("El parametro de retorno deberia ser un array pero retorno '%s'", gettype($parameters)));
            }
            return $parameters;
        };
        $parameters = $extractParameters($this->tab->getViewParameters(), []);
        $request = $this->requestStack->getCurrentRequest();
        //Renderizar parametros de la tab solo si se esta renderizando la tab y no el padre.
        if (!empty($request->get(Tab::NAME_CURRENT_TAB))) {
            $parameters = array_merge($parameters, $extractParameters($resolveCurrentTab->getViewParameters(), $parameters));
        }
        $parameters = array_merge($parameters, $this->parametersToView);
        $parameters["tab"] = $tab;
        $parameters["objectDataManager"] = $this->getObjectDataManager();
//        var_dump(array_keys($parameters));
//        die;

        $template = $this->tab->getTemplate($this->tab->getOption("default_template"));
        $view = $this->container->get("twig")->render($template, $parameters);
        return new Response($view);
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
    protected function trans($id, array $parameters = array(), $domain = 'messages')
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

    /**
     * @required
     * @param PaginatorInterface $paginatorInterface
     * @return $this
     */
    public function setPaginatorInterface(PaginatorInterface $paginatorInterface)
    {
        $this->paginator = $paginatorInterface;
        return $this;
    }

}
