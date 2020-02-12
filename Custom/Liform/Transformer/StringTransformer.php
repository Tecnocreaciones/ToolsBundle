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
	/**
     * {@inheritdoc}
     */
    public function transform(FormInterface $form, array $extensions = [], $widget = null)
    {
        $schema = ['type' => 'string'];
        $schema = $this->addCommonSpecs($form, $schema, $extensions, $widget);
        $schema = $this->addMaxLength($form, $schema);
        $schema = $this->addMinLength($form, $schema);
        $schema = $this->addHelp($form, $schema);
        $schema = $this->addData($form, $schema);

        return $schema;
    }

    /**
     * @param FormInterface $form
     * @param array         $schema
     *
     * @return array
     */
    protected function addHelp(FormInterface $form, array $schema)
    {
        $translationDomain = $form->getConfig()->getOption('translation_domain');
        if ($attr = $form->getConfig()->getOption('attr')) {
            if (isset($attr['help'])) {
                $schema['attr']['help'] = $this->translator->trans($attr['help'], [], $translationDomain);
            }
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
        $schema['data'] = "";
    	if ($data = $form->getConfig()->getOption('data')) {
            $schema['data'] = $data;
        }

        return $schema;
    }
}
