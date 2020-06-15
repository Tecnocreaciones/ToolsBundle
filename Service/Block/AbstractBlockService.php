<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Service\Block;

use Tecnocreaciones\Bundle\ToolsBundle\Service\Block\BlockContextInterface;
use Tecnocreaciones\Bundle\ToolsBundle\Model\Block\BlockInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base para Bloque
 *
 * @author Carlos Mendoza <inhack20@gmail.com>
 */
abstract class AbstractBlockService implements BlockServiceInterface
{
    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var EngineInterface|null
     */
    protected $templating;

    /**
     * @param string          $name
     * @param EngineInterface $templating
     */
    public function __construct($name,EngineInterface $templating)
    {
        $this->name = $name;
        $this->templating = $templating;
    }

    /**
     * Returns a Response object than can be cacheable.
     *
     * @param string   $view
     * @param array    $parameters
     * @param Response $response
     *
     * @return Response
     */
    public function renderResponse($view, array $parameters = [], Response $response = null)
    {
        return $this->getTemplating()->renderResponse($view, $parameters, $response);
    }

    /**
     * Returns a Response object that cannot be cacheable, this must be used if the Response is related to the user.
     * A good solution to make the page cacheable is to configure the block to be cached with javascript ...
     *
     * @param string   $view
     * @param array    $parameters
     * @param Response $response
     *
     * @return Response
     */
    public function renderPrivateResponse($view, array $parameters = [], Response $response = null)
    {
        return $this->renderResponse($view, $parameters, $response)
            ->setTtl(0)
            ->setPrivate()
        ;
    }

    /**
     * Define the default options for the block.
     *
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKeys(BlockInterface $block)
    {
        return [
            'block_id' => $block->getId(),
            'updated_at' => $block->getUpdatedAt() ? $block->getUpdatedAt()->format('U') : strtotime('now'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getJavascripts($media)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getStylesheets($media)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        return $this->renderResponse($blockContext->getTemplate(), [
            'block_context' => $blockContext,
            'block' => $blockContext->getBlock(),
        ], $response);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplating()
    {
        return $this->templating;
    }
}
