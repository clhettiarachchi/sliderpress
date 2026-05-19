<?php

/**
 * SliderPress -- Slide Manager meta box.
 *
 * Handles registration, rendering, and saving of slides on the
 * sliderpress_show edit screen.
 *
 * @package SliderPress
 */

declare(strict_types=1);

namespace SliderPress\Admin;

use SliderPress\CPT;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class SlideMetaBox
 */
final class SlideMetaBox
{

    /**
     * Register hooks.
     */
    public function register_hooks(): void
    {
        add_action('add_meta_boxes',             [$this, 'register']);
        add_action('save_post_' . CPT::SHOW,     [$this, 'save'], 10, 2);
    }

    /**
     * Register the meta box on the show edit screen.
     */
    public function register(): void
    {
        add_meta_box(
            'sliderpress_slides',
            __('Slides', 'sliderpress'),
            [$this, 'render'],
            CPT::SHOW,
            'normal',
            'high'
        );
    }

    /**
     * Render the meta box HTML.
     *
     * @param \WP_Post $post Current post object.
     */
    public function render(\WP_Post $post): void
    {
        wp_nonce_field('sliderpress_save_slides_' . $post->ID, '_sp_nonce');

        $slides = $this->get_slides($post->ID);

        require SLIDERPRESS_DIR . 'includes/admin/views/meta-box-slides.php';
    }

    /**
     * Save slides submitted from the meta box.
     *
     * @param int      $post_id Post ID.
     * @param \WP_Post $post    Post object.
     */
    public function save(int $post_id, \WP_Post $post): void
    {
        // Bail on autosave to prevent partial saves overwriting complete data.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        if (! isset($_POST['_sp_nonce'])) {
            return;
        }

        if (! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_sp_nonce'])), 'sliderpress_save_slides_' . $post_id)) {
            return;
        }

        $submitted = isset($_POST['sp_slides']) && is_array($_POST['sp_slides'])
            ? $_POST['sp_slides']
            : [];

        $existing_ids = $this->get_slide_ids($post_id);
        $kept_ids     = [];

        foreach ($submitted as $order => $data) {
            $slide_id = absint($data['slide_id'] ?? 0);

            if ($slide_id && in_array($slide_id, $existing_ids, true)) {
                // Update sort order on existing slide.
                wp_update_post([
                    'ID'         => $slide_id,
                    'menu_order' => (int) $order,
                ]);
            } else {
                // Insert new slide as a child of the show.
                $slide_id = wp_insert_post([
                    'post_type'   => CPT::SLIDE,
                    'post_status' => 'publish',
                    'post_parent' => $post_id,
                    'menu_order'  => (int) $order,
                ]);

                if (is_wp_error($slide_id)) {
                    continue;
                }
            }

            update_post_meta($slide_id, '_sp_image_id',  absint($data['image_id'] ?? 0));
            update_post_meta($slide_id, '_sp_title',     sanitize_text_field(wp_unslash($data['title'] ?? '')));
            update_post_meta($slide_id, '_sp_caption',   wp_kses_post(wp_unslash($data['caption'] ?? '')));
            update_post_meta($slide_id, '_sp_cta_label', sanitize_text_field(wp_unslash($data['cta_label'] ?? '')));
            update_post_meta($slide_id, '_sp_cta_url',   esc_url_raw(wp_unslash($data['cta_url'] ?? '')));
            update_post_meta($slide_id, '_sp_alt_text',  sanitize_text_field(wp_unslash($data['alt_text'] ?? '')));

            $kept_ids[] = $slide_id;
        }

        // Delete slides that were removed in the UI.
        foreach (array_diff($existing_ids, $kept_ids) as $removed_id) {
            wp_delete_post((int) $removed_id, true);
        }
    }

	// Helpers -------------------------------------------------------------------

    /**
     * Get all slides for a given show, ordered by menu_order.
     *
     * @param int $show_id Show post ID.
     * @return array[] Array of slide data arrays.
     */
    public function get_slides(int $show_id): array
    {
        $posts = get_posts([
            'post_type'      => CPT::SLIDE,
            'post_parent'    => $show_id,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'no_found_rows'  => true,
        ]);

        $slides = [];

        foreach ($posts as $post) {
            $image_id = (int) get_post_meta($post->ID, '_sp_image_id', true);

            $slides[] = [
                'slide_id'  => $post->ID,
                'image_id'  => $image_id,
                'image_url' => $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '',
                'title'     => (string) get_post_meta($post->ID, '_sp_title',     true),
                'caption'   => (string) get_post_meta($post->ID, '_sp_caption',   true),
                'cta_label' => (string) get_post_meta($post->ID, '_sp_cta_label', true),
                'cta_url'   => (string) get_post_meta($post->ID, '_sp_cta_url',   true),
                'alt_text'  => (string) get_post_meta($post->ID, '_sp_alt_text',  true),
                'order'     => (int) $post->menu_order,
            ];
        }

        return $slides;
    }

    /**
     * Get slide post IDs for a given show.
     *
     * @param int $show_id Show post ID.
     * @return int[]
     */
    private function get_slide_ids(int $show_id): array
    {
        return get_posts([
            'post_type'      => CPT::SLIDE,
            'post_parent'    => $show_id,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ]);
    }
}
