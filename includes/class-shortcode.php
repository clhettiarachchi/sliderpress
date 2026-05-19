<?php

/**
 * SliderPress -- Shortcode handler.
 *
 * Registers [sliderpress] for classic editor and widget use.
 * Delegates rendering to Block\Renderer so output is always identical
 * whether content comes from a block or a shortcode.
 *
 * Usage: [sliderpress id="42" transition="fade" autoplay="true" interval="5"]
 *
 * @package SliderPress
 */

declare(strict_types=1);

namespace SliderPress;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Shortcode
 */
final class Shortcode
{

    /**
     * Register WordPress hooks.
     */
    public function register_hooks(): void
    {
        add_shortcode('sliderpress', [$this, 'render']);
    }

    /**
     * Render the shortcode output.
     *
     * @param array|string $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function render($atts): string
    {
        $atts = shortcode_atts([
            'id'         => 0,
            'transition' => '',
            'autoplay'   => '',
            'interval'   => '',
            'arrows'     => '',
            'dots'       => '',
        ], $atts, 'sliderpress');

        $show_id = absint($atts['id']);

        if (! $show_id) {
            return '';
        }

        // Build a minimal attributes array mirroring what the block passes.
        $block_atts = [
            'slideshowId' => $show_id,
            'slides'      => [],
        ];

        if ($atts['transition']) {
            $block_atts['transition'] = sanitize_key($atts['transition']);
        }

        if ('' !== $atts['autoplay']) {
            $block_atts['autoplay'] = rest_sanitize_boolean($atts['autoplay']);
        }

        if ($atts['interval']) {
            $block_atts['autoplayInterval'] = absint($atts['interval']);
        }

        if ('' !== $atts['arrows']) {
            $block_atts['showArrows'] = rest_sanitize_boolean($atts['arrows']);
        }

        if ('' !== $atts['dots']) {
            $block_atts['showDots'] = rest_sanitize_boolean($atts['dots']);
        }

        $renderer = new Block\Renderer();

        return $renderer->render(
            $block_atts,
            '',
            new \WP_Block(['blockName' => 'sliderpress/slideshow', 'attrs' => $block_atts])
        );
    }
}
