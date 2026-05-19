<?php

/**
 * SliderPress -- Settings page view.
 *
 * Rendered by Admin::render_settings_page(). No logic or DB calls here.
 * Settings are registered and sanitised via class-settings.php.
 *
 * @package SliderPress
 */

if (! defined('ABSPATH')) {
    exit;
}

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('You do not have permission to access this page.', 'sliderpress'));
}
?>
<div class="wrap">

    <h1><?php esc_html_e('SliderPress Settings', 'sliderpress'); ?></h1>

    <form method="post" action="options.php">

        <?php settings_fields('sliderpress_settings_group'); ?>
        <?php do_settings_sections('sliderpress_settings'); ?>
        <?php submit_button(__('Save Settings', 'sliderpress')); ?>

    </form>

</div><!-- .wrap -->