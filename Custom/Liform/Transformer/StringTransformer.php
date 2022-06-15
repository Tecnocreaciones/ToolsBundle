<?php

/*
 * This file is part of the Limenius\Liform package.
 *
 * (c) Limenius <https://github.com/Limenius/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\Transformer;

use Symfony\Component\Form\FormInterface;
use Limenius\Liform\Transformer\StringTransformer as AbstractStringTransformer;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
class StringTransformer extends AbstractStringTransformer
{
    use \Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\CommonFunctionsTrait;
    
    /**
     * {@inheritdoc}
     */
    public function transform(FormInterface $form, array $extensions = [], $widget = null)
    {
        $this->initCommonCustom($form);
        $schema = ['type' => 'string'];
        $schema = $this->addCommonSpecs($form, $schema, $extensions, $widget);
        $schema = $this->addMaxLength($form, $schema);
        $schema = $this->addMinLength($form, $schema);
        $schema = $this->addEmptyData($form, $schema);
        $schema = $this->addData($form, $schema);
        $schema = $this->addCommonCustom($form, $schema);

        return $schema;
    }

    /**
     * @param FormInterface $form
     * @param array         $schema
     *
     * @return array
     */
    protected function addEmptyData(FormInterface $form, array $schema)
    {
        $schema['empty_data'] = null;
    	if ($emptyData = $form->getConfig()->getOption('empty_data')) {
            if($emptyData instanceof \Closure){
                $emptyData = $emptyData($form);
            }
            $schema['empty_data'] = $emptyData;
        }

        return $schema;
    }

    /**
     * @param FormInterface $form
     * @param array         $schema
     *
     * @return array
     */
    protected function addData(FormInterface $form, array $schema)
    {
        $schema['data'] = $form->getData() ?:"";
        if ($data = $form->getConfig()->getOption('data')) {
            $schema['data'] = $data;
        }

        return $schema;
    }
}
