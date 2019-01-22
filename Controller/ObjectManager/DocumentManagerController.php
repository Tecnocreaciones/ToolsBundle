<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Controller\ObjectManager;

use Symfony\Component\HttpFoundation\Request;
use Tecnocreaciones\Bundle\ToolsBundle\Form\Tab\DocumentsType;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Controlador de manejador de documentos
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DocumentManagerController extends ManagerController
{
    public function uploadAction(Request $request)
    {
        $objectDataManager = $this->getObjectDataManager($request);
        
        $form = $this->createForm(DocumentsType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $documents = $form->get("documents")->getData();
            foreach ($documents as $document) {
                $objectDataManager->documents()->upload($document);
            }
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
        $disposition = $request->get("disposition",ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        $file = $objectDataManager->documents()->get($request->get("filename"));
        $response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($file);
        $response->setContentDisposition($disposition);
        return $response;
    }
    
}
