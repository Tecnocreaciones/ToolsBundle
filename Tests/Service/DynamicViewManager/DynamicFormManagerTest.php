<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\DynamicViewManager;

use Tecnocreaciones\Bundle\ToolsBundle\Service\DynamicViewManager\DynamicFormManager;
use Tecnocreaciones\Bundle\ToolsBundle\Tests\BaseWebTestCase;
use Symfony\Component\Form\FormInterface;
use Tecnocreaciones\Bundle\ToolsBundle\Form\DynamicView\DynamicTestType;

/**
 * Pruebas del constructor dinamico de vistas de formulario
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class DynamicFormManagerTest extends BaseWebTestCase
{
    public function testCreate()
    {
        $dynamicFormManager = $this->get(DynamicFormManager::class);
        if(false){
            $dynamicFormManager = new DynamicFormManager();
        }
        $form = $this->createForm(DynamicTestType::class);
        $dynamicForm = $dynamicFormManager->start();
        $dynamicForm->setTitle("Titulo");
        $dynamicForm->setForm($form);
        
        $data = $dynamicForm->end();
//        echo(json_encode($data));
        $this->assertNotNull($data);
    }
    
    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @final
     */
    protected function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->get('form.factory')->create($type, $data, $options);
    }
    
    private function getJsonResult()
    {
$result = <<<EOF
{
   "title":"Titulo",
   "form":{
      "title":"dynamic_form",
      "type":"object",
      "properties":{
         "options":{
            "choices":[
               {
                  "id":"a",
                  "label":"opcion 1"
               },
               {
                  "id":"b",
                  "label":"opcion 2"
               }
            ],
            "type":"string",
            "title":"Opciones",
            "widget":"choice",
            "required":true,
            "disabled":false
         },
         "date_at":{
            "type":"string",
            "title":"Fecha",
            "widget":"date",
            "data":"",
            "required":true,
            "disabled":false
         },
         "file_image":{
            "type":"string",
            "title":"Archivo de imagen",
            "widget":"file_widget",
            "required":true,
            "disabled":false
         },
         "check_option":{
            "type":"boolean",
            "title":"Checkbox",
            "widget":"checkbox",
            "required":true,
            "disabled":false
         },
         "texto_normal":{
            "type":"string",
            "title":"Texto corto",
            "widget":"text",
            "data":"",
            "required":true,
            "disabled":false
         },
         "texto_largo":{
            "type":"string",
            "title":"Texto largo",
            "widget":"textarea",
            "data":"",
            "required":true,
            "disabled":false
         },
         "submit":{
            "type":"string",
            "title":"Boton submit",
            "widget":"submit",
            "render_in":"form_bottom",
            "required":null,
            "disabled":false
         }
      },
      "required":[
         "options",
         "date_at",
         "file_image",
         "check_option",
         "texto_normal",
         "texto_largo"
      ],
      "action":"",
      "method":"POST"
   }
}
EOF;
    }
}
