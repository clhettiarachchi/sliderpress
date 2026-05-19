<?php

/**
 * SliderPress -- Custom post type registrations.
 *
 * Registers sliderpress_show (named slideshow container) and
 * sliderpress_slide (individual slide, child of a show).
 * Both CPTs are non-public and managed exclusively via the plugin UI.
 *
 * @package SliderPress
 */

declare(strict_types=1);

namespace SliderPress;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class CPT
 */
final class CPT
{

    /**
     * Post type slugs -- use these constants everywhere; never raw strings.
     */
    public const SHOW  = 'sliderpress_show';
    public const SLIDE = 'sliderpress_slide';

    /**
     * Register both post types.
     */
    public function register(): void
    {
        $this->register_show();
        $this->register_slide();
    }

    /**
     * Named slideshow container CPT.
     */
    private function register_show(): void
    {
        $labels = [
            'name'               => __('Slideshows', 'sliderpress'),
            'singular_name'      => __('Slideshow', 'sliderpress'),
            'add_new'            => __('Add New', 'sliderpress'),
            'add_new_item'       => __('Add New Slideshow', 'sliderpress'),
            'edit_item'          => __('Edit Slideshow', 'sliderpress'),
            'new_item'           => __('New Slideshow', 'sliderpress'),
            'view_item'          => __('View Slideshow', 'sliderpress'),
            'search_items'       => __('Search Slideshows', 'sliderpress'),
            'not_found'          => __('No slideshows found.', 'sliderpress'),
            'not_found_in_trash' => __('No slideshows found in trash.', 'sliderpress'),
            'menu_name'          => __('SliderPress', 'sliderpress'),
        ];

        register_post_type(self::SHOW, [
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => false, // Admin\Admin builds the menu manually.
            'show_in_rest'       => true,  // Required for block editor reference picker.
            'rest_base'          => 'sliderpress-shows',
            'capability_type'    => 'post',
            'hierarchical'       => false,
            'supports'           => ['title', 'revisions'],
            'has_archive'        => false,
            'rewrite'            => false,
            'delete_with_user'   => false,
        ]);
    }

    /**
     * Individual slide CPT -- always a child of sliderpress_show.
     */
    private function register_slide(): void
    {
        $labels = [
            'name'               => __('Slides', 'sliderpress'),
            'singular_name'      => __('Slide', 'sliderpress'),
            'add_new_item'       => __('Add New Slide', 'sliderpress'),
            'edit_item'          => __('Edit Slide', 'sliderpress'),
            'not_found'          => __('No slides found.', 'sliderpress'),
            'not_found_in_trash' => __('No slides found in trash.', 'sliderpress'),
        ];

        register_post_type(self::SLIDE, [
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => false, // Managed exclusively via the show edit screen.
            'show_in_menu'       => false,
            'show_in_rest'       => false,
            'capability_type'    => 'post',
            'hierarchical'       => false,
            'supports'           => ['title', 'page-attributes'], // page-attributes enables menu_order.
            'has_archive'        => false,
            'rewrite'            => false,
            'delete_with_user'   => false,
        ]);

        $this->register_slide_meta();
    }

    /**
     * Register post meta for sliderpress_slide with typed sanitise callbacks.
     *
     * Using register_post_meta (rather than bare meta keys) provides automatic
     * sanitisation and a clear schema for each field.
     */
    private function register_slide_meta(): void
    {
        $fields = [
            'image_id'  => [
                'type'              => 'integer',
                'description'       => __('Attachment ID of the slide image.', 'sliderpress'),
                'sanitize_callback' => 'absint',
            ],
            'title'     => [
                'type'              => 'string',
                'description'       => __('Optional slide title.', 'sliderpress'),
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'caption'   => [
                'type'              => 'string',
                'description'       => __('Optional slide caption.', 'sliderpress'),
                'sanitize_callback' => 'wp_kses_post',
            ],
            'cta_label' => [
                'type'              => 'string',
                'description'       => __('Call-to-action button label.', 'sliderpress'),
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'cta_url'   => [
                'type'              => 'string',
                'description'       => __('Call-to-action button URL.', 'sliderpress'),
                'sanitize_callback' => 'esc_url_raw',
            ],
            'alt_text'  => [
                'type'              => 'string',
                'description'       => __('Alt text override for the slide image.', 'sliderpress'),
                'sanitize_callback' => 'sanitize_text_field',
            ],
        ];

        foreach ($fields as $key => $args) {
            register_post_meta(self::SLIDE, '_sp_' . $key, array_merge([
                'single'        => true,
                'show_in_rest'  => false,
                'auth_callback' => [$this, 'meta_auth_callback'],
            ], $args));
        }
    }

    /**
     * Named auth callback for slide meta -- required so hooks can be removed.
     *
     * @return bool
     */
    public function meta_auth_callback(): bool
    {
        return current_user_can('edit_posts');
    }
}
