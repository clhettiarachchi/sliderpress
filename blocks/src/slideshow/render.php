<?php

/**
 * SliderPress -- Slideshow block render entry point.
 *
 * Delegates to SlideshowRenderer which is loaded by the plugin bootstrap.
 * $attributes, $content, and $block are passed by WordPress automatically.
 *
 * @package SliderPress
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner blocks HTML.
 * @var WP_Block $block      Block instance.
 */

if (! defined('ABSPATH')) {
    exit;
}

$renderer = new SliderPress\SlideshowRenderer();
echo $renderer->render($attributes, $content, $block);
