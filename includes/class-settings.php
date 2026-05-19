<?php

/**
 * SliderPress -- Plugin-wide settings.
 *
 * Stored as a single option array. Registered with the Settings API
 * and accessible anywhere via the static Settings::get() accessor.
 *
 * @package SliderPress
 */

declare(strict_types=1);

namespace SliderPress;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Settings
 */
final class Settings
{

    /**
     * Option key in wp_options.
     */
    public const OPTION_KEY = 'sliderpress_settings';

    /**
     * Default values -- used when the option does not exist yet.
     */
    public const DEFAULTS = [
        'image_size'         => 'large',
        'default_transition' => 'fade',
        'default_interval'   => 5,
        'default_ratio'      => '16:9',
        'show_arrows'        => true,
        'show_dots'          => true,
        'footer_scripts'     => true,
        'shortcode_support'  => true,
        'uninstall_cleanup'  => false,
    ];

    /**
     * Register hooks.
     */
    public function register_hooks(): void
    {
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * Register settings with the Settings API.
     */
    public function register_settings(): void
    {
        register_setting(
            'sliderpress_settings_group',
            self::OPTION_KEY,
            [
                'type'              => 'array',
                'sanitize_callback' => [$this, 'sanitize'],
                'default'           => self::DEFAULTS,
            ]
        );

        add_settings_section(
            'sp_general',
            __('General Defaults', 'sliderpress'),
            '__return_false',
            'sliderpress_settings'
        );

        $this->add_fields();
    }

    /**
     * Register individual settings fields.
     */
    private function add_fields(): void
    {
        $fields = [
            [
                'id'    => 'image_size',
                'label' => __('Image size', 'sliderpress'),
                'cb'    => [$this, 'field_image_size'],
            ],
            [
                'id'    => 'default_transition',
                'label' => __('Default transition', 'sliderpress'),
                'cb'    => [$this, 'field_transition'],
            ],
            [
                'id'    => 'default_interval',
                'label' => __('Autoplay interval (seconds)', 'sliderpress'),
                'cb'    => [$this, 'field_interval'],
            ],
            [
                'id'    => 'footer_scripts',
                'label' => __('Enqueue JS in footer', 'sliderpress'),
                'cb'    => [$this, 'field_checkbox'],
                'extra' => 'footer_scripts',
            ],
            [
                'id'    => 'shortcode_support',
                'label' => __('Enable shortcode [sliderpress]', 'sliderpress'),
                'cb'    => [$this, 'field_checkbox'],
                'extra' => 'shortcode_support',
            ],
            [
                'id'    => 'uninstall_cleanup',
                'label' => __('Remove all data on uninstall', 'sliderpress'),
                'cb'    => [$this, 'field_checkbox'],
                'extra' => 'uninstall_cleanup',
            ],
        ];

        foreach ($fields as $field) {
            add_settings_field(
                'sp_' . $field['id'],
                $field['label'],
                $field['cb'],
                'sliderpress_settings',
                'sp_general',
                $field['extra'] ?? $field['id']
            );
        }
    }

    /** @internal */
    public function field_image_size(): void
    {
        $current = self::get('image_size');
        $sizes   = ['thumbnail', 'medium', 'medium_large', 'large', 'full'];

        echo '<select name="' . esc_attr(self::OPTION_KEY) . '[image_size]">';

        foreach ($sizes as $size) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr($size),
                selected($current, $size, false),
                esc_html($size)
            );
        }

        echo '</select>';
    }

    /** @internal */
    public function field_transition(): void
    {
        $current = self::get('default_transition');

        echo '<select name="' . esc_attr(self::OPTION_KEY) . '[default_transition]">';

        foreach (['fade', 'slide'] as $t) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr($t),
                selected($current, $t, false),
                esc_html(ucfirst($t))
            );
        }

        echo '</select>';
    }

    /** @internal */
    public function field_interval(): void
    {
        printf(
            '<input type="number" min="1" max="30" step="1" name="%s[default_interval]" value="%d">',
            esc_attr(self::OPTION_KEY),
            (int) self::get('default_interval')
        );
    }

    /** @internal */
    public function field_checkbox(string $key): void
    {
        printf(
            '<input type="checkbox" name="%s[%s]" value="1"%s>',
            esc_attr(self::OPTION_KEY),
            esc_attr($key),
            checked((bool) self::get($key), true, false)
        );
    }

    /**
     * Sanitise the settings array before saving.
     *
     * @param mixed $input Raw POST values.
     * @return array Sanitised settings.
     */
    public function sanitize($input): array
    {
        $clean = self::DEFAULTS;

        if (! is_array($input)) {
            return $clean;
        }

        $clean['image_size']         = sanitize_key($input['image_size'] ?? 'large');
        $clean['default_transition'] = in_array($input['default_transition'] ?? '', ['fade', 'slide'], true)
            ? $input['default_transition']
            : 'fade';
        $clean['default_interval']   = min(30, max(1, absint($input['default_interval'] ?? 5)));
        $clean['footer_scripts']     = ! empty($input['footer_scripts']);
        $clean['shortcode_support']  = ! empty($input['shortcode_support']);
        $clean['uninstall_cleanup']  = ! empty($input['uninstall_cleanup']);

        return $clean;
    }

    /**
     * Retrieve a single setting value, falling back to the default.
     *
     * @param string $key Setting key.
     * @return mixed
     */
    public static function get(string $key): mixed
    {
        $options = get_option(self::OPTION_KEY, self::DEFAULTS);

        if (! is_array($options)) {
            $options = self::DEFAULTS;
        }

        return $options[$key] ?? (self::DEFAULTS[$key] ?? null);
    }
}
