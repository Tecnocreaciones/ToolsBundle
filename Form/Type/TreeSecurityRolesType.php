<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Type;

use Sonata\UserBundle\Form\Transformer\RestoreRolesTransformer;
use Sonata\UserBundle\Security\EditableRolesBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Roles tipo arbol en sonata admin
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class TreeSecurityRolesType extends AbstractType
{
    /**
     * @var EditableRolesBuilder
     */
    protected $rolesBuilder;

    /**
     * @param EditableRolesBuilder $rolesBuilder
     */
    public function __construct(EditableRolesBuilder $rolesBuilder)
    {
        $this->rolesBuilder = $rolesBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        /*
         * The form shows only roles that the current user can edit for the targeted user. Now we still need to persist
         * all other roles. It is not possible to alter those values inside an event listener as the selected
         * key will be validated. So we use a Transformer to alter the value and an listener to catch the original values
         *
         * The transformer will then append non editable roles to the user ...
         */
        $transformer = new RestoreRolesTransformer($this->rolesBuilder);

        // GET METHOD
        $formBuilder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($transformer) {
            $transformer->setOriginalRoles($event->getData());
        });

        // POST METHOD
        $formBuilder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($transformer) {
            $transformer->setOriginalRoles($event->getForm()->getData());
        });

        $formBuilder->addModelTransformer($transformer);
        $formBuilder->setAttribute("choices", []);
        $formBuilder->setAttribute("choice_list", []);
        //var_dump($formBuilder->getOptions());
        //die;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attr = $view->vars['attr'];

        if (isset($attr['class']) && empty($attr['class'])) {
            $attr['class'] = 'sonata-medium';
        }

        $view->vars['attr'] = $attr;
        $view->vars['read_only_choices'] = $options['read_only_choices'];
        //var_dump($options["roles_not_allowed"]);
        //var_dump($view->vars["choices"]);
        //die;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated Remove it when bumping requirements to Symfony 2.7+
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        list($roles, $rolesReadOnly) = $this->rolesBuilder->getRoles();
        //var_export($roles);
        //die;
        $resolver->setDefaults(array(
            'choices' => function (Options $options, $parentChoices) use ($roles) {
                return empty($parentChoices) ? $roles : array();
            },

            'read_only_choices' => function (Options $options) use ($rolesReadOnly) {
                return empty($options['choices']) ? $rolesReadOnly : array();
            },

            'data_class' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return
            method_exists('Symfony\Component\Form\FormTypeInterface', 'setDefaultOptions') ?
                'choice' : // support for symfony < 2.8.0
                'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tecno_security_roles';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
    
    public static function buildStandardRoles($roles,$group) {
        $build = [];
        foreach ($roles as $rol) {
            $ready = [
                $rol => 'value',
                $group => 'data-section',
            ];
            $length = strlen($rol);
            if(substr($rol, $length - 5,$length ) === "ADMIN"){
                $ready["admin.alert"] = "data-description";
            }
            $build[] = $ready;
        }
        return $build;
    }
}
