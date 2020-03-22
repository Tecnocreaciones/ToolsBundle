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
use Limenius\Liform\Transformer\AbstractTransformer;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
class BooleanTransformer extends AbstractTransformer
{
    use \Tecnocreaciones\Bundle\ToolsBundle\Custom\Liform\CommonFunctionsTrait;
    
    /**
     * {@inheritdoc}
     */
    public function transform(FormInterface $form, array $extensions = [], $widget = null)
    {
        $schema = ['type' => 'boolean'];
        $schema = $this->addCommonSpecs($form, $schema, $extensions, $widget);
        $emptyData = $form->getConfig()->getOption('empty_data');
        if(($emptyData instanceof \Closure) === false){
            $schema["empty_data"] = $emptyData;
        }
        $schema = $this->addConstraints($form, $schema);
        return $schema;
    }
}
