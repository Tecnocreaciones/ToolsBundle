<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Controller\ObjectManager;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use FOS\RestBundle\Util\Codes;
use Tecnocreaciones\Bundle\ToolsBundle\Form\Tab\ExporterType;
use Tecnocreaciones\Bundle\ToolsBundle\Form\Tab\UploadType;

/**
 * Controlador para exportar los documentos
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class ExporterController extends ManagerController
{
    /**
     * Genera un documento
     */
    public function generateAction(Request $request) 
    {
        $objectDataManager = $this->getObjectDataManager($request);
        $chain = $objectDataManager->exporter()->resolveChainModel();
        $choices = [];
        $models = $chain->getModels();
        foreach ($models as $model) {
            $choices[$model->getName()] = $model->getName();
        }
        $form = $this->createForm(ExporterType::class,$choices);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $options = [
                "fileName" => $request->get("fileName"),
                "request" => $request
            ];
            $name = $form->get("name")->getData();
            $objectDataManager->exporter()->generateWithSource($name,$options);
        }
        
        return $this->toReturnUrl();
    }
    
    public function deleteAction(Request $request)
    {
        $objectDataManager = $this->getObjectDataManager($request);
        $objectDataManager->documents()->delete($request->get("filename"));
        return $this->toReturnUrl();
    }
    
    public function getAction(Request $request)
    {
        $objectDataManager = $this->getObjectDataManager($request);
        $file = $objectDataManager->documents()->get($request->get("filename"));
        $response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        return $response;
    }

    public function downloadAction(Request $request)
    {
        $objectDataManager = $this->getObjectDataManager($request);
        $file = $objectDataManager->documents()->get($request->get("filename"));
        $response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($file);
        $response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($file->getRealPath());
        $response->setContentDisposition(\Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        return $response;
    }

    /**
     * Cargar documento
     *  
     * @author Máximo Sojo <maxsojo13@gmail.com>
     * @param  Request $request
     * @return File
     */
    public function uploadAction(Request $request)
    {
        $objectDataManager = $this->getObjectDataManager($request);
        $chain = $objectDataManager->exporter()->resolveChainModel();
        $choices = [];
        $models = $chain->getModels();
        foreach ($models as $model) {
            $choices[$model->getName()] = $model->getName();
        }
        $form = $this->createForm(UploadType::class,$choices);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $file = $form->get("file")->getData();
            $objectDataManager->exporter()->uploadWithSource($file);
        }
        
        return $this->toReturnUrl();
    }

    public function allAction(Request $request)
    {
        $arrayFile = [];
        $objectDataManager = $this->getObjectDataManager($request);
        $documentsManager = $objectDataManager->documents();
        $files = $documentsManager->getAll();
        foreach ($files as $file) {
            $arrayFile[] = $documentsManager->toArray($file);
        }

        return new \Symfony\Component\HttpFoundation\JsonResponse(["files" => $arrayFile]);
    }
}
