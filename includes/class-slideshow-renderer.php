<?php

/**
 * SliderPress -- Slideshow block renderer.
 *
 * Reads inner sliderpress/slide blocks from $content, wraps them in
 * accessible slideshow markup, and enqueues front-end assets.
 * Registered as the render_callback for the sliderpress/slideshow block.
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

        $settings = $this->resolve_settings($attributes);

        $this->enqueue_frontend_assets();

        return $this->build_html($content, $settings, count($block->inner_blocks));
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
     * Asset enqueueing
     *
     * Enqueues front-end JS and CSS once per page load. Guarded by
     * wp_script_is so multiple blocks on one page only enqueue once.
     */
    private function enqueue_frontend_assets(): void
    {
        if (wp_script_is('sliderpress-front', 'enqueued')) {
            return;
        }

        $in_footer = (bool) Settings::get('footer_scripts');

        wp_enqueue_style(
            'sliderpress-front',
            SLIDERPRESS_ASSETS . 'css/slideshow.css',
            [],
            SLIDERPRESS_VERSION
        );

        wp_enqueue_script(
            'sliderpress-front',
            SLIDERPRESS_ASSETS . 'js/slideshow.js',
            [],
            SLIDERPRESS_VERSION,
            $in_footer
        );
    }

    /**
     * HTML builder
     *
     * Wraps the inner blocks HTML ($content) in accessible slideshow markup.
     * The slide HTML itself comes from each sliderpress/slide block's save().
     *
     * @param string $content     Inner blocks HTML.
     * @param array  $settings    Resolved settings.
     * @param int    $slide_count Number of inner slide blocks.
     * @return string
     */
    private function build_html(string $content, array $settings, int $slide_count): string
    {
        $uid         = 'sp-' . wp_unique_id();
        $ratio_style = $this->ratio_to_padding($settings['aspectRatio']);

        $data_attrs = sprintf(
            'data-transition="%s" data-autoplay="%s" data-interval="%d" data-arrows="%s" data-dots="%s"',
            esc_attr($settings['transition']),
            $settings['autoplay'] ? 'true' : 'false',
            $settings['autoplayInterval'] * 1000,
            $settings['showArrows'] ? 'true' : 'false',
            $settings['showDots']   ? 'true' : 'false'
        );

        $html = sprintf(
            '<figure id="%s" class="sp-slideshow sp-slideshow--%s" role="region" aria-label="%s" %s>',
            esc_attr($uid),
            esc_attr($settings['transition']),
            esc_attr__('Slideshow', 'sliderpress'),
            $data_attrs
        );

        // Inline style is intentional -- ratio value is dynamic and cannot be a modifier class.
        $html .= sprintf('<div class="sp-ratio-box" style="%s">', esc_attr($ratio_style));

        // Inner blocks HTML contains the rendered sliderpress/slide blocks.
        // Each slide's save() outputs a .sp-slide div with its image.
        $html .= '<ul class="sp-slides" role="list">';
        $html .= $this->wrap_slides_in_list_items($content, $slide_count);
        $html .= '</ul>';

        if ($settings['showArrows'] && $slide_count > 1) {
            $html .= sprintf(
                '<button class="sp-arrow sp-arrow--prev" aria-label="%s">&#8249;</button>',
                esc_attr__('Previous slide', 'sliderpress')
            );
            $html .= sprintf(
                '<button class="sp-arrow sp-arrow--next" aria-label="%s">&#8250;</button>',
                esc_attr__('Next slide', 'sliderpress')
            );
        }

        $html .= '</div>'; // .sp-ratio-box

        if ($settings['showDots'] && $slide_count > 1) {
            $html .= sprintf(
                '<div class="sp-dots" role="tablist" aria-label="%s">',
                esc_attr__('Slide navigation', 'sliderpress')
            );

            for ($i = 0; $i < $slide_count; $i++) {
                $html .= sprintf(
                    '<button class="sp-dot%s" role="tab" aria-selected="%s" aria-label="%s"></button>',
                    0 === $i ? ' is-active' : '',
                    0 === $i ? 'true' : 'false',
                    /* translators: 1: current slide number, 2: total slides */
                    esc_attr(sprintf(__('Slide %1$d of %2$d', 'sliderpress'), $i + 1, $slide_count))
                );
            }

            $html .= '</div>';
        }

        $html .= sprintf(
            '<div class="sp-live-region" aria-live="polite" aria-atomic="true">%s</div>',
            /* translators: %d: total number of slides */
            esc_html(sprintf(__('Slide 1 of %d', 'sliderpress'), $slide_count))
        );

        $html .= '</figure>';

        return $html;
    }

    /**
     * Wrap each .sp-slide div from inner block content in a <li> element.
     *
     * The first slide gets is-active; all others get aria-hidden="true".
     *
     * @param string $content     Raw inner blocks HTML.
     * @param int    $slide_count Total slide count.
     * @return string List item HTML.
     */
    private function wrap_slides_in_list_items(string $content, int $slide_count): string
    {
        $output = '';
        $index  = 0;

        // Match each .sp-slide block div from the saved slide block output.
        preg_match_all('/<div[^>]+class="[^"]*sp-slide[^"]*"[^>]*>.*?<\/div>/s', $content, $matches);

        foreach ($matches[0] as $slide_html) {
            $is_active   = 0 === $index;
            $aria_hidden = $is_active ? 'false' : 'true';

            $output .= sprintf(
                '<li class="sp-slide-wrap%s" aria-hidden="%s" role="listitem">%s</li>',
                $is_active ? ' is-active' : '',
                $aria_hidden,
                $slide_html
            );

            $index++;
        }

        return $output;
    }

    /**
     * Convert an aspect ratio string (e.g. "16:9") to a padding-top percentage.
     *
     * @param string $ratio Ratio string.
     * @return string CSS padding-top declaration.
     */
    private function ratio_to_padding(string $ratio): string
    {
        $parts = explode(':', $ratio);

        if (2 === count($parts) && (float) $parts[0] > 0) {
            $pct = round(((float) $parts[1] / (float) $parts[0]) * 100, 4);
            return "padding-top:{$pct}%";
        }

        // Default 16:9.
        return 'padding-top:56.25%';
    }
}
