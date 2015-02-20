<?php

/*
 * This file is part of the TecnoCreaciones package.
 * 
 * (c) www.tecnocreaciones.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Tecnocreaciones\Bundle\ToolsBundle\Model\Block\DefinitionBlockWidgetBoxInterface;
use Tecnocreaciones\Bundle\ToolsBundle\Model\Block\Manager\BlockWidgetBoxManagerInterface;
use Tecnocreaciones\Bundle\ToolsBundle\Service\GridWidgetBoxService;

/**
 * Controlador de los bloques widgets
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class BlockWidgetBoxController extends Controller 
{
    public function indexAction(Request $request) 
    {
        $gridWidgetBoxService = $this->getGridWidgetBoxService();
        $definitionsBlockGrid = $gridWidgetBoxService->getDefinitionsBlockGrid();
        
        
        foreach ($definitionsBlockGrid as $definitionBlockGrid)
        {
            if($definitionBlockGrid->hasPermission() === true){
                
            }
        }
        
        return $this->render(
            'TecnocreacionesToolsBundle:BlockWidgetBox:index.html.twig',array(
                'definitionsBlockGrid' => $definitionsBlockGrid
            )
        );
    }
    
    public function createAction(Request $request)
    {
        $type = $request->get('type');
        if($request->isMethod('POST')){
            $data = $request->get('form');
            $type = $data['type'];
        }
        $gridWidgetBoxService = $this->getGridWidgetBoxService();
        
        $definitionBlockGrid = $gridWidgetBoxService->getDefinitionBlockGrid($type);
        if($definitionBlockGrid->hasPermission() == false){
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }
        $formBuilderWidget = $this->createFormBuilder(array(),array(
            'csrf_protection' => true,
            'translation_domain' => $definitionBlockGrid->getTranslationDomain(),
            'action' => $this->generateUrl('block_widget_box_create'),
        ));
        
        $this->buildFormWidget($formBuilderWidget, $definitionBlockGrid);
        
        $form = $formBuilderWidget->getForm();
        if($request->isMethod('POST')){
            $form->submit($request);
            if($form->isValid()){
                $widgetBoxManager = $this->getWidgetBoxManager();
                $blockWidgetBox = $widgetBoxManager->buildBlockWidget();
                
                $data = $form->getData();
                $events = $definitionBlockGrid->getEvents();
                $names = $definitionBlockGrid->getNames();
                
                $blockWidgetBox->setType($data['type']);
                $blockWidgetBox->setName($names[$data['name']]);
                $blockWidgetBox->setSetting('template',$data['template']);
                $blockWidgetBox->setEvent($events[$data['event']]);
                $blockWidgetBox->setCreatedAt(new \DateTime());
                $blockWidgetBox->setEnabled(true);
                
                $widgetBoxManager->save($blockWidgetBox);
                
                $this->getFlashBag()->add('success',  $this->trans('widget_box.flashes.success'));
                
                return $this->redirect($this->generateUrl('block_widget_box_index'));
            }
        }
        
        return $this->render(
            'TecnocreacionesToolsBundle:BlockWidgetBox:create.html.twig',array(
                'form' => $form->createView(),
            )
        );
    }
    
    function addAllAction(Request $request) 
    {
        $type = $request->get('type');
        $gridWidgetBoxService = $this->getGridWidgetBoxService();
        
        $definitionBlockGrid = $gridWidgetBoxService->getDefinitionBlockGrid($type);
        if($definitionBlockGrid->hasPermission() == false){
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }
        $events = $definitionBlockGrid->getEvents();
        $names = $definitionBlockGrid->getNames();
        $templates = $definitionBlockGrid->getTemplates();
        
        $templatesKeys = array_keys($templates);
        $widgetBoxManager = $this->getWidgetBoxManager();
        $i = 0;
        foreach ($names as $name) {
            $blockWidgetBox = $widgetBoxManager->buildBlockWidget();
            $blockWidgetBox->setType($type);
            $blockWidgetBox->setName($name);
            $blockWidgetBox->setSetting('template',$templatesKeys[0]);
            $blockWidgetBox->setEvent($events[0]);
            $blockWidgetBox->setCreatedAt(new \DateTime());
            $blockWidgetBox->setEnabled(true);
            $widgetBoxManager->save($blockWidgetBox);
            $i++;
        }
        $this->getFlashBag()->add('success',  $this->trans('widget_box.flashes.success_all',array(
            '%num%' => $i,
        )));

        return $this->redirect($this->generateUrl('block_widget_box_index'));
    }
    
    public function deleteAction(Request $request)
    {
        $widgetBoxManager = $this->getWidgetBoxManager();
        $blockWidgetBox = $widgetBoxManager->find($request->get('id'));
        if(!$blockWidgetBox){
            throw $this->createNotFoundException();
        }
        
        $success = $widgetBoxManager->remove($blockWidgetBox);
        $data = array(
            'success' => $success
        );
        $response = new \Symfony\Component\HttpFoundation\JsonResponse($data);
        return $response;
    }
    
    public function updateAction(Request $request) 
    {
        $widgetBoxManager = $this->getWidgetBoxManager();
        $data = $request->get('data');
        
        $ids = array();
        $dataById = array();
        foreach ($data as $value) {
            $ids[] = $value['id'];
            $dataById[$value['id']] = $value;
        }
        
        $widgetsBox = $widgetBoxManager->findByIds($ids);
        foreach ($widgetsBox as $widgetBox) 
        {
            $dataWidget = $dataById[$widgetBox->getId()];
            $widgetBox->setSetting('positionX',$dataWidget['row']);
            $widgetBox->setSetting('positionY',$dataWidget['col']);
            $widgetBox->setSetting('sizeX',$dataWidget['size_x']);
            $widgetBox->setSetting('sizeY',$dataWidget['size_y']);
            $widgetBox->setSetting('widgetColor',$dataWidget['widget_color']);
            
            $widgetBoxManager->save($widgetBox,false);
        }
        $widgetBoxManager->save($widgetBox,true);
        
        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
            'success' => true
        ));
    }
    
    public function minimizeAction(Request $request) 
    {
        $id = $request->get('id');
        $data = $request->get('data');
        
        $widgetBoxManager = $this->getWidgetBoxManager();
        
        $widgetBox = $widgetBoxManager->find($id);
        $widgetBox->setSetting('isCollapsed',true);
        $widgetBox->setSetting('oldSizeY',$data[0]['size_y']);
        
        $widgetBoxManager->save($widgetBox,true);
        
        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
            'success' => true
        ));
    }
    
    public function maximizeAction(Request $request) 
    {
        $id = $request->get('id');
        $widgetBoxManager = $this->getWidgetBoxManager();
        
        $widgetBox = $widgetBoxManager->find($id);
        $widgetBox->setSetting('isCollapsed',false);
        
        $widgetBoxManager->save($widgetBox,true);
        
        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
            'success' => true
        ));
    }
    
    public function refreshAction(Request $request) 
    {
        $id = $request->get('id');
        $widgetBoxManager = $this->getWidgetBoxManager();
        
        $widgetBox = $widgetBoxManager->find($id);
        
        $blockHelper = $this->getBlockHelper();
        $widgetBox->setSetting('name',$widgetBox->getName());
        $widgetBox->setSetting('isCollapsed',false);
        $widgetBox->setSetting('blockBase','TecnocreacionesToolsBundle:WidgetBox:block_widget_box_empty.html.twig');
        
        $blockContent = $blockHelper->render(array(
            'type' => $widgetBox->getType()
        ), $widgetBox->getSettings());
        return new \Symfony\Component\HttpFoundation\Response($blockContent);
    }
    
    private function buildFormWidget(FormBuilderInterface &$formBuilderWidget,  DefinitionBlockWidgetBoxInterface $definitionBlockGrid) 
    {
        $emptyValue = '';
        $formBuilderWidget
            ->add('type','hidden',array(
                'data' => $definitionBlockGrid->getType()
            ))
            ->add('name','choice',array(
                'label' => 'widget_box.form.name',
                'choices' => $definitionBlockGrid->getNames(),
                'empty_value' => $emptyValue,
            ))
            ->add('template','choice',array(
                'label' => 'widget_box.form.template',
                'choices' => $definitionBlockGrid->getTemplates(),
                'empty_value' => $emptyValue,
            ))
            ->add('event','choice',array(
                'label' => 'widget_box.form.event',
                'choices' => $definitionBlockGrid->getEvents(),
                'empty_value' => $emptyValue,
            ))
            ->add('send','submit')
            ->add('cancel','button')
            ;
    }

    /**
     * 
     * @return \Sonata\BlockBundle\Templating\Helper\BlockHelper
     */
    private function getBlockHelper()
    {
        return $this->get('sonata.block.templating.helper');
    }


    /**
     * 
     * @return GridWidgetBoxService
     */
    private function getGridWidgetBoxService()
    {
        return $this->get('tecnocreaciones_tools.service.grid_widget_box');
    }
    
    /**
     * 
     * @return BlockWidgetBoxManagerInterface
     */
    private function getWidgetBoxManager()
    {
        return $this->get($this->container->getParameter('tecnocreaciones_tools.block_grid.widget_box_manager'));
    }
    
    protected function trans($id, array $parameters = array(), $domain = 'widgetBox') {
        return $this->get('translator')->trans($id, $parameters, $domain);
    }
    
    /**
     * @return \Symfony\Component\HttpFoundation\Session\Flash\FlashBag
     */
    protected function getFlashBag()
    {
        return $this->get('session')->getBag('flashes');
    }
}
