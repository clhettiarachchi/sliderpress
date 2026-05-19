<?php

/**
 * SliderPress -- Slides wrapper template.
 *
 * @package SliderPress
 *
 * @var string[] $slides List of slide HTML strings, each pre-escaped.
 */
?>

<div class="sp-slides swiper-wrapper">
    <?php foreach ($slides as $index => $slide_html) : ?>
        <div
            class="sp-slide-wrap swiper-slide"
            aria-hidden="<?php echo 0 === $index ? 'false' : 'true'; ?>">
            <?php echo $slide_html; // phpcs:ignore WordPress.Security.EscapeOutput -- pre-escaped in renderer. 
            ?>
        </div>
    <?php endforeach; ?>
</div>