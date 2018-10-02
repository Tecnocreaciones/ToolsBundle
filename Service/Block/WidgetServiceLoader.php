<?php

/*
 * This file is part of the Witty Growth C.A. - J406095737 package.
 * 
 * (c) www.mpandco.com
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\Block;

use Sonata\BlockBundle\Block\Loader\ServiceLoader;
use Sonata\BlockBundle\Block\BlockLoaderInterface;
use Sonata\BlockBundle\Model\Block;

/**
 * Carga los bloques definidos como widgets
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
class WidgetServiceLoader extends ServiceLoader
{
    /**
     * {@inheritdoc}
     */
    public function load($configuration)
    {
        if (!in_array($configuration['widget'], $this->types)) {
            throw new \RuntimeException(sprintf(
                'The block type "%s" does not exist',
                $configuration['widget']
            ));
        }

        $block = new Block();
        $block->setId(uniqid());
        $block->setType($configuration['widget']);
        $block->setEnabled(true);
        $block->setCreatedAt(new \DateTime());
        $block->setUpdatedAt(new \DateTime());
        $block->setSettings(isset($configuration['settings']) ? $configuration['settings'] : []);

        return $block;
    }

    /**
     * {@inheritdoc}
     */
    public function support($configuration)
    {
        if (!is_array($configuration)) {
            return false;
        }

        if (!isset($configuration['widget'])) {
            return false;
        }

        return true;
    }
}
