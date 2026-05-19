<?php

/**
 * SliderPress -- Slideshow block renderer.
 *
 * Resolves data from block attributes and inner blocks, then delegates
 * all HTML output to template parts in includes/template-parts/slideshow/.
 *
 * @package SliderPress
 */

declare(strict_types=1);

namespace SliderPress;

use SliderPress\Settings;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class SlideshowRenderer
 */
final class SlideshowRenderer
{

    /**
     * Render the slideshow block output.
     *
     * @param array     $attributes Block attributes.
     * @param string    $content    Serialised inner blocks HTML (slide markup).
     * @param \WP_Block $block      Block instance.
     * @return string HTML output.
     */
    public function render(array $attributes, string $content, \WP_Block $block): string
    {
        // Bail early if there are no inner blocks (no slides added yet).
        if (empty($block->inner_blocks)) {
            return '';
        }

        $settings    = $this->resolve_settings($attributes);
        $slide_count = count($block->inner_blocks);

        return $this->render_slideshow($content, $settings, $slide_count);
    }

    /**
     * Settings resolution
     *
     * Merges block-level attributes with global plugin defaults.
     * Block attributes always win; unset values fall back to Settings::get().
     */
    private function resolve_settings(array $attributes): array
    {
        return [
            'transition'       => sanitize_key($attributes['transition'] ?? Settings::get('default_transition')),
            'autoplay'         => (bool) ($attributes['autoplay'] ?? false),
            'autoplayInterval' => max(1, (int) ($attributes['autoplayInterval'] ?? Settings::get('default_interval'))),
            'showArrows'       => (bool) ($attributes['showArrows'] ?? Settings::get('show_arrows')),
            'showDots'         => (bool) ($attributes['showDots'] ?? Settings::get('show_dots')),
            'aspectRatio'      => sanitize_text_field($attributes['aspectRatio'] ?? Settings::get('default_ratio')),
        ];
    }

    /**
     * Slideshow orchestration
     *
     * Prepares all variables and passes them to the slideshow template.
     */
    private function render_slideshow(string $content, array $settings, int $slide_count): string
    {
        $uid       = 'sp-' . wp_unique_id();
        $ratio_css = $this->ratio_to_css_value($settings['aspectRatio']);

        $data_attrs = sprintf(
            'data-transition="%s" data-autoplay="%s" data-interval="%d" data-arrows="%s" data-dots="%s"',
            esc_attr($settings['transition']),
            $settings['autoplay'] ? 'true' : 'false',
            $settings['autoplayInterval'] * 1000,
            $settings['showArrows'] ? 'true' : 'false',
            $settings['showDots']   ? 'true' : 'false'
        );

        $slides = $this->render_template('slides', [
            'slides' => $this->get_slides_from_content($content),
        ]);

        return $this->render_template('slideshow', [
            'uid'         => $uid,
            'ratio_css'   => $ratio_css,
            'transition'  => $settings['transition'],
            'data_attrs'  => $data_attrs,
            'slides'      => $slides,
            'show_arrows' => $settings['showArrows'],
            'show_dots'   => $settings['showDots'],
            'slide_count' => $slide_count,
        ]);
    }

    /**
     * Template loader
     *
     * Loads a template part from includes/template-parts/slideshow/, extracts
     * the provided variables into its scope, and returns the buffered output.
     *
     * @param string $name Template file name without extension.
     * @param array  $vars Variables to extract into the template scope.
     * @return string Rendered HTML.
     */
    private function render_template(string $name, array $vars = []): string
    {
        $path = SLIDERPRESS_DIR . 'template-parts/' . $name . '.php';

        if (! file_exists($path)) {
            return '';
        }

        // phpcs:ignore WordPress.PHP.DontExtract -- intentional use for template rendering.
        extract($vars);

        ob_start();
        require $path;
        return ob_get_clean();
    }

    /**
     * Slide extraction
     *
     * Parses inner block HTML and returns an array of individual slide HTML strings.
     *
     * @param string $content Raw inner blocks HTML.
     * @return string[] Array of individual slide HTML strings.
     */
    private function get_slides_from_content(string $content): array
    {
        preg_match_all('/<div[^>]+class="[^"]*sp-slide[^"]*"[^>]*>.*?<\/div>/s', $content, $matches);

        return $matches[0] ?? [];
    }

    /**
     * Convert an aspect ratio string (e.g. "16:9") to a CSS aspect-ratio value (e.g. "16/9").
     *
     * @param string $ratio Ratio string.
     * @return string CSS aspect-ratio value.
     */
    private function ratio_to_css_value(string $ratio): string
    {
        $parts = explode(':', $ratio);

        if (2 === count($parts) && (float) $parts[0] > 0) {
            return (int) $parts[0] . '/' . (int) $parts[1];
        }

        return '16/9';
    }
}
