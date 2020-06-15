<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\Block;

use Tecnocreaciones\Bundle\ToolsBundle\Model\Block\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
interface BlockServiceInterface
{
     /**
     * @param BlockContextInterface $blockContext
     * @param Response              $response
     *
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null);

    /**
     * @return string
     */
    public function getName();

    /**
     * Define the default options for the block.
     *
     * NEXT_MAJOR: rename this method.
     *
     * @param OptionsResolver $resolver
     *
     */
    public function configureSettings(OptionsResolver $resolver);

    /**
     * @param BlockInterface $block
     */
    public function load(BlockInterface $block);

    /**
     * @deprecated since 3.x, to be removed in 4.0
     *
     * @param string $media
     *
     * @return array
     */
    public function getJavascripts($media);

    /**
     * @deprecated since 3.x, to be removed in 4.0
     *
     * @param string $media
     *
     * @return array
     */
    public function getStylesheets($media);

    /**
     * @param BlockInterface $block
     *
     * @return array
     */
    public function getCacheKeys(BlockInterface $block);
}
