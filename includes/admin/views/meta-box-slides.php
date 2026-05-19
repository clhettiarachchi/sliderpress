<?php

/**
 * SliderPress -- Slide manager meta box view.
 *
 * Rendered by SlideMetaBox::render(). No logic or DB calls here.
 * Available variables:
 *   $slides  array[]  Slide data arrays from get_slides(), may be empty.
 *   $post    WP_Post  Current post object (set by WP before meta box callback).
 *
 * @package SliderPress
 */

if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="sp-meta-box">

    <div class="sp-meta-box__slides" id="sp-slide-list">

        <?php if (empty($slides)) : ?>

            <p class="sp-meta-box__empty">
                <?php esc_html_e('No slides yet. Click "Add Slide" to get started.', 'sliderpress'); ?>
            </p>

        <?php else : ?>

            <?php foreach ($slides as $index => $slide) : ?>

                <div class="sp-meta-box__slide" data-index="<?php echo (int) $index; ?>">

                    <div class="sp-meta-box__slide-handle">
                        <span class="dashicons dashicons-menu" aria-hidden="true"></span>
                        <span class="screen-reader-text"><?php esc_html_e('Drag to reorder', 'sliderpress'); ?></span>
                    </div>

                    <div class="sp-meta-box__slide-thumb">
                        <?php if ($slide['image_url']) : ?>
                            <img src="<?php echo esc_url($slide['image_url']); ?>"
                                alt="<?php echo esc_attr($slide['alt_text'] ?: __('Slide thumbnail', 'sliderpress')); ?>"
                                width="80"
                                height="60">
                        <?php else : ?>
                            <div class="sp-meta-box__no-thumb">
                                <span class="dashicons dashicons-format-image" aria-hidden="true"></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="sp-meta-box__slide-fields">

                        <input type="hidden"
                            name="sp_slides[<?php echo (int) $index; ?>][slide_id]"
                            value="<?php echo (int) $slide['slide_id']; ?>">

                        <input type="hidden"
                            name="sp_slides[<?php echo (int) $index; ?>][image_id]"
                            value="<?php echo (int) $slide['image_id']; ?>">

                        <p>
                            <label for="sp-slide-<?php echo (int) $index; ?>-title">
                                <?php esc_html_e('Title', 'sliderpress'); ?>
                            </label>
                            <input type="text"
                                id="sp-slide-<?php echo (int) $index; ?>-title"
                                name="sp_slides[<?php echo (int) $index; ?>][title]"
                                value="<?php echo esc_attr($slide['title']); ?>"
                                class="widefat">
                        </p>

                        <p>
                            <label for="sp-slide-<?php echo (int) $index; ?>-caption">
                                <?php esc_html_e('Caption', 'sliderpress'); ?>
                            </label>
                            <textarea id="sp-slide-<?php echo (int) $index; ?>-caption"
                                name="sp_slides[<?php echo (int) $index; ?>][caption]"
                                class="widefat"
                                rows="2"><?php echo esc_textarea($slide['caption']); ?></textarea>
                        </p>

                        <p>
                            <label for="sp-slide-<?php echo (int) $index; ?>-alt-text">
                                <?php esc_html_e('Alt text', 'sliderpress'); ?>
                            </label>
                            <input type="text"
                                id="sp-slide-<?php echo (int) $index; ?>-alt-text"
                                name="sp_slides[<?php echo (int) $index; ?>][alt_text]"
                                value="<?php echo esc_attr($slide['alt_text']); ?>"
                                class="widefat">
                        </p>

                        <p class="sp-meta-box__cta-fields">
                            <label for="sp-slide-<?php echo (int) $index; ?>-cta-label">
                                <?php esc_html_e('CTA label', 'sliderpress'); ?>
                            </label>
                            <input type="text"
                                id="sp-slide-<?php echo (int) $index; ?>-cta-label"
                                name="sp_slides[<?php echo (int) $index; ?>][cta_label]"
                                value="<?php echo esc_attr($slide['cta_label']); ?>"
                                placeholder="<?php esc_attr_e('e.g. Learn more', 'sliderpress'); ?>">

                            <label for="sp-slide-<?php echo (int) $index; ?>-cta-url">
                                <?php esc_html_e('CTA URL', 'sliderpress'); ?>
                            </label>
                            <input type="url"
                                id="sp-slide-<?php echo (int) $index; ?>-cta-url"
                                name="sp_slides[<?php echo (int) $index; ?>][cta_url]"
                                value="<?php echo esc_url($slide['cta_url']); ?>"
                                class="widefat"
                                placeholder="https://">
                        </p>

                    </div><!-- .sp-meta-box__slide-fields -->

                    <div class="sp-meta-box__slide-actions">
                        <button type="button"
                            class="button sp-select-image"
                            data-index="<?php echo (int) $index; ?>"
                            aria-label="<?php esc_attr_e('Change image', 'sliderpress'); ?>">
                            <?php esc_html_e('Change image', 'sliderpress'); ?>
                        </button>
                        <button type="button"
                            class="button button-link-delete sp-remove-slide"
                            data-index="<?php echo (int) $index; ?>"
                            aria-label="<?php esc_attr_e('Remove slide', 'sliderpress'); ?>">
                            <?php esc_html_e('Remove', 'sliderpress'); ?>
                        </button>
                    </div>

                </div><!-- .sp-meta-box__slide -->

            <?php endforeach; ?>

        <?php endif; ?>

    </div><!-- #sp-slide-list -->

    <div class="sp-meta-box__footer">
        <button type="button" class="button button-primary sp-add-slide">
            <?php esc_html_e('Add Slide', 'sliderpress'); ?>
        </button>
    </div>

</div><!-- .sp-meta-box -->

<script type="text/html" id="sp-slide-template">
    <div class="sp-meta-box__slide" data-index="{{index}}">
        <div class="sp-meta-box__slide-handle">
            <span class="dashicons dashicons-menu" aria-hidden="true"></span>
            <span class="screen-reader-text"><?php esc_html_e('Drag to reorder', 'sliderpress'); ?></span>
        </div>
        <div class="sp-meta-box__slide-thumb">
            <div class="sp-meta-box__no-thumb">
                <span class="dashicons dashicons-format-image" aria-hidden="true"></span>
            </div>
        </div>
        <div class="sp-meta-box__slide-fields">
            <input type="hidden" name="sp_slides[{{index}}][slide_id]" value="0">
            <input type="hidden" name="sp_slides[{{index}}][image_id]" value="0">
            <p>
                <label><?php esc_html_e('Title', 'sliderpress'); ?></label>
                <input type="text" name="sp_slides[{{index}}][title]" value="" class="widefat">
            </p>
            <p>
                <label><?php esc_html_e('Caption', 'sliderpress'); ?></label>
                <textarea name="sp_slides[{{index}}][caption]" class="widefat" rows="2"></textarea>
            </p>
            <p>
                <label><?php esc_html_e('Alt text', 'sliderpress'); ?></label>
                <input type="text" name="sp_slides[{{index}}][alt_text]" value="" class="widefat">
            </p>
            <p class="sp-meta-box__cta-fields">
                <label><?php esc_html_e('CTA label', 'sliderpress'); ?></label>
                <input type="text" name="sp_slides[{{index}}][cta_label]" value="" placeholder="<?php esc_attr_e('e.g. Learn more', 'sliderpress'); ?>">
                <label><?php esc_html_e('CTA URL', 'sliderpress'); ?></label>
                <input type="url" name="sp_slides[{{index}}][cta_url]" value="" class="widefat" placeholder="https://">
            </p>
        </div>
        <div class="sp-meta-box__slide-actions">
            <button type="button" class="button sp-select-image" data-index="{{index}}"><?php esc_html_e('Select image', 'sliderpress'); ?></button>
            <button type="button" class="button button-link-delete sp-remove-slide" data-index="{{index}}"><?php esc_html_e('Remove', 'sliderpress'); ?></button>
        </div>
    </div>
</script>