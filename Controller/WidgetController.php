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
use Tecnocreaciones\Bundle\ToolsBundle\Model\Block\WidgetInterface;
use Tecnocreaciones\Bundle\ToolsBundle\Model\Block\Manager\BlockWidgetBoxManagerInterface;
use Tecnocreaciones\Bundle\ToolsBundle\Service\GridWidgetBoxService;
use Tecnoready\Common\Service\Block\WidgetManager;

/**
 * Controlador de los bloques widgets
 *
 * @author Carlos Mendoza <inhack20@tecnocreaciones.com>
 */
class WidgetController extends Controller 
{
    /**
     * @var WidgetManager 
     */
    private $widgetManager;
    
    public function __construct(WidgetManager $widgetManager)
    {
        $this->widgetManager = $widgetManager;
    }
    
    public function indexAction(Request $request) 
    {
        $definitions = $this->widgetManager->getWidgetsByGroup();;
//        $this->denyAccessUnlessGranted("ROLE_APP_WIDGET_*");
        
        return $this->render(
            'TecnocreacionesToolsBundle:BlockWidgetBox:index.html.twig',array(
                'gridWidgetBoxService' => $this->widgetManager,
                'definitions' => $definitions
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
        $definitionBlockGrid = $this->widgetManager->getWidget($type);
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
                $blockWidgetBox = $this->widgetManager->getAdapter()->buildBlockWidget();
                
                $data = $form->getData();
                $events = $definitionBlockGrid->getEvents();
                $name = $data['name'];
                if($definitionBlockGrid->hasPermission($name) === false){
                    throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
                }
                
                $blockWidgetBox->setType($data['type']);
                $blockWidgetBox->setName($name);
                $blockWidgetBox->setSetting('template',$data['template']);
                $blockWidgetBox->setEvent($events[$data['event']]);
                $blockWidgetBox->setCreatedAt(new \DateTime());
                $blockWidgetBox->setEnabled(true);
                $this->widgetManager->getAdapter()->save($blockWidgetBox);
                
                $this->getFlashBag()->add('success',  $this->trans('widget_box.flashes.success'));
                
                return $this->redirect($this->generateUrl('block_widget_box_index',[
                    "eventName" => $request->get("eventName"),
                ]));
            }
        }
        
        return $this->render(
            'TecnocreacionesToolsBundle:BlockWidgetBox:create.html.twig',array(
                'definitionBlockGrid' => $definitionBlockGrid,
                'form' => $form->createView(),
            )
        );
    }
    
    function addAllAction(Request $request) 
    {
        $type = $request->get('type');
        $name = $request->get('name');
        $i = $this->widgetManager->addAll($type,$name);
        if($i > 0){
        }
        $this->getFlashBag()->add('success',  $this->trans('widget_box.flashes.success_all',array(
            '%num%' => $i,
        )));

        return $this->redirect($this->generateUrl('block_widget_box_index',[
            "eventName" => $request->get("eventName"),
        ]));
    }
    
    public function deleteAllAction(Request $request) {
        $eventName = $request->get('eventName');

        $i = $this->widgetManager->clearAllByEvent($eventName);
        $this->getFlashBag()->add('success',  $this->trans('widget_box.flashes.success_all_remove',array(
            '%num%' => $i,
        )));
        
        return $this->redirect($this->generateUrl('block_widget_box_index',[
            "eventName" => $request->get("eventName"),
        ]));
    }
    
    public function deleteAction(Request $request)
    {
        $blockWidgetBox = $this->widgetManager->getAdapter()->find($request->get('id'));
        if(!$blockWidgetBox){
            throw $this->createNotFoundException();
        }
        
        $success = $this->widgetManager->getAdapter()->remove($blockWidgetBox);
        $data = array(
            'success' => $success
        );
        $response = new \Symfony\Component\HttpFoundation\JsonResponse($data);
        return $response;
    }
    
    public function updateAction(Request $request) 
    {
        $data = $request->get('data');
        
        $ids = array();
        $dataById = array();
        foreach ($data as $value) {
            $ids[] = $value['id'];
            $dataById[$value['id']] = $value;
        }
        
        $widgetsBox = $this->widgetManager->getAdapter()->findByIds($ids);
        foreach ($widgetsBox as $widgetBox) 
        {
            $dataWidget = $dataById[$widgetBox->getId()];
            $widgetBox->setSetting('positionX',$dataWidget['row']);
            $widgetBox->setSetting('positionY',$dataWidget['col']);
            $widgetBox->setSetting('sizeX',$dataWidget['size_x']);
            $widgetBox->setSetting('sizeY',$dataWidget['size_y']);
            $widgetBox->setSetting('widgetColor',$dataWidget['widget_color']);
            
            $this->widgetManager->getAdapter()->save($widgetBox,false);
        }
        $this->widgetManager->getAdapter()->save($widgetBox,true);
        
        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
            'success' => true
        ));
    }
    
    public function minimizeAction(Request $request) 
    {
        $id = $request->get('id');
        $data = $request->get('data');
        
        $widgetBox = $this->widgetManager->getAdapter()->find($id);
        $widgetBox->setSetting('isCollapsed',true);
        $widgetBox->setSetting('oldSizeY',$data[0]['size_y']);
        
        $this->widgetManager->getAdapter()->save($widgetBox,true);
        
        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
            'success' => true
        ));
    }
    
    public function maximizeAction(Request $request) 
    {
        $id = $request->get('id');
        
        $widgetBox = $this->widgetManager->getAdapter()->find($id);
        $widgetBox->setSetting('isCollapsed',false);
        
        $this->widgetManager->getAdapter()->save($widgetBox,true);
        
        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
            'success' => true
        ));
    }
    
    public function refreshAction(Request $request) 
    {
        $id = $request->get('id');
        
        $widgetBox = $this->widgetManager->getAdapter()->find($id);
        
        $blockHelper = $this->widgetManager;
        $widgetBox->setSetting('name',$widgetBox->getName());
        $widgetBox->setSetting('isCollapsed',false);
        $widgetBox->setSetting('blockBase','TecnocreacionesToolsBundle:WidgetBox:block_widget_box_empty.html.twig');
        
        $blockContent = $blockHelper->render(array(
            'widget' => $widgetBox->getType()
        ), $widgetBox->getSettings());
        return new \Symfony\Component\HttpFoundation\Response($blockContent);
    }
    
    private function buildFormWidget(FormBuilderInterface &$formBuilderWidget,  WidgetInterface $definitionBlockGrid) 
    {
        $templatesData = $eventsData = $nameData = null;
        $names = $definitionBlockGrid->getNames();
        $events = $definitionBlockGrid->getEvents();
        $templates = $definitionBlockGrid->getTemplates();
        
        if(count($names) == 1){
            $namesKeys = array_keys($names);
            $nameData = $namesKeys[0];
        }
        if(count($templates) == 1){
            $templatesKeys = array_keys($templates);
            $templatesData = $templatesKeys[0];
        }
        if(count($templates) == 1){
            $eventsKeys = array_keys($events);
            $eventsData = $eventsKeys[0];
        }
        $namesFilter = array();
        foreach ($names as $name => $parameters) {
            if($definitionBlockGrid->hasPermission($name) === false){
                continue;
            }
            $namesFilter[$name] = $name;
        }
        $emptyValue = '';
        $formBuilderWidget
            ->add('type','hidden',array(
                'data' => $definitionBlockGrid->getType()
            ))
            ->add('name','choice',array(
                'label' => 'widget_box.form.name',
                'choices' => $namesFilter,
                'empty_value' => $emptyValue,
                'data' => $nameData
            ))
            ->add('template','choice',array(
                'label' => 'widget_box.form.template',
                'choices' => $templates,
                'empty_value' => $emptyValue,
                'data' => $templatesData,
            ))
            ->add('event','choice',array(
                'label' => 'widget_box.form.event',
                'choices' => $events,
                'empty_value' => $emptyValue,
                'data' => $eventsData,
            ))
//            ->add('send','submit')
//            ->add('cancel','button')
            ;
    }

    /**
     * 
     * @return BlockWidgetBoxManagerInterface
     */
    private function getWidgetBoxManager()
    {
        return $this->get($this->container->getParameter('tecnocreaciones_tools.widget.widget_adapter'));
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
