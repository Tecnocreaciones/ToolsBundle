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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Select2 type
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class Select2Type extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver) {
        $compound = function (\Symfony\Component\OptionsResolver\Options $options) {
            return $options['multiple'];
        };
        
        $resolver->setDefaults(array(
            'attr'                            => array(),
            'compound'                        => $compound,
            'callback'                        => null,
            'multiple'                        => false,
            'width'                           => '200px',
            'placeholder'                     => '',
            'to_string_callback'              => null,

            'empty_value' => null,
        ));
        
    }
    
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {

    }
    
    public function buildView(\Symfony\Component\Form\FormView $view, \Symfony\Component\Form\FormInterface $form, array $options) {
//       $view->vars['entity_alias'] = $form->getConfig()->getAttribute('entity_alias');
        
        $view->vars['placeholder'] = $options['placeholder'];
        $view->vars['multiple'] = $options['multiple'];
        $view->vars['width'] = $options['width'];

    }
  
    public function getParent() {
        return \Symfony\Component\Form\Extension\Core\Type\FormType::class;
    }

    public function getBlockPrefix() {
        return 'select2';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
