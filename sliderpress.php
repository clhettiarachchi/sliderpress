<?php

/**
 * Plugin Name:       SliderPress
 * Description:       A lightweight, accessible slideshow block for the WordPress block editor.
 * Version:           0.1.0
 * Requires at least: 6.4
 * Requires PHP:      8.1
 * Author:            Chandika Hettiarachchi
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sliderpress
 * Domain Path:       /languages
 *
 * @package SliderPress
 */

declare(strict_types=1);

namespace SliderPress;

if (! defined('ABSPATH')) {
    exit;
}

define('SLIDERPRESS_VERSION', '0.1.0');
define('SLIDERPRESS_DIR',     plugin_dir_path(__FILE__));
define('SLIDERPRESS_URL',     plugin_dir_url(__FILE__));
define('SLIDERPRESS_ASSETS',  SLIDERPRESS_URL . 'assets/build/');

// Load classes in dependency order -- dependencies before dependants.
require_once SLIDERPRESS_DIR . 'includes/class-settings.php';
require_once SLIDERPRESS_DIR . 'includes/class-cpt.php';
require_once SLIDERPRESS_DIR . 'includes/class-shortcode.php';
require_once SLIDERPRESS_DIR . 'includes/admin/class-slide-meta-box.php';
require_once SLIDERPRESS_DIR . 'includes/admin/class-admin.php';
require_once SLIDERPRESS_DIR . 'includes/class-slideshow-renderer.php';
require_once SLIDERPRESS_DIR . 'includes/class-plugin.php';

add_action('plugins_loaded', __NAMESPACE__ . '\sliderpress_boot');

/**
 * Instantiate the plugin singleton on plugins_loaded.
 */
function sliderpress_boot(): void
{
    new Plugin();
}
