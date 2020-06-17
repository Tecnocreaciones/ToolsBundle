<?php

namespace Tecnocreaciones\Bundle\ToolsBundle\Twig\Extension;

use Tecnoready\Common\Service\Block\WidgetManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BlockExtension extends AbstractExtension
{
    /**
     * @var WidgetManager
     */
    protected $widgetManager;

    /**
     * BlockExtension constructor.
     *
     * @param BlockHelper $widgetManager
     */
    public function __construct(WidgetManager $widgetManager)
    {
        $this->widgetManager = $widgetManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
//            new TwigFunction(
//                'sonata_block_exists',
//                [$this->widgetManager, 'exists']
//            ),
//            new TwigFunction(
//                'sonata_block_render',
//                [$this->widgetManager, 'render'],
//                ['is_safe' => ['html']]
//            ),
            new TwigFunction(
                'tecno_block_render_event',
                [$this->widgetManager, 'renderEvent'],
                ['is_safe' => ['html']]
            ),
//            new TwigFunction(
//                'sonata_block_include_javascripts',
//                [$this->widgetManager, 'includeJavascripts'],
//                ['is_safe' => ['html']]
//            ),
//            new TwigFunction(
//                'sonata_block_include_stylesheets',
//                [$this->widgetManager, 'includeStylesheets'],
//                ['is_safe' => ['html']]
//            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tecno_widget';
    }
}
