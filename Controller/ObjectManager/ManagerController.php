<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Controller\ObjectManager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Tecnoready\Common\Service\ObjectManager\ObjectDataManager;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base de controladores del manager
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class ManagerController extends AbstractController
{
    /**
     * Configuracion de la instancia
     * @var array
     */
    protected $config;
    
    /**
     * Obtiene y configura desde el request el ObjectDataManager
     * @param Request $request
     * @return ObjectDataManager
     */
    protected function getObjectDataManager(Request $request)
    {
        $objectDataManager = $this->container->get(ObjectDataManager::class);
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            "folder" => null,
        ]);
        $resolver->setRequired(["returnUrl","objectId","objectType"]);
        $config = $resolver->resolve($request->get("_conf"));
        $objectDataManager->configure($config["objectId"],$config["objectType"]);
        $objectDataManager->documents()->folder($config["folder"]);
        $this->config = $config;
        return $objectDataManager;
    }
    
    /**
     * Redirecciona a la url de retorno
     * @return type
     */
    protected function toReturnUrl()
    {
        return $this->redirect($this->config["returnUrl"]);
    }
}
