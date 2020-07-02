<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Transformer;

use Limenius\Liform\FormUtil;
use Limenius\Liform\ResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Limenius\Liform\Transformer\CompoundTransformer as AbstractCompoundTransformer;
use Exception;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
class CompoundTransformer extends AbstractCompoundTransformer
{

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    /**
     * Convierten las validaciones en validaciones compatible
     * @var \Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\SymfonyConstraintsParser
     */
    private $constraintsParsers;

    /**
     * Cache de meta data de validaciones
     * @var array
     */
    private $metadataClass = [];

    /**
     * Validaciones de una clase
     * @var array
     */
    private $validationsByClass;

    /**
     * {@inheritdoc}
     */
    public function transform(FormInterface $form, array $extensions = [], $widget = null)
    {
        $schema = parent::transform($form, $extensions, $widget);
        $properties = $schema["properties"];
        if ($form->isRoot() === true && !empty($dataClass = $form->getConfig()->getDataClass())) {
            $validationGroups = $form->getConfig()->getOption("validation_groups");
            if(is_array($validationGroups)){
                $this->extractContraints($dataClass, $validationGroups);
            }
        }
//            var_dump($dataClass);
//        var_dump($form->getConfig()->getOption("validation_groups"));
//        var_dump($form->isRoot());
//        var_dump($form->getName());
        foreach ($form->all() as $name => $field) {
//            var_dump($name);
            $transformerData = $this->resolver->resolve($field);
            $properties[$name]["required"] = $transformerData['transformer']->isRequired($field);
            $properties[$name]["disabled"] = $this->isDisabled($field);
            $properties[$name]["property_order"] = $properties[$name]["propertyOrder"];//Orden de campo a renderizar
            unset($properties[$name]["propertyOrder"]);//Elimina campo sin uso
            
            //Si tiene modelo asociado, se busca la metadata y se le agrega las validaciones que esten alli
            if(!empty($dataClass) 
                    && isset($this->validationsByClass[$dataClass]["properties"][$name])
                    && count($this->validationsByClass[$dataClass]["properties"][$name]) > 0
                    ){
                $properties[$name]["constraints"] = 
                        array_merge($properties[$name]["constraints"],$this->validationsByClass[$dataClass]["properties"][$name]);
            }
            
            if (isset($properties[$name]["constraints"]) && count($properties[$name]["constraints"]) > 0) {
                $constraints = [];
                foreach ($properties[$name]["constraints"] as $constraint) {
                    $constraints[] = $this->constraintsParsers->parse($constraint);
                }
                $properties[$name]["constraints"] = $constraints;
            }
        }
        
        unset($schema["required"]);//Esto va en cada campo
        $schema["action"] = $form->getConfig()->getOption('action');
        $schema["method"] = $form->getConfig()->getOption('method');
        $schema["properties"] = $properties;

        return $schema;
    }

    /**
     * @param FormInterface $form
     *
     * @return boolean
     */
    protected function isDisabled(FormInterface $form)
    {
        return $form->getConfig()->getOption('disabled');
    }

    /**
     * Helper - Extract all the constraints
     */
    private function extractContraints($dataClass,array $validationGroups = [])
    {
        if (!isset($this->metadataClass[$dataClass])) {
            try {
                // get meta data from entity
                $this->metadataClass[$dataClass] = $this->validator->getMetadataFor($dataClass);
            } catch (Exception $ex) {
                return;
            } catch (\Symfony\Component\Validator\Exception\NoSuchMetadataException $e) {
                return;
            }
        }
        if (!isset($this->validationsByClass[$dataClass])) {
            $this->validationsByClass[$dataClass] = [
                "constraints" => [],
                "properties" => [],
            ];
            $metadata = $this->metadataClass[$dataClass];
            // loop through members
            foreach ($metadata->members AS $property => $inner) {
                //            var_dump($property);
                //            if ($property == $this->pathName[0]) {
                // init property name index

                foreach ($inner AS $definition) {
                    // if group
                    if (empty($validationGroups)) {
                        foreach ($definition->constraints AS $ungroupedConstraints) {
                            // add constraint object
                            $this->validationsByClass[$dataClass]['constraints'][] = $this->formatConstraint($ungroupedConstraints);
                        }
                    } else {
                        $constraintsProperty = [];
                        foreach ($definition->constraintsByGroup AS $constraintGroup => $constraints) {
                            if (in_array($constraintGroup, $validationGroups)) {
                                foreach ($constraints AS $constraint) {
                                    // add constraint object
//                                    $constraintsProperty[$constraintGroup][] = $this->formatConstraint($constraint);
                                    $constraintsProperty[] = $this->formatConstraint($constraint);
                                }
                            } else {
                                foreach ($validationGroups as $validationGroup) {
                                    if (in_array($constraintGroup, $validationGroup)) {
                                        foreach ($constraints AS $constraint) {
                                            // add constraint object
//                                            $constraintsProperty[$constraintGroup][] = $this->formatConstraint($constraint);
                                            $constraintsProperty[] = $this->formatConstraint($constraint);
                                        }
                                    }
                                }
                            }
                        }
                        $this->validationsByClass[$dataClass]["properties"][$property] = $constraintsProperty;
                    }
                }
                //            }
            }
//            print_r($this->validationsByClass);
        }
    }

    /**
     * 
     * @param type $constraint
     */
    private function formatConstraint($constraint)
    {
//        var_dump($constraint);
//        $toInsert = (array) $constraint;
//        $toInsert['class'] = get_class($constraint);
        return $constraint;
    }

    /**
     * @required
     * @param \Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\SymfonyConstraintsParser $constraintsParsers
     * @return $this
     */
    public function setConstraintsParsers(\Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\SymfonyConstraintsParser $constraintsParsers)
    {
        $this->constraintsParsers = $constraintsParsers;
        return $this;
    }

    /**
     * @required
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     * @return $this
     */
    public function setValidator(\Symfony\Component\Validator\Validator\ValidatorInterface $validator)
    {
        $this->validator = $validator;
        return $this;
    }

}
