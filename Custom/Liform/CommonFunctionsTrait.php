<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Funciones comunes
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
trait CommonFunctionsTrait
{

    /**
     * @var ValidatorInterface
     */
    private $validator;
    protected $formView;

    protected function initCommonCustom(FormInterface $form)
    {
        $this->formView = $form->createView();
    }

    protected function addCommonCustom(FormInterface $form, array $schema)
    {
        $formView = $this->formView;
//        var_dump($formView->vars);
        $formRoot = $form->getRoot();

        $schema["full_name"] = $formView->vars["full_name"];
//        $schema["full_name"] = $formView->vars["name"];
        $schema = $this->addConstraints($form, $schema, $formRoot);
        $schema = $this->addDateParams($form, $schema);
        $schema = $this->addCommonConfigOptions($form, $schema);
        $schema = $this->addFromAttr($form, $schema);

        return $schema;
    }

    /**
     * Añade opciones de configuracion extra en el parametro "attr" que es dinamico
     * @param FormInterface $form
     * @param array $schema
     * @return type
     */
    protected function addFromAttr(FormInterface $form, array $schema)
    {
        if ($attr = $form->getConfig()->getOption('attr')) {
            $options = [
                "help_auto_hide", "icon"
            ];
            foreach ($options as $option) {
                if (isset($attr[$option])) {
                    $schema[$option] = $schema['attr'][$option];
                    unset($schema['attr'][$option]);
                }
            }
            if (count($schema['attr']) == 0) {
                unset($schema['attr']);
            }
        }

        return $schema;
    }

    protected function addDateParams(FormInterface $form, array $schema)
    {
        if ($form->getConfig()->hasOption("format_from_server")) {
            $schema["format_from_server"] = $form->getConfig()->getOption("format_from_server");
            $schema["format_to_server"] = $form->getConfig()->getOption("format_to_server");
        }
        return $schema;
    }

    /**
     * Opciones comunes a configurar en los tipos para no agregar uno por uno
     * @param FormInterface $form
     * @param array $schema
     * @return type
     */
    protected function addCommonConfigOptions(FormInterface $form, array $schema)
    {
        $options = ["mode","crop_imagen_mode","placeholder" => function($value){
            if(!is_string($value)){
                $value = null;
            }
            return $value;
        }];
        foreach ($options as $key => $option) {
            if(is_callable($option)){
                //
            }else{
                $key = $option;
                $option = function($value){
                    return $value;
                };
            }
            if ($form->getConfig()->hasOption($key)) {
                $schema[$key] = $option($form->getConfig()->getOption($key));
            }
        }
        return $schema;
    }

    /**
     * Añade las validaciones
     * @param FormInterface $form
     * @param array $schema
     * @return type
     */
    protected function addConstraints(FormInterface $form, array $schema, FormInterface $formRoot)
    {
        $propertyName = $form->getName();
        $data = $form->getConfig()->getDataClass();
        $ignoreClass = [File::class];
        if (empty($data) || in_array($data,$ignoreClass)) {
            $formIterate = $form;
             while($formIterate->getParent() !== null){
                $formIterate = $formIterate->getParent();
                if($formIterate){
                    $dataClass = $formIterate->getConfig()->getDataClass();
                    if(!empty($dataClass) && !in_array($dataClass,$ignoreClass)){
                        $data = $formIterate->getConfig()->getDataClass();
                        break;
                    }
                }
            }
            
        }
//        if ($propertyName == "vehiclePhotoFile") {
//            var_dump($form->getConfig()->getDataClass());
//            var_dump($form->getConfig());
//            var_dump($data);
//            die;
//        }
        $schema['constraints'] = [];
        if ($constraints = $form->getConfig()->getOption('constraints')) {
            
        } else {
            $groups = $this->getValidationGroups($form);

            if (!$groups || !$this->validator->hasMetadataFor($data)) {
                return $schema;
            }

            $metadata = $this->validator->getMetadataFor($data);

            if (isset($metadata->properties[$propertyName]) && ($property = $metadata->properties[$propertyName]) !== null && count($property->constraintsByGroup) > 0 && count($groups) > 0) {
                foreach ($groups as $group) {
                    if (isset($property->constraintsByGroup[$group])) {
                        foreach ($property->constraintsByGroup[$group] as $constraint) {
                            //Evitar duplicidad
                            if (!in_array($constraint, $constraints)) {
                                $constraints[] = $constraint;
                            }
                        }
                    }
                }
            }
        }
        $schema['constraints'] = $constraints;

        return $schema;
    }

    /**
     * Añade el atributo data
     * @author  Máximo Sojo <maxsojo13@gmail.com>
     * @param FormInterface $form
     * @param array $schema
     */
    protected function addData(FormInterface $form, array $schema)
    {
        if ($data = $form->getConfig()->getOption('data')) {
            $schema['data'] = $data;
        }

        return $schema;
    }

    /**
     * Returns the validation groups of the given form.
     *
     * @return string|GroupSequence|(string|GroupSequence)[] The validation groups
     */
    private function getValidationGroups(FormInterface $form)
    {
        // Determine the clicked button of the complete form tree
        $clickedButton = null;

        if (method_exists($form, 'getClickedButton')) {
            $clickedButton = $form->getClickedButton();
        }

        if (null !== $clickedButton) {
            $groups = $clickedButton->getConfig()->getOption('validation_groups');

            if (null !== $groups) {
                return self::resolveValidationGroups($groups, $form);
            }
        }

        do {
            $groups = $form->getConfig()->getOption('validation_groups');

            if (null !== $groups) {
                return self::resolveValidationGroups($groups, $form);
            }

            if (isset($this->resolvedGroups[$form])) {
                return $this->resolvedGroups[$form];
            }

            $form = $form->getParent();
        } while (null !== $form);

        return [Constraint::DEFAULT_GROUP];
    }

    /**
     * Post-processes the validation groups option for a given form.
     *
     * @param string|GroupSequence|(string|GroupSequence)[]|callable $groups The validation groups
     *
     * @return GroupSequence|(string|GroupSequence)[] The validation groups
     */
    private static function resolveValidationGroups($groups, FormInterface $form)
    {
        if (!\is_string($groups) && \is_callable($groups)) {
            $groups = $groups($form);
        }

        if ($groups instanceof GroupSequence) {
            return $groups->groups;
        }

        return (array) $groups;
    }

    /**
     * @required
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
        return $this;
    }

}
