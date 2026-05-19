<?php

/**
 * SliderPress -- Core plugin bootstrapper.
 *
 * Instantiates every service class and wires up WordPress hooks.
 *
 * @package SliderPress
 */

declare(strict_types=1);

namespace SliderPress;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Plugin
 *
 * Single entry point. Constructed once via sliderpress_boot().
 */
final class Plugin
{

    /**
     * Boot the plugin.
     */
    public function __construct()
    {
        $this->register_hooks();
    }

    /**
     * Register all top-level hooks.
     */
    private function register_hooks(): void
    {
        add_action('init', [$this, 'load_textdomain']);
        add_action('init', [$this, 'register_cpts']);
        add_action('init', [$this, 'register_blocks']);

        if (is_admin()) {
            $admin = new Admin\Admin();
            $admin->register_hooks();
        }

        $shortcode = new Shortcode();
        $shortcode->register_hooks();
    }

    /**
     * Load plugin translations.
     */
    public function load_textdomain(): void
    {
        load_plugin_textdomain(
            'sliderpress',
            false,
            dirname(plugin_basename(SLIDERPRESS_DIR)) . '/languages'
        );
    }

    /**
     * Register custom post types via the CPT service.
     */
    public function register_cpts(): void
    {
        $cpt = new CPT();
        $cpt->register();
    }

    /**
     * Register all plugin blocks.
     *
     * Each block is registered from its own block.json. The slideshow block
     * uses a dynamic render_callback; the slide block uses a static save().
     */
    public function register_blocks(): void
    {
        register_block_type(SLIDERPRESS_DIR . 'blocks/build/slideshow/block.json');

        register_block_type(SLIDERPRESS_DIR . 'blocks/build/slide/block.json');
    }
}
