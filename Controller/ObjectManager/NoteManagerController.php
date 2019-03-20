<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Controller\ObjectManager;

use Symfony\Component\HttpFoundation\Request;
use Tecnocreaciones\Bundle\ToolsBundle\Form\Tab\NotesType;

/**
 * Controlador del manejador de notas
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class NoteManagerController extends ManagerController
{
    public function addAction(Request $request)
    {
        $objectDataManager = $this->getObjectDataManager($request);
        $form = $this->createForm(NotesType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $options = [
                "user" => $user,
            ];
            $publicNote = $form->get("publicNote")->getData();
            $privateNote = $form->get("privateNote")->getData();
            if (!empty($publicNote) && strlen($publicNote) > 2) {
                $objectDataManager->notes()->addPublic($publicNote,$options);
            }
            if (!empty($privateNote) && strlen($privateNote) > 2) {
                $objectDataManager->notes()->addPrivate($privateNote,$options);
            }
        }
        return $this->toReturnUrl();
    }

}
