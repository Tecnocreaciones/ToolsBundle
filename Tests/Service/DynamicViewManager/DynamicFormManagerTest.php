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
        echo(json_encode($data,JSON_PRETTY_PRINT));
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
   "title":"Titulo del formulario",
   "form":{
      "title":"dynamic_form",
      "type":"object",
      "properties":{
         "select_options":{
            "type":"string",
            "title":"Opciones",
            "widget":"choice",
            "data":"",
            "constraints":[
               {
                  "message":"This value should not be blank.",
                  "name":"NotBlank",
                  "fullyQualifiedName":"Tecnocreaciones\\Bundle\\ToolsBundle\\Custom\\Liform\\Constraints\\NotBlank"
               },
               {
                  "maxMessage":"This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.",
                  "minMessage":"This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.",
                  "exactMessage":"This value should have exactly {{ limit }} character.|This value should have exactly {{ limit }} characters.",
                  "max":null,
                  "min":3,
                  "name":"Length",
                  "fullyQualifiedName":"Tecnocreaciones\\Bundle\\ToolsBundle\\Custom\\Liform\\Constraints\\Length"
               }
            ],
            "required":true,
            "disabled":false
         },
         "date_at":{
            "type":"string",
            "title":"Fecha",
            "widget":"date",
            "data":"",
            "constraints":[
               {
                  "message":"This value should not be blank.",
                  "name":"NotBlank",
                  "fullyQualifiedName":"Tecnocreaciones\\Bundle\\ToolsBundle\\Custom\\Liform\\Constraints\\NotBlank"
               },
               {
                  "maxMessage":"This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.",
                  "minMessage":"This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.",
                  "exactMessage":"This value should have exactly {{ limit }} character.|This value should have exactly {{ limit }} characters.",
                  "max":null,
                  "min":3,
                  "name":"Length",
                  "fullyQualifiedName":"Tecnocreaciones\\Bundle\\ToolsBundle\\Custom\\Liform\\Constraints\\Length"
               }
            ],
            "required":true,
            "disabled":false
         },
         "file_image":{
            "type":"string",
            "title":"Archivo de imagen",
            "widget":"file_widget",
            "data":"",
            "constraints":[
               {
                  "message":"This value should not be blank.",
                  "name":"NotBlank",
                  "fullyQualifiedName":"Tecnocreaciones\\Bundle\\ToolsBundle\\Custom\\Liform\\Constraints\\NotBlank"
               },
               {
                  "maxMessage":"This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.",
                  "minMessage":"This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.",
                  "exactMessage":"This value should have exactly {{ limit }} character.|This value should have exactly {{ limit }} characters.",
                  "max":null,
                  "min":3,
                  "name":"Length",
                  "fullyQualifiedName":"Tecnocreaciones\\Bundle\\ToolsBundle\\Custom\\Liform\\Constraints\\Length"
               }
            ],
            "required":true,
            "disabled":false
         },
         "check_option":{
            "type":"boolean",
            "title":"Checkbox",
            "widget":"checkbox",
            "constraints":[
               {
                  "message":"This value should not be blank.",
                  "name":"NotBlank",
                  "fullyQualifiedName":"Tecnocreaciones\\Bundle\\ToolsBundle\\Custom\\Liform\\Constraints\\NotBlank"
               },
               {
                  "maxMessage":"This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.",
                  "minMessage":"This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.",
                  "exactMessage":"This value should have exactly {{ limit }} character.|This value should have exactly {{ limit }} characters.",
                  "max":null,
                  "min":3,
                  "name":"Length",
                  "fullyQualifiedName":"Tecnocreaciones\\Bundle\\ToolsBundle\\Custom\\Liform\\Constraints\\Length"
               }
            ],
            "required":true,
            "disabled":false
         },
         "texto_normal":{
            "type":"string",
            "title":"Texto corto",
            "widget":"text",
            "data":"",
            "constraints":[
               {
                  "message":"This value should not be blank.",
                  "name":"NotBlank",
                  "fullyQualifiedName":"Tecnocreaciones\\Bundle\\ToolsBundle\\Custom\\Liform\\Constraints\\NotBlank"
               },
               {
                  "maxMessage":"This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.",
                  "minMessage":"This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.",
                  "exactMessage":"This value should have exactly {{ limit }} character.|This value should have exactly {{ limit }} characters.",
                  "max":null,
                  "min":3,
                  "name":"Length",
                  "fullyQualifiedName":"Tecnocreaciones\\Bundle\\ToolsBundle\\Custom\\Liform\\Constraints\\Length"
               }
            ],
            "required":true,
            "disabled":false
         },
         "texto_largo":{
            "type":"string",
            "title":"Texto largo",
            "widget":"textarea",
            "data":"",
            "constraints":[
               {
                  "message":"This value should not be blank.",
                  "name":"NotBlank",
                  "fullyQualifiedName":"Tecnocreaciones\\Bundle\\ToolsBundle\\Custom\\Liform\\Constraints\\NotBlank"
               },
               {
                  "maxMessage":"This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.",
                  "minMessage":"This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.",
                  "exactMessage":"This value should have exactly {{ limit }} character.|This value should have exactly {{ limit }} characters.",
                  "max":null,
                  "min":3,
                  "name":"Length",
                  "fullyQualifiedName":"Tecnocreaciones\\Bundle\\ToolsBundle\\Custom\\Liform\\Constraints\\Length"
               }
            ],
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
         "select_options",
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
