<?php

/**
 * SliderPress -- Slideshow wrapper template.
 *
 * @package SliderPress
 *
 * @var string $uid         Unique element ID.
 * @var string $ratio_css   CSS aspect-ratio value (e.g. "16/9").
 * @var string $transition  Transition type slug.
 * @var string $data_attrs  Serialised data-* attribute string.
 * @var string $slides      Rendered slides HTML from slides.php.
 * @var bool   $show_arrows Whether to render navigation arrows.
 * @var bool   $show_dots   Whether to render dot indicators.
 * @var int    $slide_count Total number of slides.
 */

?>
<figure
    id="<?php echo esc_attr($uid); ?>"
    class="sp-slideshow swiper sp-slideshow--<?php echo esc_attr($transition); ?>"
    role="region"
    aria-label="<?php esc_attr_e('Slideshow', 'sliderpress'); ?>"
    style="--sp-ratio:<?php echo esc_attr($ratio_css); ?>"
    <?php echo $data_attrs; // phpcs:ignore WordPress.Security.EscapeOutput -- pre-escaped in renderer. 
    ?>>
    <?php echo $slides; // phpcs:ignore WordPress.Security.EscapeOutput -- pre-escaped in renderer. 
    ?>

    <?php if ($show_arrows && $slide_count > 1) : ?>
        <button class="sp-arrow sp-arrow--prev" aria-label="<?php esc_attr_e('Previous slide', 'sliderpress'); ?>">
            &#8249;
        </button>
        <button class="sp-arrow sp-arrow--next" aria-label="<?php esc_attr_e('Next slide', 'sliderpress'); ?>">
            &#8250;
        </button>
    <?php endif; ?>

    <?php if ($show_dots && $slide_count > 1) : ?>
        <div
            class="sp-dots swiper-pagination"
            aria-label="<?php esc_attr_e('Slide navigation', 'sliderpress'); ?>"></div>
    <?php endif; ?>

    <div class="sp-live-region" aria-live="polite" aria-atomic="true">
        <?php
        printf(
            /* translators: %d: total number of slides */
            esc_html__('Slide 1 of %d', 'sliderpress'),
            (int) $slide_count
        );
        ?>
    </div>
</figure>