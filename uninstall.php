<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * Removes all SliderPress data only when the user has opted in
 * via the "Remove all data on uninstall" setting.
 *
 * This file runs in an isolated scope — no plugin code is loaded,
 * so we use direct $wpdb calls rather than any plugin classes.
 *
 * @package SliderPress
 */

if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$settings = get_option('sliderpress_settings', []);

if (empty($settings['uninstall_cleanup'])) {
    return;
}

global $wpdb;

// Delete all sliderpress_slide posts and their meta.
$slide_ids = $wpdb->get_col(
    "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'sliderpress_slide'"
);

foreach ($slide_ids as $id) {
    wp_delete_post((int) $id, true);
}

// Delete all sliderpress_show posts and their meta.
$show_ids = $wpdb->get_col(
    "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'sliderpress_show'"
);

foreach ($show_ids as $id) {
    wp_delete_post((int) $id, true);
}

// Remove plugin options.
delete_option('sliderpress_settings');
