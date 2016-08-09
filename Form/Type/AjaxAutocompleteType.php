<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tecnocreaciones\Bundle\ToolsBundle\Form\DataTransformer\EntityToPropertyTransformer;
use Tecnocreaciones\Bundle\ToolsBundle\Form\DataTransformer\ModelToIdPropertyTransformerAjax;
use Tecnocreaciones\Bundle\ToolsBundle\Model\ModelManager;

class AjaxAutocompleteType extends AbstractType
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
        $resolver->setDefaults(array(
            'entity_alias' => null,
            
            'attr'                            => array(),
            'compound'                        => true,
            'model_manager'                   => null,
            'class'                           => null,
            'admin_code'                      => null,
            'callback'                        => null,
            'multiple'                        => false,
            'width'                           => '200px',
            'context'                         => '',

            'placeholder'                     => '',
            'minimum_input_length'            => 3, //minimum 3 chars should be typed to load ajax data
            'items_per_page'                  => 10, //number of items per page

            'to_string_callback'              => null,

            // ajax parameters
            'url'                             => '',
//            'route'                           => array('name'=>'sonata_admin_retrieve_autocomplete_items', 'parameters'=>array()),
            'route'                           => array('name'=>'tecno_ajaxautocomplete', 'parameters'=>array()),
            'req_params'                      => array(),
            'req_param_name_search'           => 'q',
            'req_param_name_page_number'      => '_page',
            'req_param_name_items_per_page'   => '_per_page',

            // CSS classes
            'dropdown_css_class'             => 'sonata-autocomplete-dropdown',
            'dropdown_item_css_class'        => '',

            'dropdown_auto_width'            => false,

            'template'                        => 'TecnocreacionesToolsBundle:ExtraFormTypes/Type:type_model_autocomplete.html.twig'
        ));
        
        $resolver->setRequired(array('property'));
    }

    public function getName()
    {
        return 'tecno_ajax_autocomplete';
    }

    public function getParent()
    {
        return 'form';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entities = $this->container->getParameter('tecnocreaciones.extra_form_types.autocomplete_entities');

        if (null === $options['entity_alias']) {
            throw new RuntimeException('You must provide a entity alias "entity_alias" and tune it in config file');
        }

        if (!isset ($entities[$options['entity_alias']])){
            throw new RuntimeException('There are no entity alias "' . $options['entity_alias'] . '" in your config file');
        }

        $options['class'] = $entities[$options['entity_alias']]['class'];
//        $options['property'] = $entities[$options['entity_alias']]['property'];

        $builder->setAttribute('entity_alias', $options['entity_alias']);
        
        $builder->addViewTransformer(new ModelToIdPropertyTransformerAjax(new ModelManager($this->container->get('doctrine')), $options['class'], $options['property'], $options['multiple'], $options['to_string_callback']), true);

        $builder->add('title', 'choice', array('attr' => $options['attr'], 'property_path' => '[labels][0]'));
        $builder->add('identifiers', 'collection', array('type' => 'hidden', 'allow_add' => true, 'allow_delete' => true));

        $builder->setAttribute('property', $options['property']);
        $builder->setAttribute('callback', $options['callback']);
        $builder->setAttribute('minimum_input_length', $options['minimum_input_length']);
        $builder->setAttribute('items_per_page', $options['items_per_page']);
        $builder->setAttribute('req_param_name_page_number', $options['req_param_name_page_number']);
        $builder->setAttribute('disabled', $options['disabled'] || $options['read_only']);
        $builder->setAttribute('to_string_callback', $options['to_string_callback']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['entity_alias'] = $form->getConfig()->getAttribute('entity_alias');
        
        $view->vars['width'] = $options['width'];

        // CSS classes
//        $view->vars['container_css_class'] = $options['container_css_class'];
//        $view->vars['dropdown_item_css_class'] = $options['dropdown_item_css_class'];
        
        $view->vars['dropdown_css_class'] = $options['dropdown_css_class'];

//        $view->vars['dropdown_auto_width'] = $options['dropdown_auto_width'];

        // template
        $view->vars['template'] = $options['template'];

        $view->vars['context'] = $options['context'];
        
        $view->vars['placeholder'] = $options['placeholder'];
        $view->vars['multiple'] = $options['multiple'];
        $view->vars['minimum_input_length'] = $options['minimum_input_length'];
        $view->vars['items_per_page'] = $options['items_per_page'];

        // ajax parameters
        $view->vars['url'] = $options['url'];
        $view->vars['route'] = $options['route'];
        $view->vars['req_params'] = $options['req_params'];
        $view->vars['req_param_name_search'] = $options['req_param_name_search'];
        $view->vars['req_param_name_page_number'] = $options['req_param_name_page_number'];
        $view->vars['req_param_name_items_per_page'] = $options['req_param_name_items_per_page'];

        // dropdown list css class
        $view->vars['dropdown_css_class'] = $options['dropdown_css_class'];
    }

}
