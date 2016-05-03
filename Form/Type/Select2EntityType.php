<?php

/*
 * This file is part of the TecnoReady Solutions C.A. package.
 * 
 * (c) www.tecnoready.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\ChoiceList\DoctrineChoiceLoader;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Bridge\Doctrine\Form\ChoiceList\IdReader;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Bridge\Doctrine\Form\EventListener\MergeDoctrineCollectionListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Factory\CachingFactoryDecorator;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Factory\DefaultChoiceListFactory;
use Symfony\Component\Form\ChoiceList\Factory\PropertyAccessDecorator;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Description of Select2EntityType
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class Select2EntityType extends AbstractType
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    
    public function configureOptions(OptionsResolver $resolver) {
        $compound = function (Options $options) {
            return $options['multiple'];
        };
        
        $resolver->setDefaults(array(
            'entity_alias' => null,
            'use_ajax' => false,
            
            'attr'                            => array(),
            'choices'                            => array(),
            'compound'                        => $compound,
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
            'container_css_class'            => '',
            'dropdown_css_class'             => '',
            'dropdown_item_css_class'        => '',

            'dropdown_auto_width'            => false,

            'template'                        => 'TecnocreacionesToolsBundle:ExtraFormTypes/Type:type_model_autocomplete.html.twig',
            'empty_value' => null,
            'property' => null,
        ));
        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $entities = $this->container->getParameter('tecnocreaciones.extra_form_types.autocomplete_entities');

//        $options['property'] = $entities[$options['entity_alias']]['property'];

        
        $builder->addViewTransformer(new \Tecnocreaciones\Bundle\ToolsBundle\Form\DataTransformer\ModelToIdPropertyTransformer(
            new \Tecnocreaciones\Bundle\ToolsBundle\Model\ModelManager($this->container->get('doctrine')),
            $options['class'],
            $options['property'],
            $options['multiple'],
            $options['to_string_callback']
        ), true);

        $builder->setAttribute('property', $options['property']);
        $builder->setAttribute('callback', $options['callback']);
        $builder->setAttribute('minimum_input_length', $options['minimum_input_length']);
        $builder->setAttribute('items_per_page', $options['items_per_page']);
        $builder->setAttribute('req_param_name_page_number', $options['req_param_name_page_number']);
        $builder->setAttribute('disabled', $options['disabled'] || $options['read_only']);
        $builder->setAttribute('to_string_callback', $options['to_string_callback']);
        
        if ($options['multiple']) {
            $resizeListener = new \Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener(
                'hidden', array(), true, true, true
            );
            
            $builder->addEventSubscriber($resizeListener);
        }
    }
    
    public function buildView(\Symfony\Component\Form\FormView $view, \Symfony\Component\Form\FormInterface $form, array $options) {
//       $view->vars['entity_alias'] = $form->getConfig()->getAttribute('entity_alias');
        
        $view->vars['placeholder'] = $options['placeholder'];
        $view->vars['choices'] = $options['choices'];
        $view->vars['multiple'] = $options['multiple'];
        $view->vars['minimum_input_length'] = $options['minimum_input_length'];
        $view->vars['items_per_page'] = $options['items_per_page'];
        $view->vars['width'] = $options['width'];

        // ajax parameters
        $view->vars['use_ajax'] = $options['use_ajax'];
        $view->vars['url'] = $options['url'];
        $view->vars['route'] = $options['route'];
        $view->vars['req_params'] = $options['req_params'];
        $view->vars['req_param_name_search'] = $options['req_param_name_search'];
        $view->vars['req_param_name_page_number'] = $options['req_param_name_page_number'];
        $view->vars['req_param_name_items_per_page'] = $options['req_param_name_items_per_page'];

        // CSS classes
        $view->vars['container_css_class'] = $options['container_css_class'];
        $view->vars['dropdown_css_class'] = $options['dropdown_css_class'];
        $view->vars['dropdown_item_css_class'] = $options['dropdown_item_css_class'];

        $view->vars['dropdown_auto_width'] = $options['dropdown_auto_width'];

        // template
        $view->vars['template'] = $options['template'];

        $view->vars['context'] = $options['context'];

    }
  
    public function getParent() {
        return \Symfony\Component\Form\Extension\Core\Type\FormType::class;
    }

    public function getBlockPrefix() {
        return 'select2_entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
