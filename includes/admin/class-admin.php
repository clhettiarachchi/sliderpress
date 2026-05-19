<?php

/**
 * SliderPress -- Admin area bootstrap.
 *
 * Registers menus, enqueues admin assets, and wires screen-specific classes.
 *
 * @package SliderPress
 */

declare(strict_types=1);

namespace SliderPress\Admin;

use SliderPress\CPT;
use SliderPress\Settings;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Admin
 */
final class Admin
{

    /**
     * Register all admin hooks.
     */
    public function register_hooks(): void
    {
        add_action('admin_menu',            [$this, 'register_menus']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);

        $meta_box = new SlideMetaBox();
        $meta_box->register_hooks();

        $settings = new Settings();
        $settings->register_hooks();
    }

    /**
     * Register top-level menu and sub-pages.
     */
    public function register_menus(): void
    {
        // Top-level menu -- points to the slideshow list table.
        add_menu_page(
            __('SliderPress', 'sliderpress'),
            __('SliderPress', 'sliderpress'),
            'edit_posts',
            'sliderpress',
            [$this, 'render_list_page'],
            'dashicons-images-alt2',
            58
        );

        // Mirrors top-level so the sub-menu label is explicit.
        add_submenu_page(
            'sliderpress',
            __('All Slideshows', 'sliderpress'),
            __('All Slideshows', 'sliderpress'),
            'edit_posts',
            'sliderpress',
            [$this, 'render_list_page']
        );

        add_submenu_page(
            'sliderpress',
            __('Add New Slideshow', 'sliderpress'),
            __('Add New', 'sliderpress'),
            'edit_posts',
            'post-new.php?post_type=' . CPT::SHOW
        );

        add_submenu_page(
            'sliderpress',
            __('SliderPress Settings', 'sliderpress'),
            __('Settings', 'sliderpress'),
            'manage_options',
            'sliderpress-settings',
            [$this, 'render_settings_page']
        );
    }

    /**
     * Enqueue admin assets on SliderPress screens only.
     *
     * @param string $hook Current admin page hook suffix.
     */
    public function enqueue_assets(string $hook): void
    {
        if (! $this->is_sliderpress_screen($hook)) {
            return;
        }

        wp_enqueue_style(
            'sliderpress-admin',
            SLIDERPRESS_ASSETS . 'css/admin.css',
            [],
            SLIDERPRESS_VERSION
        );

        wp_enqueue_script(
            'sliderpress-admin',
            SLIDERPRESS_ASSETS . 'js/admin.js',
            ['jquery', 'jquery-ui-sortable', 'wp-api-fetch'],
            SLIDERPRESS_VERSION,
            true
        );

        wp_localize_script('sliderpress-admin', 'sliderpressAdmin', [
            'nonce'   => wp_create_nonce('sliderpress_admin'),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'i18n'    => [
                'confirmDelete'     => __('Delete this slide? This cannot be undone.', 'sliderpress'),
                'confirmDeleteShow' => __('Delete this slideshow and all its slides?', 'sliderpress'),
                'saved'             => __('Saved.', 'sliderpress'),
            ],
        ]);
    }

    /**
     * Render the slideshow list screen.
     */
    public function render_list_page(): void
    {
        require SLIDERPRESS_DIR . 'includes/admin/views/list-table.php';
    }

    /**
     * Render the settings screen.
     */
    public function render_settings_page(): void
    {
        require SLIDERPRESS_DIR . 'includes/admin/views/settings.php';
    }

    /**
     * Determine whether the current screen belongs to SliderPress.
     *
     * @param string $hook Admin page hook suffix.
     * @return bool
     */
    private function is_sliderpress_screen(string $hook): bool
    {
        $sliderpress_hooks = [
            'toplevel_page_sliderpress',
            'sliderpress_page_sliderpress-settings',
        ];

        if (in_array($hook, $sliderpress_hooks, true)) {
            return true;
        }

        // Also load on the CPT edit screen.
        $screen = get_current_screen();

        return $screen && $screen->post_type === CPT::SHOW;
    }
}
