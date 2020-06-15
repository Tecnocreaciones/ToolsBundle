<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Twig\Extension;

use Tecnocreaciones\Bundle\ToolsBundle\Service\Block\BlockHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BlockExtension extends AbstractExtension
{
    /**
     * @var BlockHelper
     */
    protected $blockHelper;

    /**
     * BlockExtension constructor.
     *
     * @param BlockHelper $blockHelper
     */
    public function __construct(BlockHelper $blockHelper)
    {
        $this->blockHelper = $blockHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
//            new TwigFunction(
//                'sonata_block_exists',
//                [$this->blockHelper, 'exists']
//            ),
//            new TwigFunction(
//                'sonata_block_render',
//                [$this->blockHelper, 'render'],
//                ['is_safe' => ['html']]
//            ),
            new TwigFunction(
                'tecno_block_render_event',
                [$this->blockHelper, 'renderEvent'],
                ['is_safe' => ['html']]
            ),
//            new TwigFunction(
//                'sonata_block_include_javascripts',
//                [$this->blockHelper, 'includeJavascripts'],
//                ['is_safe' => ['html']]
//            ),
//            new TwigFunction(
//                'sonata_block_include_stylesheets',
//                [$this->blockHelper, 'includeStylesheets'],
//                ['is_safe' => ['html']]
//            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_block';
    }
}
